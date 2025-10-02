<?php
/**
 * Cấu hình môi trường động cho việc deploy
 * Tự động detect môi trường local/production và cấu hình đường dẫn phù hợp
 */

// Detect môi trường dựa trên domain hoặc server
function detectEnvironment() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    
    // Kiểm tra nếu là localhost hoặc IP local
    if (strpos($host, 'localhost') !== false || 
        strpos($host, '127.0.0.1') !== false || 
        strpos($host, '192.168.') !== false ||
        strpos($host, '10.0.') !== false) {
        return 'local';
    }
    
    return 'production';
}

// Cấu hình theo môi trường
$environment = detectEnvironment();

if ($environment === 'local') {
    // Cấu hình cho môi trường local
    define('BASE_URL', 'http://localhost/Bookstore_DATN');
    define('BASE_PATH', '/Bookstore_DATN');
    define('DB_HOST', 'localhost');
    define('DB_PORT', 3308);
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'websach');
} else {
    // Cấu hình cho môi trường production
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $domain = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . '://' . $domain);
    define('BASE_PATH', ''); // Không cần subfolder trên production
    define('DB_HOST', 'localhost'); // Thay đổi theo server của bạn
    define('DB_PORT', 3306); // Port mặc định của MySQL
    define('DB_USER', 'bookstore_user'); // Thay đổi theo server
    define('DB_PASSWORD', 'Tan123@@'); // Thay đổi theo server
    define('DB_NAME', 'websach');
}

// Cấu hình VNPay
define('VNPAY_RETURN_URL', BASE_URL . '/src/controllers/order_success.php');
define('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction');

// Cấu hình upload paths
define('UPLOAD_BASE_PATH', realpath(__DIR__ . '/../'));
define('UPLOAD_RELATIVE_PATH', 'assets/img/products/');

// Hàm helper để tạo URL
function getBaseUrl() {
    return BASE_URL;
}

function getBasePath() {
    return BASE_PATH;
}

function getApiUrl($endpoint) {
    return BASE_URL . BASE_PATH . '/src/controllers/' . $endpoint;
}

function getAssetUrl($path) {
    return BASE_URL . BASE_PATH . '/' . ltrim($path, '/');
}

// Export các hằng số để sử dụng trong các file khác
return [
    'environment' => $environment,
    'base_url' => BASE_URL,
    'base_path' => BASE_PATH,
    'db_config' => [
        'host' => DB_HOST,
        'port' => DB_PORT,
        'user' => DB_USER,
        'password' => DB_PASSWORD,
        'name' => DB_NAME
    ],
    'vnpay' => [
        'return_url' => VNPAY_RETURN_URL,
        'api_url' => VNPAY_API_URL
    ]
];
?>
