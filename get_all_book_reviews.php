<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi để tránh HTML trong JSON response
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Kiểm tra file config có tồn tại không
    if (!file_exists('config.php')) {
        echo json_encode([
            'success' => false,
            'message' => 'File config.php không tồn tại',
            'reviews' => []
        ]);
        exit;
    }

    // Kết nối database
    include 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Kiểm tra bảng có tồn tại không (đổi từ books_reviews thành book_reviews)
        $checkTable = "SHOW TABLES LIKE 'book_reviews'";
        $result = $conn->query($checkTable);
        
        if ($result->num_rows == 0) {
            // Bảng chưa tồn tại, trả về mảng rỗng
            echo json_encode([
                'success' => true,
                'reviews' => [],
                'total_reviews' => 0,
                'message' => 'Bảng book_reviews chưa được tạo'
            ]);
            exit;
        }

        // Kiểm tra bảng users có tồn tại không (đổi từ accounts thành users)
        $checkUsersTable = "SHOW TABLES LIKE 'users'";
        $usersResult = $conn->query($checkUsersTable);
        
        // Lấy tất cả đánh giá với thông tin user nếu có bảng users
        if ($usersResult && $usersResult->num_rows > 0) {
            $sql = "SELECT br.*, u.fullname as user_name 
                    FROM book_reviews br 
                    LEFT JOIN users u ON br.user_id = u.id 
                    ORDER BY br.created_at DESC";
        } else {
            // Nếu không có bảng users, chỉ lấy đánh giá
            $sql = "SELECT br.*, 'Ẩn danh' as user_name 
                    FROM book_reviews br 
                    ORDER BY br.created_at DESC";
        }
        
        $result = $conn->query($sql);
        
        $reviews = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = [
                    'id' => $row['id'],
                    'user_id' => $row['user_id'],
                    'user_name' => $row['user_name'] ?? 'Ẩn danh',
                    'product_id' => $row['product_id'],
                    'order_id' => $row['order_id'],
                    'rating' => $row['rating'],
                    'content' => $row['content'],
                    'image' => $row['image'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at']
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'reviews' => $reviews,
            'total_reviews' => count($reviews)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Method không được hỗ trợ'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
        'reviews' => []
    ]);
}

if (isset($conn)) {
    $conn->close();
}
?>
