<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    // Lấy tất cả chi tiết đơn hàng từ database
    $sql = "SELECT * FROM orderdetails";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception('Lỗi truy vấn: ' . mysqli_error($conn));
    }

    $orderDetails = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orderDetails[] = array(
            'id' => $row['id'], // ID của orderdetail
            'product_id' => $row['product_id'], // ID của sản phẩm
            'madon' => $row['madon'],
            'price' => (int)$row['product_price'],
            'quantity' => (int)$row['soluong'], // Đổi tên từ soluong thành quantity
            'note' => isset($row['note']) ? $row['note'] : ''
        );
    }

    echo json_encode($orderDetails);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'message' => $e->getMessage()
    ));
}

mysqli_close($conn);
?> 