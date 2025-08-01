<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Connect to database
require_once '../../config/config.php';

// Get data from request body
$requestData = json_decode(file_get_contents('php://input'), true);

if (!isset($requestData['orderId']) || !isset($requestData['paymentStatus'])) {
    // Return error if required parameters are missing
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Get parameters from request
$orderId = $requestData['orderId'];
$paymentStatus = (int)$requestData['paymentStatus']; // Ensure it's an integer

// Validate payment status (should be 0 or 1)
if ($paymentStatus !== 0 && $paymentStatus !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid payment status value. Must be 0 or 1.'
    ]);
    exit;
}

try {
    // Prepare SQL statement to update the payment status
    $sql = "UPDATE `order` SET payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("is", $paymentStatus, $orderId);
    
    // Execute the update
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Successfully updated
            echo json_encode([
                'success' => true,
                'message' => 'Payment status updated successfully',
                'data' => [
                    'orderId' => $orderId,
                    'paymentStatus' => $paymentStatus
                ]
            ]);
        } else {
            // No rows affected (order ID not found)
            echo json_encode([
                'success' => false,
                'message' => 'Order not found or payment status already set to this value',
                'affected_rows' => $stmt->affected_rows
            ]);
        }
    } else {
        // Error executing the statement
        throw new Exception("Error executing statement: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?> 