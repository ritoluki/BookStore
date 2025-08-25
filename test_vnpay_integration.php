<?php
/**
 * File test để kiểm tra tích hợp VNPay với order_success.php
 * Sử dụng để test các tham số VNPay mà không cần thực hiện thanh toán thật
 */

// Simulate VNPay return parameters
$test_params = array(
    'vnp_TxnRef' => 'DH999', // Mã đơn hàng test
    'vnp_ResponseCode' => '00', // 00 = thành công, khác 00 = thất bại
    'vnp_Amount' => '1000000', // 10,000 VNĐ (VNPay trả về số tiền * 100)
    'vnp_BankCode' => 'NCB', // Ngân hàng NCB
    'vnp_PayDate' => '20241201120000', // Thời gian thanh toán
    'vnp_TransactionNo' => '12345678', // Mã giao dịch VNPay
    'vnp_CardType' => 'ATM', // Loại thẻ
    'vnp_OrderInfo' => 'Thanh toan GD:DH999', // Thông tin đơn hàng
    'vnp_CurrCode' => 'VND', // Loại tiền
    'vnp_TmnCode' => 'DLTZCNT3', // Mã merchant
    'vnp_Command' => 'pay', // Lệnh thanh toán
    'vnp_CreateDate' => '20241201120000', // Thời gian tạo
    'vnp_IpAddr' => '127.0.0.1', // IP khách hàng
    'vnp_Locale' => 'vn', // Ngôn ngữ
    'vnp_OrderType' => 'other', // Loại hàng hóa
    'vnp_ReturnUrl' => 'http://localhost/Bookstore_DATN/src/controllers/order_success.php', // URL return
    'vnp_ExpireDate' => '20241201121500', // Thời gian hết hạn
    'vnp_Version' => '2.1.0' // Phiên bản API
);

// Tạo hash để test
$vnp_HashSecret = "L2DBGVM47JV0DBS2OCQB756IHSQVYK3R";
$inputData = $test_params;
ksort($inputData);

$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
$test_params['vnp_SecureHash'] = $vnp_SecureHash;

// Tạo URL test
$test_url = 'http://localhost/Bookstore_DATN/src/controllers/order_success.php?' . http_build_query($test_params);

echo "<h1>Test VNPay Integration</h1>";
echo "<h2>Test Parameters:</h2>";
echo "<pre>";
print_r($test_params);
echo "</pre>";

echo "<h2>Hash Data:</h2>";
echo "<pre>$hashData</pre>";

echo "<h2>Secure Hash:</h2>";
echo "<pre>$vnp_SecureHash</pre>";

echo "<h2>Test URL:</h2>";
echo "<p><a href='$test_url' target='_blank'>Click here to test order_success.php with VNPay params</a></p>";

echo "<h2>Test Scenarios:</h2>";
echo "<h3>1. Test Success (ResponseCode = 00):</h3>";
echo "<p>Current test uses ResponseCode = 00 (success)</p>";

echo "<h3>2. Test Failure (ResponseCode != 00):</h3>";
$failure_params = $test_params;
$failure_params['vnp_ResponseCode'] = '07'; // Mã lỗi khác
$failure_params['vnp_SecureHash'] = hash_hmac('sha512', $hashData, $vnp_HashSecret);
$failure_url = 'http://localhost/Bookstore_DATN/src/controllers/order_success.php?' . http_build_query($failure_params);
echo "<p><a href='$failure_url' target='_blank'>Test failure scenario</a></p>";

echo "<h3>3. Test Invalid Hash:</h3>";
$invalid_params = $test_params;
$invalid_params['vnp_SecureHash'] = 'invalid_hash';
$invalid_url = 'http://localhost/Bookstore_DATN/src/controllers/order_success.php?' . http_build_query($invalid_params);
echo "<p><a href='$invalid_url' target='_blank'>Test invalid hash scenario</a></p>";

echo "<h2>Expected Results:</h2>";
echo "<ul>";
echo "<li><strong>Success:</strong> Green header, VNPay payment info displayed, payment_status = 1</li>";
echo "<li><strong>Failure:</strong> Red header, error message, payment_status = 2</li>";
echo "<li><strong>Invalid Hash:</strong> Red header, error message, payment_status = 2</li>";
echo "</ul>";

echo "<h2>Database Check:</h2>";
echo "<p>After testing, check the database to verify:</p>";
echo "<ul>";
echo "<li>payment_method is updated to 'VNPay'</li>";
echo "<li>payment_status is updated (1 for success, 2 for failure)</li>";
echo "<li>trangthai is updated to 1 for successful payments</li>";
echo "</ul>";

echo "<h2>Notes:</h2>";
echo "<ul>";
echo "<li>Make sure the database has the required columns (payment_method, payment_status)</li>";
echo "<li>Check that order_success.php can access the database</li>";
echo "<li>Verify that the VNPay hash secret matches the one in config.php</li>";
echo "<li>Test with both existing and non-existing order IDs</li>";
echo "</ul>";
?>
