<?php
require_once '../../config/config.php';

// Đọc dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);
$phone = $data['phone'];

// Xóa tài khoản khỏi cơ sở dữ liệu
$sql = "DELETE FROM users WHERE phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $phone);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Xóa tài khoản thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Đã xảy ra lỗi khi xóa tài khoản!"]);
}

$stmt->close();
$conn->close();
?>
