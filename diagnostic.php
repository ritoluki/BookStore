<?php
/**
 * Diagnostic Tool - Simplified Version
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔍 System Diagnostic</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #3498db; margin-top: 30px; }
        .pass { background: #d4edda; color: #155724; padding: 10px; margin: 5px 0; border-left: 4px solid #28a745; }
        .fail { background: #f8d7da; color: #721c24; padding: 10px; margin: 5px 0; border-left: 4px solid #dc3545; }
        .warn { background: #fff3cd; color: #856404; padding: 10px; margin: 5px 0; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; margin: 5px 0; border-left: 4px solid #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3498db; color: white; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .summary { background: #3498db; color: white; padding: 20px; border-radius: 10px; text-align: center; margin-top: 30px; }
        .badge { padding: 5px 10px; border-radius: 3px; font-weight: bold; margin: 0 5px; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 System Diagnostic Tool</h1>
        <div class="info">
            <strong>Run Time:</strong> <?= date('Y-m-d H:i:s') ?> | 
            <strong>Host:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'CLI' ?>
        </div>

        <?php
        $errors = 0;
        $warnings = 0;
        $passed = 0;

        // ========================================
        // TEST 1: Environment Detection
        // ========================================
        echo '<h2>1️⃣ Environment Detection</h2>';
        
        try {
            $env_config = require 'config/env_loader.php';
            
            if (is_array($env_config)) {
                echo '<div class="pass">✅ Environment configuration loaded successfully</div>';
                $passed++;
                
                echo '<table>';
                echo '<tr><th>Config Key</th><th>Value</th></tr>';
                echo '<tr><td>Environment</td><td><strong>' . ($env_config['environment'] ?? 'N/A') . '</strong></td></tr>';
                echo '<tr><td>Is Production</td><td>' . (($env_config['is_production'] ?? false) ? '✅ Yes' : '❌ No') . '</td></tr>';
                echo '<tr><td>Debug Mode</td><td>' . (($env_config['debug'] ?? false) ? '⚠️ Enabled' : '✅ Disabled') . '</td></tr>';
                echo '<tr><td>Base URL</td><td>' . ($env_config['base_url'] ?? 'N/A') . '</td></tr>';
                echo '<tr><td>Base Path</td><td>' . (($env_config['base_path'] ?? '') ?: '<em>(root)</em>') . '</td></tr>';
                echo '</table>';
                
                if (($env_config['debug'] ?? false) && ($env_config['is_production'] ?? false)) {
                    echo '<div class="warn">⚠️ Warning: Debug mode is enabled in production!</div>';
                    $warnings++;
                }
            } else {
                echo '<div class="fail">❌ Environment config is not an array</div>';
                $errors++;
            }
        } catch (Exception $e) {
            echo '<div class="fail">❌ Failed to load environment: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $errors++;
        }

        // ========================================
        // TEST 2: Database Connection
        // ========================================
        echo '<h2>2️⃣ Database Connection</h2>';
        
        if (isset($env_config['db_config'])) {
            $db = $env_config['db_config'];
            
            echo '<table>';
            echo '<tr><th>Setting</th><th>Value</th></tr>';
            echo '<tr><td>Host</td><td>' . ($db['host'] ?? 'N/A') . '</td></tr>';
            echo '<tr><td>Port</td><td>' . ($db['port'] ?? 'N/A') . '</td></tr>';
            echo '<tr><td>Database</td><td>' . ($db['name'] ?? 'N/A') . '</td></tr>';
            echo '<tr><td>Username</td><td>' . ($db['user'] ?? 'N/A') . '</td></tr>';
            echo '</table>';
            
            $conn = @mysqli_connect($db['host'], $db['user'], $db['password'], $db['name'], $db['port']);
            
            if ($conn) {
                echo '<div class="pass">✅ Database connected successfully</div>';
                $passed++;
                
                // Check tables
                $result = mysqli_query($conn, "SHOW TABLES");
                if ($result) {
                    $tables = mysqli_fetch_all($result);
                    echo '<div class="info">📊 Found ' . count($tables) . ' tables</div>';
                }
                
                // Check users table
                $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    echo '<div class="info">👥 Users: ' . $row['count'] . '</div>';
                }
                
                mysqli_close($conn);
            } else {
                echo '<div class="fail">❌ Database connection failed: ' . mysqli_connect_error() . '</div>';
                $errors++;
            }
        } else {
            echo '<div class="fail">❌ Database config not found</div>';
            $errors++;
        }

        // ========================================
        // TEST 3: PHP Configuration
        // ========================================
        echo '<h2>3️⃣ PHP Configuration</h2>';
        
        $php_version = phpversion();
        if (version_compare($php_version, '7.4.0', '>=')) {
            echo '<div class="pass">✅ PHP Version: ' . $php_version . '</div>';
            $passed++;
        } else {
            echo '<div class="fail">❌ PHP Version: ' . $php_version . ' (Required >= 7.4)</div>';
            $errors++;
        }
        
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th></tr>';
        echo '<tr><td>Server Software</td><td>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'CLI') . '</td></tr>';
        echo '<tr><td>Document Root</td><td>' . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>Max Upload Size</td><td>' . ini_get('upload_max_filesize') . '</td></tr>';
        echo '<tr><td>Max POST Size</td><td>' . ini_get('post_max_size') . '</td></tr>';
        echo '<tr><td>Memory Limit</td><td>' . ini_get('memory_limit') . '</td></tr>';
        echo '</table>';

        // ========================================
        // TEST 4: Required PHP Extensions
        // ========================================
        echo '<h2>4️⃣ PHP Extensions</h2>';
        
        $required_ext = ['mysqli', 'mbstring', 'openssl', 'curl', 'json'];
        foreach ($required_ext as $ext) {
            if (extension_loaded($ext)) {
                echo '<div class="pass">✅ ' . $ext . '</div>';
                $passed++;
            } else {
                echo '<div class="fail">❌ ' . $ext . ' (missing)</div>';
                $errors++;
            }
        }

        // ========================================
        // TEST 5: File Permissions
        // ========================================
        echo '<h2>5️⃣ File Permissions</h2>';
        
        $check_dirs = ['logs', 'assets/img/products', 'vendor', 'config'];
        
        foreach ($check_dirs as $dir) {
            $exists = file_exists($dir);
            $writable = is_writable($dir);
            
            if ($exists && $writable) {
                echo '<div class="pass">✅ ' . $dir . ' - Writable</div>';
                $passed++;
            } elseif ($exists && !$writable) {
                echo '<div class="warn">⚠️ ' . $dir . ' - Not Writable</div>';
                $warnings++;
            } else {
                echo '<div class="fail">❌ ' . $dir . ' - Not Found</div>';
                $errors++;
            }
        }

        // ========================================
        // TEST 6: Composer Dependencies
        // ========================================
        echo '<h2>6️⃣ Composer Dependencies</h2>';
        
        if (file_exists('vendor/autoload.php')) {
            echo '<div class="pass">✅ Composer autoload found</div>';
            $passed++;
            
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                echo '<div class="pass">✅ PHPMailer available</div>';
                $passed++;
            } else {
                echo '<div class="fail">❌ PHPMailer not found</div>';
                $errors++;
            }
            
            if (class_exists('Dotenv\Dotenv')) {
                echo '<div class="pass">✅ PHP Dotenv available</div>';
                $passed++;
            } else {
                echo '<div class="fail">❌ PHP Dotenv not found</div>';
                $errors++;
            }
        } else {
            echo '<div class="fail">❌ Composer autoload not found. Run: composer install</div>';
            $errors++;
        }

        // ========================================
        // SUMMARY
        // ========================================
        $total_tests = $passed + $warnings + $errors;
        
        echo '<div class="summary">';
        echo '<h2>📊 Test Summary</h2>';
        
        if ($errors == 0 && $warnings == 0) {
            echo '<h3>✅ ALL TESTS PASSED!</h3>';
        } elseif ($errors == 0) {
            echo '<h3>⚠️ PASSED WITH WARNINGS</h3>';
        } else {
            echo '<h3>❌ ISSUES FOUND</h3>';
        }
        
        echo '<p>';
        echo '<span class="badge badge-success">✅ Passed: ' . $passed . '</span>';
        echo '<span class="badge badge-warning">⚠️ Warnings: ' . $warnings . '</span>';
        echo '<span class="badge badge-error">❌ Errors: ' . $errors . '</span>';
        echo '</p>';
        
        if ($errors == 0 && $warnings == 0) {
            echo '<p style="margin-top: 20px;">🚀 System is ready for deployment!</p>';
        } elseif ($errors > 0) {
            echo '<p style="margin-top: 20px;">⚠️ Please fix the errors before deploying</p>';
        }
        
        echo '</div>';
        ?>

    </div>
</body>
</html>
