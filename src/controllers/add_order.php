<?php
require_once '../../config/config.php';

// Lấy dữ liệu từ yêu cầu POST
$order = json_decode($_POST['order'], true);
$orderDetails = json_decode($_POST['orderDetails'], true);

// Đảm bảo thoigiandat có định dạng chuẩn cho MySQL
$thoigiandat = date('Y-m-d H:i:s', strtotime($order['thoigiandat']));

// Lấy thông tin email của khách hàng
$user_email = "";
$khachhang = $order['khachhang'];

// Nếu không phải số, hoặc là số nhưng không tìm thấy theo id, thử tìm theo số điện thoại
$sql_user = "SELECT email FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $khachhang);
$stmt_user->execute();
$result = $stmt_user->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_email = $row['email'];
} else {
    // Thử tìm theo số điện thoại
    $stmt_user->close();
    $sql_user_phone = "SELECT email, id FROM users WHERE phone = ?";
    $stmt_user_phone = $conn->prepare($sql_user_phone);
    $stmt_user_phone->bind_param("s", $khachhang);
    $stmt_user_phone->execute();
    $result_phone = $stmt_user_phone->get_result();
    if ($result_phone->num_rows > 0) {
        $row_phone = $result_phone->fetch_assoc();
        $user_email = $row_phone['email'];
        // Cập nhật lại id cho đơn hàng nếu cần
        $order['khachhang'] = $row_phone['id'];
    }
    $stmt_user_phone->close();
}

// Chuẩn bị câu lệnh SQL để thêm đơn hàng vào bảng 'order'
$sqlOrder = "INSERT INTO `order` (id, khachhang, hinhthucgiao, ngaygiaohang, thoigiangiao, ghichu, tenguoinhan, sdtnhan, diachinhan, thoigiandat, tongtien, trangthai, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bind_param("ssssssssssiis", $order['id'], $order['khachhang'], $order['hinhthucgiao'], $order['ngaygiaohang'], $order['thoigiangiao'], $order['ghichu'], $order['tenguoinhan'], $order['sdtnhan'], $order['diachinhan'], $thoigiandat, $order['tongtien'], $order['trangthai'], $order['payment_method']);

// Thực thi câu lệnh SQL để thêm đơn hàng
if ($stmtOrder->execute()) {
    // Chuẩn bị câu lệnh SQL để thêm chi tiết đơn hàng vào bảng 'orderDetails'
    $sqlOrderDetails = "INSERT INTO orderDetails (madon, product_id, note, product_price, soluong) VALUES (?, ?, ?, ?, ?)";
    $stmtOrderDetails = $conn->prepare($sqlOrderDetails);
    
    // Loại bỏ duplicate theo product_id + madon
    $uniqueOrderDetails = [];
    $seen = [];
    foreach ($orderDetails as $detail) {
        $productId = isset($detail['product_id']) ? $detail['product_id'] : (isset($detail['id']) ? $detail['id'] : null);
        $key = $productId . '-' . $detail['madon'];
        if (!isset($seen[$key])) {
            $uniqueOrderDetails[] = [
                'madon' => $detail['madon'],
                'product_id' => $productId,
                'note' => $detail['note'],
                'price' => $detail['price'],
                'soluong' => $detail['soluong']
            ];
            $seen[$key] = true;
        }
    }
    foreach ($uniqueOrderDetails as $detail) {
        $stmtOrderDetails->bind_param("sisii", $detail['madon'], $detail['product_id'], $detail['note'], $detail['price'], $detail['soluong']);
        $stmtOrderDetails->execute();
    }

    // Gửi email thông báo đặt hàng thành công
    if (!empty($user_email)) {
        error_log("Chuẩn bị gửi email xác nhận đơn hàng đến: " . $user_email);
        require_once 'order_mail_helper.php';
        // Lấy lại chi tiết đơn hàng vừa đặt từ database
        $sqlGetOrderDetails = "SELECT * FROM orderDetails WHERE madon = ?";
        $stmtGetOrderDetails = $conn->prepare($sqlGetOrderDetails);
        $stmtGetOrderDetails->bind_param("s", $order['id']);
        $stmtGetOrderDetails->execute();
        $resultOrderDetails = $stmtGetOrderDetails->get_result();
        $orderDetailsForMail = [];
        while ($row = $resultOrderDetails->fetch_assoc()) {
            $orderDetailsForMail[] = [
                'id' => $row['product_id'],
                'soluong' => $row['soluong'],
                'price' => $row['product_price'],
                'note' => $row['note'],
                'madon' => $row['madon']
            ];
        }
        $stmtGetOrderDetails->close();

        $emailResult = sendOrderConfirmationEmail($order, $orderDetailsForMail, $user_email, $conn);
        error_log("Kết quả gửi email: " . ($emailResult ? "Thành công" : "Thất bại"));
    } else {
        error_log("Không tìm thấy email của người dùng ID: " . $order['khachhang']);
    }
}

$stmtOrder->close();
$stmtOrderDetails->close();
$conn->close();

// Đảm bảo không có ký tự thừa trước khi trả về JSON
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "message" => "Đặt hàng thành công!"
]);
exit;
?>
