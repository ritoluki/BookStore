<?php
require_once 'config/config.php';

echo "🔧 Sửa kiểu dữ liệu cột id của bảng order...\n\n";

if (isPostgreSQL($conn)) {
    echo "1. Kiểm tra dữ liệu hiện tại trong cột id:\n";
    $checkSql = "SELECT id FROM \"order\" WHERE id ~ '^[0-9]+$' LIMIT 5";
    $result = db_query($conn, $checkSql);
    if ($result && db_num_rows($result) > 0) {
        echo "✅ Cột id chứa số hợp lệ\n";
        while ($row = db_fetch_assoc($result)) {
            echo "- ID: {$row['id']}\n";
        }
    } else {
        echo "❌ Cột id không chứa số hợp lệ\n";
    }
    
    echo "\n2. Sửa kiểu dữ liệu cột id:\n";
    try {
        // Tạo cột id mới kiểu integer
        $addCol = "ALTER TABLE \"order\" ADD COLUMN id_new INTEGER";
        db_query($conn, $addCol);
        echo "✅ Đã thêm cột id_new kiểu INTEGER\n";
        
        // Copy dữ liệu từ cột cũ sang cột mới (chỉ những giá trị là số)
        $copyData = "UPDATE \"order\" SET id_new = CAST(id AS INTEGER) WHERE id ~ '^[0-9]+$'";
        db_query($conn, $copyData);
        echo "✅ Đã copy dữ liệu sang cột mới\n";
        
        // Xóa cột cũ
        $dropOld = "ALTER TABLE \"order\" DROP COLUMN id";
        db_query($conn, $dropOld);
        echo "✅ Đã xóa cột id cũ\n";
        
        // Đổi tên cột mới thành id
        $renameCol = "ALTER TABLE \"order\" RENAME COLUMN id_new TO id";
        db_query($conn, $renameCol);
        echo "✅ Đã đổi tên cột id_new thành id\n";
        
        // Tạo sequence
        $createSeq = "CREATE SEQUENCE IF NOT EXISTS order_id_seq";
        db_query($conn, $createSeq);
        echo "✅ Đã tạo sequence order_id_seq\n";
        
        // Sửa cột id để dùng sequence
        $alterCol = "ALTER TABLE \"order\" ALTER COLUMN id SET DEFAULT nextval('order_id_seq')";
        db_query($conn, $alterCol);
        echo "✅ Đã sửa cột id để dùng sequence\n";
        
        // Set sequence value
        $setSeq = "SELECT setval('order_id_seq', COALESCE((SELECT MAX(id) FROM \"order\"), 1))";
        db_query($conn, $setSeq);
        echo "✅ Đã set sequence value\n";
        
        // Đặt cột id làm PRIMARY KEY
        $addPK = "ALTER TABLE \"order\" ADD PRIMARY KEY (id)";
        db_query($conn, $addPK);
        echo "✅ Đã đặt cột id làm PRIMARY KEY\n";
        
    } catch (Exception $e) {
        echo "❌ Lỗi: " . $e->getMessage() . "\n";
        
        // Nếu lỗi về null values, hãy sửa
        if (strpos($e->getMessage(), 'null values') !== false) {
            echo "\n3. Sửa lỗi null values:\n";
            try {
                // Xóa các bản ghi có id null
                $deleteNull = "DELETE FROM \"order\" WHERE id IS NULL";
                db_query($conn, $deleteNull);
                echo "✅ Đã xóa các bản ghi có id null\n";
                
                // Thử đặt PRIMARY KEY lại
                $addPK = "ALTER TABLE \"order\" ADD PRIMARY KEY (id)";
                db_query($conn, $addPK);
                echo "✅ Đã đặt cột id làm PRIMARY KEY\n";
                
            } catch (Exception $e2) {
                echo "❌ Lỗi khi sửa null values: " . $e2->getMessage() . "\n";
            }
        }
    }
    
} else {
    echo "Chỉ hỗ trợ PostgreSQL\n";
}

echo "\n🎯 Hoàn tất sửa cột id!\n";
db_close($conn);
?>
