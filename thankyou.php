<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cảm ơn bạn đã đặt hàng!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            background: #f7fafc; 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
        }
        .thankyou-container {
            max-width: 480px;
            margin: 60px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.09);
            padding: 40px 30px 32px 30px;
            text-align: center;
        }
        .thankyou-icon {
            font-size: 64px;
            color: #2ecc71;
            margin-bottom: 16px;
        }
        .thankyou-title {
            font-size: 2rem;
            color: #222;
            margin-bottom: 12px;
        }
        .thankyou-message {
            font-size: 1.1rem;
            color: #444;
            margin-bottom: 18px;
        }
        .thankyou-note {
            font-size: 0.98rem;
            color: #888;
            margin-bottom: 24px;
        }
        .thankyou-btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 12px 32px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .thankyou-btn:hover {
            background: #0056b3;
        }
        @media (max-width: 600px) {
            .thankyou-container { padding: 24px 8px; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>
    <div class="thankyou-container">
        <div class="thankyou-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="thankyou-title">Cảm ơn bạn đã đặt hàng!</div>
        <div class="thankyou-message">
            Đơn hàng của bạn đã được ghi nhận và đang chờ xác nhận từ cửa hàng.<br>
            Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận và giao hàng.
        </div>
        <div class="thankyou-note">
            <?php if (isset($_GET['email'])): ?>
                Vui lòng kiểm tra email <b><?= htmlspecialchars($_GET['email']) ?></b> để xem chi tiết đơn hàng.<br>
            <?php else: ?>
                Nếu bạn có cung cấp email, vui lòng kiểm tra hộp thư để xem chi tiết đơn hàng.
            <?php endif; ?>
            <br>Bạn có thể tra cứu trạng thái đơn hàng tại trang chủ.
        </div>
        <a href="index.php" class="thankyou-btn"><i class="fa-solid fa-house"></i> Về trang chủ</a>
    </div>
</body>
</html>
