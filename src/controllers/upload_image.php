<?php
header('Content-Type: application/json');

// Lấy category từ client, nếu không có thì dùng 'default'
$category = isset($_POST['category']) ? $_POST['category'] : 'default';
$upload_dir = './assets/img/products/' . $category . '/';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$max_file_size = 5 * 1024 * 1024; // 5MB

// Đảm bảo thư mục upload tồn tại
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể tạo thư mục upload'
        ]);
        exit;
    }
}

// Kiểm tra xem có file được gửi lên không
if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    $error_message = isset($_FILES['product_image']) ? getUploadErrorMessage($_FILES['product_image']['error']) : 'Không có file được gửi lên';
    echo json_encode([
        'success' => false,
        'message' => $error_message
    ]);
    exit;
}

$file = $_FILES['product_image'];

// Kiểm tra kích thước file
if ($file['size'] > $max_file_size) {
    echo json_encode([
        'success' => false,
        'message' => 'Kích thước file không được vượt quá 5MB'
    ]);
    exit;
}

// Kiểm tra loại file
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode([
        'success' => false,
        'message' => 'Chỉ cho phép các định dạng hình ảnh: ' . implode(', ', $allowed_extensions)
    ]);
    exit;
}

// Tạo tên file duy nhất để tránh trùng lặp
$new_file_name = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
$upload_path = $upload_dir . $new_file_name;

// Di chuyển file tải lên vào thư mục đích
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Đảm bảo file có quyền hạn đọc cho web server
    chmod($upload_path, 0644);
    
    // Trả về đường dẫn tương đối cho client
    echo json_encode([
        'success' => true,
        'file_path' => $upload_path,
        'file_url' => $upload_path
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Không thể lưu file, vui lòng thử lại'
    ]);
}

// Hàm trả về thông báo lỗi upload
function getUploadErrorMessage($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'File vượt quá kích thước cho phép trong php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'File vượt quá kích thước chỉ định trong form HTML';
        case UPLOAD_ERR_PARTIAL:
            return 'File chỉ được tải lên một phần';
        case UPLOAD_ERR_NO_FILE:
            return 'Không có file nào được tải lên';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Không có thư mục tạm thời';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Không thể ghi file vào ổ đĩa';
        case UPLOAD_ERR_EXTENSION:
            return 'Upload bị dừng bởi extension';
        default:
            return 'Lỗi không xác định khi tải file';
    }
}
?> 