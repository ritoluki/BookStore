# 🚀 URL Routing System - BOOK SHOP

## 📋 Tổng Quan

Hệ thống URL routing mới giúp website có URL đẹp, thân thiện với SEO và dễ nhớ thay vì URL dạng `index.php?page=...`.

## 🎯 Lợi Ích

- ✅ **SEO tốt hơn**: URL có ý nghĩa, dễ hiểu
- ✅ **Dễ nhớ**: Người dùng dễ nhớ và chia sẻ
- ✅ **Chuyên nghiệp**: Website trông chuyên nghiệp hơn
- ✅ **Dễ bảo trì**: Code sạch, dễ quản lý
- ✅ **Tương thích**: Vẫn hoạt động với hệ thống cũ

## 🔧 Cài Đặt

### 1. Yêu Cầu Hệ Thống
- Apache server với mod_rewrite được bật
- PHP 7.0 trở lên
- Quyền ghi file .htaccess

### 2. Files Đã Tạo
```
Bookstore_DATN/
├── .htaccess                    # Apache rewrite rules
├── src/utils/
│   ├── main.php                 # Include URL helper
│   └── url_helper.php           # URL helper functions
├── test_routing.php             # File test routing
└── URL_ROUTING_README.md        # Hướng dẫn này
```

### 3. Kiểm Tra mod_rewrite
Tạo file `test_rewrite.php` để kiểm tra:
```php
<?php
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "✅ mod_rewrite đã được bật";
    } else {
        echo "❌ mod_rewrite chưa được bật";
    }
} else {
    echo "⚠️ Không thể kiểm tra mod_rewrite";
}
?>
```

## 📚 Cách Sử Dụng

### 1. Functions Cơ Bản

#### Trang Chủ
```php
// Cách cũ
<a href="index.php">Trang chủ</a>

// Cách mới
<a href="<?php echo url(); ?>">Trang chủ</a>
<a href="<?php echo URLHelper::home(); ?>">Trang chủ</a>
```

#### Sản Phẩm
```php
// Danh sách sản phẩm
<a href="<?php echo url('san-pham'); ?>">Sản phẩm</a>

// Chi tiết sản phẩm
<a href="<?php echo url('san-pham/' . $slug); ?>">Chi tiết</a>
<a href="<?php echo URLHelper::product($slug); ?>">Chi tiết</a>
```

#### Giỏ Hàng & Thanh Toán
```php
// Giỏ hàng
<a href="<?php echo url('gio-hang'); ?>">Giỏ hàng</a>

// Thanh toán
<a href="<?php echo url('thanh-toan'); ?>">Thanh toán</a>
```

#### Tài Khoản
```php
// Đăng nhập
<a href="<?php echo url('dang-nhap'); ?>">Đăng nhập</a>

// Đăng ký
<a href="<?php echo url('dang-ky'); ?>">Đăng ký</a>
```

### 2. Assets (CSS, JS, Images)

```php
// CSS
<link rel="stylesheet" href="<?php echo css('main.css'); ?>">

// JavaScript
<script src="<?php echo js('main.js'); ?>"></script>

// Images
<img src="<?php echo image('logo.png'); ?>" alt="Logo">
```

### 3. Trong JavaScript

```javascript
// Chuyển hướng
window.location.href = '<?php echo url('gio-hang'); ?>';

// Ajax request
fetch('<?php echo url('api/products'); ?>')
    .then(response => response.json())
    .then(data => console.log(data));
```

### 4. Pagination

```php
// Trang tiếp theo
<a href="<?php echo URLHelper::nextPage($currentPage, $totalPages); ?>">Trang sau</a>

// Trang trước
<a href="<?php echo URLHelper::prevPage($currentPage); ?>">Trang trước</a>

// Trang cụ thể
<a href="<?php echo URLHelper::page($pageNumber); ?>">Trang <?php echo $pageNumber; ?></a>
```

## 🌐 URL Mapping

| URL Cũ | URL Mới | Function |
|--------|---------|----------|
| `index.php` | `/` | `url()` |
| `index.php?page=products` | `/san-pham` | `url('san-pham')` |
| `index.php?page=product&id=123` | `/san-pham/ten-san-pham` | `url('san-pham/' . $slug)` |
| `index.php?page=cart` | `/gio-hang` | `url('gio-hang')` |
| `index.php?page=checkout` | `/thanh-toan` | `url('thanh-toan')` |
| `index.php?page=login` | `/dang-nhap` | `url('dang-nhap')` |
| `index.php?page=signup` | `/dang-ky` | `url('dang-ky')` |
| `index.php?page=checkorder` | `/tra-cuu-don-hang` | `url('tra-cuu-don-hang')` |
| `index.php?page=search&q=...` | `/tim-kiem?q=...` | `url('tim-kiem?q=' . $query)` |

## 🔄 Migration Guide

### Bước 1: Backup
```bash
cp -r Bookstore_DATN Bookstore_DATN_backup
```

### Bước 2: Thay Thế Dần Dần
Thay thế từng phần một, không thay tất cả cùng lúc:

```php
// Thay thế này
<a href="index.php?page=cart">Giỏ hàng</a>

// Thành này
<a href="<?php echo url('gio-hang'); ?>">Giỏ hàng</a>
```

### Bước 3: Test
Sau mỗi thay đổi, test để đảm bảo hoạt động đúng.

### Bước 4: Cập Nhật Navigation
Cập nhật menu chính, footer, và các link quan trọng.

## 🧪 Testing

### 1. Test File
Truy cập: `http://localhost/Bookstore_DATN/test_routing.php`

### 2. Test URLs
- ✅ `/` → Trang chủ
- ✅ `/san-pham` → Danh sách sản phẩm
- ✅ `/gio-hang` → Giỏ hàng
- ✅ `/thanh-toan` → Thanh toán
- ✅ `/dang-nhap` → Đăng nhập

### 3. Test Assets
- ✅ CSS: `/assets/css/main.css`
- ✅ JS: `/assets/js/main.js`
- ✅ Images: `/assets/img/logo.png`

## ⚠️ Troubleshooting

### Lỗi 404
- Kiểm tra mod_rewrite đã được bật
- Kiểm tra file .htaccess có quyền đọc
- Kiểm tra Apache AllowOverride All

### URL không hoạt động
- Kiểm tra syntax trong .htaccess
- Kiểm tra đường dẫn trong URL helper
- Clear cache browser

### Assets không load
- Kiểm tra đường dẫn trong helper functions
- Kiểm tra file tồn tại
- Kiểm tra quyền đọc file

## 📝 Best Practices

### 1. Sử Dụng Helper Functions
```php
// ✅ Tốt
<a href="<?php echo url('san-pham'); ?>">Sản phẩm</a>

// ❌ Không tốt
<a href="san-pham">Sản phẩm</a>
```

### 2. Tạo Slug Tự Động
```php
// Tạo slug từ title
$slug = URLHelper::createSlug($product['title']);
$url = URLHelper::product($slug);
```

### 3. Sử Dụng Constants
```php
// Định nghĩa constants
define('ROUTE_PRODUCTS', 'san-pham');
define('ROUTE_CART', 'gio-hang');

// Sử dụng
<a href="<?php echo url(ROUTE_PRODUCTS); ?>">Sản phẩm</a>
```

### 4. Error Handling
```php
// Kiểm tra URL có hợp lệ
if (URLHelper::isValid($url)) {
    // Xử lý
} else {
    // Redirect về trang chủ
    header('Location: ' . URLHelper::home());
    exit();
}
```

## 🚀 Nâng Cấp

### 1. Thêm Routes Mới
```php
// Trong .htaccess
RewriteRule ^blog/?$ index.php?page=blog [L]
RewriteRule ^blog/([^/]+)/?$ index.php?page=blog-detail&slug=$1 [L]

// Trong URLHelper
public static function blog() {
    return self::baseUrl() . 'blog';
}

public static function blogDetail($slug) {
    return self::baseUrl() . 'blog/' . $slug;
}
```

### 2. API Routes
```php
// Trong .htaccess
RewriteRule ^api/products/?$ api/products.php [L]
RewriteRule ^api/orders/?$ api/orders.php [L]

// Trong URLHelper
public static function apiProducts() {
    return self::baseUrl() . 'api/products';
}
```

### 3. Language Support
```php
// Trong .htaccess
RewriteRule ^en/(.*)$ index.php?lang=en&page=$1 [L]
RewriteRule ^vi/(.*)$ index.php?lang=vi&page=$1 [L]

// Trong URLHelper
public static function localizedUrl($path, $lang = 'vi') {
    return self::baseUrl() . $lang . '/' . $path;
}
```

## 📞 Hỗ Trợ

Nếu gặp vấn đề:
1. Kiểm tra file log Apache
2. Test từng URL một
3. Backup và restore nếu cần
4. Liên hệ support

## 🔄 Changelog

- **v1.0.0**: Tạo hệ thống routing cơ bản
- **v1.0.1**: Thêm helper functions
- **v1.0.2**: Thêm test file và documentation

---

**Lưu ý**: Đây là hệ thống routing đơn giản, phù hợp cho website nhỏ và vừa. Với website lớn, bạn có thể cần framework routing chuyên nghiệp hơn.
