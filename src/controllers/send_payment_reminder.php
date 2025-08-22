<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Lấy dữ liệu từ request
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['order']) || !isset($data['orderDetails'])) {
        throw new Exception('Dữ liệu không hợp lệ');
    }
    
    $order = $data['order'];
    $orderDetails = $data['orderDetails'];
    
    // Kiểm tra xem đơn hàng có tồn tại không
    $sql = "SELECT o.*, u.email, u.fullname 
            FROM `order` o 
            JOIN users u ON o.khachhang = u.id 
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    $orderInfo = $result->fetch_assoc();
    $stmt->close();
    
    // Kiểm tra xem đơn hàng đã thanh toán chưa
    if ($orderInfo['payment_status'] == 1) {
        throw new Exception('Đơn hàng đã được thanh toán');
    }
    
    // Kiểm tra xem đơn hàng có bị hủy không
    if ($orderInfo['trangthai'] == 4) {
        throw new Exception('Không thể gửi nhắc nhở cho đơn hàng đã hủy');
    }
    
    // Kiểm tra xem có phải đơn hàng COD không (không gửi nhắc nhở cho COD)
    if (isset($orderInfo['hinhthucgiao']) && stripos($orderInfo['hinhthucgiao'], 'cod') !== false) {
        throw new Exception('Không thể gửi nhắc nhở thanh toán cho đơn hàng COD');
    }
    
    // Lấy email của khách hàng
    $userEmail = $orderInfo['email'];
    if (empty($userEmail)) {
        throw new Exception('Không tìm thấy email của khách hàng');
    }
    
    // Chuẩn bị dữ liệu orderDetails cho email
    $emailOrderDetails = [];
    
    // Debug logging
    error_log("DEBUG: Processing " . count($orderDetails) . " order details");
    error_log("DEBUG: First order detail: " . json_encode($orderDetails[0] ?? 'empty'));
    
    foreach ($orderDetails as $detail) {
        // Debug logging
        error_log("DEBUG: Processing detail: " . json_encode($detail));
        
        // Lấy thông tin sản phẩm
        $sqlProduct = "SELECT title, img, category FROM products WHERE id = ?";
        $stmtProduct = $conn->prepare($sqlProduct);
        $stmtProduct->bind_param("i", $detail['product_id']);
        $stmtProduct->execute();
        $resultProduct = $stmtProduct->get_result();
        
        if ($resultProduct->num_rows > 0) {
            $product = $resultProduct->fetch_assoc();
            $emailProductData = [
                'product_id' => $detail['product_id'],
                'title' => $product['title'],
                'img' => $product['img'],
                'category' => $product['category'],
                'price' => $detail['price'],
                'quantity' => $detail['quantity']
            ];
            
            // Debug logging
            error_log("DEBUG: Product found: " . json_encode($emailProductData));
            
            $emailOrderDetails[] = $emailProductData;
        } else {
            error_log("DEBUG: Product not found for ID: " . $detail['product_id']);
        }
        $stmtProduct->close();
    }
    
    // Debug logging
    error_log("DEBUG: Final emailOrderDetails: " . json_encode($emailOrderDetails));
    
    // Gửi email nhắc nhở thanh toán
    require_once '../services/order_mail_helper.php';
    $emailResult = sendPaymentReminderEmail($orderInfo, $emailOrderDetails, $userEmail, $conn);
    
    if ($emailResult) {
        // Ghi log việc gửi email
        $logSql = "INSERT INTO email_logs (order_id, email_type, recipient_email, sent_at, status) 
                   VALUES (?, 'payment_reminder', ?, NOW(), 'success')";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param("ss", $order['id'], $userEmail);
        $logStmt->execute();
        $logStmt->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã gửi email nhắc nhở thanh toán thành công',
            'order_id' => $order['id'],
            'recipient_email' => $userEmail
        ]);
    } else {
        throw new Exception('Không thể gửi email nhắc nhở thanh toán');
    }
    
} catch (Exception $e) {
    error_log("Lỗi gửi email nhắc nhở thanh toán: " . $e->getMessage());
    
    // Ghi log lỗi nếu có
    if (isset($order['id']) && isset($userEmail)) {
        $logSql = "INSERT INTO email_logs (order_id, email_type, recipient_email, sent_at, status, error_message) 
                   VALUES (?, 'payment_reminder', ?, NOW(), 'failed', ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param("sss", $order['id'], $userEmail, $e->getMessage());
        $logStmt->execute();
        $logStmt->close();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
