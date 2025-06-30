<?php
// Include file cấu hình cơ sở dữ liệu
require_once __DIR__ . '/config.php';

/**
 * Hàm định dạng tiền tệ VND
 * 
 * @param float $amount Số tiền cần định dạng
 * @return string Chuỗi tiền đã định dạng
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

/**
 * Hàm lấy trạng thái đơn hàng dưới dạng chuỗi văn bản
 * 
 * @param int $status Mã trạng thái đơn hàng
 * @return string Tên trạng thái đơn hàng
 */
function getOrderStatus($status) {
    $statuses = [
        0 => 'Đã hủy',
        1 => 'Chờ xác nhận',
        2 => 'Đã xác nhận',
        3 => 'Đang vận chuyển',
        4 => 'Đã giao',
        5 => 'Hoàn thành'
    ];
    
    return isset($statuses[$status]) ? $statuses[$status] : 'Không xác định';
}

/**
 * Hàm tạo chuỗi ngẫu nhiên dùng cho ID đơn hàng
 * 
 * @param int $length Độ dài chuỗi
 * @return string Chuỗi ngẫu nhiên
 */
function generateRandomString($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>
