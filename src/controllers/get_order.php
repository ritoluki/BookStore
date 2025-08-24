<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order ID']);
    exit;
}

$order_id = $_GET['order_id'];

try {
    // Lấy thông tin đơn hàng từ bảng order
    $sql = "SELECT * FROM "order" WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Check if payment_status exists in the row, default to 0 if not
        $paymentStatus = isset($row['payment_status']) ? (int)$row['payment_status'] : 0;
        
        echo json_encode([
            'success' => true,
            'order' => [
                'id' => $row['id'],
                'khachhang' => $row['khachhang'],
                'hinhthucgiao' => $row['hinhthucgiao'],
                'ngaygiaohang' => $row['ngaygiaohang'],
                'thoigiangiao' => $row['thoigiangiao'],
                'ghichu' => $row['ghichu'],
                'tenguoinhan' => $row['tenguoinhan'],
                'sdtnhan' => $row['sdtnhan'],
                'diachinhan' => $row['diachinhan'],
                'thoigiandat' => $row['thoigiandat'],
                'tongtien' => $row['tongtien'],
                'trangthai' => $row['trangthai'],
                'payment_status' => $paymentStatus,
                'payment_method' => $row['payment_method'] ?? null
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 