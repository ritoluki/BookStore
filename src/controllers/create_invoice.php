<?php
// Tắt hiển thị lỗi để không làm hỏng JSON response
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Kết nối database
require_once '../../config/config.php';

try {
    // Kiểm tra kết nối database
    if (!isset($conn) || !$conn) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi kết nối database'));
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $orderId = isset($data['order_id']) ? mysqli_real_escape_string($conn, $data['order_id']) : '';
    
    if (empty($orderId)) {
        echo json_encode(array('success' => false, 'message' => 'Thiếu mã đơn hàng'));
        exit;
    }
    
    // Kiểm tra đơn hàng đã có hóa đơn chưa
    $sql = "SELECT id FROM invoices WHERE order_id = '$orderId'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(array('success' => false, 'message' => 'Đơn hàng đã có hóa đơn'));
        mysqli_close($conn);
        exit;
    }
    
    // Lấy thông tin đơn hàng
    $sql = "SELECT * FROM `order` WHERE id = '$orderId'";
    $result = mysqli_query($conn, $sql);
    $order = mysqli_fetch_assoc($result);
    
    if (!$order) {
        echo json_encode(array('success' => false, 'message' => 'Không tìm thấy đơn hàng'));
        mysqli_close($conn);
        exit;
    }
    
    // Tạo số hóa đơn: HD-YYYYMMDD-XXX
    $date = date('Ymd');
    $sql = "SELECT COUNT(*) as count FROM invoices WHERE DATE(invoice_date) = CURDATE()";
    $result = mysqli_query($conn, $sql);
    $count_row = mysqli_fetch_assoc($result);
    $count = (int)$count_row['count'] + 1;
    $invoiceNumber = "HD-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    
    // Lấy thông tin từ request
    $createdBy = isset($data['created_by']) ? mysqli_real_escape_string($conn, $data['created_by']) : 'admin';
    
    // Chuẩn bị dữ liệu
    $customerName = mysqli_real_escape_string($conn, $order['tenguoinhan']);
    $customerPhone = mysqli_real_escape_string($conn, $order['sdtnhan']);
    $customerEmail = isset($order['email']) ? mysqli_real_escape_string($conn, $order['email']) : '';
    $customerAddress = mysqli_real_escape_string($conn, $order['diachinhan']);
    $totalAmount = (float)$order['tongtien'];
    $paymentMethod = isset($order['payment_method']) ? mysqli_real_escape_string($conn, $order['payment_method']) : '';
    
    // Tạo hóa đơn
    $sql = "INSERT INTO invoices (
                invoice_number, order_id, customer_name, customer_phone, 
                customer_email, customer_address, total_amount, payment_method, 
                created_by, status
            ) VALUES (
                '$invoiceNumber', '$orderId', '$customerName', '$customerPhone',
                '$customerEmail', '$customerAddress', $totalAmount, '$paymentMethod',
                '$createdBy', 'issued'
            )";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(array(
            'success' => true, 
            'message' => 'Tạo hóa đơn thành công',
            'invoice_number' => $invoiceNumber
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Lỗi tạo hóa đơn: ' . mysqli_error($conn)));
    }
    
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}

mysqli_close($conn);
?>

