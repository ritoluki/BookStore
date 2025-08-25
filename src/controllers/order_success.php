<?php
session_start();
require_once '../../config/config.php';

// Kiểm tra xem có order_id trong URL không (từ COD) hoặc vnp_TxnRef (từ VNPay)
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$vnpay_order_id = isset($_GET['vnp_TxnRef']) ? $_GET['vnp_TxnRef'] : '';

// Ưu tiên order_id từ VNPay nếu có
if ($vnpay_order_id) {
    $order_id = $vnpay_order_id;
}

$order = null;
$orderDetails = [];
$payment_status = 'pending';
$payment_method = 'COD';
$vnpay_data = null;

// Xử lý thông tin thanh toán VNPay nếu có
if (isset($_GET['vnp_ResponseCode']) && isset($_GET['vnp_SecureHash'])) {
    $vnp_ResponseCode = $_GET['vnp_ResponseCode'];
    $vnp_SecureHash = $_GET['vnp_SecureHash'];
    
    // Xác thực hash từ VNPay
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
    
    // Sử dụng hash secret từ config VNPay
    $vnp_HashSecret = "L2DBGVM47JV0DBS2OCQB756IHSQVYK3R";
    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    
    if ($secureHash == $vnp_SecureHash && $vnp_ResponseCode == '00') {
        $payment_status = 'success';
        $payment_method = 'VNPay';
        
        // Thu thập thông tin VNPay để hiển thị
        $vnpay_data = array(
            'amount' => isset($_GET['vnp_Amount']) ? number_format($_GET['vnp_Amount']/100, 0, ',', '.') . ' VNĐ' : '',
            'bankCode' => isset($_GET['vnp_BankCode']) ? $_GET['vnp_BankCode'] : '',
            'payDate' => isset($_GET['vnp_PayDate']) ? $_GET['vnp_PayDate'] : '',
            'transId' => isset($_GET['vnp_TransactionNo']) ? $_GET['vnp_TransactionNo'] : '',
            'cardType' => isset($_GET['vnp_CardType']) ? $_GET['vnp_CardType'] : ''
        );
        
        // Cập nhật trạng thái đơn hàng trong DB
        if ($order_id) {
            $stmt = $conn->prepare("UPDATE `order` SET trangthai = 1, payment_status = 1, payment_method = 'VNPay' WHERE id = ?");
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        $payment_status = 'failed';
        $payment_method = 'VNPay';
        
        // Cập nhật trạng thái đơn hàng thất bại
        if ($order_id) {
            $stmt = $conn->prepare("UPDATE `order` SET payment_status = 2, payment_method = 'VNPay' WHERE id = ?");
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if ($order_id) {
    // Lấy thông tin đơn hàng
    $sql = "SELECT o.*, u.fullname, u.phone, u.address, u.email 
            FROM `order` o 
            JOIN users u ON o.khachhang = u.id 
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order) {
        // Lấy chi tiết đơn hàng
        $sqlDetails = "SELECT od.*, p.title, p.img, p.category 
                       FROM orderdetails od 
                       JOIN products p ON od.product_id = p.id 
                       WHERE od.madon = ?";
        $stmtDetails = $conn->prepare($sqlDetails);
        $stmtDetails->bind_param("s", $order_id);
        $stmtDetails->execute();
        $resultDetails = $stmtDetails->get_result();
        while ($row = $resultDetails->fetch_assoc()) {
            $orderDetails[] = $row;
        }
        $stmtDetails->close();
    }
}

// Nếu không có order hoặc order_id, redirect về trang chủ
if (!$order) {
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - BOOK SHOP</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .success-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .success-header {
            text-align: center;
            margin-bottom: 50px;
            padding: 40px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 20px;
            color: white;
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .success-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .success-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .order-info {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .order-info h3 {
            color: var(--text-color);
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--red);
            padding-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--red);
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: var(--red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        
        .info-content h4 {
            margin: 0 0 5px 0;
            color: var(--text-color);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-content p {
            margin: 0;
            color: #666;
            font-size: 1rem;
        }
        
        .order-details {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .order-details h3 {
            color: var(--text-color);
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--red);
            padding-bottom: 10px;
        }
        
        .product-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .product-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }
        
        .product-info {
            flex: 1;
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 5px;
        }
        
        .product-category {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        
        .product-price {
            color: var(--red);
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .product-quantity {
            background: var(--red);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .order-summary {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .order-summary h3 {
            color: var(--text-color);
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--red);
            padding-bottom: 10px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .summary-item:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--red);
        }
        
        .summary-label {
            color: #666;
        }
        
        .summary-value {
            color: var(--text-color);
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: var(--red);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-shipping {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .success-container {
                margin: 20px auto;
                padding: 0 15px;
            }
            
            .success-header {
                padding: 30px 20px;
                margin-bottom: 30px;
            }
            
            .success-title {
                font-size: 2rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .product-item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <!-- Header thành công -->
        <div class="success-header" style="background: <?php echo $payment_status == 'success' ? 'linear-gradient(135deg, #28a745 0%, #20c997 100%)' : ($payment_status == 'failed' ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' : 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)'); ?>">
            <div class="success-icon">
                <i class="fas fa-<?php echo $payment_status == 'success' ? 'check-circle' : ($payment_status == 'failed' ? 'times-circle' : 'clock'); ?>"></i>
            </div>
            <h1 class="success-title">
                <?php 
                if ($payment_status == 'success') {
                    echo 'Đặt hàng thành công!';
                } elseif ($payment_status == 'failed') {
                    echo 'Thanh toán thất bại!';
                } else {
                    echo 'Đặt hàng thành công!';
                }
                ?>
            </h1>
            <p class="success-subtitle">
                <?php 
                if ($payment_status == 'success') {
                    if ($payment_method == 'VNPay') {
                        echo 'Thanh toán VNPay thành công! Cảm ơn bạn đã mua sách tại BOOK SHOP. Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.';
                    } else {
                        echo 'Cảm ơn bạn đã mua sách tại BOOK SHOP. Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.';
                    }
                } elseif ($payment_status == 'failed') {
                    echo 'Đã có lỗi xảy ra trong quá trình thanh toán VNPay. Vui lòng thử lại sau hoặc liên hệ với chúng tôi để được hỗ trợ.';
                } else {
                    echo 'Cảm ơn bạn đã mua sách tại BOOK SHOP. Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.';
                }
                ?>
            </p>
        </div>

        <!-- Thông tin đơn hàng -->
        <div class="order-info">
            <h3><i class="fas fa-receipt"></i> Thông tin đơn hàng</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div class="info-content">
                        <h4>Mã đơn hàng</h4>
                        <p><?php echo htmlspecialchars($order['id']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="info-content">
                        <h4>Ngày đặt</h4>
                        <p><?php echo isset($order['ngaydat']) && $order['ngaydat'] ? date('d/m/Y H:i', strtotime($order['ngaydat'])) : 'Chưa xác định'; ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-content">
                        <h4>Khách hàng</h4>
                        <p><?php echo htmlspecialchars($order['fullname']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h4>Số điện thoại</h4>
                        <p><?php echo htmlspecialchars($order['phone']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Địa chỉ giao hàng</h4>
                        <p><?php echo htmlspecialchars($order['address']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="info-content">
                        <h4>Trạng thái</h4>
                        <span class="status-badge status-<?php echo strtolower($order['trangthai'] == 0 ? 'pending' : ($order['trangthai'] == 1 ? 'processing' : ($order['trangthai'] == 2 ? 'shipping' : ($order['trangthai'] == 3 ? 'completed' : 'cancelled')))); ?>">
                            <?php 
                            switch($order['trangthai']) {
                                case 0: echo 'Chưa xử lý'; break;
                                case 1: echo 'Đã xác nhận'; break;
                                case 2: echo 'Đang giao hàng'; break;
                                case 3: echo 'Hoàn thành'; break;
                                case 4: echo 'Đã hủy'; break;
                                default: echo 'Chưa xử lý';
                            }
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="info-content">
                        <h4>Phương thức thanh toán</h4>
                        <p><?php echo htmlspecialchars($payment_method); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin thanh toán VNPay (nếu có) -->
        <?php if ($vnpay_data && $payment_method == 'VNPay'): ?>
        <div class="order-info" style="margin-bottom: 30px;">
            <h3><i class="fas fa-credit-card"></i> Thông tin thanh toán VNPay</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="info-content">
                        <h4>Số tiền thanh toán</h4>
                        <p><?php echo htmlspecialchars($vnpay_data['amount']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="info-content">
                        <h4>Ngân hàng</h4>
                        <p><?php echo htmlspecialchars($vnpay_data['bankCode']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="info-content">
                        <h4>Thời gian thanh toán</h4>
                        <p>
                            <?php 
                            if ($vnpay_data['payDate'] && strlen($vnpay_data['payDate']) == 14) {
                                echo substr($vnpay_data['payDate'],6,2).'/'.substr($vnpay_data['payDate'],4,2).'/'.substr($vnpay_data['payDate'],0,4).' '.substr($vnpay_data['payDate'],8,2).':'.substr($vnpay_data['payDate'],10,2).':'.substr($vnpay_data['payDate'],12,2);
                            } else {
                                echo 'Chưa xác định';
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Mã giao dịch</h4>
                        <p><?php echo htmlspecialchars($vnpay_data['transId']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="info-content">
                        <h4>Loại thẻ</h4>
                        <p><?php echo htmlspecialchars($vnpay_data['cardType']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="info-content">
                        <h4>Trạng thái thanh toán</h4>
                        <span class="status-badge status-<?php echo $payment_status == 'success' ? 'completed' : 'cancelled'; ?>">
                            <?php echo $payment_status == 'success' ? 'Thành công' : 'Thất bại'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Chi tiết sản phẩm -->
        <div class="order-details">
            <h3><i class="fas fa-box"></i> Chi tiết sản phẩm</h3>
            <?php foreach ($orderDetails as $item): ?>
            <div class="product-item">
                                 <img src="../../<?php echo htmlspecialchars($item['img']); ?>" 
                      alt="<?php echo htmlspecialchars($item['title']); ?>" 
                      class="product-image">
                <div class="product-info">
                    <h4 class="product-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                    <p class="product-category"><?php echo htmlspecialchars($item['category']); ?></p>
                    <p class="product-price"><?php echo number_format($item['product_price'], 0, ',', '.'); ?> ₫</p>
                </div>
                <div class="product-quantity">
                    x<?php echo $item['soluong']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Tóm tắt đơn hàng -->
        <div class="order-summary">
            <h3><i class="fas fa-calculator"></i> Tóm tắt đơn hàng</h3>
            <div class="summary-item">
                <span class="summary-label">Tổng tiền sản phẩm:</span>
                <span class="summary-value"><?php echo number_format($order['tongtien'], 0, ',', '.'); ?> ₫</span>
            </div>
            <?php 
            $giamgia = isset($order['giamgia']) ? (float)$order['giamgia'] : 0;
            $phigiaohang = isset($order['phigiaohang']) ? (float)$order['phigiaohang'] : 0;
            ?>
            <?php if ($giamgia > 0): ?>
            <div class="summary-item">
                <span class="summary-label">Giảm giá:</span>
                <span class="summary-value">-<?php echo number_format($giamgia, 0, ',', '.'); ?> ₫</span>
            </div>
            <?php endif; ?>
            <div class="summary-item">
                <span class="summary-label">Phí giao hàng:</span>
                <span class="summary-value"><?php echo number_format($phigiaohang, 0, ',', '.'); ?> ₫</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Tổng cộng:</span>
                <span class="summary-value"><?php echo number_format($order['tongtien'] - $giamgia + $phigiaohang, 0, ',', '.'); ?> ₫</span>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="action-buttons">
            <a href="../../index.php" class="btn-primary">
                <i class="fas fa-home"></i>
                Về trang chủ
            </a>
            
            <?php if ($payment_status == 'failed'): ?>
            <a href="../../index.php?page=checkout" class="btn-secondary" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <i class="fas fa-redo"></i>
                Thử lại thanh toán
            </a>
            <?php else: ?>
            <a href="../../index.php?page=checkorder" class="btn-secondary">
                <i class="fas fa-search"></i>
                Tra cứu đơn hàng
            </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Thêm hiệu ứng scroll mượt
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll to top khi load trang
            window.scrollTo(0, 0);
            
            // Thêm hiệu ứng fade in cho các element
            const elements = document.querySelectorAll('.order-info, .order-details, .order-summary');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
        });
    </script>
</body>
</html>
