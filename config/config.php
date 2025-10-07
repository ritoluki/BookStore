<?php
/**
 * Database Configuration
 * Load environment variables from .env files
 */

// Set timezone for PHP to Vietnam (GMT+7)
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Load environment configuration
$env_config = require_once __DIR__ . '/env_loader.php';

// Validate environment config
if (!is_array($env_config) || !isset($env_config['db_config'])) {
    die('❌ ERROR: Invalid environment configuration. Check config/env_loader.php');
}

// Database configuration
$db_config = $env_config['db_config'];
$servername = $db_config['host'];
$username = $db_config['user'];
$password = $db_config['password'];
$dbname = $db_config['name'];
$port = $db_config['port'];

// Create connection with error handling
try {
    $conn = @mysqli_connect($servername, $username, $password, $dbname, $port);

    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }

    // Set charset to utf8
    mysqli_set_charset($conn, "utf8");

    // Set time zone for MySQL
    mysqli_query($conn, "SET time_zone = '+07:00'");

    // Log successful connection (only in debug mode)
    if ($env_config['debug']) {
        error_log("✅ Database connected successfully [{$env_config['environment']}] - DB: {$dbname}");
    }

} catch (Exception $e) {
    // Log error
    error_log("❌ Database Connection Error: " . $e->getMessage());

    // Display based on environment
    if ($env_config['debug']) {
        die("❌ Connection failed: " . $e->getMessage() .
            "<br>Host: $servername:$port<br>DB: $dbname<br>User: $username<br>" .
            "<br>Environment: {$env_config['environment']}");
    } else {
        http_response_code(503);
        die('Hệ thống đang bảo trì. Vui lòng quay lại sau.');
    }
}

// Export config globally
$GLOBALS['env_config'] = $env_config;
$GLOBALS['conn'] = $conn;
?>