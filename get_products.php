<?php
require_once 'config.php';

// Truy vấn dữ liệu từ bảng sản phẩm
$sql = "SELECT id, status, title, img, category, price, soluong, describes FROM products";
$result = $conn->query($sql);

$products = array();

if ($result->num_rows > 0) {
    // Lưu dữ liệu sản phẩm vào mảng
    while($row = $result->fetch_assoc()) {
        $row['soluong'] = (int)$row['soluong'];
        $products[] = $row;
    }
}

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($products);


$conn->close();
?>
