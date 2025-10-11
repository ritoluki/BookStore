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
    $method = isset($_GET['method']) ? $_GET['method'] : 'all';
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $dateStart = isset($_GET['date_start']) ? $_GET['date_start'] : '';
    $dateEnd = isset($_GET['date_end']) ? $_GET['date_end'] : '';
    
    $sql = "SELECT * FROM `order` WHERE 1=1";
    
    if ($method !== 'all') {
        $method = mysqli_real_escape_string($conn, $method);
        $sql .= " AND payment_method = '$method'";
    }
    
    if ($status !== 'all') {
        $status = mysqli_real_escape_string($conn, $status);
        $sql .= " AND payment_status = '$status'";
    }
    
    if ($search) {
        $sql .= " AND (id LIKE '%$search%' OR khachhang LIKE '%$search%')";
    }
    
    if ($dateStart) {
        $sql .= " AND DATE(thoigiandat) >= '$dateStart'";
    }
    
    if ($dateEnd) {
        $sql .= " AND DATE(thoigiandat) <= '$dateEnd'";
    }
    
    $sql .= " ORDER BY thoigiandat DESC";
    
    $result = mysqli_query($conn, $sql);
    
    $transactions = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = array(
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
            'tongtien' => (int)$row['tongtien'],
            'trangthai' => (int)$row['trangthai'],
            'payment_method' => isset($row['payment_method']) ? $row['payment_method'] : null,
            'payment_status' => isset($row['payment_status']) ? (int)$row['payment_status'] : 0
        );
    }
    
    echo json_encode(array('success' => true, 'transactions' => $transactions));
    
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}

mysqli_close($conn);
?>

