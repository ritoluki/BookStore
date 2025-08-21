<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order ID']);
    exit;
}

$order_id = $_GET['order_id'];

try {
    // Lấy chi tiết đơn hàng từ bảng orderdetails (correct table name)
    $sql = "SELECT * FROM orderdetails WHERE madon = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orderDetails = [];
    while ($row = $result->fetch_assoc()) {
        // Kiểm tra xem sản phẩm có discount không
        $productId = $row['product_id'];
        $discountInfo = null;
        
        // Lấy thông tin discount nếu có
        $discountSql = "SELECT d.id, d.discount_type, d.discount_value, d.max_uses, d.current_uses
                        FROM discounts d
                        JOIN discount_products dp ON d.id = dp.discount_id
                        WHERE dp.product_id = ? AND d.status = 1 
                        AND NOW() BETWEEN d.start_date AND d.end_date
                        LIMIT 1";
        $discountStmt = $conn->prepare($discountSql);
        $discountStmt->bind_param("i", $productId);
        $discountStmt->execute();
        $discountResult = $discountStmt->get_result();
        
        if ($discountRow = $discountResult->fetch_assoc()) {
            $discountInfo = [
                'discount_id' => $discountRow['id'],
                'discount_type' => $discountRow['discount_type'],
                'discount_value' => $discountRow['discount_value'],
                'max_uses' => $discountRow['max_uses'],
                'current_uses' => $discountRow['current_uses']
            ];
            error_log("Found discount for product $productId: " . json_encode($discountInfo));
        } else {
            error_log("No discount found for product $productId");
        }
        $discountStmt->close();
        
        // Lấy giá gốc từ bảng products
        $productSql = "SELECT price FROM products WHERE id = ?";
        $productStmt = $conn->prepare($productSql);
        $productStmt->bind_param("i", $productId);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productRow = $productResult->fetch_assoc();
        $originalPrice = $productRow ? $productRow['price'] : $row['product_price'];
        $productStmt->close();
        
        $orderDetails[] = [
            'product_id' => $productId,
            'quantity' => $row['soluong'],
            'price' => $row['product_price'], // Giá thực tế khách hàng trả
            'original_price' => $originalPrice, // Giá gốc từ bảng products
            'note' => $row['note'] ?? '',
            'discount_info' => $discountInfo
        ];
    }
    
    echo json_encode([
        'success' => true,
        'orderDetails' => $orderDetails
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
