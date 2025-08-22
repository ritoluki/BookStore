<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Auto-detect environment and load appropriate config
if (getenv('DATABASE_URL') || getenv('DB_HOST')) {
    // Heroku environment - use PostgreSQL
    require_once 'config.heroku.php';
} else {
    // Local environment - use MySQL
    require_once 'config.local.php';
}
?>