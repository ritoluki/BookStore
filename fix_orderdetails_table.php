<?php
require_once 'config/config.php';

echo "🔧 Kiểm tra và sửa bảng orderdetails...\n\n";

if (isPostgreSQL($conn)) {
    // PostgreSQL
    echo "1. Kiểm tra cấu trúc bảng orderdetails:\n";
    $sql = "SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'orderdetails' 
            ORDER BY ordinal_position";
    
    $result = db_query($conn, $sql);
    if ($result && db_num_rows($result) > 0) {
        while ($row = db_fetch_assoc($result)) {
            echo "- {$row['column_name']}: {$row['data_type']}";
            echo " (nullable: {$row['is_nullable']})";
            if ($row['column_default']) {
                echo " (default: {$row['column_default']})";
            }
            echo "\n";
        }
    }
    
    echo "\n2. Kiểm tra sequence cho cột id:\n";
    $seqSql = "SELECT sequence_name FROM information_schema.sequences WHERE sequence_name LIKE '%orderdetails%'";
    $seqResult = db_query($conn, $seqSql);
    if ($seqResult && db_num_rows($seqResult) > 0) {
        while ($row = db_fetch_assoc($seqResult)) {
            echo "- Sequence: {$row['sequence_name']}\n";
        }
    } else {
        echo "❌ Không có sequence cho orderdetails\n";
        
        echo "\n3. Tạo sequence và sửa bảng:\n";
        try {
            // Tạo sequence
            $createSeq = "CREATE SEQUENCE IF NOT EXISTS orderdetails_id_seq";
            db_query($conn, $createSeq);
            echo "✅ Đã tạo sequence orderdetails_id_seq\n";
            
            // Sửa cột id để dùng sequence
            $alterCol = "ALTER TABLE orderdetails ALTER COLUMN id SET DEFAULT nextval('orderdetails_id_seq')";
            db_query($conn, $alterCol);
            echo "✅ Đã sửa cột id để dùng sequence\n";
            
            // Set sequence value
            $setSeq = "SELECT setval('orderdetails_id_seq', COALESCE((SELECT MAX(id) FROM orderdetails), 1))";
            db_query($conn, $setSeq);
            echo "✅ Đã set sequence value\n";
            
        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage() . "\n";
        }
    }
    
} else {
    // MySQL
    echo "1. Kiểm tra cấu trúc bảng orderdetails:\n";
    $sql = "DESCRIBE orderdetails";
    $result = db_query($conn, $sql);
    if ($result && db_num_rows($result) > 0) {
        while ($row = db_fetch_assoc($result)) {
            echo "- {$row['Field']}: {$row['Type']}";
            echo " (null: {$row['Null']})";
            if ($row['Default']) {
                echo " (default: {$row['Default']})";
            }
            if ($row['Extra']) {
                echo " ({$row['Extra']})";
            }
            echo "\n";
        }
    }
}

echo "\n🎯 Hoàn tất kiểm tra và sửa bảng!\n";
db_close($conn);
?>
