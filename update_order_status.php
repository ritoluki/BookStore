<?php
header('Content-Type: application/json');

require_once 'config.php';

try {
    // Nhận dữ liệu từ request
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['orderId']) || !isset($data['status'])) {
        throw new Exception('Missing required parameters');
    }
    
    $orderId = $data['orderId'];
    $status = $data['status'];
    
    // Lấy thông tin đơn hàng trước khi cập nhật
    $sql_get_order = "SELECT * FROM `order` WHERE id = ?";
    $stmt_get_order = $conn->prepare($sql_get_order);
    $stmt_get_order->bind_param('s', $orderId);
    $stmt_get_order->execute();
    $result = $stmt_get_order->get_result();
    $order = $result->fetch_assoc();
    $stmt_get_order->close();
    
    if (!$order) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    // Cập nhật trạng thái đơn hàng
    $sql = "UPDATE `order` SET trangthai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param('is', $status, $orderId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update order status: ' . $stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        // Gửi email thông báo khi trạng thái đơn hàng thay đổi
        // Lấy thông tin email của khách hàng
        $sql_user = "SELECT email FROM users WHERE id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $user_email = "";
        
        if ($stmt_user) {
            $stmt_user->bind_param("s", $order['khachhang']);
            $stmt_user->execute();
            $result_user = $stmt_user->get_result();
            
            if ($row_user = $result_user->fetch_assoc()) {
                $user_email = $row_user['email'];
            }
            
            $stmt_user->close();
        }
        
        if (!empty($user_email)) {
            require_once 'order_mail_helper.php';
            sendOrderStatusUpdateEmail($order, $status, $user_email);
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No orders were updated']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
