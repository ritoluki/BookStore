<?php
header('Content-Type: application/json');

// Kết nối với cơ sở dữ liệu
require_once '../../config/config.php';

// Truy vấn để lấy thông tin người dùng và giỏ hàng của họ
$sql = "SELECT u.fullname, u.phone, u.password, u.address, u.email, u.status, u.join_date, u.usertype, c.product_id, c.quantity, c.note
        FROM users u
        LEFT JOIN cart c ON u.id = c.user_id";
$result = db_query($conn, $sql);

$accounts = array();

if ($result && db_num_rows($result) > 0) {
    while($row = db_fetch_assoc($result)) {
        $userId = $row['phone']; // Giả sử phone là unique và được dùng làm key

        // Kiểm tra nếu người dùng đã có trong mảng $accounts
        if (!isset($accounts[$userId])) {
            $accounts[$userId] = array(
                'fullname' => $row['fullname'],
                'phone' => $row['phone'],
                'password' => $row['password'],
                'address' => $row['address'],
                'email' => $row['email'],
                'status' => (int)$row['status'],
                'join_date' => (new DateTime($row['join_date']))->format(DateTime::ATOM),
                'cart' => [],
                'userType' => isset($row['userType']) ? (int)$row['userType'] : 0
            );
        }

        // Thêm sản phẩm vào giỏ hàng
        if ($row['product_id'] !== null) {
            $accounts[$userId]['cart'][] = array(
                'id' => $row['product_id'],
                'soluong' => (int)$row['quantity'],
                'note' => $row['note']
            );
        }
    }
}

// Chuyển đổi mảng kết hợp thành mảng số để sử dụng trong JSON
$accounts = array_values($accounts);

echo json_encode($accounts);

db_close($conn);
?>
