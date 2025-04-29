<?php
// Nạp autoloader của Composer
require 'vendor/autoload.php';

// Import các lớp cần thiết
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Khởi tạo PHPMailer
$mail = new PHPMailer(true); // true kích hoạt exceptions

try {
    // Cấu hình server
    $mail->isSMTP();                                      // Sử dụng SMTP
    $mail->Host       = 'smtp.gmail.com';                 // Server SMTP của Gmail
    $mail->SMTPAuth   = true;                             // Bật xác thực SMTP
    $mail->Username   = 'bookshopdatn@gmail.com';         // Email của bạn
    
    // CHÚ Ý: Đây là App Password (không phải mật khẩu Gmail thông thường)
    // Để tạo mật khẩu ứng dụng:
    // 1. Truy cập Google Account > Security > 2-Step Verification (phải bật)
    // 2. Sau đó vào App Passwords > Tạo mật khẩu mới cho ứng dụng
    $mail->Password   = 'kvec wxoz ptjx utif';            // Thay bằng App Password thực của bạn
    
    // Cấu hình bảo mật
    $mail->SMTPSecure = 'ssl';                            // Sử dụng SSL
    $mail->Port       = 465;                              // Cổng SSL
    
    // Hoặc có thể dùng TLS (bỏ comment dòng dưới và comment 2 dòng trên nếu muốn dùng TLS)
    // $mail->SMTPSecure = 'tls';                         // Sử dụng TLS
    // $mail->Port       = 587;                           // Cổng TLS
    
    // Cấu hình bổ sung
    $mail->CharSet    = 'UTF-8';                          // Hỗ trợ tiếng Việt
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Bật chế độ debug
    $mail->SMTPDebug = 0;                                 // 0 = tắt debug, 1 = client, 2 = client và server
    
    // Cấu hình email
    $mail->isHTML(true);                                  // Gửi email dạng HTML
    
} catch (Exception $e) {
    error_log("Lỗi cấu hình PHPMailer: {$e->getMessage()}");
    echo "Không thể cấu hình PHPMailer. Lỗi: {$e->getMessage()}";
}

/**
 * Hàm tiện ích để gửi email
 * 
 * @param string $to Email người nhận
 * @param string $subject Tiêu đề email
 * @param string $body Nội dung email (HTML)
 * @param string $from_email Email người gửi (mặc định là bookshop)
 * @param string $from_name Tên người gửi (mặc định là BOOK SHOP)
 * @return bool Trả về true nếu gửi thành công, false nếu thất bại
 */
function sendEmail($to, $subject, $body, $from_email = 'bookshopdatn@gmail.com', $from_name = 'BOOK SHOP') {
    global $mail;
    
    try {
        // Reset recipients
        $mail->clearAddresses();
        $mail->clearReplyTos();
        
        // Cấu hình người gửi và người nhận
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to);
        
        // Cấu hình tiêu đề và nội dung
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        
        // Ghi log
        error_log("Chuẩn bị gửi email đến: $to - Tiêu đề: $subject");
        
        // Gửi email
        $mail->send();
        error_log("Đã gửi email thành công đến: $to");
        return true;
    } catch (Exception $e) {
        error_log("Lỗi gửi email đến $to: " . $e->getMessage());
        return false;
    }
}

// Hàm kiểm tra cấu hình SMTP
function testSMTPConnection() {
    global $mail;
    
    try {
        $smtp = new SMTP;
        $smtp->connect($mail->Host, $mail->Port);
        
        if ($smtp->hello(gethostname())) {
            if ($smtp->authenticate($mail->Username, $mail->Password)) {
                $result = "Kết nối SMTP thành công!";
                $smtp->quit();
                return $result;
            } else {
                return "Lỗi xác thực: " . $smtp->getError()['error'];
            }
        } else {
            return "Lỗi kết nối: " . $smtp->getError()['error'];
        }
    } catch (Exception $e) {
        return "Lỗi kiểm tra SMTP: " . $e->getMessage();
    }
}
?>