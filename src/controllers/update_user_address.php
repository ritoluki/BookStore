<?php
require_once '../../config/config.php';

// Đọc dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);

$phone = $data['phone'];
$address = $data['address'];
$province = $data['province'] ?? '';
$district = $data['district'] ?? '';
$ward = $data['ward'] ?? '';

// Kiểm tra giá trị đầu vào
if (empty($phone) || empty($address)) {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin cần thiết!"]);
    exit();
}

// Cập nhật địa chỉ trong cơ sở dữ liệu
$sql = "UPDATE users SET address = ?, province = ?, district = ?, ward = ? WHERE phone = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sssss", $address, $province, $district, $ward, $phone);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Cập nhật địa chỉ thành công!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Không có bản ghi nào được cập nhật. Kiểm tra lại số điện thoại."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Đã xảy ra lỗi khi thực thi câu lệnh: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error]);
}

$conn->close();
?>
