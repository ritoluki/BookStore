<?php
require_once '../../config/config.php';

$thongbao = "";
$status = ""; // Thêm biến status để kiểm soát loại thông báo

if (isset($_POST['btn1'])) {
    $email = trim(strip_tags($_POST['email']));

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $thongbao = "Email không đúng";
        $status = "error";
    } else {
        // Sửa: Sử dụng prepared statement để tránh SQL injection
        $sql = "SELECT id, phone, fullname FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

    if (!$row) {
            $thongbao = "Email này không phải là thành viên";
            $status = "error";
    } else {
            try {
                // Tạo token ngẫu nhiên
                $token = bin2hex(random_bytes(32));
                
                // Lưu token vào database
                // Sửa: Sử dụng prepared statement để tránh SQL injection
                $update_token = "UPDATE users SET reset_token = ? WHERE email = ?";
                $update_stmt = $conn->prepare($update_token);
                $update_stmt->bind_param("ss", $token, $email);
                
                if (!$update_stmt->execute()) {
                    throw new Exception("Lỗi cập nhật token");
                }
                $update_stmt->close();
                
                // Tạo link đổi mật khẩu
                $reset_link = getBaseUrl() . "/reset_password.php?email=" . urlencode($email) . "&token=" . $token;

                // Gửi email chứa link đổi mật khẩu
                require '../services/send_mail.php';
                
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
                        body {
                            background: linear-gradient(135deg, #e0e7ff 0%, #f5f7fa 100%);
                            margin: 0;
                            padding: 0;
                            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                        }
                        .email-container {
                            max-width: 480px;
                            margin: 40px auto;
                            background: #fff;
                            border-radius: 18px;
                            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.12);
                            overflow: hidden;
                            border: 1px solid #e3e8f7;
                        }
                        .email-header {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: #fff;
                            padding: 36px 30px 24px 30px;
                            text-align: center;
                        }
                        .email-header .icon {
                            font-size: 44px;
                            background: #fff;
                            color: #764ba2;
                            border-radius: 50%;
                            padding: 12px;
                            margin-bottom: 12px;
                            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.10);
                            display: inline-block;
                        }
                        .email-header h1 {
                            font-size: 24px;
                            margin: 0 0 6px 0;
                            font-weight: 700;
                            letter-spacing: 0.5px;
                        }
                        .email-body {
                            padding: 32px 30px 24px 30px;
                            color: #444;
                            text-align: center;
                        }
                        .greeting {
                            font-size: 18px;
                            margin-bottom: 18px;
                            color: #222;
                            font-weight: 500;
                        }
                        .message {
                            margin-bottom: 28px;
                            font-size: 16px;
                            color: #555;
                        }
                        .reset-button {
                            display: inline-block;
                            padding: 15px 38px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: #fff !important;
                            text-decoration: none;
                            border-radius: 8px;
                            font-weight: 600;
                            font-size: 16px;
                            margin: 18px 0 10px 0;
                            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.13);
                            transition: background 0.2s, box-shadow 0.2s;
                        }
                        .reset-button:hover {
                            background: linear-gradient(135deg, #5a67d8 0%, #6b47dc 100%);
                            box-shadow: 0 6px 24px rgba(102, 126, 234, 0.18);
                        }
                        .expiry-notice {
                            background: #f5f7fa;
                            border-left: 4px solid #667eea;
                            padding: 14px 18px;
                            border-radius: 6px;
                            margin: 22px 0 18px 0;
                            font-size: 14px;
                            color: #444;
                            text-align: left;
                        }
                        .security-note {
                            font-size: 13px;
                            color: #888;
                            margin-top: 18px;
                            padding-top: 14px;
                            border-top: 1px solid #eee;
                        }
                        .email-footer {
                            background: #f8f9fa;
                            padding: 18px 30px;
                            font-size: 13px;
                            color: #888;
                            text-align: center;
                            border-top: 1px solid #eee;
                        }
                        .social-links {
                            margin: 12px 0 0 0;
                        }
                        .social-icon {
                            display: inline-block;
                            width: 32px;
                            height: 32px;
                            line-height: 32px;
                            text-align: center;
                            margin: 0 5px;
                            border-radius: 50%;
                            color: #fff !important;
                            text-decoration: none;
                            font-family: Arial, sans-serif;
                            font-size: 16px;
                        }
                        .facebook { background-color: #3b5998 !important; }
                        .twitter { background-color: #1da1f2 !important; }
                        .instagram { background-color: #e1306c !important; }
                        .help-text {
                            margin-top: 10px;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            <span class="icon">🔒</span>
                            <h1>Đặt Lại Mật Khẩu</h1>
                        </div>
                        <div class="email-body">
                            <div class="greeting">Xin chào ' . htmlspecialchars($row['fullname']) . ',</div>
                            <div class="message">
                                <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu tài khoản của bạn. Để hoàn tất quá trình này, vui lòng nhấn vào nút bên dưới:</p>
                            </div>
                            <div style="text-align: center;">
                                <a href="' . $reset_link . '" style="background: #4a6ed0; color: #ffffff; text-decoration: none; padding: 15px 35px; border-radius: 5px; font-weight: 600; font-size: 16px; display: inline-block;">Đặt Lại Mật Khẩu</a>
                            </div>
                            <div class="expiry-notice">
                                <strong>Lưu ý:</strong> Liên kết này sẽ hết hạn sau 1 giờ kể từ khi bạn nhận được email.
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="30" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 30s linear infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 50px 40px;
            max-width: 450px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .header p {
            color: #6c757d;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 35px;
        }
        .form-container {
            margin-bottom: 30px;
        }
        .form-group {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
        }
        .form-label {
            display: block;
            color: #495057;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-input {
            width: 100%;
            padding: 18px 20px 18px 50px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            font-size: 16px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
            color: #495057;
        }
        .form-input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        .form-input:valid {
            border-color: #28a745;
        }
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #6c757d;
            transition: all 0.3s ease;
            margin-top: 12px;
        }
        .form-input:focus + .input-icon {
            color: #667eea;
        }
        .form-input:valid + .input-icon {
            color: #28a745;
        }
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }
        .submit-btn:hover::before {
            left: 100%;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }
        .submit-btn:active {
            transform: translateY(-1px);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .helper-text {
            margin-top: 30px;
            padding: 20px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }
        .helper-text h4 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .helper-text p {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            .container {
                padding: 40px 25px;
                margin: 0;
            }
            .header h1 {
                font-size: 24px;
            }
            .form-input {
                padding: 16px 18px 16px 45px;
            }
            .submit-btn {
                padding: 16px;
            }
        }
        /* Animation cho các element khi trang load */
        .fade-in {
            animation: fadeIn 0.8s ease forwards;
            opacity: 0;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        .slide-up {
            animation: slideUp 0.8s ease forwards;
            transform: translateY(30px);
            opacity: 0;
        }
        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .success-message {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            color: #155724;
            display: block;
        }
        .success-message .icon {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }
        .error-message {
            background: linear-gradient(135deg, #f8d7da, #f1b0b7);
            border: 2px solid #dc3545;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            color: #721c24;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container fade-in">
        <div class="logo slide-up">🔒</div>
        <div class="header slide-up">
            <h1>Quên Mật Khẩu?</h1>
            <p>Đừng lo lắng! Nhập email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu.</p>
        </div>
        <?php if ($thongbao != "") { ?>
            <?php if ($status == "success") { ?>
                <div class="success-message slide-up">
                    <span class="icon">✅</span>
                    <strong>Email đã được gửi!</strong><br>
                    <?= $thongbao ?><br>
                    Vui lòng kiểm tra hộp thư và làm theo hướng dẫn để đặt lại mật khẩu.
                </div>
            <?php } else { ?>
                <div class="error-message slide-up">
                    <strong>Có lỗi xảy ra!</strong><br>
                    <?= $thongbao ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin-top:16px;display:inline-block;padding:10px 24px;background:#6c757d;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;transition:all 0.3s;" onmouseover="this.style.background='#5a6268'" onmouseout="this.style.background='#6c757d'">Quay lại</a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <form class="form-container slide-up" method="post">
                <div class="form-group">
                    <label for="email" class="form-label">Địa chỉ Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="Nhập email của bạn..."
                        required
                        autocomplete="email"
                    />
                    <span class="input-icon">📧</span>
                </div>
                <button type="submit" name="btn1" class="submit-btn" id="submitBtn">
                    <span class="btn-text">Gửi Link Đặt Lại</span>
                </button>
            </form>
        <?php } ?>
        
        <div style="text-align:center;margin-top:20px;margin-bottom:20px;">
            <a href="<?php echo getBaseUrl(); ?>/" style="display:inline-block;padding:12px 28px;background:#667eea;color:#fff;text-decoration:none;border-radius:10px;font-weight:600;transition:all 0.3s;box-shadow:0 4px 15px rgba(102,126,234,0.3);" onmouseover="this.style.background='#5a67d8';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#667eea';this.style.transform='translateY(0)'">Về trang chủ</a>
        </div>
        
        <div class="helper-text slide-up">
            <h4>💡 Lưu ý:</h4>
            <p>
                Link đặt lại mật khẩu sẽ có hiệu lực trong 1 giờ. 
                Nếu không nhận được email, vui lòng kiểm tra thư mục spam 
                hoặc liên hệ bộ phận hỗ trợ.
            </p>
        </div>
    </div>
    <script>
        // Add staggered animation delays
        document.addEventListener('DOMContentLoaded', function() {
            const slideUpElements = document.querySelectorAll('.slide-up');
            slideUpElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>
