<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    // Lấy category nếu có (để filter theo category hiện tại)
    $category = $_GET['category'] ?? null;
    
    // Kiểm tra xem cột min_order_amount có tồn tại không
    $checkColumn = $conn->query("SHOW COLUMNS FROM discounts LIKE 'min_order_amount'");
    $hasMinOrderAmount = $checkColumn && $checkColumn->num_rows > 0;
    
    if ($hasMinOrderAmount) {
        // Query với min_order_amount
        $sql = "SELECT DISTINCT p.id, p.title, p.price, p.img, p.category, p.describes, p.status, p.soluong as current_stock,
                       COALESCE(SUM(od.soluong), 0) as sold_quantity,
                       d.discount_type, d.discount_value, d.min_order_amount,
                       CASE 
                           WHEN d.discount_type = 'percentage' THEN p.price * (1 - d.discount_value / 100)
                           WHEN d.discount_type = 'fixed_amount' THEN GREATEST(p.price - d.discount_value, 0)
                           ELSE p.price
                       END as discounted_price
                FROM products p 
                INNER JOIN discount_products dp ON p.id = dp.product_id
                INNER JOIN discounts d ON dp.discount_id = d.id
                LEFT JOIN orderDetails od ON p.id = od.product_id 
                LEFT JOIN `order` o ON od.madon = o.id 
                WHERE p.status = 1 
                AND d.status = 1
                AND NOW() BETWEEN d.start_date AND d.end_date
                AND (d.max_uses = 0 OR d.current_uses < d.max_uses)
                AND (d.min_order_amount = 0 OR p.price >= d.min_order_amount)
                " . ($category ? "AND p.category = ?" : "") . "
                AND (o.trangthai IS NULL OR o.trangthai != 4)
                GROUP BY p.id, p.title, p.price, p.img, p.category, p.describes, p.status, p.soluong, d.discount_type, d.discount_value, d.min_order_amount
                ORDER BY d.discount_value DESC";
    } else {
        // Query không có min_order_amount
        $sql = "SELECT DISTINCT p.id, p.title, p.price, p.img, p.category, p.describes, p.status, p.soluong as current_stock,
                       COALESCE(SUM(od.soluong), 0) as sold_quantity,
                       d.discount_type, d.discount_value,
                       CASE 
                           WHEN d.discount_type = 'percentage' THEN p.price * (1 - d.discount_value / 100)
                           WHEN d.discount_type = 'fixed_amount' THEN GREATEST(p.price - d.discount_value, 0)
                           ELSE p.price
                       END as discounted_price
                FROM products p 
                INNER JOIN discount_products dp ON p.id = dp.product_id
                INNER JOIN discounts d ON dp.discount_id = d.id
                LEFT JOIN orderDetails od ON p.id = od.product_id 
                LEFT JOIN `order` o ON od.madon = o.id 
                WHERE p.status = 1 
                AND d.status = 1
                AND NOW() BETWEEN d.start_date AND d.end_date
                AND (d.max_uses = 0 OR d.current_uses < d.max_uses)
                " . ($category ? "AND p.category = ?" : "") . "
                AND (o.trangthai IS NULL OR o.trangthai != 4)
                GROUP BY p.id, p.title, p.price, p.img, p.category, p.describes, p.status, p.soluong, d.discount_type, d.discount_value
                ORDER BY d.discount_value DESC";
    }
    
    $stmt = $conn->prepare($sql);
    if ($category) {
        $stmt->bind_param("s", $category);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $discountedProducts = [];
    while ($row = $result->fetch_assoc()) {
        $discountedProducts[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'price' => (int)$row['price'],
            'discounted_price' => (int)$row['discounted_price'],
            'img' => $row['img'],
            'category' => $row['category'],
            'describes' => $row['describes'],
            'status' => (int)$row['status'],
            'sold_quantity' => (int)$row['sold_quantity'],
            'current_stock' => (int)$row['current_stock'],
            'discount_type' => $row['discount_type'],
            'discount_value' => (float)$row['discount_value'],
            'min_order_amount' => isset($row['min_order_amount']) ? (float)$row['min_order_amount'] : 0,
            'is_discounted' => true
        ];
    }
    
    echo json_encode([
        'success' => true,
        'discounted_products' => $discountedProducts
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>