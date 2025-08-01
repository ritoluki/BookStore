<?php
/**
 * Simple test to verify the repository structure and file paths
 */

echo "Testing BookStore Repository Structure...\n";

// Test 1: Check if main directories exist
$directories = [
    'src',
    'src/controllers',
    'src/models',
    'src/services', 
    'src/routes',
    'src/utils',
    'config',
    'tests',
    'scripts',
    'assets'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "✓ Directory '$dir' exists\n";
    } else {
        echo "✗ Directory '$dir' missing\n";
    }
}

// Test 2: Check if key files are in correct locations
$files = [
    'config/config.php' => 'Configuration file',
    'src/utils/main.php' => 'Utility functions',
    'src/controllers/get_products.php' => 'Products controller',
    'src/services/send_mail.php' => 'Mail service',
    'scripts/database_schema.sql' => 'Database schema',
    'index.php' => 'Main entry point',
    'admin.php' => 'Admin entry point'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $description found at '$file'\n";
    } else {
        echo "✗ $description missing at '$file'\n";
    }
}

// Test 3: Check if paths in files are correct (without actually requiring them)
echo "\nTesting file path references...\n";

// Check if src/utils/main.php has correct config path
$main_content = file_get_contents('src/utils/main.php');
if (strpos($main_content, "../../config/config.php") !== false) {
    echo "✓ src/utils/main.php has correct config path\n";
} else {
    echo "✗ src/utils/main.php has incorrect config path\n";
}

// Check if index.php references src/utils/main.php
$index_content = file_get_contents('index.php');
if (strpos($index_content, "src/utils/main.php") !== false) {
    echo "✓ index.php references moved main.php correctly\n";
} else {
    echo "✗ index.php does not reference moved main.php\n";
}

// Check if a controller file has correct config path
$controller_content = file_get_contents('src/controllers/get_products.php');
if (strpos($controller_content, "../../config/config.php") !== false) {
    echo "✓ Controllers have correct config path\n";
} else {
    echo "✗ Controllers have incorrect config path\n";
}

echo "\nRepository structure test completed!\n";
?>