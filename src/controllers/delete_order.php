<?php
require_once '../../config/config.php';

// Đọc dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'] ?? null;

if (!$orderId) {
    echo json_encode(["success" => false, "message" => "Thiếu mã đơn hàng!"]);
    exit;
}

// Xóa chi tiết đơn hàng trước (bảng orderdetails)
$sqlDetail = "DELETE FROM orderdetails WHERE madon = ?";
$stmtDetail = $conn->prepare($sqlDetail);
$stmtDetail->bind_param("s", $orderId);
$stmtDetail->execute();
$stmtDetail->close();

// Xóa đơn hàng
$sql = "DELETE FROM `order` WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $orderId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Xóa đơn hàng thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Đã xảy ra lỗi khi xóa đơn hàng!"]);
}

$stmt->close();
$conn->close();
?> 