<?php
/**
 * Environment Loader with Auto-Detection
 * Tự động load .env.local hoặc .env.production
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Detect environment based on server
 */
function detectEnvironment()
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';

    // Check if local
    if (
        strpos($host, 'localhost') !== false ||
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '192.168.') !== false ||
        strpos($host, '10.0.') !== false ||
        $serverAddr === '127.0.0.1' ||
        $serverAddr === '::1'
    ) {
        return 'local';
    }

    return 'production';
}

// Detect environment
$environment = detectEnvironment();
$envFile = '.env.' . $environment;
$baseDir = __DIR__ . '/..';

// Check if specific env file exists
if (!file_exists($baseDir . '/' . $envFile)) {
    // Fallback to .env if specific file doesn't exist
    if (file_exists($baseDir . '/.env')) {
        $envFile = '.env';
    } else {
        die("❌ ERROR: Environment file not found. Please create {$envFile} or .env file.");
    }
}

// Load environment variables
try {
    $dotenv = Dotenv::createImmutable($baseDir, $envFile);
    $dotenv->load();
} catch (Exception $e) {
    die("❌ ERROR loading environment file: " . $e->getMessage());
}

// Validate required variables
try {
    $dotenv->required([
        'APP_ENV',
        'APP_URL',
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME'
    ])->notEmpty();
} catch (Exception $e) {
    die("❌ ERROR: Missing required environment variables: " . $e->getMessage());
}

// Set PHP configuration based on environment
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');

    // Create logs directory if not exists
    $logDir = __DIR__ . '/../logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }

    ini_set('error_log', $logDir . '/php-errors.log');
}

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * Helper function to get environment variable
 */
function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}

/**
 * Check if running in production
 */
function isProduction()
{
    return env('APP_ENV') === 'production';
}

/**
 * Check if running in local/development
 */
function isLocal()
{
    return env('APP_ENV') === 'local';
}

/**
 * Check if debug mode is enabled
 */
function isDebug()
{
    return env('APP_DEBUG') === 'true';
}

/**
 * Get base URL
 */
function getBaseUrl()
{
    return rtrim(env('APP_URL'), '/');
}

/**
 * Get base path
 */
function getBasePath()
{
    return env('BASE_PATH', '');
}

/**
 * Get asset URL
 */
function getAssetUrl($path)
{
    return getBaseUrl() . getBasePath() . '/' . ltrim($path, '/');
}

/**
 * Get API URL
 */
function getApiUrl($endpoint)
{
    return getBaseUrl() . getBasePath() . '/src/controllers/' . ltrim($endpoint, '/');
}

/**
 * Log custom message
 */
function logMessage($message, $level = 'INFO')
{
    $logFile = __DIR__ . '/../logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}\n";
    error_log($logEntry, 3, $logFile);
}

// Export configuration array
return [
    'environment' => env('APP_ENV'),
    'is_production' => isProduction(),
    'is_local' => isLocal(),
    'debug' => isDebug(),
    'base_url' => getBaseUrl(),
    'base_path' => getBasePath(),
    'app_name' => env('APP_NAME', 'Bookstore DATN'),

    'db_config' => [
        'host' => env('DB_HOST'),
        'port' => (int) env('DB_PORT'),
        'user' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        'name' => env('DB_DATABASE')
    ],

    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'url' => env('VNPAY_URL'),
        'return_url' => env('VNPAY_RETURN_URL'),
        'api_url' => env('VNPAY_API_URL')
    ],

    'upload' => [
        'max_size' => (int) env('UPLOAD_MAX_SIZE', 10485760),
        'allowed_types' => explode(',', env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif'))
    ],

    'session' => [
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
        'secure' => env('SESSION_SECURE') === 'true'
    ],

    'mail' => [
        'host' => env('MAIL_HOST'),
        'port' => (int) env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'from_address' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME', env('APP_NAME'))
    ]
];
