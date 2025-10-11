<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    // Truy vấn để lấy tất cả sản phẩm với số lượng đã bán
    $sql = "SELECT 
                p.id, 
                p.status, 
                p.title, 
                p.img, 
                p.category, 
                p.price, 
                p.soluong, 
                p.describes,
                COALESCE(SUM(CASE WHEN o.trangthai IS NOT NULL AND o.trangthai != 4 THEN od.soluong ELSE 0 END), 0) as sold_quantity
            FROM products p
            LEFT JOIN orderdetails od ON p.id = od.product_id
            LEFT JOIN `order` o ON od.madon = o.id
            WHERE p.status = 1
            GROUP BY p.id, p.status, p.title, p.img, p.category, p.price, p.soluong, p.describes
            ORDER BY p.id";
    $result = $conn->query($sql);

    $products = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['soluong'] = (int) $row['soluong'];
            $row['sold_quantity'] = (int) $row['sold_quantity'];
            $row['is_bestseller'] = (int) $row['sold_quantity'] > 10;
            $products[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'products' => $products
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>