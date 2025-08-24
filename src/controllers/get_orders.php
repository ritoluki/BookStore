<?php
header('Content-Type: application/json');

// Kết nối database
require_once '../../config/config.php';

// Lấy tất cả đơn hàng từ database
$sql = "SELECT * FROM \"order\" ORDER BY thoigiandat DESC";
$result = db_query($conn, $sql);

$orders = array();
if ($result && db_num_rows($result) > 0) {
    while ($row = db_fetch_assoc($result)) {
        // Check if columns exist in the result
        $payment_method = isset($row['payment_method']) ? $row['payment_method'] : null;
        $payment_status = isset($row['payment_status']) ? (int)$row['payment_status'] : 0;
        
        $orders[] = array(
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
            'payment_method' => $payment_method,
            'payment_status' => $payment_status
        );
    }
}

echo json_encode($orders);

db_close($conn);
?>
