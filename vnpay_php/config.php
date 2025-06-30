<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
  
// Cấu hình theo thông tin Merchant của bạn
$vnp_TmnCode = "DLTZCNT3"; //Mã định danh merchant kết nối (Terminal Id)
$vnp_HashSecret = "L2DBGVM47JV0DBS2OCQB756IHSQVYK3R"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost/Bookstore_DATN/vnpay_php/vnpay_return.php";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));

// Định nghĩa hằng số kết nối DB để dùng cho các file khác
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', '');
if (!defined('DB_NAME')) define('DB_NAME', 'websach');
if (!defined('DB_PORT')) define('DB_PORT', 3308);
