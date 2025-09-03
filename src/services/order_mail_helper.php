<?php
require_once __DIR__ . '/send_mail.php';
require_once __DIR__ . '/../utils/main.php';

/**
 * Gửi email xác nhận đơn hàng - ĐÃ BỎ (thay bằng trang thành công)
 * 
 * @param array $order Thông tin đơn hàng
 * @param array $orderDetails Chi tiết sản phẩm trong đơn hàng
 * @param string $email Email người nhận
 * @param mysqli $conn Kết nối database
 * @return bool Kết quả gửi email
 */
/*
function sendOrderConfirmationEmail($order, $orderDetails, $email, $conn) {
    // Lấy chi tiết sản phẩm đã mua
        $products = [];
        $total = 0;
        foreach ($orderDetails as $detail) {
            $sql = "SELECT title FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $detail['id']);
        $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $products[] = [
                    'title' => $row['title'],
                    'quantity' => $detail['soluong'],
                    'price' => $detail['price'],
                    'subtotal' => $detail['price'] * $detail['soluong']
                ];
                $total += $detail['price'] * $detail['soluong'];
            }
        $stmt->close();
    }

    // Tạo bảng sản phẩm HTML
    $productTable = '';
    if (count($products) > 0) {
        $productTable = "<table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;margin-bottom:16px;'>
            <tr style='background:#f2f2f2;'>
                <th style='border:1px solid #ddd;padding:10px;text-align:left;'>Sản phẩm</th>
                <th style='border:1px solid #ddd;padding:10px;text-align:center;'>Số lượng</th>
                <th style='border:1px solid #ddd;padding:10px;text-align:right;'>Đơn giá</th>
                <th style='border:1px solid #ddd;padding:10px;text-align:right;'>Thành tiền</th>
            </tr>";
        foreach ($products as $product) {
            $productTable .= "<tr>
                <td style='border:1px solid #ddd;padding:10px;'>" . htmlspecialchars($product['title']) . "</td>
                <td style='border:10px;text-align:center;'>" . $product['quantity'] . "</td>
                <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($product['price']) . " đ</td>
                <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($product['subtotal']) . " đ</td>
            </tr>";
        }
        // Thêm dòng phí vận chuyển nếu có
        $phivanchuyen = 0;
        if (isset($order['hinhthucgiao']) && stripos($order['hinhthucgiao'], 'giao tận nơi') !== false) {
            // Nếu có trường phí vận chuyển riêng thì lấy, không thì mặc định 30000
            $phivanchuyen = isset($order['phivanchuyen']) ? (int)$order['phivanchuyen'] : 30000;
        }
        if ($phivanchuyen > 0) {
            $productTable .= "<tr>
                <td colspan='3' style='border:1px solid #ddd;padding:10px;text-align:right;'>Phí vận chuyển:</td>
                <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($phivanchuyen) . " đ</td>
            </tr>";
        }
        $productTable .= "<tr style='font-weight:bold;background:#f8f9fa;'>
                <td colspan='3' style='border:1px solid #ddd;padding:10px;text-align:right;'>Tổng cộng:</td>
                <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($total + $phivanchuyen) . " đ</td>
            </tr>
        </table>";
    }

    // Các trường động
    $customerName = htmlspecialchars($order['tenguoinhan']);
    $orderId = htmlspecialchars($order['id']);
    $orderDate = date('d/m/Y', strtotime($order['thoigiandat']));
    $orderTotal = number_format($order['tongtien']) . ' VNĐ';
    $shippingMethod = htmlspecialchars($order['hinhthucgiao']);

    $params = [
        'icon' => '✅',
        'headerColor' => '#28a745',
        'title' => 'Cảm Ơn Quý Khách!',
        'subtitle' => 'Đơn hàng của bạn đã được xác nhận thành công',
        'mainMessage' => 'Chúng tôi chân thành cảm ơn bạn đã tin tưởng và lựa chọn sản phẩm của chúng tôi.<br>Sự ủng hộ của bạn là động lực để chúng tôi không ngừng cải thiện chất lượng dịch vụ và mang đến những trải nghiệm mua sắm tuyệt vời nhất!',
        'order' => $order,
        'orderDetails' => $orderDetails,
        'productTable' => $productTable,
        'extraBlock' => '<table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbe6;border-radius:10px;padding:20px;margin:24px 0;"><tr><td style="font-size:16px;color:#d35400;font-weight:700;">🎁 Quà Tặng Đặc Biệt Dành Cho Bạn!</td></tr><tr><td style="color:#8b4513;font-size:15px;padding:8px 0;">Để tri ân khách hàng thân thiết, chúng tôi tặng bạn mã giảm giá <b>20%</b> cho lần mua hàng tiếp theo. Mã có hiệu lực trong 30 ngày!</td></tr><tr><td style="text-align:center;"><span style="background:#fff;padding:10px 24px;border-radius:30px;font-size:18px;font-weight:700;color:#d35400;border:2px dashed #f39c12;letter-spacing:2px;display:inline-block;">THANKS20</span></td></tr></table>',
        'footerNote' => 'Cảm ơn bạn đã lựa chọn chúng tôi! Nếu có bất kỳ thắc mắc nào, đừng ngần ngại liên hệ với đội ngũ chăm sóc khách hàng 24/7.',
        'button1' => '<a href="#" style="display:inline-block;padding:14px 32px;background:#28a745;color:#fff;border-radius:30px;text-decoration:none;font-weight:600;margin:0 8px;">Theo Dõi Đơn Hàng</a>',
        'button2' => '<a href="http://localhost/Bookstore_DATN/" style="display:inline-block;padding:14px 32px;background:#495057;color:#fff;border-radius:30px;text-decoration:none;font-weight:600;margin:0 8px;">Tiếp Tục Mua Sắm</a>'
    ];
    $body = renderOrderEmailTemplate($params);
    $subject = "Cảm Ơn Quý Khách Đã Mua Hàng - Đơn #" . htmlspecialchars($order['id']);
    $result = sendEmail($email, $subject, $body);
    if (!$result) {
        throw new Exception("Không thể gửi email xác nhận đơn hàng");
    }
    return true;
}
*/

/**
 * Gửi email thông báo hủy đơn hàng
 *
 * @param array $order Thông tin đơn hàng
 * @param string $email Email người nhận
 * @param string $cancelReason Lý do hủy đơn hàng
 * @return bool Kết quả gửi email
 */
function sendOrderCancellationEmail($order, $email, $cancelReason = '')
{
    global $conn;
    // Lấy chi tiết sản phẩm đã mua
    $products = [];
    $total = 0;
    if (isset($conn) && $conn) {
        $sql = "SELECT od.product_id, od.soluong, od.product_price, p.title FROM orderDetails od JOIN products p ON od.product_id = p.id WHERE od.madon = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $order['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                'title' => $row['title'],
                'quantity' => $row['soluong'],
                'price' => $row['product_price'],
                'subtotal' => $row['product_price'] * $row['soluong']
            ];
            $total += $row['product_price'] * $row['soluong'];
        }
        $stmt->close();
    }

    // Tạo bảng sản phẩm HTML
    $productTable = '';
    if (count($products) > 0) {
        $productTable = "<table style='width: 100%; border-collapse: collapse; margin-bottom: 16px;'>
            <tr style='background-color: #f2f2f2;'>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Sản phẩm</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: center;'>Số lượng</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Đơn giá</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Thành tiền</th>
            </tr>";
        foreach ($products as $product) {
            $productTable .= "<tr>
                <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($product['title']) . "</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: center;'>" . $product['quantity'] . "</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($product['price']) . " đ</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($product['subtotal']) . " đ</td>
            </tr>";
        }
        $productTable .= "<tr style='font-weight: bold; background-color: #f8f9fa;'>
                <td colspan='3' style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Tổng cộng:</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($total) . " đ</td>
            </tr>
        </table>";
    }

    // Các trường động
    $customerName = htmlspecialchars($order['tenguoinhan']);
    $orderId = htmlspecialchars($order['id']);
    $orderDate = date('d/m/Y', strtotime($order['thoigiandat']));
    $orderTotal = number_format($order['tongtien']) . ' VNĐ';
    $orderStatus = "<span style='color: #e74c3c;'>Đã Hủy</span>";
    $cancelReason = $cancelReason ? htmlspecialchars($cancelReason) : "Không xác định hoặc không được cung cấp";
    $refundInfo = "<table width='100%' cellpadding='0' cellspacing='0' style='background:#d4edda;border-radius:10px;padding:20px;margin:24px 0;'><tr><td style='font-size:16px;color:#155724;font-weight:700;'>💰 Thông Tin Hoàn Tiền</td></tr><tr><td style='color:#155724;font-size:15px;padding:8px 0;'>$orderTotal sẽ được hoàn lại vào tài khoản của bạn trong vòng <b>3-5 ngày làm việc</b>. Bạn sẽ nhận được thông báo xác nhận khi giao dịch hoàn tiền được thực hiện thành công.</td></tr></table>";

    $params = [
        'icon' => '🛍️',
        'headerColor' => '#ff6b6b',
        'title' => 'Thông Báo Hủy Đơn Hàng',
        'subtitle' => 'Chúng tôi rất tiếc phải thông báo về việc hủy đơn hàng của bạn',
        'mainMessage' => 'Chúng tôi rất tiếc phải thông báo rằng đơn hàng của bạn đã được hủy do một số lý do không thể tránh khỏi.<br>Chúng tôi hiểu sự bất tiện này có thể gây ra cho bạn và chân thành xin lỗi về điều này.',
        'order' => $order,
        'orderDetails' => [],
        'productTable' => $productTable,
        'extraBlock' => $refundInfo . '<div style="font-size:16px;color:#555;margin:24px 0 0 0;text-align:center;">Để bù đắp cho sự bất tiện này, chúng tôi xin gửi tặng bạn mã giảm giá <b>15%</b> cho lần mua hàng tiếp theo.</div>',
        'footerNote' => 'Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ hỗ trợ.',
    ];
    $body = renderOrderEmailTemplate($params);
    $subject = "Thông Báo Hủy Đơn Hàng #" . htmlspecialchars($order['id']) . " - Book Shop";
    $result = sendEmail($email, $subject, $body);
    if (!$result) {
        throw new Exception("Không thể gửi email hủy đơn hàng");
    }
    return true;
}

/**
 * Gửi email thông báo cập nhật trạng thái đơn hàng
 *
 * @param array $order Thông tin đơn hàng
 * @param string $email Email người nhận
 * @return bool Kết quả gửi email
 */
function sendOrderStatusUpdateEmail($order, $email)
{
    global $conn;
    try {
        // Xác định trạng thái đơn hàng và nội dung phù hợp
        $statusText = '';
        $mainMessage = '';
        $mainColor = '';
        $icon = '';
        switch ($order['trangthai']) {
            case 0:
                $statusText = 'Chưa xử lý';
                $mainMessage = 'Đơn hàng của bạn đang chờ được xử lý. Chúng tôi sẽ cập nhật sớm nhất!';
                $mainColor = '#f7c744';
                $icon = '⏳';
                break;
            case 1:
                $statusText = 'Đã xác nhận/Đang giao hàng';
                $mainMessage = '<b style="color:#27ae60;">Chúc mừng!</b> Đơn hàng của bạn đã được xác nhận và sẽ sớm được giao đến bạn.';
                $mainColor = '#27ae60';
                $icon = '🎉';
                break;
            case 3:
                $statusText = 'Đã hoàn thành';
                $mainMessage = '<b style="color:#27ae60;">Chúc mừng!</b> Đơn hàng của bạn đã được giao thành công. Chúng tôi rất cảm ơn vì đã tin tưởng mua hàng của chúng tôi.';
                $mainColor = '#27ae60';
                $icon = '🎉';
                break;
            case 4:
                $statusText = 'Đã hủy';
                $mainMessage = '<b style="color:#e74c3c;">Chúng tôi rất tiếc!</b> Đơn hàng của bạn đã bị hủy. Nếu có thắc mắc, vui lòng liên hệ hỗ trợ.';
                $mainColor = '#e74c3c';
                $icon = '😢';
                break;
            default:
                $statusText = 'Không xác định';
                $mainMessage = 'Trạng thái đơn hàng không xác định.';
                $mainColor = '#7f8c8d';
                $icon = 'ℹ️';
        }

        // Lấy chi tiết sản phẩm đã mua
        $products = [];
        $total = 0;
        if (isset($conn) && $conn) {
            $sql = "SELECT od.product_id, od.soluong, od.product_price, p.title FROM orderDetails od JOIN products p ON od.product_id = p.id WHERE od.madon = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $order['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $products[] = [
                    'title' => $row['title'],
                    'quantity' => $row['soluong'],
                    'price' => $row['product_price'],
                    'subtotal' => $row['product_price'] * $row['soluong']
                ];
                $total += $row['product_price'] * $row['soluong'];
            }
            $stmt->close();
        }

        // Tạo bảng sản phẩm HTML
        $productTable = '';
        if (count($products) > 0) {
            $productTable = "<h2 style='color: #2c3e50;'>Chi tiết đơn hàng</h2>
            <table style='width: 100%; border-collapse: collapse; margin-bottom: 16px;'>
                <tr style='background-color: #f2f2f2;'>
                    <th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Sản phẩm</th>
                    <th style='border: 1px solid #ddd; padding: 10px; text-align: center;'>Số lượng</th>
                    <th style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Đơn giá</th>
                    <th style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Thành tiền</th>
                </tr>";
            foreach ($products as $product) {
                $productTable .= "<tr>
                    <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($product['title']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: center;'>" . $product['quantity'] . "</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($product['price']) . " đ</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($product['subtotal']) . " đ</td>
                </tr>";
            }
            $productTable .= "<tr style='font-weight: bold; background-color: #f8f9fa;'>
                    <td colspan='3' style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Tổng cộng:</td>
                    <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($total) . " đ</td>
                </tr>
            </table>";
        }

        $subject = "Cập nhật trạng thái đơn hàng #" . $order['id'] . " - Book Shop";
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);'>
            <div style='text-align: center; margin-bottom: 32px;'>
                <div style='font-size: 48px; margin-bottom: 8px;'>$icon</div>
                <h1 style='color: $mainColor; margin: 0 0 8px 0;'>Đơn hàng #" . $order['id'] . "</h1>
                <p style='color: #555; font-size: 18px; margin: 0 0 8px 0;'>Trạng thái mới: <b style='color: $mainColor;'>$statusText</b></p>
                <div style='margin: 12px 0 0 0; font-size: 16px;'>$mainMessage</div>
            </div>
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 24px;'>
                <h2 style='color: #2c3e50; margin-top: 0; font-size: 20px;'>Thông tin đơn hàng</h2>
                <p><strong>Người nhận:</strong> " . htmlspecialchars($order['tenguoinhan']) . "</p>
                <p><strong>Số điện thoại:</strong> " . htmlspecialchars($order['sdtnhan']) . "</p>
                <p><strong>Địa chỉ:</strong> " . htmlspecialchars($order['diachinhan']) . "</p>
                $productTable
            </div>
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                <h3 style='color: #2c3e50;'>Liên hệ với chúng tôi</h3>
                <p>Email: <a href='mailto:bookshopdatn@gmail.com' style='color:#2980b9;'>bookshopdatn@gmail.com</a></p>
                <p>Hotline: <a href='tel:0123456789' style='color:#2980b9;'>0123 456 789</a></p>
                <p>Địa chỉ: Hoài Đức, Hà Nội</p>
            </div>
            <div style='text-align: center; margin-top: 20px; color: #7f8c8d; font-size: 12px;'>
                <p>Email này được gửi tự động, vui lòng không trả lời email này.</p>
                <p>© " . date('Y') . " Book Shop. All rights reserved.</p>
            </div>
        </div>";
        $result = sendEmail($email, $subject, $body);
        if (!$result) {
            throw new Exception("Không thể gửi email cập nhật trạng thái đơn hàng");
        }
        return true;
    } catch (Exception $e) {
        error_log("Lỗi gửi email cập nhật trạng thái đơn hàng: " . $e->getMessage());
        return false;
    }
}

function sendOrderCancellationEmailByCustomer($order, $email, $cancelReason = '')
{
    global $conn;
    // Lấy chi tiết sản phẩm đã mua
    $products = [];
    $total = 0;
    if (isset($conn) && $conn) {
        $sql = "SELECT od.product_id, od.soluong, od.product_price, p.title FROM orderDetails od JOIN products p ON od.product_id = p.id WHERE od.madon = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $order['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                'title' => $row['title'],
                'quantity' => $row['soluong'],
                'price' => $row['product_price'],
                'subtotal' => $row['product_price'] * $row['soluong']
            ];
            $total += $row['product_price'] * $row['soluong'];
        }
        $stmt->close();
    }

    // Tạo bảng sản phẩm HTML
    $productTable = '';
    if (count($products) > 0) {
        $productTable = "<table style='width: 100%; border-collapse: collapse; margin-bottom: 16px;'>
            <tr style='background-color: #f2f2f2;'>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Sản phẩm</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: center;'>Số lượng</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Đơn giá</th>
                <th style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Thành tiền</th>
            </tr>";
        foreach ($products as $product) {
            $productTable .= "<tr>
                <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($product['title']) . "</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: center;'>" . $product['quantity'] . "</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($product['price']) . " đ</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($product['subtotal']) . " đ</td>
            </tr>";
        }
        $productTable .= "<tr style='font-weight: bold; background-color: #f8f9fa;'>
                <td colspan='3' style='border: 1px solid #ddd; padding: 10px; text-align: right;'>Tổng cộng:</td>
                <td style='border: 1px solid #ddd; padding: 10px; text-align: right;'>" . number_format($total) . " đ</td>
            </tr>
        </table>";
    }

    // Các trường động
    $customerName = htmlspecialchars($order['tenguoinhan']);
    $orderId = htmlspecialchars($order['id']);
    $orderDate = date('d/m/Y', strtotime($order['thoigiandat']));
    $orderTotal = number_format($order['tongtien']) . ' VNĐ';
    $orderStatus = "<span style='color: #e74c3c;'>Đã Hủy</span>";
    $cancelReason = $cancelReason ? htmlspecialchars($cancelReason) : "Không xác định hoặc không được cung cấp";

    $params = [
        'icon' => '😢',
        'headerColor' => '#ff6b6b',
        'title' => 'Rất tiếc vì bạn đã hủy đơn hàng',
        'subtitle' => 'Chúng tôi xin lỗi vì trải nghiệm chưa tốt của bạn. Rắt mong bạn góp ý để chúng tôi phục vụ tốt hơn!',
        'mainMessage' => 'Chúng tôi rất tiếc khi biết bạn đã hủy đơn hàng <b>#' . htmlspecialchars($order['id']) . '</b> đặt ngày <b>' . date('d/m/Y', strtotime($order['thoigiandat'])) . '</b> với tổng tiền <b>' . number_format($order['tongtien']) . ' VNĐ</b>.<br>Lý do hủy: <i>' . ($cancelReason ? htmlspecialchars($cancelReason) : 'Không xác định hoặc không được cung cấp') . '</i><br><br><b>Chúng tôi luôn mong muốn mang lại trải nghiệm tốt nhất cho khách hàng.</b> Nếu bạn có góp ý hoặc lý do cụ thể, hãy chia sẻ với chúng tôi để dịch vụ ngày càng hoàn thiện hơn!',
        'order' => $order,
        'orderDetails' => [],
        'productTable' => $productTable,
        'extraBlock' => '',
        'footerNote' => 'Cảm ơn bạn đã ghé thăm Book Shop. Hy vọng sẽ được phục vụ bạn trong những lần tới!',
        'button1' => '<a href="https://yourstore.com/feedback" style="display:inline-block;padding:14px 32px;background:#667eea;color:#fff;border-radius:30px;text-decoration:none;font-weight:600;margin:0 8px;">Gửi góp ý cho chúng tôi</a>',
        'button2' => '<a href="mailto:support@yourstore.com" style="display:inline-block;padding:14px 32px;background:#495057;color:#fff;border-radius:30px;text-decoration:none;font-weight:600;margin:0 8px;">Liên Hệ Hỗ Trợ</a>'
    ];
    $body = renderOrderEmailTemplate($params);
    $subject = "Đơn hàng #" . htmlspecialchars($order['id']) . " đã được hủy - Book Shop";
    $result = sendEmail($email, $subject, $body);
    if (!$result) {
        throw new Exception("Không thể gửi email hủy đơn hàng");
    }
    return true;
}

function renderOrderEmailTemplate($params)
{
    // $params: [
    //   'icon', 'headerColor', 'title', 'subtitle', 'mainMessage', 'order', 'orderDetails', 'productTable', 'extraBlock', 'footerNote', 'button1', 'button2'
    // ]
    extract($params);
    $customerName = htmlspecialchars($order['tenguoinhan']);
    $orderId = htmlspecialchars($order['id']);
    $orderDate = date('d/m/Y', strtotime($order['thoigiandat']));
    $orderTotal = number_format($order['tongtien']) . ' VNĐ';
    $shippingMethod = isset($order['hinhthucgiao']) ? htmlspecialchars($order['hinhthucgiao']) : '';
    $mainMessage = $mainMessage ?? '';
    $extraBlock = $extraBlock ?? '';
    $footerNote = $footerNote ?? '';
    $button1 = $button1 ?? '';
    $button2 = $button2 ?? '';
    $subtitle = $subtitle ?? '';
    $headerColor = $headerColor ?? '#28a745';
    $icon = $icon ?? '✅';
    $title = $title ?? '';
    $productTable = $productTable ?? '';
    return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
</head>
<body style="background: #f4f4f4; padding: 20px;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:650px;background:#fff;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.08);overflow:hidden;">
        <tr>
            <td style="background:{$headerColor};padding:32px 24px;text-align:center;">
                <div style="font-size:48px;line-height:1;margin-bottom:16px;">{$icon}</div>
                <h1 style="color:#fff;font-size:28px;margin:0 0 8px 0;">{$title}</h1>
                <p style="color:#eafbe7;font-size:16px;margin:0;">{$subtitle}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:32px 24px;">
                <p style="font-size:18px;color:#2c3e50;font-weight:600;text-align:center;margin-bottom:24px;">
                    Xin chào Anh/Chị {$customerName},
                </p>
                <div style="font-size:16px;color:#555;text-align:center;margin-bottom:24px;">{$mainMessage}</div>
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:10px;padding:20px;margin-bottom:24px;">
                    <tr>
                        <td colspan="2" style="font-size:18px;color:{$headerColor};font-weight:700;padding-bottom:12px;">
                            📦 Thông Tin Đơn Hàng
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#888;">Mã Đơn Hàng:</td>
                        <td style="padding:6px 0;color:#2c3e50;font-weight:600;">#$orderId</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#888;">Ngày Đặt:</td>
                        <td style="padding:6px 0;color:#2c3e50;">$orderDate</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#888;">Tổng Tiền:</td>
                        <td style="padding:6px 0;color:#2c3e50;">$orderTotal</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#888;">Phương Thức:</td>
                        <td style="padding:6px 0;color:#2c3e50;">$shippingMethod</td>
                    </tr>
                </table>
                $productTable
                $extraBlock
                <p style="text-align:center;margin:32px 0;">
                    $button1 $button2
                </p>
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:10px;padding:20px;margin-bottom:24px;">
                    <tr>
                        <td style="font-size:16px;color:#2c3e50;font-weight:700;">
                            Liên Hệ Với Chúng Tôi
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#888;font-size:14px;">
                            Hotline: 1900-123-456 | Email: support@yourstore.com | Website: www.yourstore.com
                        </td>
                    </tr>
                </table>
                <p style="color:#aaa;font-size:13px;text-align:center;margin-top:24px;">
                    $footerNote<br>
                    © " . date('Y') . " YourStore. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

/**
 * Gửi mail theo trạng thái đơn hàng (chuẩn hóa, dùng template gốc của từng loại mail)
 * @param array $order
 * @param array $orderDetails
 * @param string $email
 * @param mysqli $conn
 * @param string $cancelReason
 * @return bool
 */
function sendOrderStatusEmailV2($order, $orderDetails, $email, $conn, $cancelReason = '')
{
    $status = isset($order['trangthai']) ? (int) $order['trangthai'] : -1;
    // Chỉ gửi mail cho trạng thái 1, 3, 4
    if ($status === 1) {
        // Xác nhận đơn hàng: dùng template gốc
        return sendOrderConfirmationEmail($order, $orderDetails, $email, $conn);
    } elseif ($status === 3) {
        // Hoàn thành: clone template xác nhận, sửa nội dung phù hợp và cộng phí ship nếu có
        $products = [];
        $total = 0;
        foreach ($orderDetails as $detail) {
            $sql = "SELECT title FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $detail['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $products[] = [
                    'title' => $row['title'],
                    'quantity' => $detail['soluong'],
                    'price' => $detail['price'],
                    'subtotal' => $detail['price'] * $detail['soluong']
                ];
                $total += $detail['price'] * $detail['soluong'];
            }
            $stmt->close();
        }
        // Tạo bảng sản phẩm HTML và cộng phí ship nếu có
        $productTable = '';
        $phivanchuyen = 0;
        if (isset($order['hinhthucgiao']) && stripos($order['hinhthucgiao'], 'giao tận nơi') !== false) {
            $phivanchuyen = isset($order['phivanchuyen']) ? (int) $order['phivanchuyen'] : 30000;
        }
        if (count($products) > 0) {
            $productTable = "<table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;margin-bottom:16px;'>
                <tr style='background:#f2f2f2;'>
                    <th style='border:1px solid #ddd;padding:10px;text-align:left;'>Sản phẩm</th>
                    <th style='border:1px solid #ddd;padding:10px;text-align:center;'>Số lượng</th>
                    <th style='border:1px solid #ddd;padding:10px;text-align:right;'>Đơn giá</th>
                    <th style='border:1px solid #ddd;padding:10px;text-align:right;'>Thành tiền</th>
                </tr>";
            foreach ($products as $product) {
                $productTable .= "<tr>
                    <td style='border:1px solid #ddd;padding:10px;'>" . htmlspecialchars($product['title']) . "</td>
                    <td style='border:1px solid #ddd;padding:10px;text-align:center;'>" . $product['quantity'] . "</td>
                    <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($product['price']) . " đ</td>
                    <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($product['subtotal']) . " đ</td>
                </tr>";
            }
            if ($phivanchuyen > 0) {
                $productTable .= "<tr>
                    <td colspan='3' style='border:1px solid #ddd;padding:10px;text-align:right;'>Phí vận chuyển:</td>
                    <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($phivanchuyen) . " đ</td>
                </tr>";
            }
            $productTable .= "<tr style='font-weight:bold;background:#f8f9fa;'>
                    <td colspan='3' style='border:1px solid #ddd;padding:10px;text-align:right;'>Tổng cộng:</td>
                    <td style='border:1px solid #ddd;padding:10px;text-align:right;'>" . number_format($total + $phivanchuyen) . " đ</td>
                </tr>
            </table>";
        }
        // Các trường động
        $customerName = htmlspecialchars($order['tenguoinhan']);
        $orderId = htmlspecialchars($order['id']);
        $orderDate = date('d/m/Y', strtotime($order['thoigiandat']));
        $orderTotal = number_format($order['tongtien']) . ' VNĐ';
        $shippingMethod = htmlspecialchars($order['hinhthucgiao']);
        $params = [
            'icon' => '🎉',
            'headerColor' => '#00bcd4',
            'title' => 'Đơn hàng của bạn đã hoàn thành',
            'subtitle' => 'Cảm ơn bạn đã tin tưởng và mua sắm tại Book Shop!',
            'mainMessage' => 'Chúng tôi xác nhận bạn đã nhận được đơn hàng #' . $orderId . '.<br>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi.<br>Nếu hài lòng, hãy để lại đánh giá hoặc góp ý để chúng tôi phục vụ tốt hơn!',
            'order' => $order,
            'orderDetails' => $orderDetails,
            'productTable' => $productTable,
            'extraBlock' => '<div style="font-size:16px;color:#555;margin:24px 0 0 0;text-align:center;">Bạn có thể đánh giá đơn hàng hoặc liên hệ hỗ trợ nếu cần.</div>',
            'footerNote' => 'Book Shop luôn mong muốn mang lại trải nghiệm tốt nhất cho khách hàng!',
            'button1' => '<a href="http://localhost/Bookstore_DATN/tra-cuu-don" style="display:inline-block;padding:14px 32px;background:#00bcd4;color:#fff;border-radius:30px;text-decoration:none;font-weight:600;margin:0 8px;">Xem Đơn Hàng</a>',
            'button2' => '<a href="mailto:support@yourstore.com" style="display:inline-block;padding:14px 32px;background:#495057;color:#fff;border-radius:30px;text-decoration:none;font-weight:600;margin:0 8px;">Liên Hệ Hỗ Trợ</a>'
        ];
        $body = renderOrderEmailTemplate($params);
        $subject = "Đơn hàng #$orderId đã hoàn thành - Book Shop";
        $result = sendEmail($email, $subject, $body);
        if (!$result) {
            throw new Exception("Không thể gửi email hoàn thành đơn hàng");
        }
        return true;
    } elseif ($status === 4) {
        // Đã hủy: dùng template gốc
        return sendOrderCancellationEmail($order, $email, $cancelReason);
    }
    // Không gửi mail cho trạng thái 2 hoặc trạng thái không xác định
    return false;
}

/**
 * Gửi email thông báo hủy đơn hàng bởi admin với lý do cụ thể
 * @param array $order Thông tin đơn hàng
 * @param string $email Email người nhận
 * @param string $reason Lý do hủy đơn hàng
 * @param bool $isAdmin Có phải admin hủy không
 * @return bool Kết quả gửi email
 */
function sendOrderCancellationEmailWithReason($order, $email, $reason, $isAdmin = true)
{
    global $conn;

    // Lấy chi tiết sản phẩm đã mua
    $products = [];
    $total = 0;
    if (isset($conn) && $conn) {
        $sql = "SELECT od.product_id, od.soluong, od.product_price, p.title FROM orderDetails od JOIN products p ON od.product_id = p.id WHERE od.madon = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $order['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
            $total += $row['soluong'] * $row['product_price'];
        }
        $stmt->close();
    }

    $cancelledBy = $isAdmin ? 'quản trị viên' : 'khách hàng';

    $subject = "Thông báo hủy đơn hàng #{$order['id']} - Book Shop";

    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
        <div style='text-align: center; margin-bottom: 30px;'>
            <h2 style='color: #dc3545; margin: 0;'>Thông báo hủy đơn hàng</h2>
            <p style='color: #666; margin: 10px 0 0 0;'>Đơn hàng #{$order['id']} đã bị hủy</p>
        </div>
        
        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
            <h3 style='color: #dc3545; margin-top: 0;'>Lý do hủy đơn hàng:</h3>
            <p style='font-size: 16px; color: #333; margin: 0; padding: 10px; background: white; border-radius: 4px; border-left: 4px solid #dc3545;'>{$reason}</p>
            <p style='font-size: 14px; color: #666; margin-top: 10px;'>Đơn hàng được hủy bởi: {$cancelledBy}</p>
        </div>
        
        <div style='margin-bottom: 20px;'>
            <h3 style='color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Thông tin đơn hàng đã hủy:</h3>
            <p><strong>Mã đơn hàng:</strong> {$order['id']}</p>
            <p><strong>Ngày đặt:</strong> " . date('d/m/Y H:i', strtotime($order['thoigiandat'])) . "</p>
            <p><strong>Tổng tiền:</strong> " . number_format($total, 0, ',', '.') . " VNĐ</p>
        </div>";

    if (!empty($products)) {
        $body .= "
        <div style='margin-bottom: 20px;'>
            <h3 style='color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Sản phẩm trong đơn hàng:</h3>
            <table style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr style='background-color: #f8f9fa;'>
                        <th style='padding: 12px; text-align: left; border: 1px solid #ddd;'>Sản phẩm</th>
                        <th style='padding: 12px; text-align: center; border: 1px solid #ddd;'>Số lượng</th>
                        <th style='padding: 12px; text-align: right; border: 1px solid #ddd;'>Đơn giá</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($products as $product) {
            $body .= "
                    <tr>
                        <td style='padding: 12px; border: 1px solid #ddd;'>{$product['title']}</td>
                        <td style='padding: 12px; text-align: center; border: 1px solid #ddd;'>{$product['soluong']}</td>
                        <td style='padding: 12px; text-align: right; border: 1px solid #ddd;'>" . number_format($product['product_price'], 0, ',', '.') . " VNĐ</td>
                    </tr>";
        }

        $body .= "
                </tbody>
            </table>
        </div>";
    }

    $body .= "
        <div style='background-color: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin-bottom: 20px;'>
            <h4 style='color: #856404; margin-top: 0;'>Xin lỗi vì sự bất tiện!</h4>
            <p style='color: #856404; margin: 0;'>Chúng tôi rất xin lỗi vì sự bất tiện này. Nếu đơn hàng đã được thanh toán, chúng tôi sẽ hoàn tiền trong vòng 3-5 ngày làm việc.</p>
        </div>
        
        <div style='text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
            <p style='margin: 0; color: #666;'>Cảm ơn bạn đã tin tưởng Book Shop!</p>
            <p style='margin: 5px 0 0 0; color: #666;'>Hotline hỗ trợ: <strong>0123 456 789</strong></p>
        </div>
    </div>";

    return sendEmail($email, $subject, $body);
}

/**
 * Gửi email nhắc nhở thanh toán
 * 
 * @param array $order Thông tin đơn hàng
 * @param array $orderDetails Chi tiết sản phẩm trong đơn hàng
 * @param string $email Email người nhận
 * @param mysqli $conn Kết nối database
 * @return bool Kết quả gửi email
 */
function sendPaymentReminderEmail($order, $orderDetails, $email, $conn)
{
    try {
        // Lấy thông tin sản phẩm
        $products = [];
        foreach ($orderDetails as $detail) {
            $sql = "SELECT title, img, category FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $detail['product_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if ($product) {
                $products[] = [
                    'title' => $product['title'],
                    'img' => $product['img'],
                    'category' => $product['category'],
                    'price' => $detail['price'],
                    'quantity' => $detail['quantity']
                ];
            }
        }

        // Tạo nội dung email
        $subject = 'Nhắc nhở thanh toán đơn hàng #' . $order['id'] . ' - BOOK SHOP';
        $body = createPaymentReminderEmailHTML($order, $products);
        $altBody = createPaymentReminderEmailText($order, $products);

        return sendEmail($email, $subject, $body);
    } catch (Exception $e) {
        error_log("Lỗi gửi email nhắc nhở thanh toán: " . $e->getMessage());
        return false;
    }
}

/**
 * Tạo HTML cho email nhắc nhở thanh toán
 */
function createPaymentReminderEmailHTML($order, $products)
{
    $giamgia = isset($order['giamgia']) ? $order['giamgia'] : 0;
    $phigiaohang = isset($order['phigiaohang']) ? $order['phigiaohang'] : 0;
    $orderTotal = number_format($order['tongtien'] - $giamgia + $phigiaohang, 0, ',', '.');

    $html = '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nhắc nhở thanh toán</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #dc3545; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .product-item { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #dc3545; }
            .total { background: #dc3545; color: white; padding: 20px; border-radius: 8px; text-align: center; font-size: 18px; font-weight: bold; }
            .cta-button { display: inline-block; background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>⚠️ Nhắc nhở thanh toán</h1>
                <p>Đơn hàng #' . $order['id'] . ' của bạn đang chờ thanh toán</p>
            </div>
            <div class="content">
                <p>Xin chào <strong>' . htmlspecialchars($order['tenguoinhan']) . '</strong>,</p>
                <p>Chúng tôi nhận thấy đơn hàng #' . $order['id'] . ' của bạn đã được đặt thành công nhưng chưa được thanh toán.</p>
                
                <h3>Chi tiết đơn hàng:</h3>';

    foreach ($products as $product) {
        $html .= '
                <div class="product-item">
                    <strong>' . htmlspecialchars($product['title']) . '</strong><br>
                    <small>Thể loại: ' . htmlspecialchars($product['category']) . '</small><br>
                    <small>Số lượng: ' . $product['quantity'] . ' x ' . number_format($product['price'], 0, ',', '.') . ' ₫</small>
                </div>';
    }

    $html .= '
                <div class="total">
                    Tổng cộng: ' . $orderTotal . ' ₫
                </div>
                
                <p><strong>Vui lòng hoàn tất thanh toán để chúng tôi có thể xử lý đơn hàng của bạn.</strong></p>
                
                                 <div style="text-align: center;">
                     <a href="http://localhost/Bookstore_DATN/index.php?page=checkout&order_id=' . $order['id'] . '" style="display: inline-block; background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3); transition: all 0.3s ease;">
                         💳 Thanh toán ngay
                     </a>
                 </div>
                
                <p><small>Nếu bạn đã thanh toán, vui lòng bỏ qua email này. Liên hệ với chúng tôi nếu cần hỗ trợ.</small></p>
            </div>
        </div>
    </body>
    </html>';

    return $html;
}

/**
 * Tạo text cho email nhắc nhở thanh toán
 */
function createPaymentReminderEmailText($order, $products)
{
    $giamgia = isset($order['giamgia']) ? $order['giamgia'] : 0;
    $phigiaohang = isset($order['phigiaohang']) ? $order['phigiaohang'] : 0;

    $text = "Nhắc nhở thanh toán đơn hàng #" . $order['id'] . "\n\n";
    $text .= "Xin chào " . $order['tenguoinhan'] . ",\n\n";
    $text .= "Chúng tôi nhận thấy đơn hàng #" . $order['id'] . " của bạn đã được đặt thành công nhưng chưa được thanh toán.\n\n";

    $text .= "Chi tiết đơn hàng:\n";
    foreach ($products as $product) {
        $text .= "- " . $product['title'] . " (" . $product['category'] . ") x" . $product['quantity'] . " - " . number_format($product['price'], 0, ',', '.') . " đ\n";
    }

    $text .= "\nTổng cộng: " . number_format($order['tongtien'] - $giamgia + $phigiaohang, 0, ',', '.') . " đ\n\n";
    $text .= "Vui lòng hoàn tất thanh toán để chúng tôi có thể xử lý đơn hàng của bạn.\n\n";
    $text .= "Thanh toán tại: http://localhost/Bookstore_DATN/index.php?page=checkout&order_id=" . $order['id'] . "\n\n";
    $text .= "Nếu bạn đã thanh toán, vui lòng bỏ qua email này.";

    return $text;
}

/**
 * Gửi email thông báo giao hàng thành công
 * 
 * @param array $order Thông tin đơn hàng
 * @param array $orderDetails Chi tiết sản phẩm trong đơn hàng
 * @param string $email Email người nhận
 * @param mysqli $conn Kết nối database
 * @return bool Kết quả gửi email
 */
function sendDeliverySuccessEmail($order, $orderDetails, $email, $conn)
{
    try {
        // Lấy thông tin sản phẩm
        $products = [];
        foreach ($orderDetails as $detail) {
            $sql = "SELECT title, img, category FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $detail['product_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if ($product) {
                $products[] = [
                    'title' => $product['title'],
                    'img' => $product['img'],
                    'category' => $product['category'],
                    'price' => $detail['price'],
                    'quantity' => $detail['quantity']
                ];
            }
        }

        // Tạo nội dung email
        $subject = 'Giao hàng thành công đơn hàng #' . $order['id'] . ' - BOOK SHOP';
        $body = createDeliverySuccessEmailHTML($order, $products);
        $altBody = createDeliverySuccessEmailText($order, $products);

        return sendEmail($email, $subject, $body);
    } catch (Exception $e) {
        error_log("Lỗi gửi email thông báo giao hàng thành công: " . $e->getMessage());
        return false;
    }
}

/**
 * Tạo HTML cho email thông báo giao hàng thành công
 */
function createDeliverySuccessEmailHTML($order, $products)
{
    $giamgia = isset($order['giamgia']) ? $order['giamgia'] : 0;
    $phigiaohang = isset($order['phigiaohang']) ? $order['phigiaohang'] : 0;
    $orderTotal = number_format($order['tongtien'] - $giamgia + $phigiaohang, 0, ',', '.');

    return '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Giao hàng thành công</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #28a745; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .product-item { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #28a745; }
            .total { background: #28a745; color: white; padding: 20px; border-radius: 8px; text-align: center; font-size: 18px; font-weight: bold; }
            .cta-button { display: inline-block; background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🎉 Giao hàng thành công!</h1>
                <p>Đơn hàng #' . $order['id'] . ' đã được giao đến bạn</p>
            </div>
            <div class="content">
                <p>Xin chào <strong>' . htmlspecialchars($order['tenguoinhan']) . '</strong>,</p>
                <p>Chúc mừng! Đơn hàng #' . $order['id'] . ' của bạn đã được giao thành công.</p>
                
                <h3>Chi tiết đơn hàng đã giao:</h3>';

    foreach ($products as $product) {
        $html .= '
                <div class="product-item">
                    <strong>' . htmlspecialchars($product['title']) . '</strong><br>
                    <small>Thể loại: ' . htmlspecialchars($product['category']) . '</small><br>
                    <small>Số lượng: ' . $product['quantity'] . ' x ' . number_format($product['price'], 0, ',', '.') . ' ₫</small>
                </div>';
    }

    $html .= '
                <div class="total">
                    Tổng cộng: ' . $orderTotal . ' ₫
                </div>
                
                <p><strong>Cảm ơn bạn đã tin tưởng BOOK SHOP!</strong></p>
                
                <div style="text-align: center;">
                    <a href="http://localhost/Bookstore_DATN/" class="cta-button">
                        Tiếp tục mua sắm
                    </a>
                </div>
                
                <p><small>Nếu có vấn đề gì với đơn hàng, vui lòng liên hệ với chúng tôi để được hỗ trợ.</small></p>
            </div>
        </div>
    </body>
    </html>';

    return $html;
}

/**
 * Tạo text cho email thông báo giao hàng thành công
 */
function createDeliverySuccessEmailText($order, $products)
{
    $giamgia = isset($order['giamgia']) ? $order['giamgia'] : 0;
    $phigiaohang = isset($order['phigiaohang']) ? $order['phigiaohang'] : 0;

    $text = "Giao hàng thành công đơn hàng #" . $order['id'] . "\n\n";
    $text .= "Xin chào " . $order['tenguoinhan'] . ",\n\n";
    $text .= "Chúc mừng! Đơn hàng #" . $order['id'] . " của bạn đã được giao thành công.\n\n";

    $text .= "Chi tiết đơn hàng đã giao:\n";
    foreach ($products as $product) {
        $text .= "- " . $product['title'] . " (" . $product['category'] . ") x" . $product['quantity'] . " - " . number_format($product['price'], 0, ',', '.') . " ₫\n";
    }

    $text .= "\nTổng cộng: " . number_format($order['tongtien'] - $giamgia + $phigiaohang, 0, ',', '.') . " ₫\n\n";
    $text .= "Cảm ơn bạn đã tin tưởng BOOK SHOP!\n\n";
    $text .= "Tiếp tục mua sắm tại: http://localhost/Bookstore_DATN/\n\n";
    $text .= "Nếu có vấn đề gì với đơn hàng, vui lòng liên hệ với chúng tôi để được hỗ trợ.";

    return $text;
}
?>