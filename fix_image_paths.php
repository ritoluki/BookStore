<?php
// Script để sửa đường dẫn hình ảnh trong database
// Chạy: php fix_image_paths.php

require_once 'config/config.php';

try {
    // Sửa đường dẫn trong bảng products
    $sql = "UPDATE products SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
    $result = db_query($conn, $sql);
    echo "Đã sửa đường dẫn hình ảnh trong bảng products\n";
    
    // Sửa đường dẫn trong bảng categories (nếu có)
    $sql = "UPDATE categories SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
    $result = db_query($conn, $sql);
    echo "Đã sửa đường dẫn hình ảnh trong bảng categories\n";
    
    // Sửa đường dẫn trong bảng banners (nếu có)
    $sql = "UPDATE banners SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
    $result = db_query($conn, $sql);
    echo "Đã sửa đường dẫn hình ảnh trong bảng banners\n";
    
    // Sửa đường dẫn trong bảng sliders (nếu có)
    $sql = "UPDATE sliders SET img = REPLACE(img, 'http://localhost/bookstore_datn/', './') WHERE img LIKE '%localhost/bookstore_datn%'";
    $result = db_query($conn, $sql);
    echo "Đã sửa đường dẫn hình ảnh trong bảng sliders\n";
    
    echo "Hoàn thành sửa đường dẫn hình ảnh!\n";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}

db_close($conn);
?>
