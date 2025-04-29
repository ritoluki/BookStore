<?php
require_once 'config.php';

// Lấy dữ liệu từ yêu cầu POST
$order = json_decode($_POST['order'], true);
$orderDetails = json_decode($_POST['orderDetails'], true);

// Đảm bảo thoigiandat có định dạng chuẩn cho MySQL
$thoigiandat = date('Y-m-d H:i:s', strtotime($order['thoigiandat']));

// Lấy thông tin email của khách hàng
$user_email = "";
$sql_user = "SELECT email FROM users WHERE id = '{$order['khachhang']}'";
$result = $conn->query($sql_user);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_email = $row['email'];
}

// Chuẩn bị câu lệnh SQL để thêm đơn hàng vào bảng 'order'
$sqlOrder = "INSERT INTO `order` (id, khachhang, hinhthucgiao, ngaygiaohang, thoigiangiao, ghichu, tenguoinhan, sdtnhan, diachinhan, thoigiandat, tongtien, trangthai) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bind_param("ssssssssssii", $order['id'], $order['khachhang'], $order['hinhthucgiao'], $order['ngaygiaohang'], $order['thoigiangiao'], $order['ghichu'], $order['tenguoinhan'], $order['sdtnhan'], $order['diachinhan'], $thoigiandat, $order['tongtien'], $order['trangthai']);

// Thực thi câu lệnh SQL để thêm đơn hàng
if ($stmtOrder->execute()) {
    // Chuẩn bị câu lệnh SQL để thêm chi tiết đơn hàng vào bảng 'orderDetails'
    $sqlOrderDetails = "INSERT INTO orderDetails (madon, product_id, note, product_price, soluong) VALUES (?, ?, ?, ?, ?)";
    $stmtOrderDetails = $conn->prepare($sqlOrderDetails);
    
    foreach ($orderDetails as $detail) {
        $stmtOrderDetails->bind_param("sisii", $detail['madon'], $detail['id'], $detail['note'], $detail['price'], $detail['soluong']);
        $stmtOrderDetails->execute();
    }

    // Gửi email thông báo đặt hàng thành công
    if (!empty($user_email)) {
        require_once 'order_mail_helper.php';
        // Đảm bảo file order_mail_helper.php gọi đúng send_mail.php và hàm sendEmail
        sendOrderConfirmationEmail($order, $orderDetails, $user_email, $conn);
    }
}

$stmtOrder->close();
$stmtOrderDetails->close();
$conn->close();

// Đảm bảo không có ký tự thừa trước khi trả về JSON
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "message" => "Đặt hàng thành công!",
    "redirect" => "thankyou.php"
]);
exit;
?>
