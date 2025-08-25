<?php
/**
 * File test để demo URL routing mới
 * Truy cập: http://localhost/Bookstore_DATN/test_routing.php
 */

require_once 'src/utils/main.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test URL Routing - BOOK SHOP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .section h2 {
            color: #666;
            margin-top: 0;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .url-example {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
            border-left: 4px solid #007bff;
        }
        .url-example a {
            color: #007bff;
            text-decoration: none;
        }
        .url-example a:hover {
            text-decoration: underline;
        }
        .note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Test URL Routing Mới</h1>
        
        <div class="note">
            <strong>📝 Lưu ý:</strong> Để sử dụng URL đẹp, bạn cần:
            <ol>
                <li>Bật mod_rewrite trong Apache</li>
                <li>File .htaccess đã được tạo</li>
                <li>URL Helper đã được include</li>
            </ol>
        </div>

        <div class="section">
            <h2>🏠 Trang Chủ</h2>
            <div class="url-example">
                <strong>URL cũ:</strong> <a href="index.php">index.php</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url(); ?>"><?php echo url(); ?></a><br>
                <strong>Code:</strong> url() hoặc URLHelper::home()
            </div>
        </div>

        <div class="section">
            <h2>📚 Sản Phẩm</h2>
            <div class="url-example">
                <strong>Danh sách sản phẩm:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=products">index.php?page=products</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('san-pham'); ?>"><?php echo url('san-pham'); ?></a><br>
                <strong>Code:</strong> url('san-pham') hoặc URLHelper::products()
            </div>
            
            <div class="url-example">
                <strong>Chi tiết sản phẩm:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=product&id=123">index.php?page=product&id=123</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('san-pham/muon-kiep-nhan-sinh'); ?>"><?php echo url('san-pham/muon-kiep-nhan-sinh'); ?></a><br>
                <strong>Code:</strong> url('san-pham/' . $slug) hoặc URLHelper::product($slug)
            </div>
        </div>

        <div class="section">
            <h2>🛒 Giỏ Hàng & Thanh Toán</h2>
            <div class="url-example">
                <strong>Giỏ hàng:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=cart">index.php?page=cart</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('gio-hang'); ?>"><?php echo url('gio-hang'); ?></a><br>
                <strong>Code:</strong> url('gio-hang') hoặc URLHelper::cart()
            </div>
            
            <div class="url-example">
                <strong>Thanh toán:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=checkout">index.php?page=checkout</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('thanh-toan'); ?>"><?php echo url('thanh-toan'); ?></a><br>
                <strong>Code:</strong> url('thanh-toan') hoặc URLHelper::checkout()
            </div>
        </div>

        <div class="section">
            <h2>👤 Tài Khoản</h2>
            <div class="url-example">
                <strong>Đăng nhập:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=login">index.php?page=login</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('dang-nhap'); ?>"><?php echo url('dang-nhap'); ?></a><br>
                <strong>Code:</strong> url('dang-nhap') hoặc URLHelper::login()
            </div>
            
            <div class="url-example">
                <strong>Đăng ký:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=signup">index.php?page=signup</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('dang-ky'); ?>"><?php echo url('dang-ky'); ?></a><br>
                <strong>Code:</strong> url('dang-ky') hoặc URLHelper::signup()
            </div>
        </div>

        <div class="section">
            <h2>🔍 Tìm Kiếm & Tra Cứu</h2>
            <div class="url-example">
                <strong>Tìm kiếm:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=search&q=sach">index.php?page=search&q=sach</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('tim-kiem?q=sach'); ?>"><?php echo url('tim-kiem?q=sach'); ?></a><br>
                <strong>Code:</strong> url('tim-kiem?q=' . $query) hoặc URLHelper::search($query)
            </div>
            
            <div class="url-example">
                <strong>Tra cứu đơn hàng:</strong><br>
                <strong>URL cũ:</strong> <a href="index.php?page=checkorder">index.php?page=checkorder</a><br>
                <strong>URL mới:</strong> <a href="<?php echo url('tra-cuu-don-hang'); ?>"><?php echo url('tra-cuu-don-hang'); ?></a><br>
                <strong>Code:</strong> url('tra-cuu-don-hang') hoặc URLHelper::checkOrder()
            </div>
        </div>

        <div class="section">
            <h2>📁 Assets (CSS, JS, Images)</h2>
            <div class="url-example">
                <strong>CSS:</strong><br>
                <strong>URL cũ:</strong> <a href="assets/css/main.css">assets/css/main.css</a><br>
                <strong>URL mới:</strong> <a href="<?php echo css('main.css'); ?>"><?php echo css('main.css'); ?></a><br>
                <strong>Code:</strong> css('main.css') hoặc URLHelper::css('main.css')
            </div>
            
            <div class="url-example">
                <strong>JavaScript:</strong><br>
                <strong>URL cũ:</strong> <a href="assets/js/main.js">assets/js/main.js</a><br>
                <strong>URL mới:</strong> <a href="<?php echo js('main.js'); ?>"><?php echo js('main.js'); ?></a><br>
                <strong>Code:</strong> js('main.js') hoặc URLHelper::js('main.js')
            </div>
            
            <div class="url-example">
                <strong>Images:</strong><br>
                <strong>URL cũ:</strong> <a href="assets/img/logo.png">assets/img/logo.png</a><br>
                <strong>URL mới:</strong> <a href="<?php echo image('logo.png'); ?>"><?php echo image('logo.png'); ?></a><br>
                <strong>Code:</strong> image('logo.png') hoặc URLHelper::image('logo.png')
            </div>
        </div>

        <div class="section">
            <h2>🔧 Cách Sử Dụng</h2>
            <div class="url-example">
                <strong>1. Trong PHP:</strong><br>
                &lt;a href="&lt;?php echo url('san-pham'); ?&gt;"&gt;Sản phẩm&lt;/a&gt;
            </div>
            
            <div class="url-example">
                <strong>2. Trong JavaScript:</strong><br>
                window.location.href = '&lt;?php echo url('gio-hang'); ?&gt;';
            </div>
            
            <div class="url-example">
                <strong>3. Trong CSS:</strong><br>
                background-image: url('&lt;?php echo image('bg.jpg'); ?&gt;');
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Lưu ý quan trọng:</strong>
            <ul>
                <li>Đảm bảo Apache mod_rewrite đã được bật</li>
                <li>File .htaccess phải có quyền đọc</li>
                <li>Test từng URL để đảm bảo hoạt động đúng</li>
                <li>Backup code cũ trước khi thay thế</li>
            </ul>
        </div>

        <div class="success">
            <strong>✅ Lợi ích của URL đẹp:</strong>
            <ul>
                <li>SEO tốt hơn</li>
                <li>Dễ nhớ và chia sẻ</li>
                <li>Chuyên nghiệp hơn</li>
                <li>Dễ bảo trì và mở rộng</li>
            </ul>
        </div>

        <div class="section">
            <h2>🧪 Test URLs</h2>
            <p>Click vào các link bên dưới để test routing:</p>
            <div class="url-example">
                <a href="<?php echo url('san-pham'); ?>">📚 Sản phẩm</a> |
                <a href="<?php echo url('gio-hang'); ?>">🛒 Giỏ hàng</a> |
                <a href="<?php echo url('dang-nhap'); ?>">👤 Đăng nhập</a> |
                <a href="<?php echo url('thanh-toan'); ?>">💳 Thanh toán</a>
            </div>
        </div>
    </div>
</body>
</html>
