<?php
/**
 * Script test để verify tất cả đường dẫn hoạt động đúng
 * Chạy script này để kiểm tra trước khi deploy
 */

// Mock $_SERVER cho command line
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_NAME'] = 'localhost';
}

$env_config = require_once __DIR__ . '/config/environment.php';

echo "=== KIỂM TRA ĐƯỜNG DẪN DEPLOY ===\n\n";

// Test 1: Kiểm tra environment detection
echo "1. Kiểm tra Environment Detection:\n";
echo "   Environment: " . $env_config['environment'] . "\n";
echo "   Base URL: " . $env_config['base_url'] . "\n";
echo "   Base Path: " . $env_config['base_path'] . "\n";
echo "   Database Host: " . $env_config['db_config']['host'] . "\n";
echo "   Database Port: " . $env_config['db_config']['port'] . "\n\n";

// Test 2: Kiểm tra database connection
echo "2. Kiểm tra Database Connection:\n";
try {
    $conn = mysqli_connect(
        $env_config['db_config']['host'],
        $env_config['db_config']['user'],
        $env_config['db_config']['password'],
        $env_config['db_config']['name'],
        $env_config['db_config']['port']
    );
     
    if ($conn) {
        echo "   ✅ Database connection: SUCCESS\n";
        mysqli_close($conn);
    } else {
        echo "   ❌ Database connection: FAILED - " . mysqli_connect_error() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database connection: ERROR - " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Kiểm tra các file quan trọng
echo "3. Kiểm tra các file quan trọng:\n";
$important_files = [
    'config/environment.php',
    'js/path-manager.js',
    'js/main.js',
    'js/admin.js',
    'js/checkout.js',
    'vnpay_php/config.php',
    'src/services/order_mail_helper.php'
];

foreach ($important_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✅ $file: EXISTS\n";
    } else {
        echo "   ❌ $file: MISSING\n";
    }
}
echo "\n";

// Test 4: Kiểm tra thư mục upload
echo "4. Kiểm tra thư mục upload:\n";
$upload_dirs = [
    'assets/img/products/khoahoc',
    'assets/img/products/kinhdoanh',
    'assets/img/products/kynangsong',
    'assets/img/products/lichsu',
    'assets/img/products/sachhay',
    'assets/img/products/tamlinh',
    'assets/img/products/tamly',
    'assets/img/products/thieunhi',
    'assets/img/products/tieuthuyet'
];

foreach ($upload_dirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        if (is_writable(__DIR__ . '/' . $dir)) {
            echo "   ✅ $dir: EXISTS & WRITABLE\n";
        } else {
            echo "   ⚠️  $dir: EXISTS but NOT WRITABLE\n";
        }
    } else {
        echo "   ❌ $dir: MISSING\n";
    }
}
echo "\n";

// Test 5: Kiểm tra VNPay config
echo "5. Kiểm tra VNPay Configuration:\n";
echo "   Return URL: " . $env_config['vnpay']['return_url'] . "\n";
echo "   API URL: " . $env_config['vnpay']['api_url'] . "\n\n";

// Test 6: Kiểm tra JavaScript PathManager
echo "6. Kiểm tra JavaScript PathManager:\n";
$path_manager_content = file_get_contents(__DIR__ . '/js/path-manager.js');
if (strpos($path_manager_content, 'class PathManager') !== false) {
    echo "   ✅ PathManager class: FOUND\n";
} else {
    echo "   ❌ PathManager class: NOT FOUND\n";
}

if (strpos($path_manager_content, 'getApiUrl') !== false) {
    echo "   ✅ getApiUrl method: FOUND\n";
} else {
    echo "   ❌ getApiUrl method: NOT FOUND\n";
}
echo "\n";

// Test 7: Kiểm tra các API endpoints
echo "7. Kiểm tra các API endpoints:\n";
$api_endpoints = [
    'src/controllers/get_products.php',
    'src/controllers/get_orders.php',
    'src/controllers/add_order.php',
    'src/controllers/update_order_status.php',
    'src/controllers/get_order_details.php',
    'src/controllers/check_discount_availability.php',
    'src/controllers/upload_image.php'
];

foreach ($api_endpoints as $endpoint) {
    if (file_exists(__DIR__ . '/' . $endpoint)) {
        echo "   ✅ $endpoint: EXISTS\n";
    } else {
        echo "   ❌ $endpoint: MISSING\n";
    }
}
echo "\n";

echo "=== KẾT QUẢ KIỂM TRA ===\n";
echo "Nếu tất cả đều ✅, bạn có thể deploy an toàn!\n";
echo "Nếu có ❌ hoặc ⚠️, hãy sửa trước khi deploy.\n\n";

echo "=== HƯỚNG DẪN DEPLOY ===\n";
echo "1. Upload toàn bộ code lên server\n";
echo "2. Cấu hình database trong config/environment.php (phần production)\n";
echo "3. Cấu hình VNPay (nếu cần)\n";
echo "4. Đảm bảo quyền ghi cho thư mục assets/img/products/\n";
echo "5. Test lại trên server\n\n";

echo "Script test hoàn thành!\n";
?>
