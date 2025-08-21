<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['order_id'])) {
        throw new Exception('Thiếu order_id');
    }
    
    $orderId = $data['order_id'];
    
    // Lấy chi tiết đơn hàng
    $sql = "SELECT od.product_id, od.soluong 
            FROM orderDetails od 
            WHERE od.madon = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $updatedDiscounts = [];
    
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        $quantity = $row['soluong'];
        
        // Tìm discount áp dụng cho sản phẩm này
        $sqlDiscount = "SELECT d.id, d.max_uses, d.current_uses
                        FROM discounts d
                        INNER JOIN discount_products dp ON d.id = dp.discount_id
                        WHERE dp.product_id = ?
                        AND d.status = 1
                        AND NOW() BETWEEN d.start_date AND d.end_date
                        AND (d.max_uses = 0 OR d.current_uses < d.max_uses)
                        LIMIT 1";
        
        $stmtDiscount = $conn->prepare($sqlDiscount);
        $stmtDiscount->bind_param("i", $productId);
        $stmtDiscount->execute();
        $discountResult = $stmtDiscount->get_result();
        
        if ($discountResult->num_rows > 0) {
            $discount = $discountResult->fetch_assoc();
            $discountId = $discount['id'];
            
            // Tính số lượng có thể áp dụng giảm giá
            $maxUsesRemaining = $discount['max_uses'] == 0 ? $quantity : min($quantity, $discount['max_uses'] - $discount['current_uses']);
            
            if ($maxUsesRemaining > 0) {
                // Cập nhật current_uses
                $sqlUpdate = "UPDATE discounts SET current_uses = current_uses + ? WHERE id = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ii", $maxUsesRemaining, $discountId);
                $stmtUpdate->execute();
                $stmtUpdate->close();
                
                $updatedDiscounts[] = [
                    'discount_id' => $discountId,
                    'product_id' => $productId,
                    'quantity_used' => $maxUsesRemaining
                ];
            }
        }
        $stmtDiscount->close();
    }
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã cập nhật số lượng sử dụng giảm giá',
        'updated_discounts' => $updatedDiscounts
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
