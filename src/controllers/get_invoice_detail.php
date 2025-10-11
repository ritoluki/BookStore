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
    
    $invoiceId = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
    
    if (empty($invoiceId)) {
        echo json_encode(array('success' => false, 'message' => 'Thiếu ID hóa đơn'));
        mysqli_close($conn);
        exit;
    }
    
    // Lấy thông tin hóa đơn (có thể tìm bằng ID hoặc invoice_number)
    $sql = "SELECT * FROM invoices WHERE id = '$invoiceId' OR invoice_number = '$invoiceId'";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi query database: ' . mysqli_error($conn)));
        mysqli_close($conn);
        exit;
    }
    
    $invoice = mysqli_fetch_assoc($result);
    
    if (!$invoice) {
        echo json_encode(array('success' => false, 'message' => 'Không tìm thấy hóa đơn'));
        mysqli_close($conn);
        exit;
    }
    
    // Lấy chi tiết đơn hàng
    $orderId = mysqli_real_escape_string($conn, $invoice['order_id']);
    $sql = "SELECT od.*, p.title, p.img 
            FROM orderdetails od
            JOIN products p ON od.product_id = p.id
            WHERE od.madon = '$orderId'";
    $result = mysqli_query($conn, $sql);
    
    $orderDetails = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orderDetails[] = array(
            'id' => (int)$row['id'],
            'madon' => $row['madon'],
            'product_id' => (int)$row['product_id'],
            'price' => (float)$row['product_price'],  // Sửa từ 'price' thành 'product_price'
            'quantity' => (int)$row['soluong'],  // Sửa từ 'quantity' thành 'soluong'
            'note' => isset($row['note']) ? $row['note'] : '',
            'title' => $row['title'],
            'img' => $row['img']
        );
    }
    
    // Format invoice data
    $invoiceData = array(
        'id' => (int)$invoice['id'],
        'invoice_number' => $invoice['invoice_number'],
        'order_id' => $invoice['order_id'],
        'customer_name' => $invoice['customer_name'],
        'customer_phone' => $invoice['customer_phone'],
        'customer_email' => $invoice['customer_email'],
        'customer_address' => $invoice['customer_address'],
        'total_amount' => (float)$invoice['total_amount'],
        'payment_method' => $invoice['payment_method'],
        'invoice_date' => $invoice['invoice_date'],
        'created_by' => $invoice['created_by'],
        'status' => $invoice['status'],
        'notes' => $invoice['notes']
    );
    
    echo json_encode(array(
        'success' => true,
        'invoice' => $invoiceData,
        'order_details' => $orderDetails
    ));
    
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}

mysqli_close($conn);
?>

