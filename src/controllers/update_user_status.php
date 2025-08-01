<?php
header('Content-Type: application/json');

// Kết nối database
require_once '../../config/config.php';

// Lấy dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);
$phone = $data['phone'];
$status = $data['status'];

// Cập nhật trạng thái người dùng
$sql = "UPDATE users SET status = ? WHERE phone = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $status, $phone);

$response = array();
if (mysqli_stmt_execute($stmt)) {
    $response['success'] = true;
    $response['message'] = 'Cập nhật trạng thái thành công';
} else {
    $response['success'] = false;
    $response['message'] = 'Lỗi khi cập nhật trạng thái: ' . mysqli_error($conn);
}

echo json_encode($response);

mysqli_close($conn);
?> 