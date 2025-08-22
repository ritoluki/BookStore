<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Database configuration for HEROKU PostgreSQL
$servername = getenv('DB_HOST') ?: 'cer3tutrbi7n1t.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com';
$username = getenv('DB_USERNAME') ?: 'ua0jkfhl3p1fia';
$password = getenv('DB_PASSWORD') ?: 'p756f0c958d7908f1665f4437396fdee30f12dd3ee3eb8f59bb1798dd108c51f2';
$dbname = getenv('DB_NAME') ?: 'dd8h6q47p5eua4'; 
$port = getenv('DB_PORT') ?: 5432;

// Create PostgreSQL connection
try {
    $dsn = "pgsql:host=$servername;port=$port;dbname=$dbname;user=$username;password=$password";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set timezone for PostgreSQL
    $pdo->exec("SET timezone = '+07:00'");
    
    // For backward compatibility, keep $conn variable
    $conn = $pdo;
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
