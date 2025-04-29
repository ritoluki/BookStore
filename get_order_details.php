<?php
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order ID']);
    exit;
}

$order_id = $_GET['order_id'];

try {
    // Lấy chi tiết đơn hàng từ bảng orderdetails (correct table name)
    $sql = "SELECT * FROM orderdetails WHERE madon = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orderDetails = [];
    while ($row = $result->fetch_assoc()) {
        $orderDetails[] = [
            'product_id' => $row['product_id'],
            'quantity' => $row['soluong'],
            'price' => $row['product_price'],
            'note' => $row['note'] ?? ''
        ];
    }
    
    echo json_encode([
        'success' => true,
        'orderDetails' => $orderDetails
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
