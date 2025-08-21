<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['product_id']) || !isset($data['quantity'])) {
        throw new Exception('Thiếu thông tin sản phẩm hoặc số lượng');
    }
    
    $productId = (int)$data['product_id'];
    $requestedQuantity = (int)$data['quantity'];
    
    // Kiểm tra xem cột min_order_amount có tồn tại không
    $checkColumn = $conn->query("SHOW COLUMNS FROM discounts LIKE 'min_order_amount'");
    $hasMinOrderAmount = $checkColumn && $checkColumn->num_rows > 0;
    
    // Lấy thông tin giảm giá hiện tại cho sản phẩm
    $sql = "SELECT d.*, p.price
            FROM discounts d
            INNER JOIN discount_products dp ON d.id = dp.discount_id
            INNER JOIN products p ON dp.product_id = p.id
            WHERE dp.product_id = ?
            AND d.status = 1
            AND NOW() BETWEEN d.start_date AND d.end_date
            AND (d.max_uses = 0 OR d.current_uses < d.max_uses)
            " . ($hasMinOrderAmount ? "AND (d.min_order_amount = 0 OR p.price >= d.min_order_amount)" : "") . "
            ORDER BY 
                CASE 
                    WHEN d.discount_type = 'percentage' THEN d.discount_value
                    WHEN d.discount_type = 'fixed_amount' THEN d.discount_value
                    ELSE 0
                END DESC
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $discount = $result->fetch_assoc();
        
        // Kiểm tra số lượng có thể áp dụng giảm giá
        $maxUsesRemaining = $discount['max_uses'] == 0 ? PHP_INT_MAX : ($discount['max_uses'] - $discount['current_uses']);
        $applicableQuantity = min($requestedQuantity, $maxUsesRemaining);
        
        // Tính giá
        $originalPrice = $discount['price'];
        $discountedPrice = $originalPrice;
        
        if ($discount['discount_type'] === 'percentage') {
            $discountedPrice = $originalPrice * (1 - $discount['discount_value'] / 100);
        } elseif ($discount['discount_type'] === 'fixed_amount') {
            $discountedPrice = max($originalPrice - $discount['discount_value'], 0);
        }
        
        echo json_encode([
            'success' => true,
            'has_discount' => true,
            'discount_info' => [
                'id' => $discount['id'],
                'name' => $discount['name'],
                'discount_type' => $discount['discount_type'],
                'discount_value' => (float)$discount['discount_value'],
                'max_uses' => (int)$discount['max_uses'],
                'current_uses' => (int)$discount['current_uses'],
                'max_uses_remaining' => $maxUsesRemaining,
                'original_price' => (int)$originalPrice,
                'discounted_price' => (int)$discountedPrice
            ],
            'requested_quantity' => $requestedQuantity,
            'applicable_quantity' => $applicableQuantity,
            'remaining_quantity' => $requestedQuantity - $applicableQuantity,
            'can_apply_full_discount' => $applicableQuantity >= $requestedQuantity
        ]);
        
    } else {
        // Không có giảm giá áp dụng
        echo json_encode([
            'success' => true,
            'has_discount' => false,
            'requested_quantity' => $requestedQuantity,
            'applicable_quantity' => 0,
            'remaining_quantity' => $requestedQuantity,
            'can_apply_full_discount' => false
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($stmt) && $stmt) {
    $stmt->close();
}
$conn->close();
?>
