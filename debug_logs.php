<?php
// Hiển thị báo lỗi PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Đường dẫn đến file error log của PHP
$error_log_path = ini_get('error_log');

echo "<h1>Debug Logs</h1>";

// Kiểm tra xem file error log có tồn tại không
if (file_exists($error_log_path)) {
    echo "<p>Error log path: <code>$error_log_path</code></p>";
    
    // Đọc nội dung file error log
    $log_content = file_get_contents($error_log_path);
    
    // Lọc các dòng chứa 'email'
    $email_logs = [];
    $lines = explode("\n", $log_content);
    foreach ($lines as $line) {
        if (stripos($line, 'email') !== false || stripos($line, 'mail') !== false || stripos($line, 'smtp') !== false) {
            $email_logs[] = $line;
        }
    }
    
    if (count($email_logs) > 0) {
        echo "<h2>Email Related Logs</h2>";
        echo "<pre style='background-color: #f5f5f5; padding: 10px; max-height: 500px; overflow: auto;'>";
        foreach ($email_logs as $log) {
            echo htmlspecialchars($log) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<p>Không tìm thấy log liên quan đến email.</p>";
    }
    
    // Hiển thị các log gần đây nhất
    $recent_logs = array_slice($lines, -50);
    
    echo "<h2>50 Logs Gần Đây Nhất</h2>";
    echo "<pre style='background-color: #f5f5f5; padding: 10px; max-height: 500px; overflow: auto;'>";
    foreach ($recent_logs as $log) {
        echo htmlspecialchars($log) . "\n";
    }
    echo "</pre>";
} else {
    echo "<p>Không tìm thấy file error log tại đường dẫn: <code>$error_log_path</code></p>";
    
    // Hiển thị các vị trí có thể của error log
    echo "<h2>Các vị trí error log có thể</h2>";
    echo "<ul>";
    echo "<li>/var/log/apache2/error.log (Linux with Apache)</li>";
    echo "<li>/var/log/nginx/error.log (Linux with Nginx)</li>";
    echo "<li>C:/xampp/apache/logs/error.log (Windows with XAMPP)</li>";
    echo "<li>C:/xampp/php/logs/php_error_log (Windows with XAMPP)</li>";
    echo "</ul>";
    
    // Kiểm tra một số vị trí phổ biến
    $common_paths = [
        'C:/xampp/apache/logs/error.log',
        'C:/xampp/php/logs/php_error_log'
    ];
    
    foreach ($common_paths as $path) {
        if (file_exists($path)) {
            echo "<p>Tìm thấy log tại: <code>$path</code></p>";
            $log_content = file_get_contents($path);
            $recent_logs = array_slice(explode("\n", $log_content), -50);
            
            echo "<pre style='background-color: #f5f5f5; padding: 10px; max-height: 500px; overflow: auto;'>";
            foreach ($recent_logs as $log) {
                echo htmlspecialchars($log) . "\n";
            }
            echo "</pre>";
            break;
        }
    }
}

// Thêm nút refresh
echo "<p><a href='debug_logs.php' class='button'>Refresh Logs</a></p>";
?> 