<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kết Quả Thanh Toán - VNPay</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 24px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                padding: 40px;
                width: 100%;
                max-width: 500px;
                text-align: center;
                position: relative;
                overflow: hidden;
                animation: slideUp 0.8s ease-out;
            }
            .container::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 4px;
                background: linear-gradient(90deg, #4CAF50, #2196F3, #FF9800);
                animation: shimmer 2s infinite;
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(50px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes shimmer {
                0%, 100% { transform: translateX(-100%); }
                50% { transform: translateX(100%); }
            }
            .status-icon {
                width: 80px; height: 80px; margin: 0 auto 24px; position: relative; animation: bounce 1s ease-out;
            }
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                40% { transform: translateY(-10px); }
                60% { transform: translateY(-5px); }
            }
            .success-icon {
                background: linear-gradient(135deg, #4CAF50, #45a049);
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                color: white; font-size: 36px; box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
            }
            .error-icon {
                background: linear-gradient(135deg, #f44336, #d32f2f);
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                color: white; font-size: 36px; box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3);
            }
            .status-title { font-size: 28px; font-weight: 700; margin-bottom: 12px; color: #2c3e50; }
            .status-message { font-size: 16px; color: #7f8c8d; margin-bottom: 32px; line-height: 1.5; }
            .payment-details {
                background: #f8f9fa; border-radius: 16px; padding: 24px; margin: 24px 0; text-align: left;
            }
            .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e9ecef; }
            .detail-row:last-child { border-bottom: none; }
            .detail-label { font-weight: 600; color: #495057; }
            .detail-value { color: #2c3e50; font-weight: 500; }
            .amount { font-size: 18px; font-weight: 700; color: #e74c3c; }
            .btn {
                display: inline-block; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s ease; cursor: pointer; border: none; margin: 8px; position: relative; overflow: hidden;
            }
            .btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; }
            .btn:hover::before { left: 100%; }
            .btn-primary { background: linear-gradient(135deg, #3498db, #2980b9); color: white; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
            .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4); }
            .btn-secondary { background: #ecf0f1; color: #2c3e50; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
            .btn-secondary:hover { background: #d5dbdb; transform: translateY(-2px); }
            .vnpay-logo { width: 120px; height: auto; margin-bottom: 20px; opacity: 0.8; }
            .transaction-id { background: #e3f2fd; color: #1976d2; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; display: inline-block; margin-top: 16px; }
            @media (max-width: 768px) {
                .container { padding: 24px; margin: 16px; }
                .status-title { font-size: 24px; }
                .btn { width: 100%; margin: 8px 0; }
            }
            .particles { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: -1; }
            .particle { position: absolute; background: rgba(255, 255, 255, 0.1); border-radius: 50%; animation: float 6s infinite ease-in-out; }
            @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0; } 50% { opacity: 1; } 100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; } }
        </style>
    </head>
    <body>
        <div class="particles" id="particles"></div>
        <div class="container">
            <!-- VNPay Logo -->
            <svg class="vnpay-logo" viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg">
                <rect x="0" y="0" width="200" height="50" rx="8" fill="#1976d2"/>
                <text x="100" y="32" font-family="Arial, sans-serif" font-size="20" font-weight="bold" fill="white" text-anchor="middle">VNPay</text>
            </svg>
            <?php
            require_once("./config.php");
            $vnp_SecureHash = $_GET['vnp_SecureHash'];
            $inputData = array();
            foreach ($_GET as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);
            $i = 0;
            $hashData = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }
            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $isSuccess = false;
            $statusTitle = '';
            $statusMessage = '';
            $statusIcon = '';
            $statusColor = '';
            if ($secureHash == $vnp_SecureHash && $_GET['vnp_ResponseCode'] == '00') {
                $isSuccess = true;
                $statusTitle = 'Thanh toán thành công!';
                $statusMessage = 'Giao dịch của bạn đã được xử lý thành công. Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.';
                $statusIcon = '<div class="status-icon success-icon">✓</div>';
                $statusColor = '#4CAF50';
                // Cập nhật trạng thái đơn hàng trong DB
                $orderId = isset($_GET['vnp_TxnRef']) ? $_GET['vnp_TxnRef'] : '';
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
                if (!$conn->connect_error && $orderId) {
                    $stmt = $conn->prepare("UPDATE "order" SET trangthai = 1, payment_status = 1 WHERE id = ?");
                    $stmt->bind_param("s", $orderId);
                    $stmt->execute();
                    $stmt->close();
                    $conn->close();
                }
            } else {
                $statusTitle = 'Thanh toán thất bại!';
                $statusMessage = 'Đã có lỗi xảy ra trong quá trình xử lý giao dịch. Vui lòng thử lại sau.';
                $statusIcon = '<div class="status-icon error-icon">✕</div>';
                $statusColor = '#f44336';
            }
            // Lấy thông tin hiển thị
            $amount = isset($_GET['vnp_Amount']) ? number_format($_GET['vnp_Amount']/100, 0, ',', '.') . ' VNĐ' : '';
            $orderId = isset($_GET['vnp_TxnRef']) ? $_GET['vnp_TxnRef'] : '';
            $bankCode = isset($_GET['vnp_BankCode']) ? $_GET['vnp_BankCode'] : '';
            $payDate = isset($_GET['vnp_PayDate']) ? $_GET['vnp_PayDate'] : '';
            $transId = isset($_GET['vnp_TransactionNo']) ? $_GET['vnp_TransactionNo'] : '';
            $cardType = isset($_GET['vnp_CardType']) ? $_GET['vnp_CardType'] : '';
            // Định dạng lại thời gian giao dịch
            $payDateFmt = '';
            if ($payDate && strlen($payDate) == 14) {
                $payDateFmt = substr($payDate,6,2).'/'.substr($payDate,4,2).'/'.substr($payDate,0,4).' '.substr($payDate,8,2).':'.substr($payDate,10,2).':'.substr($payDate,12,2);
            }
            ?>
            <?php echo $statusIcon; ?>
            <h1 class="status-title" style="color:<?php echo $statusColor ?>"><?php echo $statusTitle; ?></h1>
            <p class="status-message"><?php echo $statusMessage; ?></p>
            <div class="payment-details">
                <div class="detail-row">
                    <span class="detail-label">Số tiền:</span>
                    <span class="detail-value amount"><?php echo $amount; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phương thức:</span>
                    <span class="detail-value"><?php echo $cardType ? $cardType : $bankCode; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Thời gian:</span>
                    <span class="detail-value"><?php echo $payDateFmt; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Trạng thái:</span>
                    <span class="detail-value" style="color:<?php echo $statusColor ?>; font-weight:700;"><?php echo $isSuccess ? 'Thành công' : 'Thất bại'; ?></span>
                </div>
            </div>
            <div class="transaction-id">
                Mã GD: #<?php echo $transId ? $transId : $orderId; ?>
            </div>
            <div style="margin-top: 32px;">
                <button class="btn btn-primary" onclick="window.location.href='http://localhost/Bookstore_DATN/'">Tiếp tục mua sắm</button>
                <button class="btn btn-secondary" onclick="window.print()">In hóa đơn</button>
            </div>
        </div>
        <script>
            // Create floating particles
            function createParticles() {
                const particlesContainer = document.getElementById('particles');
                for (let i = 0; i < 20; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.width = Math.random() * 6 + 4 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.animationDelay = Math.random() * 6 + 's';
                    particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                    particlesContainer.appendChild(particle);
                }
            }
            document.addEventListener('DOMContentLoaded', function() {
                createParticles();
            });
        </script>
    </body>
</html>
