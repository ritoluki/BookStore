<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi để tránh HTML trong JSON response
error_reporting(0);
ini_set('display_errors', 0);

// Log để debug
error_log("get_user_id.php được gọi");

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
        $phone = $input['phone'] ?? null;
        
        error_log("Tìm kiếm user với phone: " . $phone);
        
        if (!$phone) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu số điện thoại'
            ]);
            exit;
        }
        
        // Kiểm tra bảng users có tồn tại không (đã đổi từ accounts thành users)
        $checkTable = "SHOW TABLES LIKE 'users'";
        $result = $conn->query($checkTable);
        
        if ($result->num_rows == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Bảng users chưa được tạo'
            ]);
            exit;
        }
        
        // Thêm debug: kiểm tra tất cả user trong database
        $debugSql = "SELECT phone, fullname FROM users LIMIT 5";
        $debugResult = $conn->query($debugSql);
        if ($debugResult) {
            error_log("Một số user trong database:");
            while ($debugRow = $debugResult->fetch_assoc()) {
                error_log("Phone: " . $debugRow['phone'] . ", Name: " . $debugRow['fullname']);
            }
        }
        
        $sql = "SELECT id, fullname FROM users WHERE phone = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("Tìm thấy user ID: " . $row['id'] . " cho phone: " . $phone);
            echo json_encode([
                'success' => true,
                'user_id' => $row['id'],
                'user_name' => $row['fullname']
            ]);
        } else {
            error_log("Không tìm thấy user cho phone: " . $phone);
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy user với số điện thoại: ' . $phone
            ]);
        }
        
        if (isset($stmt)) $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Method không được hỗ trợ'
        ]);
    }

} catch (Exception $e) {
    error_log("Lỗi trong get_user_id.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}

if (isset($conn)) {
    $conn->close();
}
?>
