<?php
require_once 'config/config.php';

echo "🔍 Kiểm tra cấu trúc bảng orderdetails...\n\n";

if (isPostgreSQL($conn)) {
    // PostgreSQL
    $sql = "SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'orderdetails' 
            ORDER BY ordinal_position";
} else {
    // MySQL
    $sql = "DESCRIBE orderdetails";
}

$result = db_query($conn, $sql);

if ($result && db_num_rows($result) > 0) {
    echo "Cấu trúc bảng orderdetails:\n";
    while ($row = db_fetch_assoc($result)) {
        if (isPostgreSQL($conn)) {
            echo "- {$row['column_name']}: {$row['data_type']}";
            echo " (nullable: {$row['is_nullable']})";
            if ($row['column_default']) {
                echo " (default: {$row['column_default']})";
            }
            echo "\n";
        } else {
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
} else {
    echo "❌ Không thể kiểm tra cấu trúc bảng\n";
}

echo "\n--- Kiểm tra dữ liệu mẫu ---\n";
$sampleSql = "SELECT * FROM orderdetails LIMIT 3";
$sampleResult = db_query($conn, $sampleSql);

if ($sampleResult && db_num_rows($sampleResult) > 0) {
    echo "Dữ liệu mẫu:\n";
    while ($row = db_fetch_assoc($sampleResult)) {
        echo "- " . json_encode($row) . "\n";
    }
} else {
    echo "❌ Không có dữ liệu mẫu\n";
}

db_close($conn);
echo "\n🎯 Kiểm tra hoàn tất!\n";
?>
