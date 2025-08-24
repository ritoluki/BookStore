<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

// Kiểm tra xem cột min_order_amount có tồn tại không (PostgreSQL compatible)
if (isPostgreSQL($conn)) {
    // PostgreSQL
    $checkColumn = $conn->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'discounts' AND column_name = 'min_order_amount'");
    $hasMinOrderAmount = $checkColumn && db_num_rows($checkColumn) > 0;
} else {
    // MySQL
    $checkColumn = $conn->query("SHOW COLUMNS FROM discounts LIKE 'min_order_amount'");
    $hasMinOrderAmount = $checkColumn && db_num_rows($checkColumn) > 0;
}

// Truy vấn dữ liệu từ bảng sản phẩm với số lượng đã bán và thông tin giảm giá
$sql = "SELECT DISTINCT
            p.id, 
            p.status, 
            p.title, 
            p.img, 
            p.category, 
            p.price, 
            p.soluong, 
            p.describes,
            COALESCE(SUM(od.soluong), 0) as sold_quantity,
            
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
        LEFT JOIN orderdetails od ON p.id = od.product_id
        LEFT JOIN \"order\" o ON od.madon = o.id
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
        
        WHERE (o.trangthai IS NULL OR o.trangthai != 4) -- Loại trừ đơn hàng đã hủy
          " . ($hasMinOrderAmount ? "AND (d.min_order_amount IS NULL OR d.min_order_amount = 0 OR p.price >= d.min_order_amount)" : "") . "
        GROUP BY p.id, p.status, p.title, p.img, p.category, p.price, p.soluong, p.describes, 
                 d.discount_type, d.discount_value" . ($hasMinOrderAmount ? ", d.min_order_amount" : "") . "
        ORDER BY p.id";

$result = db_query($conn, $sql);

$products = array();

if ($result && db_num_rows($result) > 0) {
    // Lưu dữ liệu sản phẩm vào mảng
    while($row = db_fetch_assoc($result)) {
        $row['soluong'] = (int)$row['soluong'];
        $row['sold_quantity'] = (int)$row['sold_quantity'];
        $row['is_bestseller'] = (int)$row['sold_quantity'] > 10; // Đánh dấu sách bán chạy
        
        // Xử lý thông tin giảm giá
        if ($row['discounted_price'] !== null) {
            $row['discounted_price'] = (int)$row['discounted_price'];
            $row['discount_value'] = (float)$row['discount_value'];
            $row['is_discounted'] = true;
            if ($hasMinOrderAmount) {
                $row['min_order_amount'] = (float)($row['min_order_amount'] ?? 0);
            }
        } else {
            $row['discounted_price'] = null;
            $row['discount_type'] = null;
            $row['discount_value'] = null;
            $row['is_discounted'] = false;
            if ($hasMinOrderAmount) {
                $row['min_order_amount'] = 0;
            }
        }
        
        $products[] = $row;
    }
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($products);
db_close($conn);
?>