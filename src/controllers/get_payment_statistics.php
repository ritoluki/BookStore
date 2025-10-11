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
    // Tổng doanh thu (đã thanh toán và không bị hủy)
    $sql = "SELECT SUM(tongtien) as total_revenue 
            FROM `order` 
            WHERE payment_status = 1 AND trangthai != 4";
    $result = mysqli_query($conn, $sql);
    $revenue = mysqli_fetch_assoc($result);
    
    // Đếm theo phương thức thanh toán
    $sql = "SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(tongtien) as total
            FROM `order` 
            WHERE payment_status = 1 AND trangthai != 4
            GROUP BY payment_method";
    $result = mysqli_query($conn, $sql);
    
    $methods = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $methods[] = array(
            'payment_method' => $row['payment_method'],
            'count' => (int)$row['count'],
            'total' => (float)$row['total']
        );
    }
    
    // Đơn chờ thanh toán (chưa thanh toán và không phải hoàn thành/hủy)
    $sql = "SELECT COUNT(*) as pending_count 
            FROM `order` 
            WHERE payment_status = 0 AND trangthai NOT IN (3, 4)";
    $result = mysqli_query($conn, $sql);
    $pending = mysqli_fetch_assoc($result);
    
    echo json_encode(array(
        'success' => true,
        'statistics' => array(
            'total_revenue' => $revenue['total_revenue'] ? (float)$revenue['total_revenue'] : 0,
            'payment_methods' => $methods,
            'pending_count' => (int)$pending['pending_count']
        )
    ));
    
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}

mysqli_close($conn);
?>

