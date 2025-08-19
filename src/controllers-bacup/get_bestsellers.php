<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    // Kiểm tra xem cột min_order_amount có tồn tại không
    $checkColumn = $conn->query("SHOW COLUMNS FROM discounts LIKE 'min_order_amount'");
    $hasMinOrderAmount = $checkColumn && $checkColumn->num_rows > 0;
    
    // Lấy danh sách sách bán chạy dựa trên số lượng đã bán với thông tin giảm giá
    $sql = "SELECT DISTINCT
                p.id,
                p.title,
                p.price,
                p.img,
                p.category,
                p.describes,
                p.status,
                COALESCE(SUM(od.soluong), 0) as sold_quantity,
                p.soluong as current_stock,
                
                -- Thông tin giảm giá tốt nhất (nếu có)
                d.discount_type,
                d.discount_value,
                " . ($hasMinOrderAmount ? "d.min_order_amount," : "") . "
                CASE 
                    WHEN d.discount_type = 'percentage' THEN p.price * (1 - d.discount_value / 100)
                    WHEN d.discount_type = 'fixed_amount' THEN GREATEST(p.price - d.discount_value, 0)
                    ELSE NULL
                END as discounted_price
                
            FROM products p
            LEFT JOIN orderDetails od ON p.id = od.product_id
            LEFT JOIN `order` o ON od.madon = o.id
            LEFT JOIN (
                -- Subquery để lấy discount tốt nhất cho mỗi sản phẩm
                SELECT 
                    dp.product_id,
                    d2.discount_type,
                    d2.discount_value,
                    " . ($hasMinOrderAmount ? "d2.min_order_amount," : "") . "
                    CASE 
                        WHEN d2.discount_type = 'percentage' THEN d2.discount_value
                        WHEN d2.discount_type = 'fixed_amount' THEN d2.discount_value
                        ELSE 0
                    END as discount_priority
                FROM discount_products dp
                INNER JOIN discounts d2 ON dp.discount_id = d2.id
                WHERE d2.status = 1
                  AND NOW() BETWEEN d2.start_date AND d2.end_date
                  AND (d2.max_uses = 0 OR d2.current_uses < d2.max_uses)
                ORDER BY discount_priority DESC
            ) d ON p.id = d.product_id
            
            WHERE p.status = 1 
            AND (o.trangthai IS NULL OR o.trangthai != 4) -- Loại trừ đơn hàng đã hủy
            " . ($hasMinOrderAmount ? "AND (d.min_order_amount IS NULL OR d.min_order_amount = 0 OR p.price >= d.min_order_amount)" : "") . "
            GROUP BY p.id, p.title, p.price, p.img, p.category, p.describes, p.status, p.soluong,
                     d.discount_type, d.discount_value" . ($hasMinOrderAmount ? ", d.min_order_amount" : "") . "
            ORDER BY sold_quantity DESC
            LIMIT 20"; // Lấy top 20 sách bán chạy

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bestsellers = [];
    while ($row = $result->fetch_assoc()) {
        $bestsellers[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'price' => (int)$row['price'],
            'img' => $row['img'],
            'category' => $row['category'],
            'describes' => $row['describes'],
            'status' => (int)$row['status'],
            'sold_quantity' => (int)$row['sold_quantity'],
            'current_stock' => (int)$row['current_stock'],
            'is_bestseller' => (int)$row['sold_quantity'] > 10, // Đánh dấu sách bán chạy
            
            // Thông tin giảm giá
            'discounted_price' => isset($row['discounted_price']) ? (int)$row['discounted_price'] : null,
            'discount_type' => $row['discount_type'],
            'discount_value' => isset($row['discount_value']) ? (float)$row['discount_value'] : null,
            'min_order_amount' => $hasMinOrderAmount ? (float)($row['min_order_amount'] ?? 0) : 0,
            'is_discounted' => isset($row['discounted_price']) && $row['discounted_price'] !== null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'bestsellers' => $bestsellers
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
