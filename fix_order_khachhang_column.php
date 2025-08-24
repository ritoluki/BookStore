<?php
require_once 'config/config.php';

echo "🔧 Sửa kiểu dữ liệu cột khachhang của bảng order...\n\n";

if (isPostgreSQL($conn)) {
    echo "1. Kiểm tra cấu trúc cột khachhang:\n";
    $checkSql = "SELECT column_name, data_type, is_nullable 
                  FROM information_schema.columns 
                  WHERE table_name = 'order' AND column_name = 'khachhang'";
    $result = db_query($conn, $checkSql);
    if ($result && db_num_rows($result) > 0) {
        $row = db_fetch_assoc($result);
        echo "- Cột khachhang: {$row['data_type']} (nullable: {$row['is_nullable']})\n";
        
        if ($row['data_type'] === 'integer') {
            echo "✅ Cột khachhang đã là INTEGER\n";
        } else {
            echo "❌ Cột khachhang chưa phải INTEGER, cần sửa\n";
            
            echo "\n2. Kiểm tra dữ liệu hiện tại:\n";
            $dataSql = "SELECT khachhang FROM \"order\" LIMIT 5";
            $dataResult = db_query($conn, $dataSql);
            if ($dataResult && db_num_rows($dataResult) > 0) {
                while ($dataRow = db_fetch_assoc($dataResult)) {
                    echo "- Khachhang: {$dataRow['khachhang']}\n";
                }
            }
            
            echo "\n3. Sửa kiểu dữ liệu cột khachhang:\n";
            try {
                // Tạo cột khachhang mới kiểu integer
                $addCol = "ALTER TABLE \"order\" ADD COLUMN khachhang_new INTEGER";
                db_query($conn, $addCol);
                echo "✅ Đã thêm cột khachhang_new kiểu INTEGER\n";
                
                // Copy dữ liệu từ cột cũ sang cột mới (chỉ những giá trị là số)
                $copyData = "UPDATE \"order\" SET khachhang_new = CAST(khachhang AS INTEGER) WHERE khachhang ~ '^[0-9]+$'";
                db_query($conn, $copyData);
                echo "✅ Đã copy dữ liệu sang cột mới\n";
                
                // Xóa cột cũ
                $dropOld = "ALTER TABLE \"order\" DROP COLUMN khachhang";
                db_query($conn, $dropOld);
                echo "✅ Đã xóa cột khachhang cũ\n";
                
                // Đổi tên cột mới thành khachhang
                $renameCol = "ALTER TABLE \"order\" RENAME COLUMN khachhang_new TO khachhang";
                db_query($conn, $renameCol);
                echo "✅ Đã đổi tên cột khachhang_new thành khachhang\n";
                
                // Thêm FOREIGN KEY constraint
                $addFK = "ALTER TABLE \"order\" ADD CONSTRAINT fk_order_users 
                          FOREIGN KEY (khachhang) REFERENCES users(id)";
                db_query($conn, $addFK);
                echo "✅ Đã thêm FOREIGN KEY constraint\n";
                
            } catch (Exception $e) {
                echo "❌ Lỗi: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "❌ Không thể kiểm tra cột khachhang\n";
    }
    
} else {
    echo "Chỉ hỗ trợ PostgreSQL\n";
}

echo "\n🎯 Hoàn tất sửa cột khachhang!\n";
db_close($conn);
?>
