<?php
require_once '../../config/config.php';

// Đọc dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$action = $data['action']; // 'delete' hoặc 'restore'

// Kiểm tra hành động và cập nhật trạng thái sản phẩm tương ứng
if ($action == 'delete') {
    $status = 0;
} elseif ($action == 'restore') {
    $status = 1;
} else {
    echo json_encode(["success" => false, "message" => "Hành động không hợp lệ!"]);
    exit();
}

// Cập nhật trạng thái sản phẩm trong cơ sở dữ liệu
$sql = "UPDATE products SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $status, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => ($action == 'delete' ? "Xóa sản phẩm thành công!" : "Khôi phục sản phẩm thành công!")]);
} else {
    echo json_encode(["success" => false, "message" => "Đã xảy ra lỗi khi cập nhật sản phẩm!"]);
}

$stmt->close();
$conn->close();
?>
