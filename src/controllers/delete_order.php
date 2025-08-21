<?php
require_once '../../config/config.php';

// Đọc dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'] ?? null;

if (!$orderId) {
    echo json_encode(["success" => false, "message" => "Thiếu mã đơn hàng!"]);
    exit;
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Lấy thông tin chi tiết đơn hàng trước khi xóa để hoàn trả số lượng
    $sqlGetDetails = "SELECT product_id, soluong FROM orderdetails WHERE madon = ?";
    $stmtGetDetails = $conn->prepare($sqlGetDetails);
    $stmtGetDetails->bind_param("s", $orderId);
    $stmtGetDetails->execute();
    $result = $stmtGetDetails->get_result();
    
    $orderDetails = [];
    while ($row = $result->fetch_assoc()) {
        $orderDetails[] = $row;
    }
    $stmtGetDetails->close();
    
    // Hoàn trả số lượng sản phẩm về kho và trả lại discount usage
    foreach ($orderDetails as $detail) {
        $productId = $detail['product_id'];
        $quantity = $detail['soluong'];
        
        // Hoàn trả số lượng sản phẩm
        $sqlRestore = "UPDATE products SET soluong = soluong + ? WHERE id = ?";
        $stmtRestore = $conn->prepare($sqlRestore);
        $stmtRestore->bind_param("ii", $quantity, $productId);
        
        if (!$stmtRestore->execute()) {
            throw new Exception("Không thể hoàn trả số lượng sản phẩm ID: " . $productId);
        }
        $stmtRestore->close();
        
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
    
    // Xóa chi tiết đơn hàng
    $sqlDetail = "DELETE FROM orderdetails WHERE madon = ?";
    $stmtDetail = $conn->prepare($sqlDetail);
    $stmtDetail->bind_param("s", $orderId);
    
    if (!$stmtDetail->execute()) {
        throw new Exception("Không thể xóa chi tiết đơn hàng");
    }
    $stmtDetail->close();
    
    // Xóa đơn hàng
    $sql = "DELETE FROM `order` WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderId);
    
    if (!$stmt->execute()) {
        throw new Exception("Không thể xóa đơn hàng");
    }
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "success" => true, 
        "message" => "Xóa đơn hàng thành công và đã hoàn trả " . count($orderDetails) . " sản phẩm về kho!"
    ]);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo json_encode([
        "success" => false, 
        "message" => "Lỗi: " . $e->getMessage()
    ]);
}

$conn->close();
?> 