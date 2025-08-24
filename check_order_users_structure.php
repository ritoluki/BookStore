<?php
require_once 'config/config.php';

echo "🔧 Kiểm tra cấu trúc bảng order và users...\n\n";

if (isPostgreSQL($conn)) {
    echo "1. Kiểm tra cấu trúc bảng \"order\":\n";
    $orderSql = "SELECT column_name, data_type, is_nullable 
                  FROM information_schema.columns 
                  WHERE table_name = 'order' 
                  ORDER BY ordinal_position";
    $result = db_query($conn, $orderSql);
    if ($result && db_num_rows($result) > 0) {
        while ($row = db_fetch_assoc($result)) {
            echo "- {$row['column_name']}: {$row['data_type']} (nullable: {$row['is_nullable']})\n";
        }
    }
    
    echo "\n2. Kiểm tra cấu trúc bảng users:\n";
    $usersSql = "SELECT column_name, data_type, is_nullable 
                  FROM information_schema.columns 
                  WHERE table_name = 'users' 
                  ORDER BY ordinal_position";
    $result = db_query($conn, $usersSql);
    if ($result && db_num_rows($result) > 0) {
        while ($row = db_fetch_assoc($result)) {
            echo "- {$row['column_name']}: {$row['data_type']} (nullable: {$row['is_nullable']})\n";
        }
    }
    
    echo "\n3. Kiểm tra dữ liệu mẫu:\n";
    $sampleSql = "SELECT o.id, o.khachhang, u.id as user_id, u.fullname 
                  FROM \"order\" o 
                  LEFT JOIN users u ON o.khachhang = u.id 
                  LIMIT 3";
    $result = db_query($conn, $sampleSql);
    if ($result && db_num_rows($result) > 0) {
        while ($row = db_fetch_assoc($result)) {
            echo "- Order ID: {$row['id']}, Khachhang: {$row['khachhang']}, User ID: {$row['user_id']}, Name: {$row['fullname']}\n";
        }
    } else {
        echo "❌ Không thể JOIN giữa order và users\n";
    }
    
} else {
    echo "Chỉ hỗ trợ PostgreSQL\n";
}

echo "\n🎯 Hoàn tất kiểm tra!\n";
db_close($conn);
?>
