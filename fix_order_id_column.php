<?php
require_once 'config/config.php';

echo "🔧 Sửa kiểu dữ liệu cột id của bảng order...\n\n";

if (isPostgreSQL($conn)) {
    echo "1. Kiểm tra cấu trúc bảng order hiện tại:\n";
    $checkSql = "SELECT column_name, data_type, is_nullable, column_default 
                  FROM information_schema.columns 
                  WHERE table_name = 'order' AND column_name = 'id'";
    $result = db_query($conn, $checkSql);
    if ($result && db_num_rows($result) > 0) {
        $row = db_fetch_assoc($result);
        echo "- Cột id: {$row['data_type']} (nullable: {$row['is_nullable']})";
        if ($row['column_default']) {
            echo " (default: {$row['column_default']})";
        }
        echo "\n";
        
        if ($row['data_type'] === 'integer') {
            echo "✅ Cột id đã là INTEGER\n";
            
            echo "\n2. Kiểm tra sequence:\n";
            $seqSql = "SELECT sequence_name FROM information_schema.sequences WHERE sequence_name LIKE '%order_id_seq%'";
            $seqResult = db_query($conn, $seqSql);
            if ($seqResult && db_num_rows($seqResult) > 0) {
                while ($seqRow = db_fetch_assoc($seqResult)) {
                    echo "- Sequence: {$seqRow['sequence_name']}\n";
                }
                
                echo "\n3. Kiểm tra PRIMARY KEY:\n";
                $pkSql = "SELECT constraint_name FROM information_schema.table_constraints 
                          WHERE table_name = 'order' AND constraint_type = 'PRIMARY KEY'";
                $pkResult = db_query($conn, $pkSql);
                if ($pkResult && db_num_rows($pkResult) > 0) {
                    echo "✅ Bảng order đã có PRIMARY KEY\n";
                } else {
                    echo "❌ Bảng order chưa có PRIMARY KEY\n";
                    
                    // Kiểm tra và xóa các bản ghi có id null
                    echo "\n4. Xử lý các bản ghi có id null:\n";
                    $nullCheck = "SELECT COUNT(*) as null_count FROM \"order\" WHERE id IS NULL";
                    $nullResult = db_query($conn, $nullCheck);
                    if ($nullResult && db_num_rows($nullResult) > 0) {
                        $nullRow = db_fetch_assoc($nullResult);
                        $nullCount = $nullRow['null_count'];
                        echo "- Số bản ghi có id null: {$nullCount}\n";
                        
                        if ($nullCount > 0) {
                            try {
                                $deleteNull = "DELETE FROM \"order\" WHERE id IS NULL";
                                db_query($conn, $deleteNull);
                                echo "✅ Đã xóa {$nullCount} bản ghi có id null\n";
                            } catch (Exception $e) {
                                echo "❌ Lỗi khi xóa bản ghi null: " . $e->getMessage() . "\n";
                            }
                        }
                    }
                    
                    // Thử đặt PRIMARY KEY lại
                    try {
                        $addPK = "ALTER TABLE \"order\" ADD PRIMARY KEY (id)";
                        db_query($conn, $addPK);
                        echo "✅ Đã thêm PRIMARY KEY cho cột id\n";
                    } catch (Exception $e) {
                        echo "❌ Lỗi khi thêm PRIMARY KEY: " . $e->getMessage() . "\n";
                    }
                }
            } else {
                echo "❌ Chưa có sequence cho order.id\n";
                try {
                    $createSeq = "CREATE SEQUENCE IF NOT EXISTS order_id_seq";
                    db_query($conn, $createSeq);
                    echo "✅ Đã tạo sequence order_id_seq\n";
                    
                    $alterCol = "ALTER TABLE \"order\" ALTER COLUMN id SET DEFAULT nextval('order_id_seq')";
                    db_query($conn, $alterCol);
                    echo "✅ Đã sửa cột id để dùng sequence\n";
                    
                    $setSeq = "SELECT setval('order_id_seq', COALESCE((SELECT MAX(id) FROM \"order\"), 1))";
                    db_query($conn, $setSeq);
                    echo "✅ Đã set sequence value\n";
                } catch (Exception $e) {
                    echo "❌ Lỗi khi tạo sequence: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "❌ Cột id chưa phải INTEGER, cần sửa\n";
            // Code sửa cột sẽ được thêm ở đây nếu cần
        }
    } else {
        echo "❌ Không thể kiểm tra cột id\n";
    }
    
} else {
    echo "Chỉ hỗ trợ PostgreSQL\n";
}

echo "\n🎯 Hoàn tất kiểm tra và sửa cột id!\n";
db_close($conn);
?>
