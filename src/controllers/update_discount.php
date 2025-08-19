<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

error_log("Update discount API called");

try {
    $rawInput = file_get_contents('php://input');
    error_log("Raw input: " . $rawInput);
    
    $data = json_decode($rawInput, true);
    error_log("Decoded data: " . print_r($data, true));
    
    if (!$data || !isset($data['discount_id'])) {
        throw new Exception('Thiếu ID chương trình giảm giá');
    }
    
    if (empty($data['name']) || empty($data['discount_type']) || !isset($data['discount_value']) ||
        empty($data['start_date']) || empty($data['end_date'])) {
        throw new Exception('Thiếu thông tin bắt buộc');
    }
    
    $discountId = (int)$data['discount_id'];
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Kiểm tra discount có tồn tại không
        $sql = "SELECT id FROM discounts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $discountId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Không tìm thấy chương trình giảm giá');
        }
        $stmt->close();
        
        // Kiểm tra xem cột min_order_amount có tồn tại không
        $checkColumn = $conn->query("SHOW COLUMNS FROM discounts LIKE 'min_order_amount'");
        $hasMinOrderAmount = $checkColumn && $checkColumn->num_rows > 0;
        
        if ($hasMinOrderAmount) {
            // Cập nhật với min_order_amount
            $sql = "UPDATE discounts SET 
                    name = ?, 
                    description = ?, 
                    discount_type = ?, 
                    discount_value = ?, 
                    start_date = ?, 
                    end_date = ?, 
                    max_uses = ?, 
                    min_order_amount = ?, 
                    status = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdssidii", 
                $data['name'],
                $data['description'],
                $data['discount_type'],
                $data['discount_value'],
                $data['start_date'],
                $data['end_date'],
                $data['max_uses'],
                $data['min_order_amount'],
                $data['status'],
                $discountId
            );
        } else {
            // Cập nhật không có min_order_amount
            $sql = "UPDATE discounts SET 
                    name = ?, 
                    description = ?, 
                    discount_type = ?, 
                    discount_value = ?, 
                    start_date = ?, 
                    end_date = ?, 
                    max_uses = ?, 
                    status = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdssiii", 
                $data['name'],
                $data['description'],
                $data['discount_type'],
                $data['discount_value'],
                $data['start_date'],
                $data['end_date'],
                $data['max_uses'],
                $data['status'],
                $discountId
            );
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Không thể cập nhật chương trình giảm giá');
        }
        $stmt->close();
        
        // Xóa các liên kết sản phẩm cũ
        $sql = "DELETE FROM discount_products WHERE discount_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $discountId);
        $stmt->execute();
        $stmt->close();
        
        // Thêm liên kết sản phẩm mới
        if ($data['apply_type'] === 'category' && !empty($data['category'])) {
            $sql = "SELECT id FROM products WHERE category = ? AND status = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $data['category']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $sql2 = "INSERT INTO discount_products (discount_id, product_id) VALUES (?, ?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("ii", $discountId, $row['id']);
                $stmt2->execute();
                $stmt2->close();
            }
            $stmt->close();
        } elseif ($data['apply_type'] === 'specific_products' && !empty($data['products'])) {
            foreach ($data['products'] as $productId) {
                $sql = "INSERT INTO discount_products (discount_id, product_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $discountId, $productId);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật chương trình giảm giá thành công'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error in update_discount.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
