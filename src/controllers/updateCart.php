<?php
header('Content-Type: application/json');

// Bật chế độ hiển thị lỗi để dễ dàng debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối tới cơ sở dữ liệu
require_once '../../config/config.php';

// Lấy dữ liệu JSON từ phía client
$input = json_decode(file_get_contents('php://input'), true);
$userPhone = $input['phone'];
$cart = $input['cart'];

// Xóa giỏ hàng cũ của người dùng
$sql = "DELETE FROM cart WHERE user_id = (SELECT id FROM users WHERE phone = '$userPhone')";
$result = db_query($conn, $sql);
if (!$result) {
    die(json_encode(["status" => "error", "message" => "Lỗi khi xóa giỏ hàng"]));
}

// Chèn giỏ hàng mới vào cơ sở dữ liệu
foreach ($cart as $item) {
    $productId = $item['id']; // Chỉnh lại key thành 'id' theo đúng dữ liệu bạn cung cấp
    $quantity = $item['soluong']; // Chỉnh lại key thành 'soluong'
    $note = $conn->real_escape_string($item['note']); // Tránh lỗi SQL Injection

    // Thực hiện truy vấn chèn sản phẩm vào giỏ hàng
    $sql = "INSERT INTO cart (user_id, product_id, quantity, note) VALUES (
        (SELECT id FROM users WHERE phone = '$userPhone'),
        '$productId', '$quantity', '$note')";

    $result = db_query($conn, $sql);
    if (!$result) {
        die(json_encode(["status" => "error", "message" => "Lỗi khi chèn giỏ hàng"]));
    }
}

echo json_encode(["status" => "success", "message" => "Giỏ hàng đã được cập nhật."]);

db_close($conn);
?>
