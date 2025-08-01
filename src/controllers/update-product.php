<?php
// Kết nối đến cơ sở dữ liệu
require_once '../../config/config.php';

// Lấy dữ liệu từ yêu cầu
$id = $_POST['id'];
$title = $_POST['title'];
$img = $_POST['img'];
$category = $_POST['category'];
$price = $_POST['price'];
$status = $_POST['status'];
$desc = $_POST['desc'];
$soluong = $_POST['soluong'];
$oldImagePath = $_POST['oldImagePath']; // Đường dẫn ảnh cũ

$newImagePath = $oldImagePath; // Mặc định là ảnh cũ

// Kiểm tra xem có ảnh mới được tải lên không
if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] == UPLOAD_ERR_OK) {
    $newImage = $_FILES['newImage'];
    $targetDir = "assets/img/products/"; // Thư mục lưu trữ ảnh trên server
    $newImagePath = $targetDir . basename($newImage['name']);
    
    // Di chuyển ảnh mới vào thư mục chỉ định
    if (move_uploaded_file($newImage['tmp_name'], $newImagePath)) {
        // Xóa ảnh cũ nếu đường dẫn ảnh cũ khác với ảnh mới
        if ($oldImagePath && $oldImagePath !== $newImagePath) {
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Không thể tải ảnh mới lên!"]);
        exit;
    }
}

// Cập nhật sản phẩm trong cơ sở dữ liệu
$sql = "UPDATE products SET title = ?, category = ?, price = ?, soluong = ?, describes = ?, img = ?, status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisssii", $title, $category, $price, $soluong, $desc, $img, $status, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Sản phẩm đã được cập nhật thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật sản phẩm!"]);
}

$stmt->close();
$conn->close();
?>
