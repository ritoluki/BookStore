<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi để tránh HTML trong JSON response
error_reporting(0);
ini_set('display_errors', 0);

// Kiểm tra file config có tồn tại không
if (!file_exists('../../config/config.php')) {
    echo json_encode([
        'success' => false,
        'message' => 'File config.php không tồn tại',
        'reviews' => []
    ]);
    exit;
}

// Kết nối database
include '../../config/config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $product_id = $_GET['product_id'] ?? null;
        
        if (!$product_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu product_id',
                'reviews' => []
            ]);
            exit;
        }
        
        // Kiểm tra bảng có tồn tại không (PostgreSQL compatible)
        if (isPostgreSQL($conn)) {
            // PostgreSQL
            $checkTable = "SELECT table_name FROM information_schema.tables WHERE table_name = 'book_reviews'";
            $result = $conn->query($checkTable);
            $tableExists = $result && db_num_rows($result) > 0;
        } else {
            // MySQL
            $checkTable = "SHOW TABLES LIKE 'book_reviews'";
            $result = $conn->query($checkTable);
            $tableExists = $result && $result->num_rows > 0;
        }
        
        if (!$tableExists) {
            // Bảng chưa tồn tại, trả về mảng rỗng
            echo json_encode([
                'success' => true,
                'reviews' => [],
                'total_reviews' => 0,
                'average_rating' => 0,
                'message' => 'Bảng book_reviews chưa được tạo'
            ]);
            exit;
        }

        // Lấy đánh giá theo product_id (đổi từ accounts thành users)
        $sql = "SELECT br.*, u.fullname as user_name 
                FROM book_reviews br 
                LEFT JOIN users u ON br.user_id = u.id 
                WHERE br.product_id = " . (int)$product_id . " 
                ORDER BY br.created_at DESC";
        
        $result = db_query($conn, $sql);
        
        $reviews = [];
        $total_rating = 0;
        
        if ($result && db_num_rows($result) > 0) {
            while ($row = db_fetch_assoc($result)) {
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
                $total_rating += $row['rating'];
            }
        }
        
        $average_rating = count($reviews) > 0 ? round($total_rating / count($reviews), 1) : 0;
        
        echo json_encode([
            'success' => true,
            'reviews' => $reviews,
            'total_reviews' => count($reviews),
            'average_rating' => $average_rating
        ]);
        
        db_close($conn);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Method không được hỗ trợ'
        ]);
    }
?>
