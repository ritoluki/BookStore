<?php
header('Content-Type: application/json');

// Include config file
require_once 'config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['orderId'])) {
        throw new Exception('Không tìm thấy mã đơn hàng');
    }
    
    $orderId = $data['orderId'];
    
    // Get the current user's phone
    $userPhone = null;
    if (isset($data['userPhone'])) {
        $userPhone = $data['userPhone'];
    } elseif (isset($_COOKIE['userPhone'])) {
        $userPhone = $_COOKIE['userPhone'];
    }
    
    // Kiểm tra trạng thái đơn hàng
    $checkSql = "SELECT * FROM `order` WHERE id = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    if (!$stmt) {
        throw new Exception('Lỗi chuẩn bị truy vấn: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $orderId);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Lỗi thực thi truy vấn kiểm tra: ' . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
        exit;
    }

    $order = mysqli_fetch_assoc($result);
    
    // Check if order can be cancelled (status must be 0)
    if ($order['trangthai'] != 0) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không thể hủy']);
        exit;
    }
    
    // Check if the user is authorized to cancel this order
    // Admin can cancel any order, regular user can only cancel their own
    $isAdmin = isset($data['isAdmin']) && $data['isAdmin'] === true;
    if (!$isAdmin) {
        // Chỉ cần khách đăng nhập và số điện thoại nhận hàng trùng userPhone
        if ($userPhone === null || $order['sdtnhan'] !== $userPhone) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền hủy đơn hàng này']);
            exit;
        }
    }

    // Cập nhật trạng thái đơn hàng thành đã hủy (4)
    $updateSql = "UPDATE `order` SET trangthai = 4 WHERE id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    if (!$updateStmt) {
        throw new Exception('Lỗi chuẩn bị truy vấn cập nhật: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($updateStmt, "s", $orderId);
    if (!mysqli_stmt_execute($updateStmt)) {
        throw new Exception('Lỗi cập nhật trạng thái: ' . mysqli_stmt_error($updateStmt));
    }
    
    $affected = mysqli_stmt_affected_rows($updateStmt);
    
    if ($affected > 0) {
        // Hoàn trả số lượng về kho
        $sqlDetails = "SELECT product_id, soluong FROM orderDetails WHERE madon = ?";
        $stmtDetails = $conn->prepare($sqlDetails);
        $stmtDetails->bind_param("s", $orderId);
        $stmtDetails->execute();
        $resultDetails = $stmtDetails->get_result();
        while ($row = $resultDetails->fetch_assoc()) {
            $sqlUpdateProduct = "UPDATE products SET soluong = soluong + ? WHERE id = ?";
            $stmtUpdateProduct = $conn->prepare($sqlUpdateProduct);
            $soluong = intval($row['soluong']);
            $product_id = intval($row['product_id']);
            $stmtUpdateProduct->bind_param("ii", $soluong, $product_id);
            $stmtUpdateProduct->execute();
            $stmtUpdateProduct->close();
        }
        $stmtDetails->close();
        
        // Lấy thông tin email của khách hàng
        $getUserSql = "SELECT email FROM users WHERE id = ?";
        $userStmt = mysqli_prepare($conn, $getUserSql);
        $user_email = "";
        
        if ($userStmt) {
            mysqli_stmt_bind_param($userStmt, "s", $order['khachhang']);
            mysqli_stmt_execute($userStmt);
            $userResult = mysqli_stmt_get_result($userStmt);
            
            if ($userRow = mysqli_fetch_assoc($userResult)) {
                $user_email = $userRow['email'];
            }
            
            mysqli_stmt_close($userStmt);
        }
        
        // Gửi email thông báo hủy đơn hàng
        if (!empty($user_email)) {
            require_once 'order_mail_helper.php';
            sendOrderCancellationEmailByCustomer($order, $user_email);
        }

        // Truy vấn lại để lấy thông tin đơn hàng đã cập nhật
        $refreshSql = "SELECT * FROM `order` WHERE id = ?";
        $refreshStmt = mysqli_prepare($conn, $refreshSql);
        mysqli_stmt_bind_param($refreshStmt, "s", $orderId);
        mysqli_stmt_execute($refreshStmt);
        $refreshResult = mysqli_stmt_get_result($refreshStmt);
        $updatedOrder = mysqli_fetch_assoc($refreshResult);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Đã hủy đơn hàng thành công',
            'order' => [
                'id' => $updatedOrder['id'],
                'trangthai' => (int)$updatedOrder['trangthai'],
                'message' => 'Đơn hàng đã được cập nhật thành trạng thái ' . $updatedOrder['trangthai']
            ]
        ]);
        
        mysqli_stmt_close($refreshStmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không có đơn hàng nào được cập nhật']);
    }
    
    mysqli_stmt_close($updateStmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
} catch (Exception $e) {
    error_log("Lỗi hủy đơn hàng: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
    exit;
}
?> 