<?php
require_once 'config/config.php';

echo "🔧 Sửa kiểu dữ liệu cột madon của bảng orderdetails...\n\n";

if (isPostgreSQL($conn)) {
    echo "1. Kiểm tra cấu trúc cột madon:\n";
    $checkSql = "SELECT column_name, data_type, is_nullable, column_default 
                  FROM information_schema.columns 
                  WHERE table_name = 'orderdetails' AND column_name = 'madon'";
    $result = db_query($conn, $checkSql);
    if ($result && db_num_rows($result) > 0) {
        $row = db_fetch_assoc($result);
        echo "- Cột madon: {$row['data_type']} (nullable: {$row['is_nullable']})";
        if ($row['column_default']) {
            echo " (default: {$row['column_default']})";
        }
        echo "\n";
        
        if ($row['data_type'] === 'integer') {
            echo "✅ Cột madon đã là INTEGER\n";
        } else {
            echo "❌ Cột madon chưa phải INTEGER, cần sửa\n";
            
            echo "\n2. Sửa kiểu dữ liệu cột madon:\n";
            try {
                // Tạo cột madon mới kiểu integer
                $addCol = "ALTER TABLE orderdetails ADD COLUMN madon_new INTEGER";
                db_query($conn, $addCol);
                echo "✅ Đã thêm cột madon_new kiểu INTEGER\n";
                
                // Copy dữ liệu từ cột cũ sang cột mới (chỉ những giá trị là số)
                $copyData = "UPDATE orderdetails SET madon_new = CAST(madon AS INTEGER) WHERE madon ~ '^[0-9]+$'";
                db_query($conn, $copyData);
                echo "✅ Đã copy dữ liệu sang cột mới\n";
                
                // Xóa cột cũ
                $dropOld = "ALTER TABLE orderdetails DROP COLUMN madon";
                db_query($conn, $dropOld);
                echo "✅ Đã xóa cột madon cũ\n";
                
                // Đổi tên cột mới thành madon
                $renameCol = "ALTER TABLE orderdetails RENAME COLUMN madon_new TO madon";
                db_query($conn, $renameCol);
                echo "✅ Đã đổi tên cột madon_new thành madon\n";
                
                // Thêm FOREIGN KEY constraint
                $addFK = "ALTER TABLE orderdetails ADD CONSTRAINT fk_orderdetails_order 
                          FOREIGN KEY (madon) REFERENCES \"order\"(id)";
                db_query($conn, $addFK);
                echo "✅ Đã thêm FOREIGN KEY constraint\n";
                
            } catch (Exception $e) {
                echo "❌ Lỗi: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "❌ Không thể kiểm tra cột madon\n";
    }
    
} else {
    echo "Chỉ hỗ trợ PostgreSQL\n";
}

echo "\n🎯 Hoàn tất sửa cột madon!\n";
db_close($conn);
?>
