<?php
require_once '../../config/config.php';

// Lấy dữ liệu từ yêu cầu POST
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$img = isset($_POST['img']) ? trim($_POST['img']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : 'Không Phân Loại';
$price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
$desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

// Chuẩn bị câu lệnh SQL để thêm sản phẩm vào cơ sở dữ liệu
$sql = "INSERT INTO products (title, img, category, price, describes, status) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssisi", $title, $img, $category, $price, $desc, $status);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Sản phẩm đã được thêm vào cơ sở dữ liệu thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu!"]);
}

$stmt->close();
$conn->close();
?>
