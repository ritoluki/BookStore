<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

// Debug log
error_log("Create discount API called");

try {
    // Lấy dữ liệu từ request
    $rawInput = file_get_contents('php://input');
    error_log("Raw input: " . $rawInput);
    
    $data = json_decode($rawInput, true);
    error_log("Decoded data: " . print_r($data, true));
    
    if (!$data) {
        throw new Exception('Không có dữ liệu được gửi');
    }
    
    // Validate dữ liệu
    if (empty($data['name']) || empty($data['discount_type']) || !isset($data['discount_value']) || 
        empty($data['start_date']) || empty($data['end_date'])) {
        throw new Exception('Thiếu thông tin bắt buộc');
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Kiểm tra xem cột min_order_amount có tồn tại không
        $checkColumn = $conn->query("SHOW COLUMNS FROM discounts LIKE 'min_order_amount'");
        $hasMinOrderAmount = $checkColumn && $checkColumn->num_rows > 0;
        
        if ($hasMinOrderAmount) {
            // Tạo chương trình giảm giá với min_order_amount
            $sql = "INSERT INTO discounts (name, description, discount_type, discount_value, start_date, end_date, max_uses, min_order_amount, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdssidi", 
                $data['name'],
                $data['description'],
                $data['discount_type'],
                $data['discount_value'],
                $data['start_date'],
                $data['end_date'],
                $data['max_uses'],
                $data['min_order_amount'],
                $data['status']
            );
        } else {
            // Tạo chương trình giảm giá không có min_order_amount
            $sql = "INSERT INTO discounts (name, description, discount_type, discount_value, start_date, end_date, max_uses, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdssii", 
                $data['name'],
                $data['description'],
                $data['discount_type'],
                $data['discount_value'],
                $data['start_date'],
                $data['end_date'],
                $data['max_uses'],
                $data['status']
            );
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Không thể tạo chương trình giảm giá');
        }
        
        $discountId = $conn->insert_id;
        
        // Thêm sản phẩm vào chương trình giảm giá
        if ($data['apply_type'] === 'category' && !empty($data['category'])) {
            // Lấy tất cả sản phẩm trong category
            $sql = "SELECT id FROM products WHERE category = ? AND status = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $data['category']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $sql = "INSERT INTO discount_products (discount_id, product_id) VALUES (?, ?)";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param("ii", $discountId, $row['id']);
                $stmt2->execute();
                $stmt2->close();
            }
        } elseif ($data['apply_type'] === 'specific_products' && !empty($data['products'])) {
            // Thêm từng sản phẩm cụ thể
            foreach ($data['products'] as $productId) {
                $sql = "INSERT INTO discount_products (discount_id, product_id) VALUES (?, ?)";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param("ii", $discountId, $productId);
                $stmt2->execute();
                $stmt2->close();
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Tạo chương trình giảm giá thành công',
            'discount_id' => $discountId
        ]);
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        throw $e;
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
