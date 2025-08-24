<?php
require_once 'config/config.php';

echo "🔍 Kiểm tra và sửa quyền admin...\n\n";

// 1. Kiểm tra cấu trúc bảng users
echo "1. Kiểm tra cấu trúc bảng users:\n";
if (isPostgreSQL($conn)) {
    $sql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users' ORDER BY ordinal_position";
} else {
    $sql = "DESCRIBE users";
}

$result = db_query($conn, $sql);
if ($result && db_num_rows($result) > 0) {
    while ($row = db_fetch_assoc($result)) {
        if (isPostgreSQL($conn)) {
            echo "- {$row['column_name']}: {$row['data_type']}\n";
        } else {
            echo "- {$row['Field']}: {$row['Type']}\n";
        }
    }
} else {
    echo "❌ Không thể kiểm tra cấu trúc bảng\n";
}

echo "\n2. Kiểm tra tài khoản admin hiện tại:\n";
$sql = "SELECT id, fullname, phone, userType FROM users WHERE phone = '0123456789'";
$result = db_query($conn, $sql);

if ($result && db_num_rows($result) > 0) {
    $row = db_fetch_assoc($result);
    echo "- User: {$row['fullname']}\n";
    echo "- Phone: {$row['phone']}\n";
    echo "- userType: " . ($row['userType'] ?? 'NULL') . "\n";
    
    if (isset($row['userType']) && $row['userType'] == 1) {
        echo "Status: ADMIN ✅\n";
    } else {
        echo "Status: USER ❌ (Cần sửa)\n";
        
        // 3. Sửa quyền admin
        echo "\n3. Đang sửa quyền admin...\n";
        $updateSql = "UPDATE users SET userType = 1 WHERE phone = '0123456789'";
        $updateResult = db_query($conn, $updateSql);
        
        if ($updateResult) {
            echo "✅ Đã cập nhật userType = 1 thành công!\n";
            
            // 4. Kiểm tra lại
            echo "\n4. Kiểm tra lại sau khi sửa:\n";
            $checkResult = db_query($conn, $sql);
            if ($checkResult && db_num_rows($result) > 0) {
                $checkRow = db_fetch_assoc($checkResult);
                echo "- userType mới: {$checkRow['userType']}\n";
                if ($checkRow['userType'] == 1) {
                    echo "Status: ADMIN ✅ (Đã sửa xong!)\n";
                }
            }
        } else {
            echo "❌ Lỗi khi cập nhật: " . (isPostgreSQL($conn) ? $conn->errorInfo()[2] : $conn->error) . "\n";
        }
    }
} else {
    echo "❌ Không tìm thấy tài khoản admin\n";
}

db_close($conn);
echo "\n🎯 Hoàn thành! Hãy refresh trang và kiểm tra menu admin.\n";
?>
