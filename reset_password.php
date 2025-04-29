<?php
session_start();
$thongbao = "";

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];
    
    // Kiểm tra token có hợp lệ không
    require_once 'php/config.php';
    $sql = "SELECT id FROM users WHERE email = '{$email}' AND reset_token = '{$token}'";
    $kq = $conn->query($sql);
    
    if ($kq->num_rows > 0) {
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
                $update_sql = "UPDATE users SET password = '{$new_password}', reset_token = NULL WHERE email = '{$email}'";
                if ($conn->query($update_sql)) {
                    $thongbao .= "Đổi mật khẩu thành công!<br>";
                    // Cập nhật localStorage
                    $update_js = "<script>
                        if (localStorage.getItem('accounts')) {
                            let accounts = JSON.parse(localStorage.getItem('accounts'));
                            let accountIndex = accounts.findIndex(acc => acc.email === '{$email}');
                            if (accountIndex !== -1) {
                                accounts[accountIndex].password = '{$new_password}';
                                localStorage.setItem('accounts', JSON.stringify(accounts));
                            }
                        }
                    </script>";
                    echo $update_js;
                } else {
                    $thongbao .= "Có lỗi xảy ra khi đổi mật khẩu<br>";
                }
            }
        }
    } else {
        $thongbao .= "Link đổi mật khẩu không hợp lệ hoặc đã hết hạn<br>";
    }
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
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <?php if ($thongbao != ""): ?>
                    <div class="alert alert-info text-center">
                        <?= $thongbao ?>
                        <a href="http://localhost/websach/" class="btn btn-primary mt-2">Về trang chủ</a>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="text-center">Đổi mật khẩu mới</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label for="new_password">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" name="btn_reset" class="btn btn-primary btn-block">Đổi mật khẩu</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 