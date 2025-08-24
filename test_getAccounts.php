<?php
// Test script để kiểm tra getAccounts.php
echo "🔍 Testing getAccounts.php...\n\n";

// Test trực tiếp database
require_once 'config/config.php';

echo "1. Kiểm tra database trực tiếp:\n";
$sql = "SELECT id, fullname, phone, userType FROM users WHERE phone = '0123456789'";
$result = db_query($conn, $sql);

if ($result && db_num_rows($result) > 0) {
    $row = db_fetch_assoc($result);
    echo "✅ User found:\n";
    echo "   - ID: {$row['id']}\n";
    echo "   - Name: {$row['fullname']}\n";
    echo "   - Phone: {$row['phone']}\n";
    echo "   - userType: " . ($row['userType'] ?? 'NULL') . "\n";
    echo "   - Type: " . gettype($row['userType']) . "\n";
    
    if (isset($row['userType']) && $row['userType'] == 1) {
        echo "   - Status: ADMIN ✅\n";
    } else {
        echo "   - Status: USER ❌\n";
    }
} else {
    echo "❌ User not found\n";
}

echo "\n2. Test getAccounts.php output:\n";
$accountsUrl = "src/controllers/getAccounts.php";
if (file_exists($accountsUrl)) {
    echo "✅ File exists\n";
    
    // Capture output
    ob_start();
    include $accountsUrl;
    $output = ob_get_clean();
    
    echo "Output length: " . strlen($output) . "\n";
    echo "First 500 chars:\n" . substr($output, 0, 500) . "\n";
    
    // Parse JSON
    $accounts = json_decode($output, true);
    if ($accounts) {
        echo "\n✅ JSON parsed successfully\n";
        echo "Number of accounts: " . count($accounts) . "\n";
        
        // Find admin account
        foreach ($accounts as $account) {
            if ($account['phone'] === '0123456789') {
                echo "\n🔍 Found admin account:\n";
                echo "   - userType: " . ($account['userType'] ?? 'NULL') . "\n";
                echo "   - Type: " . gettype($account['userType']) . "\n";
                break;
            }
        }
    } else {
        echo "\n❌ JSON parse failed: " . json_last_error_msg() . "\n";
    }
} else {
    echo "❌ File not found\n";
}

db_close($conn);
echo "\n🎯 Test completed!\n";
?>
