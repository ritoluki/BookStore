<?php
session_start();
$thongbao = "";

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];
    
    // Kiểm tra token có hợp lệ không
    require_once 'php/config.php';

    $sql = "SELECT id FROM users WHERE email = ? AND reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Token hợp lệ, cho phép đổi mật khẩu
        if (isset($_POST['btn_reset'])) {
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            if (strlen($new_password) < 6) {
                $thongbao .= "Mật khẩu phải có ít nhất 6 ký tự<br>";
            } elseif ($new_password !== $confirm_password) {
                $thongbao .= "Mật khẩu xác nhận không khớp<br>";
            } else {
                // Cập nhật mật khẩu mới và xóa token
                // Sửa: Sử dụng prepared statement để tránh SQL injection
                $update_sql = "UPDATE users SET password = ?, reset_token = NULL WHERE email = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_password, $email);
                
                if ($update_stmt->execute()) {
                    $thongbao .= "Đổi mật khẩu thành công!<br>";
                    // Cập nhật localStorage - Lưu ý: JavaScript vẫn có thể có lỗ hổng XSS nếu không escape đúng
                    echo "<script>
                        if (localStorage.getItem('accounts')) {
                            let accounts = JSON.parse(localStorage.getItem('accounts'));
                            let accountIndex = accounts.findIndex(acc => acc.email === " . json_encode($email) . ");
                            if (accountIndex !== -1) {
                                accounts[accountIndex].password = " . json_encode($new_password) . ";
                                localStorage.setItem('accounts', JSON.stringify(accounts));
                            }
                        }
                    </script>";
                } else {
                    $thongbao .= "Có lỗi xảy ra khi đổi mật khẩu<br>";
                }
                $update_stmt->close();
            }
        }
    } else {
        $thongbao .= "Link đổi mật khẩu không hợp lệ hoặc đã hết hạn<br>";
    }
    $stmt->close();
} else {
    $thongbao .= "Thiếu thông tin cần thiết<br>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - BOOK SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4a6ed0 0%, #2b3f7a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .center-card {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            margin: 0 auto;
            max-width: 500px;
            width: 100%;
            background: #fff;
            border: none;
        }
        .card-header {
            background: linear-gradient(90deg, #4a6ed0 60%, #2b3f7a 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            text-align: center;
            font-size: 1.7rem;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 2rem 1rem 1.5rem 1rem;
        }
        .card-body {
            padding: 2.5rem 2.5rem 2rem 2.5rem;
        }
        .form-group label {
            font-weight: 500;
            font-size: 1.1rem;
        }
        .form-control {
            font-size: 1.1rem;
            padding: 0.7rem 1rem;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            display: block;
        }
        .position-relative { position: relative; }
        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            z-index: 2;
            display: flex;
            align-items: center;
            height: 100%;
        }
        .progress {
            height: 7px;
            margin-top: 7px;
            background: #e9ecef;
            border-radius: 5px;
        }
        .progress-bar {
            transition: width 0.3s;
        }
        .btn-primary {
            background: linear-gradient(90deg, #4a6ed0 60%, #2b3f7a 100%);
            border: none;
            border-radius: 7px;
            font-weight: 600;
            font-size: 1.2rem;
            padding: 0.9rem 0;
            margin-top: 18px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        .btn-primary:disabled {
            background: #b0b8c1;
            color: #fff;
        }
        @media (max-width: 500px) {
            .card { max-width: 98vw; }
            .card-body { padding: 1.2rem 0.5rem 1rem 0.5rem; }
        }
        .success-message {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .success-message h2 {
            color: #28a745;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
        }
        .success-message p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .success-message .btn-home {
            background: linear-gradient(90deg, #4a6ed0 60%, #2b3f7a 100%);
            color: #fff;
            font-size: 1.25rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 0.9rem 2.5rem;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
            transition: background 0.2s, box-shadow 0.2s;
            margin-top: 10px;
            display: inline-block;
        }
        .success-message .btn-home:hover {
            background: linear-gradient(90deg, #2b3f7a 60%, #4a6ed0 100%);
            color: #fff;
            box-shadow: 0 4px 16px rgba(44,62,80,0.18);
        }
    </style>
</head>
<body>
<div class="center-card">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <?php if ($thongbao != ""): ?>
                    <div class="center-card">
                        <div class="success-message">
                            <h2>Đổi mật khẩu thành công!</h2>
                            <p>Bạn đã đổi mật khẩu thành công. Hãy sử dụng mật khẩu mới để đăng nhập.</p>
                            <a href="./" class="btn-home">Về trang chủ</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            Đổi mật khẩu mới
                        </div>
                        <div class="card-body">
                            <form method="post" id="resetForm" autocomplete="off">
                                <div class="form-group position-relative">
                                    <label for="new_password">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6" autocomplete="new-password">
                                  
                                    <div id="new_password_feedback" class="invalid-feedback"></div>
                                    <div class="progress">
                                        <div id="password_strength_bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="form-group position-relative">
                                    <label for="confirm_password">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6" autocomplete="new-password">
                                    
                                    <div id="confirm_password_feedback" class="invalid-feedback"></div>
                                </div>
                                <button type="submit" name="btn_reset" class="btn btn-primary btn-block" id="btn_reset" disabled>Đổi mật khẩu</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
// Toggle password visibility
function togglePassword(id, el) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        el.style.color = '#4a6ed0';
    } else {
        input.type = 'password';
        el.style.color = '#888';
    }
}
// Password strength checker
function checkStrength(password) {
    let strength = 0;
    if (password.length >= 6) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}
function getStrengthBar(strength) {
    // 0-5
    let percent = [0, 20, 40, 60, 80, 100][strength];
    let color = '#dc3545'; // đỏ
    if (strength === 3 || strength === 4) color = '#ffc107'; // vàng
    if (strength >= 5) color = '#28a745'; // xanh
    return { percent, color };
}
const newPassword = document.getElementById('new_password');
const confirmPassword = document.getElementById('confirm_password');
const newPasswordFeedback = document.getElementById('new_password_feedback');
const confirmPasswordFeedback = document.getElementById('confirm_password_feedback');
const passwordStrengthBar = document.getElementById('password_strength_bar');
const btnReset = document.getElementById('btn_reset');
function validateForm() {
    let valid = true;
    // Check new password
    if (newPassword.value.length < 6) {
        newPassword.classList.add('is-invalid');
        newPasswordFeedback.textContent = 'Mật khẩu phải có ít nhất 6 ký tự';
        valid = false;
    } else {
        newPassword.classList.remove('is-invalid');
        newPasswordFeedback.textContent = '';
    }
    // Check confirm password
    if (confirmPassword.value !== newPassword.value || confirmPassword.value.length < 6) {
        confirmPassword.classList.add('is-invalid');
        if (confirmPassword.value.length < 6) {
            confirmPasswordFeedback.textContent = 'Mật khẩu xác nhận phải có ít nhất 6 ký tự';
        } else {
            confirmPasswordFeedback.textContent = 'Mật khẩu xác nhận không khớp';
        }
        valid = false;
    } else {
        confirmPassword.classList.remove('is-invalid');
        confirmPasswordFeedback.textContent = '';
    }
    btnReset.disabled = !valid;
}
newPassword.addEventListener('input', function() {
    // Strength bar
    const strength = checkStrength(newPassword.value);
    const bar = getStrengthBar(strength);
    passwordStrengthBar.style.width = bar.percent + '%';
    passwordStrengthBar.style.background = bar.color;
    validateForm();
});
confirmPassword.addEventListener('input', validateForm);
</script>
</body>
</html> 