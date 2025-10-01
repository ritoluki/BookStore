<?php
require_once '../../config/config.php';

// Đọc dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);

$phone = $data['phone'];

// Kiểm tra giá trị đầu vào
if (empty($phone)) {
    echo json_encode(["success" => false, "message" => "Thiếu số điện thoại!"]);
    exit();
}

// Lấy thông tin địa chỉ từ cơ sở dữ liệu
$sql = "SELECT address, province, district, ward FROM users WHERE phone = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true, 
            "address" => $row['address'] ?? '',
            "province" => $row['province'] ?? '',
            "district" => $row['district'] ?? '',
            "ward" => $row['ward'] ?? ''
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Không tìm thấy người dùng!"]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error]);
}

$conn->close();
?>
