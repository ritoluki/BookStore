<?php
header('Content-Type: application/json');

// Kết nối đến cơ sở dữ liệu
require_once '../../config/config.php';

// Nhận dữ liệu JSON từ client
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['soluong'])) {
    echo json_encode(["success" => false, "message" => "Thiếu tham số!"]);
    exit;
}

$id = intval($data['id']);
$soluong = intval($data['soluong']);

// Cập nhật số lượng sản phẩm
$sql = "UPDATE products SET soluong = ? WHERE id = ?";
$result = db_query($conn, $sql, [$soluong, $id]);

if ($result) {
    echo json_encode(["success" => true, "message" => "Cập nhật số lượng thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật số lượng!"]);
}

db_close($conn);
?> 