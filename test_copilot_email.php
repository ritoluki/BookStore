<?php
require 'src/services/send_mail.php';

echo "<h2>Test kết nối SMTP trên nhánh Copilot</h2>";
echo "<p>" . testSMTPConnection() . "</p>";

echo "<h2>Test gửi email</h2>";
$result = sendEmail(
    'bookshopdatn@gmail.com',
    'Test Email từ nhánh Copilot - ' . date('Y-m-d H:i:s'),
    '<h1>Test Email từ nhánh Copilot</h1><p>Đây là email test từ BOOK SHOP với cấu trúc mới</p>'
);

if ($result) {
    echo "<p style='color:green;'>✅ Gửi email thành công!</p>";
} else {
    echo "<p style='color:red;'>❌ Gửi email thất bại!</p>";
}
?>