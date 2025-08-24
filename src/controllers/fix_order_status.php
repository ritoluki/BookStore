<?php
header('Content-Type: application/json');

require_once '../../config/config.php';

try {
    // Check for any orders where status might be stored as a string
    $checkSql = "SELECT id, trangthai FROM "order"";
    $result = mysqli_query($conn, $checkSql);
    
    $issues = [];
    $fixed = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $status = $row['trangthai'];
        
        // Check if status is not numeric
        if (!is_numeric($status)) {
            $issues[] = [
                'id' => $id,
                'old_status' => $status,
                'type' => 'non_numeric'
            ];
            
            // Try to fix it by converting to integer
            $fixedStatus = (int)$status;
            $updateSql = "UPDATE "order" SET trangthai = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($stmt, "is", $fixedStatus, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $fixed[] = [
                    'id' => $id,
                    'old_status' => $status,
                    'new_status' => $fixedStatus,
                    'result' => 'success'
                ];
            } else {
                $fixed[] = [
                    'id' => $id,
                    'old_status' => $status,
                    'result' => 'failed',
                    'error' => mysqli_stmt_error($stmt)
                ];
            }
            
            mysqli_stmt_close($stmt);
        }
    }
    
    // Return result
    echo json_encode([
        'success' => true,
        'issues_found' => count($issues),
        'issues' => $issues,
        'fixed' => $fixed,
        'message' => 'Kiểm tra và sửa lỗi trạng thái đơn hàng hoàn tất.'
    ]);
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
    ]);
}
?> 