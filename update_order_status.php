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
    
    // Lấy thông tin đơn hàng trước khi cập nhật, JOIN với users để lấy email (JOIN qua id)
    $sql_get_order = "SELECT o.*, u.email FROM `order` o JOIN users u ON o.khachhang = u.id WHERE o.id = ?";
    $stmt_get_order = $conn->prepare($sql_get_order);
    $stmt_get_order->bind_param('s', $orderId);
    $stmt_get_order->execute();
    $result = $stmt_get_order->get_result();
    $order = $result->fetch_assoc();
    $stmt_get_order->close();
    
    if (!$order) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    // Kiểm tra luồng chuyển trạng thái hợp lệ
    $currentStatus = (int)$order['trangthai'];
    $allowedTransitions = [
        0 => [1, 4], // Chưa xử lý -> Đã xử lý hoặc Đã hủy
        1 => [2],    // Đã xử lý -> Đang giao hàng
        2 => [3],    // Đang giao hàng -> Hoàn thành
        // 3, 4: không chuyển được nữa
    ];
    if (!isset($allowedTransitions[$currentStatus]) || !in_array((int)$status, $allowedTransitions[$currentStatus])) {
        throw new Exception('Chuyển trạng thái không hợp lệ!');
    }
    
    // Trước khi cho phép chuyển trạng thái, kiểm tra nếu là online và chưa thanh toán thì chặn và gửi mail nhắc nhở
    if (in_array((int)$status, [2,3]) && ($order['payment_method'] == 'online' || $order['payment_method'] == 1) && $order['payment_status'] != 1) {
        // Gửi mail nhắc nhở thanh toán
        require_once 'send_mail.php';
        sendPaymentReminderMail($order);
        throw new Exception('Khách hàng chưa thanh toán online. Đã gửi mail nhắc nhở!');
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
        // Truy vấn lại đơn hàng để lấy trạng thái mới nhất
        $sql_get_order = "SELECT * FROM `order` WHERE id = ?";
        $stmt_get_order = $conn->prepare($sql_get_order);
        $stmt_get_order->bind_param('s', $orderId);
        $stmt_get_order->execute();
        $result = $stmt_get_order->get_result();
        $order = $result->fetch_assoc();
        $stmt_get_order->close();

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
            if ($order['trangthai'] == 1) {
                sendOrderConfirmationEmail($order, [], $user_email, $conn);
            } elseif ($order['trangthai'] == 3) {
                sendOrderStatusUpdateEmail($order, $user_email);
            } elseif ($order['trangthai'] == 4) {
                sendOrderCancellationEmail($order, $user_email);
            }
            // Không gửi mail cho trạng thái 2
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
