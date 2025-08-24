<?php
header('Content-Type: application/json');
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
$result = db_query($conn, $sql_user, [$khachhang]);
if ($result && db_num_rows($result) > 0) {
    $row = db_fetch_assoc($result);
    $user_email = $row['email'];
} else {
    // Thử tìm theo số điện thoại
    $sql_user_phone = "SELECT email, id FROM users WHERE phone = ?";
    $result_phone = db_query($conn, $sql_user_phone, [$khachhang]);
    if ($result_phone && db_num_rows($result_phone) > 0) {
        $row_phone = db_fetch_assoc($result_phone);
        $user_email = $row_phone['email'];
        // Cập nhật lại id cho đơn hàng nếu cần
        $order['khachhang'] = $row_phone['id'];
    }
}

// Chuẩn bị câu lệnh SQL để thêm đơn hàng vào bảng 'order'
// Bỏ cột id vì nó sẽ tự động tăng
$sqlOrder = "INSERT INTO \"order\" (khachhang, hinhthucgiao, ngaygiaohang, thoigiangiao, ghichu, tenguoinhan, sdtnhan, diachinhan, thoigiandat, tongtien, trangthai, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$orderParams = [$order['khachhang'], $order['hinhthucgiao'], $order['ngaygiaohang'], $order['thoigiangiao'], $order['ghichu'], $order['tenguoinhan'], $order['sdtnhan'], $order['diachinhan'], $thoigiandat, $order['tongtien'], $order['trangthai'], $order['payment_method']];

// Thực thi câu lệnh SQL để thêm đơn hàng
$orderResult = db_query($conn, $sqlOrder, $orderParams);
if ($orderResult) {
    // Lấy ID của đơn hàng vừa tạo TRƯỚC KHI sử dụng
    $newOrderId = db_insert_id($conn);
    
    // Chuẩn bị câu lệnh SQL để thêm chi tiết đơn hàng vào bảng 'orderdetails'
    // Bỏ cột id vì nó sẽ tự động tăng
    $sqlOrderDetails = "INSERT INTO orderdetails (madon, product_id, note, product_price, soluong) VALUES (?, ?, ?, ?, ?)";
    
    // Loại bỏ duplicate theo product_id + madon
    $uniqueOrderDetails = [];
    $seen = [];
    foreach ($orderDetails as $detail) {
        $productId = isset($detail['product_id']) ? $detail['product_id'] : (isset($detail['id']) ? $detail['id'] : null);
        $key = $productId . '-' . $newOrderId;
        if (!isset($seen[$key])) {
            $uniqueOrderDetails[] = [
                'madon' => $newOrderId, // Dùng ID mới của order
                'product_id' => $productId,
                'note' => $detail['note'],
                'price' => $detail['price'],
                'soluong' => $detail['soluong']
            ];
            $seen[$key] = true;
        }
    }
    foreach ($uniqueOrderDetails as $detail) {
        $detailParams = [$detail['madon'], $detail['product_id'], $detail['note'], $detail['price'], $detail['soluong']];
        db_query($conn, $sqlOrderDetails, $detailParams);
    }
    
    // Cập nhật số lượng sử dụng discount
    updateDiscountUsage($newOrderId, $conn);
}

// Hàm cập nhật số lượng sử dụng discount
function updateDiscountUsage($orderId, $conn) {
    try {
        // Lấy chi tiết đơn hàng và discount áp dụng
        $sql = "SELECT od.product_id, od.soluong, d.id as discount_id, d.max_uses, d.current_uses
                FROM orderdetails od
                JOIN products p ON od.product_id = p.id
                JOIN discount_products dp ON p.id = dp.product_id
                JOIN discounts d ON dp.discount_id = d.id
                WHERE od.madon = ?
                  AND d.status = 1
                  AND NOW() BETWEEN d.start_date AND d.end_date
                  AND (d.max_uses = 0 OR d.current_uses < d.max_uses)";
        
        $result = db_query($conn, $sql, [$orderId]);
        if ($result) {
            $updatedDiscounts = [];
            while ($row = db_fetch_assoc($result)) {
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
                    db_query($conn, $sqlUpdate, [$incrementAmount, $discountId]);
                    $updatedDiscounts[$discountId] = ($updatedDiscounts[$discountId] ?? 0) + $incrementAmount;
                }
            }
            
            if (!empty($updatedDiscounts)) {
                error_log("Đã cập nhật discount usage: " . json_encode($updatedDiscounts));
            }
        }
        
    } catch (Exception $e) {
        error_log("Lỗi cập nhật discount usage: " . $e->getMessage());
    }
}



db_close($conn);

// Đảm bảo không có ký tự thừa trước khi trả về JSON
        // Lấy ID của đơn hàng vừa tạo (đã có từ trước)
        
        echo json_encode([
            "success" => true,
            "message" => "Đặt hàng thành công!",
            "orderId" => $newOrderId,
            "redirect_url" => "./src/controllers/order_success.php?order_id=" . $newOrderId
        ]);
exit;
?>
