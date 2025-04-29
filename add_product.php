<?php
require_once 'config.php';

// Lấy dữ liệu từ yêu cầu POST
$title = $_POST['title'];
$img = $_POST['img'];
$category = $_POST['category'];
$price = $_POST['price'];
$desc = $_POST['desc'];
$status = $_POST['status'];

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
