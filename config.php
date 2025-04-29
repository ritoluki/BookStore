<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

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