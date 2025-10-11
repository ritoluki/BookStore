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
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    
    $sql = "SELECT * FROM invoices WHERE 1=1";
    
    if ($search) {
        $sql .= " AND (invoice_number LIKE '%$search%' OR order_id LIKE '%$search%' OR customer_name LIKE '%$search%')";
    }
    
    $sql .= " ORDER BY invoice_date DESC";
    
    $result = mysqli_query($conn, $sql);
    
    $invoices = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $invoices[] = array(
            'id' => (int)$row['id'],
            'invoice_number' => $row['invoice_number'],
            'order_id' => $row['order_id'],
            'customer_name' => $row['customer_name'],
            'customer_phone' => $row['customer_phone'],
            'customer_email' => $row['customer_email'],
            'customer_address' => $row['customer_address'],
            'total_amount' => (float)$row['total_amount'],
            'payment_method' => $row['payment_method'],
            'invoice_date' => $row['invoice_date'],
            'created_by' => $row['created_by'],
            'status' => $row['status'],
            'notes' => $row['notes']
        );
    }
    
    echo json_encode(array('success' => true, 'invoices' => $invoices));
    
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}

mysqli_close($conn);
?>

