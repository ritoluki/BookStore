<?php
// Load config first to get environment variables
require_once 'config.php';

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
    $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';     // Server SMTP từ env
    $mail->SMTPAuth = true;                             // Bật xác thực SMTP
    $mail->Username = $_ENV['SMTP_USERNAME'] ?? 'bookshopdatn@gmail.com';  // Email từ env
    $mail->Password = $_ENV['SMTP_PASSWORD'] ?? '';     // App Password từ env

    // Cấu hình bảo mật
    $mail->SMTPSecure = 'ssl';                            // Sử dụng SSL
    $mail->Port = $_ENV['SMTP_PORT'] ?? 465;       // Cổng từ env

    // Cấu hình bổ sung
    $mail->CharSet = 'UTF-8';                          // Hỗ trợ tiếng Việt
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Bật chế độ debug
    $mail->SMTPDebug = 2;                                 // 2 = hiển thị chi tiết để debug

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
function sendEmail($to, $subject, $body, $from_email = 'bookshopdatn@gmail.com', $from_name = 'BOOK SHOP')
{
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
function testSMTPConnection()
{
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

function sendPaymentReminderMail($order)
{
    $to = $order['email'];
    $orderId = htmlspecialchars($order['id']);
    $customerName = htmlspecialchars($order['tenguoinhan']);
    $subject = "Vui lòng hoàn tất thanh toán đơn hàng #$orderId";
    $body = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);'>
        <h2 style='color:#e67e22;'>Nhắc nhở thanh toán đơn hàng #$orderId</h2>
        <p>Xin chào $customerName,</p>
        <p>Bạn đã đặt đơn hàng tại Book Shop nhưng chưa hoàn tất thanh toán online. Vui lòng hoàn tất thanh toán để chúng tôi có thể xử lý và giao hàng cho bạn sớm nhất.</p>
        <p><b>Thông tin đơn hàng:</b></p>
        <ul>
            <li><b>Mã đơn hàng:</b> #$orderId</li>
            <li><b>Ngày đặt:</b> " . date('d/m/Y', strtotime($order['thoigiandat'])) . "</li>
            <li><b>Tổng tiền:</b> " . number_format($order['tongtien']) . " VNĐ</li>
        </ul>
        <p>Nếu bạn cần hỗ trợ, vui lòng liên hệ với chúng tôi qua email hoặc hotline.</p>
        <p style='color:#888;font-size:13px;'>Cảm ơn bạn đã lựa chọn Book Shop!</p>
    </div>";
    return sendEmail($to, $subject, $body);
}
?>