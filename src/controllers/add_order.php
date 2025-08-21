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
        require_once '../services/order_mail_helper.php';
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

        try {
            $emailResult = sendOrderConfirmationEmail($order, $orderDetailsForMail, $user_email, $conn);
            error_log("Kết quả gửi email: " . ($emailResult ? "Thành công" : "Thất bại"));
        } catch (Exception $e) {
            error_log("Lỗi gửi email: " . $e->getMessage());
            // Không để lỗi email làm fail đơn hàng
        }
        
        // Cập nhật số lượng sử dụng discount
        updateDiscountUsage($order['id'], $conn);
    } else {
        error_log("Không tìm thấy email của người dùng ID: " . $order['khachhang']);
    }
}

// Hàm cập nhật số lượng sử dụng discount
function updateDiscountUsage($orderId, $conn) {
    try {
        // Lấy chi tiết đơn hàng và discount áp dụng
        $sql = "SELECT od.product_id, od.soluong, d.id as discount_id, d.max_uses, d.current_uses
                FROM orderDetails od
                JOIN products p ON od.product_id = p.id
                JOIN discount_products dp ON p.id = dp.product_id
                JOIN discounts d ON dp.discount_id = d.id
                WHERE od.madon = ?
                  AND d.status = 1
                  AND NOW() BETWEEN d.start_date AND d.end_date
                  AND (d.max_uses = 0 OR d.current_uses < d.max_uses)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $updatedDiscounts = [];
        while ($row = $result->fetch_assoc()) {
            $discountId = $row['discount_id'];
            $maxUses = (int)$row['max_uses'];
            $currentUses = (int)$row['current_uses'];
            $orderedQuantity = (int)$row['soluong'];
            
            // Tính số lượng có thể áp dụng giảm giá
            $incrementAmount = $orderedQuantity;
            if ($maxUses > 0) {
                $remainingUses = $maxUses - $currentUses;
                $incrementAmount = min($orderedQuantity, $remainingUses);
            }
            
            if ($incrementAmount > 0) {
                // Cập nhật current_uses cho discount
                $sqlUpdate = "UPDATE discounts SET current_uses = current_uses + ? WHERE id = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ii", $incrementAmount, $discountId);
                if (!$stmtUpdate->execute()) {
                    error_log("Không thể cập nhật current_uses cho discount ID: " . $discountId);
                }
                $stmtUpdate->close();
                $updatedDiscounts[$discountId] = ($updatedDiscounts[$discountId] ?? 0) + $incrementAmount;
            }
        }
        $stmt->close();
        
        if (!empty($updatedDiscounts)) {
            error_log("Đã cập nhật discount usage: " . json_encode($updatedDiscounts));
        }
        
    } catch (Exception $e) {
        error_log("Lỗi cập nhật discount usage: " . $e->getMessage());
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
    "orderId" => $order['id']
]);
exit;
?>
