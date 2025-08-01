<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi để tránh HTML trong JSON response
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Kiểm tra file config có tồn tại không
    if (!file_exists('config.php')) {
        echo json_encode([
            'success' => false,
            'message' => 'File config.php không tồn tại'
        ]);
        exit;
    }

    // Kết nối database
    include '../../config/config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $user_id = $input['user_id'] ?? null;
        $product_id = $input['product_id'] ?? null;
        $rating = $input['rating'] ?? null;
        $content = $input['content'] ?? '';
        $order_id = $input['order_id'] ?? null;
        
        // Validation
        if (!$user_id || !$product_id || !$rating) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin bắt buộc',
                'debug' => ['user_id' => $user_id, 'product_id' => $product_id, 'rating' => $rating]
            ]);
            exit;
        }
        
        if ($rating < 1 || $rating > 5) {
            echo json_encode([
                'success' => false,
                'message' => 'Điểm đánh giá phải từ 1 đến 5'
            ]);
            exit;
        }
        
        // Chuyển đổi thành integer
        $user_id = (int)$user_id;
        $product_id = (int)$product_id;
        $rating = (int)$rating;
        
        // Kiểm tra bảng có tồn tại không, nếu không thì tạo (đã đổi từ books_reviews thành book_reviews)
        $checkTable = "SHOW TABLES LIKE 'book_reviews'";
        $result = $conn->query($checkTable);
        
        if ($result->num_rows == 0) {
            // Tạo bảng book_reviews
            $createTable = "CREATE TABLE IF NOT EXISTS `book_reviews` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `product_id` int(11) NOT NULL,
              `order_id` varchar(50) NULL,
              `rating` int(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
              `content` text,
              `image` varchar(255),
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `idx_product_id` (`product_id`),
              INDEX `idx_user_id` (`user_id`),
              INDEX `idx_rating` (`rating`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if (!$conn->query($createTable)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể tạo bảng book_reviews'
                ]);
                exit;
            }
        }
        
        // Kiểm tra xem user đã đánh giá sản phẩm này chưa
        $checkSql = "SELECT id FROM book_reviews WHERE user_id = ? AND product_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $user_id, $product_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi!'
            ]);
            $checkStmt->close();
            exit;
        }
        
        // Thêm đánh giá mới
        $sql = "INSERT INTO book_reviews (user_id, product_id, order_id, rating, content, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisis", $user_id, $product_id, $order_id, $rating, $content);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Đánh giá thành công!',
                'review_id' => $conn->insert_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lưu đánh giá'
            ]);
        }
        
        if (isset($stmt)) $stmt->close();
        if (isset($checkStmt)) $checkStmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Method không được hỗ trợ'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}

if (isset($conn)) {
    $conn->close();
}
?>
