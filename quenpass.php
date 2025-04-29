<?php
$thongbao = "";
$status = ""; // Thêm biến status để kiểm soát loại thông báo

if (isset($_POST['btn1'])) {
    $email = trim(strip_tags($_POST['email']));

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $thongbao = "Email không đúng";
        $status = "error";
    } else {
    require_once 'php/config.php';
        $sql = "SELECT id, phone, fullname FROM users WHERE email = '{$email}'";
    $kq = $conn->query($sql);
        $row = $kq->fetch_assoc();

    if (!$row) {
            $thongbao = "Email này không phải là thành viên";
            $status = "error";
    } else {
            try {
                // Tạo token ngẫu nhiên
                $token = bin2hex(random_bytes(32));
                
                // Lưu token vào database
                $update_token = "UPDATE users SET reset_token = '{$token}' WHERE email = '{$email}'";
                if (!$conn->query($update_token)) {
                    throw new Exception("Lỗi cập nhật token");
                }
                
                // Tạo link đổi mật khẩu
                $reset_link = "http://localhost/websach/reset_password.php?email=" . urlencode($email) . "&token=" . $token;

                // Gửi email chứa link đổi mật khẩu
                require 'send_mail.php';
                
                $mail->setFrom('bookshopdatn@gmail.com', 'BOOK SHOP');
                $mail->addAddress($email);
                $mail->Subject = 'Đặt Lại Mật Khẩu - BOOK SHOP';
                
                // Nội dung email với HTML đẹp
                $mail->Body = '
                <!DOCTYPE html>
                <html lang="vi">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Đặt Lại Mật Khẩu</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                        }
                        body {
                            background-color: #f4f4f4;
                            color: #333;
                            line-height: 1.6;
                        }
                        .email-container {
                            max-width: 600px;
                            margin: 20px auto;
                            background: #ffffff;
                            border-radius: 10px;
                            overflow: hidden;
                            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                        }
                        .email-header {
                            background: linear-gradient(135deg, #4a6ed0, #2b3f7a);
                            color: white;
                            padding: 30px;
                            text-align: center;
                        }
                        .email-header img {
                            width: 80px;
                            height: auto;
                            margin-bottom: 15px;
                        }
                        .email-header h1 {
                            font-size: 24px;
                            margin: 0;
                            font-weight: 600;
                        }
                        .email-body {
                            padding: 30px;
                            color: #555;
                        }
                        .greeting {
                            font-size: 18px;
                            margin-bottom: 20px;
                            color: #333;
                        }
                        .message {
                            margin-bottom: 30px;
                            font-size: 16px;
                        }
                        .button-container {
                            text-align: center;
                            margin: 35px 0;
                        }
                        .reset-button {
                            display: inline-block;
                            padding: 15px 35px;
                            background: #4a6ed0;
                            color: #ffffff !important;
                            text-decoration: none;
                            border-radius: 5px;
                            font-weight: 600;
                            font-size: 16px;
                            margin: 20px 0;
                        }
                        .reset-button:hover {
                            background: #3a5db9;
                        }
                        .expiry-notice {
                            background-color: #fff8e6;
                            border-left: 4px solid #ffc107;
                            padding: 15px;
                            border-radius: 4px;
                            margin: 25px 0;
                            font-size: 14px;
                        }
                        .security-note {
                            font-size: 14px;
                            color: #777;
                            margin-top: 25px;
                            padding-top: 20px;
                            border-top: 1px solid #eee;
                        }
                        .email-footer {
                            background: #f8f9fa;
                            padding: 20px 30px;
                            font-size: 13px;
                            color: #888;
                            text-align: center;
                            border-top: 1px solid #eee;
                        }
                        .social-links {
                            margin: 15px 0;
                        }
                        .social-icon {
                            display: inline-block;
                            width: 32px;
                            height: 32px;
                            line-height: 32px;
                            text-align: center;
                            margin: 0 5px;
                            border-radius: 50%;
                            color: #ffffff !important;
                            text-decoration: none;
                            font-family: Arial, sans-serif;
                        }
                        .facebook { background-color: #3b5998 !important; }
                        .twitter { background-color: #1da1f2 !important; }
                        .instagram { background-color: #e1306c !important; }
                        .help-text {
                            margin-top: 15px;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            <h1>Đặt Lại Mật Khẩu</h1>
                        </div>
                        
                        <div class="email-body">
                            <p class="greeting">Xin chào ' . htmlspecialchars($row['fullname']) . ',</p>
                            
                            <div class="message">
                                <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu tài khoản của bạn. Để hoàn tất quá trình này, vui lòng nhấn vào nút bên dưới:</p>
                            </div>
                            
                            <div style="text-align: center;">
                                <a href="' . $reset_link . '" style="background: #4a6ed0; color: #ffffff; text-decoration: none; padding: 15px 35px; border-radius: 5px; font-weight: 600; font-size: 16px; display: inline-block;">Đặt Lại Mật Khẩu</a>
                            </div>
                            
                            <div class="expiry-notice">
                                <strong>Lưu ý:</strong> Liên kết này sẽ hết hạn sau 24 giờ kể từ khi bạn nhận được email.
                            </div>
                            
                            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này hoặc liên hệ với bộ phận hỗ trợ của chúng tôi nếu bạn có bất kỳ thắc mắc nào.</p>
                            
                            <div class="security-note">
                                <p>Vì lý do bảo mật, chúng tôi khuyên bạn nên tạo mật khẩu mạnh với ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
                            </div>
                        </div>
                        
                        <div class="email-footer">
                            <p>© 2024 BOOK SHOP. Tất cả các quyền được bảo lưu.</p>
                            
                            <div class="social-links">
                                <a href="https://www.facebook.com/nhtanf" class="social-icon facebook" style="background-color: #3b5998; color: #ffffff; text-decoration: none;">F</a>
                                <a href="https://www.facebook.com/nhtanf" class="social-icon twitter" style="background-color: #1da1f2; color: #ffffff; text-decoration: none;">T</a>
                                <a href="https://www.instagram.com/nhtanf" class="social-icon instagram" style="background-color: #e1306c; color: #ffffff; text-decoration: none;">I</a>
                            </div>
                            
                            <p class="help-text">Nếu bạn cần hỗ trợ, vui lòng gửi email đến <a href="mailto:bookshopdatn@gmail.com" style="color: #4a6ed0; text-decoration: underline;">bookshopdatn@gmail.com</a></p>
                        </div>
                    </div>
                </body>
                </html>';
                
                $mail->AltBody = "Đặt lại mật khẩu: " . $reset_link;
                $mail->isHTML(true);

            $mail->send();
                $thongbao = "Chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn";
                $status = "success";
        } catch (Exception $e) {
                $thongbao = "Có lỗi xảy ra khi gửi email: " . $mail->ErrorInfo;
                $status = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - BOOK SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .success-title {
            color: #28a745;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .success-message {
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-custom {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        .btn-home {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
        .email-tips {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .tips-list {
            text-align: left;
            list-style: none;
            padding-left: 0;
        }
        .tips-list li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }
        .tips-list li:before {
            content: "•";
            color: #007bff;
            position: absolute;
            left: 0;
        }
    </style>
</head>
<body>
<?php if ($thongbao != "") { ?>
        <div class="success-container">
            <?php if ($status == "success") { ?>
                <i class="fas fa-check-circle success-icon"></i>
                <h2 class="success-title">Gửi Email Thành Công!</h2>
                <p class="success-message">
                    <?= $thongbao ?><br>
                    Vui lòng kiểm tra hộp thư đến để tiếp tục.
                </p>
                <div class="email-tips">
                    <h5>Mẹo kiểm tra email:</h5>
                    <ul class="tips-list">
                        <li>Kiểm tra thư mục Spam nếu không thấy email trong hộp thư đến</li>
                        <li>Đảm bảo địa chỉ email bạn nhập chính xác</li>
                        <li>Đợi vài phút và làm mới hộp thư của bạn</li>
                    </ul>
                </div>
            <?php } else { ?>
                <i class="fas fa-exclamation-circle success-icon" style="color: #dc3545;"></i>
                <h2 class="success-title" style="color: #dc3545;">Có lỗi xảy ra!</h2>
                <p class="success-message"><?= $thongbao ?></p>
            <?php } ?>
            <div class="mt-4">
                <button class="btn btn-custom btn-back" onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>Trở lại
                </button>
                <a href="http://localhost/websach/" class="btn btn-custom btn-home">
                    <i class="fas fa-home mr-2"></i>Trang chủ
                </a>
    </div>
</div>
    <?php } else { ?>
        <div class="col-5 m-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Quên Mật Khẩu</h4>
    </div>
                <div class="card-body">
                    <form action="" method="post">
    <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope mr-2"></i>Nhập email
                            </label>
                            <input class="form-control" name="email" type="email" 
                                   placeholder="Nhập địa chỉ email của bạn" required>
    </div>
                        <button type="submit" name="btn1" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-2"></i>Gửi yêu cầu
                        </button>
</form>
                </div>
            </div>
        </div>
    <?php } ?>
</body>
</html>
