<?php
// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Bắt đầu output buffering để bắt lỗi
ob_start();

header('Content-Type: application/json');
require_once '../../config/config.php';

// Kiểm tra kết nối database
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if (!isset($_GET['order_id']) && !isset($_GET['madon'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order ID or madon']);
    exit;
}

$order_id = $_GET['order_id'] ?? $_GET['madon'];

// Log để debug
error_log("get_order_details.php called with order_id: " . $order_id);

try {
    // Lấy chi tiết đơn hàng từ bảng orderdetails (correct table name)
    $sql = "SELECT * FROM orderdetails WHERE madon = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $order_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Failed to get result: ' . $stmt->error);
    }
    
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
        if (!$discountStmt) {
            error_log("Failed to prepare discount statement: " . $conn->error);
            continue;
        }
        
        $discountStmt->bind_param("i", $productId);
        if (!$discountStmt->execute()) {
            error_log("Failed to execute discount statement: " . $discountStmt->error);
            $discountStmt->close();
            continue;
        }
        
        $discountResult = $discountStmt->get_result();
        if (!$discountResult) {
            error_log("Failed to get discount result: " . $discountStmt->error);
            $discountStmt->close();
            continue;
        }
        
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
        if (!$productStmt) {
            error_log("Failed to prepare product statement: " . $conn->error);
            $originalPrice = $row['product_price'];
        } else {
            $productStmt->bind_param("i", $productId);
            if (!$productStmt->execute()) {
                error_log("Failed to execute product statement: " . $productStmt->error);
                $originalPrice = $row['product_price'];
            } else {
                $productResult = $productStmt->get_result();
                if (!$productResult) {
                    error_log("Failed to get product result: " . $productStmt->error);
                    $originalPrice = $row['product_price'];
                } else {
                    $productRow = $productResult->fetch_assoc();
                    $originalPrice = $productRow ? $productRow['price'] : $row['product_price'];
                }
            }
            $productStmt->close();
        }
        
        $orderDetails[] = [
            'product_id' => $productId,
            'quantity' => $row['soluong'],
            'price' => $row['product_price'], // Giá thực tế khách hàng trả
            'original_price' => $originalPrice, // Giá gốc từ bảng products
            'note' => $row['note'] ?? '',
            'discount_info' => $discountInfo
        ];
    }
    
    // Kiểm tra output buffer trước khi gửi JSON
    $output = ob_get_contents();
    if (!empty($output)) {
        error_log("Unexpected output before JSON: " . $output);
        ob_clean();
    }
    
    echo json_encode([
        'success' => true,
        'orderDetails' => $orderDetails
    ]);
} catch (Exception $e) {
    // Kiểm tra output buffer trước khi gửi JSON
    $output = ob_get_contents();
    if (!empty($output)) {
        error_log("Unexpected output before JSON error: " . $output);
        ob_clean();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}

// Đảm bảo output buffer được xử lý đúng
ob_end_flush();
?>
