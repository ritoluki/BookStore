<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

try {
    $sql = "SELECT d.*, 
                   COUNT(dp.product_id) as product_count,
                   CASE 
                       WHEN d.status = 0 THEN 'inactive'
                       WHEN d.status = 1 AND NOW() < d.start_date THEN 'pending'
                       WHEN d.status = 1 AND NOW() BETWEEN d.start_date AND d.end_date THEN 'active'
                       WHEN d.status = 1 AND NOW() > d.end_date THEN 'expired'
                       ELSE 'unknown'
                   END as current_status
            FROM discounts d
            LEFT JOIN discount_products dp ON d.id = dp.discount_id
            GROUP BY d.id
            ORDER BY d.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $discounts = [];
    while ($row = $result->fetch_assoc()) {
        $discounts[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'discount_type' => $row['discount_type'],
            'discount_value' => (float)$row['discount_value'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'max_uses' => (int)$row['max_uses'],
            'current_uses' => (int)$row['current_uses'],
            'status' => (int)$row['status'],
            'current_status' => $row['current_status'],
            'product_count' => (int)$row['product_count'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'discounts' => $discounts
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
