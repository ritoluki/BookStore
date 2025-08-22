<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['orderId']) || !isset($data['reason'])) {
        throw new Exception('Thiếu thông tin bắt buộc');
    }
    
    $orderId = $data['orderId'];
    $reason = $data['reason'];
    $isAdmin = $data['isAdmin'] ?? false;
    
    // Lấy thông tin đơn hàng
    $sql = "SELECT o.*, u.email, u.fullname FROM `order` o 
            LEFT JOIN users u ON o.khachhang = u.id 
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    $order = $result->fetch_assoc();
    $stmt->close();
    
    // Kiểm tra trạng thái đơn hàng
    if ($order['trangthai'] == 3) {
        throw new Exception('Không thể hủy đơn hàng đã hoàn thành');
    }
    
    if ($order['trangthai'] == 4) {
        throw new Exception('Đơn hàng đã được hủy trước đó');
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Cập nhật trạng thái đơn hàng thành "Đã hủy"
        $sql = "UPDATE `order` SET trangthai = 4, cancel_reason = ?, cancelled_by = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $cancelledBy = $isAdmin ? 'admin' : 'customer';
        $stmt->bind_param("sss", $reason, $cancelledBy, $orderId);
        
        if (!$stmt->execute()) {
            throw new Exception('Không thể cập nhật trạng thái đơn hàng');
        }
        $stmt->close();
        
        // Hoàn trả số lượng sản phẩm
        $sql = "SELECT od.product_id, od.soluong FROM orderdetails od WHERE od.madon = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $productId = $row['product_id'];
            $quantity = $row['soluong'];
            
            // Hoàn trả số lượng vào kho
            $sql_restore = "UPDATE products SET soluong = soluong + ? WHERE id = ?";
            $stmt_restore = $conn->prepare($sql_restore);
            $stmt_restore->bind_param("ii", $quantity, $productId);
            $stmt_restore->execute();
            $stmt_restore->close();
            
            // Trả lại discount usage nếu sản phẩm có áp dụng discount
            $sqlDiscount = "SELECT d.id, d.current_uses, od.soluong
                           FROM discounts d
                           JOIN discount_products dp ON d.id = dp.discount_id
                           JOIN orderdetails od ON dp.product_id = od.product_id
                           WHERE od.product_id = ? AND od.madon = ? AND d.status = 1";
            $stmtDiscount = $conn->prepare($sqlDiscount);
            $stmtDiscount->bind_param("is", $productId, $orderId);
            $stmtDiscount->execute();
            $discountResult = $stmtDiscount->get_result();
            
            if ($discountRow = $discountResult->fetch_assoc()) {
                $discountId = $discountRow['id'];
                $currentUses = (int)$discountRow['current_uses'];
                $orderedQuantity = (int)$discountRow['soluong'];
                
                // Tính số lượng discount cần trả lại
                $discountToRefund = min($currentUses, $orderedQuantity);
                
                if ($discountToRefund > 0) {
                    $sqlRefund = "UPDATE discounts SET current_uses = current_uses - ? WHERE id = ?";
                    $stmtRefund = $conn->prepare($sqlRefund);
                    $stmtRefund->bind_param("ii", $discountToRefund, $discountId);
                    if (!$stmtRefund->execute()) {
                        error_log("Không thể trả lại discount usage cho discount ID: " . $discountId);
                    } else {
                        error_log("Đã trả lại discount usage: $discountToRefund cho discount ID: $discountId");
                    }
                    $stmtRefund->close();
                }
            }
            $stmtDiscount->close();
        }
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        // Gửi email thông báo hủy đơn (nếu có email)
        if (!empty($order['email'])) {
            try {
                require_once '../services/order_mail_helper.php';
                sendOrderCancellationEmailWithReason($order, $order['email'], $reason, $isAdmin);
            } catch (Exception $mailError) {
                // Log lỗi mail nhưng không dừng quá trình
                error_log("Mail sending error: " . $mailError->getMessage());
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã hủy đơn hàng thành công'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
