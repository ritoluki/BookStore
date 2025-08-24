<?php
require_once 'config/config.php';

echo "🔧 Thêm PRIMARY KEY cho bảng users...\n\n";

if (isPostgreSQL($conn)) {
    echo "1. Kiểm tra PRIMARY KEY của bảng users:\n";
    $pkSql = "SELECT constraint_name FROM information_schema.table_constraints 
               WHERE table_name = 'users' AND constraint_type = 'PRIMARY KEY'";
    $result = db_query($conn, $pkSql);
    if ($result && db_num_rows($result) > 0) {
        while ($row = db_fetch_assoc($result)) {
            echo "✅ Bảng users đã có PRIMARY KEY: {$row['constraint_name']}\n";
        }
    } else {
        echo "❌ Bảng users chưa có PRIMARY KEY\n";
        
        echo "\n2. Thêm PRIMARY KEY cho cột id:\n";
        try {
            $addPK = "ALTER TABLE users ADD PRIMARY KEY (id)";
            db_query($conn, $addPK);
            echo "✅ Đã thêm PRIMARY KEY cho cột id\n";
        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage() . "\n";
        }
    }
    
} else {
    echo "Chỉ hỗ trợ PostgreSQL\n";
}

echo "\n🎯 Hoàn tất thêm PRIMARY KEY!\n";
db_close($conn);
?>
