<?php
require_once '../../config/config.php';

// Đọc dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);
$fullname = $data['fullname'];
$phone = $data['phone'];
$password = $data['password'];
$status = $data['status'];

// Cập nhật thông tin tài khoản trong cơ sở dữ liệu
$sql = "UPDATE users SET fullname = ?, password = ?, status = ? WHERE phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $fullname, $password, $status, $phone);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Cập nhật tài khoản thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Đã xảy ra lỗi khi cập nhật tài khoản!"]);
}

$stmt->close();
$conn->close();
?>
