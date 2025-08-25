<?php
// Kết nối đến cơ sở dữ liệu
require_once '../../config/config.php';

// Lấy dữ liệu từ yêu cầu
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$img = isset($_POST['img']) ? trim($_POST['img']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
$desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$soluong = isset($_POST['soluong']) ? (int)$_POST['soluong'] : 0;
$oldImagePath = isset($_POST['oldImagePath']) ? trim($_POST['oldImagePath']) : '';

$newImagePath = $oldImagePath; // Mặc định là ảnh cũ

// Kiểm tra xem có ảnh mới được tải lên không
if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] == UPLOAD_ERR_OK) {
    $newImage = $_FILES['newImage'];
    $targetDir = __DIR__ . '/../assets/img/products/'; // Thư mục lưu trữ ảnh trên server (đường dẫn tuyệt đối)
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = uniqid() . '_' . basename($newImage['name']);
    $newImagePathAbs = $targetDir . $fileName;
    $newImagePathRel = 'assets/img/products/' . $fileName; // Lưu vào DB đường dẫn tương đối
    
    // Di chuyển ảnh mới vào thư mục chỉ định
    if (move_uploaded_file($newImage['tmp_name'], $newImagePathAbs)) {
        // Xóa ảnh cũ nếu đường dẫn ảnh cũ khác với ảnh mới
        if ($oldImagePath && $oldImagePath !== $newImagePathRel) {
            $oldAbs = __DIR__ . '/../' . ltrim($oldImagePath, '/');
            if (file_exists($oldAbs)) {
                unlink($oldAbs);
            }
        }
        $img = $newImagePathRel;
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
