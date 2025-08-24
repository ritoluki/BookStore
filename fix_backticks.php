<?php
// Script để sửa tất cả backticks trong tên bảng
// Chạy: php fix_backticks.php

$files = [
    'src/controllers/get_products.php',
    'src/controllers/get_orders.php',
    'src/controllers/get_bestsellers.php',
    'src/controllers/get_discounted_products.php',
    'src/controllers/get_order.php',
    'src/controllers/add_order.php',
    'src/controllers/cancel_order.php',
    'src/controllers/cancel_order_admin.php',
    'src/controllers/delete_order.php',
    'src/controllers/fix_order_status.php',
    'src/controllers/order_success.php',
    'src/controllers/send_payment_reminder.php',
    'src/controllers/update_order_status.php',
    'src/controllers/update_payment_status.php',
    'vnpay_php/vnpay_return.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "Processing: $file\n";
        
        $content = file_get_contents($file);
        
        // Thay thế `order` bằng "order"
        $content = str_replace('`order`', '"order"', $content);
        
        // Thay thế các backticks khác nếu có
        $content = str_replace('`products`', 'products', $content);
        $content = str_replace('`users`', 'users', $content);
        $content = str_replace('`cart`', 'cart', $content);
        $content = str_replace('`orderdetails`', 'orderdetails', $content);
        $content = str_replace('`discounts`', 'discounts', $content);
        $content = str_replace('`discount_products`', 'discount_products', $content);
        
        file_put_contents($file, $content);
        echo "Fixed: $file\n";
    } else {
        echo "File not found: $file\n";
    }
}

echo "Done! All backticks have been replaced.\n";
?>
