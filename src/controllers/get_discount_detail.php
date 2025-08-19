<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    $discountId = $_GET['id'] ?? null;
    
    if (!$discountId) {
        throw new Exception('Thiếu ID chương trình giảm giá');
    }
    
    $sql = "SELECT * FROM discounts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $discountId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy chương trình giảm giá');
    }
    
    $discount = $result->fetch_assoc();
    
    // Lấy danh sách sản phẩm áp dụng
    $sql = "SELECT p.id, p.title FROM products p 
            INNER JOIN discount_products dp ON p.id = dp.product_id 
            WHERE dp.discount_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $discountId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'discount' => [
            'id' => $discount['id'],
            'name' => $discount['name'],
            'description' => $discount['description'],
            'discount_type' => $discount['discount_type'],
            'discount_value' => (float)$discount['discount_value'],
            'start_date' => $discount['start_date'],
            'end_date' => $discount['end_date'],
            'max_uses' => (int)$discount['max_uses'],
            'min_order_amount' => isset($discount['min_order_amount']) ? (float)$discount['min_order_amount'] : 0,
            'status' => (int)$discount['status'],
            'products' => $products
        ]
    ]);

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
