<?php
// Script để sửa đường dẫn hình ảnh trong database
// Chạy: php fix_image_paths.php

require_once 'config/config.php';

try {
    // Sửa đường dẫn trong bảng products
    $sql = "UPDATE products SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
    $result = db_query($conn, $sql);
    echo "Đã sửa đường dẫn hình ảnh trong bảng products\n";
    
    // Kiểm tra và sửa đường dẫn trong bảng categories (nếu có)
    if (isPostgreSQL($conn)) {
        $checkTable = "SELECT table_name FROM information_schema.tables WHERE table_name = 'categories'";
    } else {
        $checkTable = "SHOW TABLES LIKE 'categories'";
    }
    $result = $conn->query($checkTable);
    $tableExists = isPostgreSQL($conn) ? ($result && db_num_rows($result) > 0) : ($result && $result->num_rows > 0);
    
    if ($tableExists) {
        $sql = "UPDATE categories SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
        $result = db_query($conn, $sql);
        echo "Đã sửa đường dẫn hình ảnh trong bảng categories\n";
    } else {
        echo "Bảng categories không tồn tại, bỏ qua\n";
    }
    
    // Kiểm tra và sửa đường dẫn trong bảng banners (nếu có)
    if (isPostgreSQL($conn)) {
        $checkTable = "SELECT table_name FROM information_schema.tables WHERE table_name = 'banners'";
    } else {
        $checkTable = "SHOW TABLES LIKE 'banners'";
    }
    $result = $conn->query($checkTable);
    $tableExists = isPostgreSQL($conn) ? ($result && db_num_rows($result) > 0) : ($result && $result->num_rows > 0);
    
    if ($tableExists) {
        $sql = "UPDATE banners SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
        $result = db_query($conn, $sql);
        echo "Đã sửa đường dẫn hình ảnh trong bảng banners\n";
    } else {
        echo "Bảng banners không tồn tại, bỏ qua\n";
    }
    
    // Kiểm tra và sửa đường dẫn trong bảng sliders (nếu có)
    if (isPostgreSQL($conn)) {
        $checkTable = "SELECT table_name FROM information_schema.tables WHERE table_name = 'sliders'";
    } else {
        $checkTable = "SHOW TABLES LIKE 'sliders'";
    }
    $result = $conn->query($checkTable);
    $tableExists = isPostgreSQL($conn) ? ($result && db_num_rows($result) > 0) : ($result && $result->num_rows > 0);
    
    if ($tableExists) {
        $sql = "UPDATE sliders SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
        $result = db_query($conn, $sql);
        echo "Đã sửa đường dẫn hình ảnh trong bảng sliders\n";
    } else {
        echo "Bảng sliders không tồn tại, bỏ qua\n";
    }
    
    echo "Hoàn thành sửa đường dẫn hình ảnh!\n";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}

db_close($conn);
?>
