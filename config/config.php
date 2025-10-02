<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Load environment configuration
$env_config = require_once __DIR__ . '/environment.php';

// Database configuration từ environment
$servername = $env_config['db_config']['host'];
$username = $env_config['db_config']['user'];
$password = $env_config['db_config']['password'];
$dbname = $env_config['db_config']['name']; 
$port = $env_config['db_config']['port'];

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

// Set time zone for MySQL
mysqli_query($conn, "SET time_zone = '+07:00'");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Export environment config để sử dụng trong các file khác
$GLOBALS['env_config'] = $env_config;
?>