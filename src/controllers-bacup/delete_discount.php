<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

error_log("Delete discount API called");

try {
    $rawInput = file_get_contents('php://input');
    error_log("Raw input: " . $rawInput);
    
    $data = json_decode($rawInput, true);
    error_log("Decoded data: " . print_r($data, true));
    
    if (!$data || !isset($data['discount_id'])) {
        throw new Exception('Thiếu ID chương trình giảm giá');
    }
    
    $discountId = (int)$data['discount_id'];
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Kiểm tra chương trình giảm giá có tồn tại không
        $sql = "SELECT id, name FROM discounts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $discountId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Không tìm thấy chương trình giảm giá');
        }
        
        $discount = $result->fetch_assoc();
        $stmt->close();
        
        // Xóa các liên kết sản phẩm trước (do có foreign key constraint)
        $sql = "DELETE FROM discount_products WHERE discount_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $discountId);
        
        if (!$stmt->execute()) {
            throw new Exception('Không thể xóa liên kết sản phẩm');
        }
        $stmt->close();
        
        // Xóa lịch sử sử dụng (nếu bảng tồn tại)
        $checkTable = $conn->query("SHOW TABLES LIKE 'discount_usage_history'");
        if ($checkTable && $checkTable->num_rows > 0) {
            $sql = "DELETE FROM discount_usage_history WHERE discount_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $discountId);
            $stmt->execute();
            $stmt->close();
        }
        
        // Cuối cùng xóa chương trình giảm giá
        $sql = "DELETE FROM discounts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $discountId);
        
        if (!$stmt->execute()) {
            throw new Exception('Không thể xóa chương trình giảm giá');
        }
        $stmt->close();
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Xóa chương trình giảm giá "' . $discount['name'] . '" thành công'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error in delete_discount.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
