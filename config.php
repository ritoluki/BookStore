<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Load environment variables
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "websach";
$port = 3308;

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
?>