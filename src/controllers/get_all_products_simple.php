<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    // Truy vấn đơn giản để lấy tất cả sản phẩm
    $sql = "SELECT id, status, title, img, category, price, soluong, describes FROM products WHERE status = 1 ORDER BY id";
    $result = $conn->query($sql);
    
    $products = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['soluong'] = (int)$row['soluong'];
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
