<?php
require_once 'config/config.php';

// Kiểm tra tài khoản admin
$sql = "SELECT id, fullname, phone, userType FROM users WHERE phone = '123456'";
$result = db_query($conn, $sql);

if ($result && db_num_rows($result) > 0) {
    $row = db_fetch_assoc($result);
    echo "User: {$row['fullname']}\n";
    echo "Phone: {$row['phone']}\n";
    echo "userType: " . ($row['userType'] ?? 'NULL') . "\n";
    
    if (isset($row['userType']) && $row['userType'] == 1) {
        echo "Status: ADMIN ✅\n";
    } else {
        echo "Status: USER ❌\n";
    }
} else {
    echo "User not found\n";
}

db_close($conn);
?>
