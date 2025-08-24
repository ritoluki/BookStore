<?php
require_once 'config/config.php';

echo "🔍 Debug userType trong database...\n\n";

// Kiểm tra tài khoản admin
$sql = "SELECT id, fullname, phone, userType FROM users WHERE phone = '0123456789'";
$result = db_query($conn, $sql);

if ($result && db_num_rows($result) > 0) {
    $row = db_fetch_assoc($result);
    echo "User: {$row['fullname']}\n";
    echo "Phone: {$row['phone']}\n";
    echo "userType trong DB: " . ($row['userType'] ?? 'NULL') . "\n";
    echo "Type: " . gettype($row['userType']) . "\n";
    
    if (isset($row['userType']) && $row['userType'] == 1) {
        echo "Status: ADMIN ✅\n";
    } else {
        echo "Status: USER ❌\n";
    }
} else {
    echo "User not found\n";
}

echo "\n--- Kiểm tra getAccounts.php ---\n";
echo "URL: " . $_SERVER['REQUEST_URI'] . "\n";

// Test getAccounts.php
$accountsUrl = "src/controllers/getAccounts.php";
if (file_exists($accountsUrl)) {
    echo "File getAccounts.php tồn tại\n";
    
    // Lấy output của getAccounts.php
    ob_start();
    include $accountsUrl;
    $output = ob_get_clean();
    
    echo "Output length: " . strlen($output) . "\n";
    echo "First 200 chars: " . substr($output, 0, 200) . "\n";
    
    // Parse JSON
    $accounts = json_decode($output, true);
    if ($accounts) {
        echo "JSON parsed successfully\n";
        echo "Number of accounts: " . count($accounts) . "\n";
        
        // Tìm admin account
        foreach ($accounts as $account) {
            if ($account['phone'] === '0123456789') {
                echo "Found admin account:\n";
                echo "- userType: " . ($account['userType'] ?? 'NULL') . "\n";
                echo "- Type: " . gettype($account['userType']) . "\n";
                break;
            }
        }
    } else {
        echo "JSON parse failed: " . json_last_error_msg() . "\n";
    }
} else {
    echo "File getAccounts.php không tồn tại\n";
}

db_close($conn);
?>
