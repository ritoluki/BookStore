# 🚀 Hướng dẫn Deploy Bookstore lên Server

## 📋 Tổng quan
Hệ thống đã được cập nhật để tự động detect môi trường và sử dụng đường dẫn phù hợp cho cả local và production.

## 🔧 Các thay đổi đã thực hiện

### 1. Cấu hình môi trường động
- **File mới**: `config/environment.php` - Tự động detect local/production
- **File cập nhật**: `config/config.php` - Sử dụng cấu hình từ environment
- **File cập nhật**: `vnpay_php/config.php` - Cấu hình VNPay động

### 2. Quản lý đường dẫn JavaScript
- **File mới**: `js/path-manager.js` - Quản lý đường dẫn động
- **File cập nhật**: `js/main.js` - Sử dụng PathManager
- **File cập nhật**: `js/checkout.js` - Sử dụng PathManager

### 3. Cập nhật HTML
- **File cập nhật**: `index.php` - Include PathManager
- **File cập nhật**: `vnpay_php/vnpay_return.php` - Redirect động

## 🌐 Cách hoạt động

### Local Environment (localhost)
```
BASE_URL: http://localhost/Bookstore_DATN
BASE_PATH: /Bookstore_DATN
API URLs: /Bookstore_DATN/src/controllers/...
```

### Production Environment
```
BASE_URL: https://yourdomain.com
BASE_PATH: (empty)
API URLs: /src/controllers/...
```

## 📝 Hướng dẫn Deploy

### Bước 1: Chuẩn bị Server
1. Upload toàn bộ code lên server
2. Cấu hình web server (Apache/Nginx) để trỏ domain về thư mục gốc
3. Đảm bảo PHP và MySQL đã được cài đặt

### Bước 2: Cấu hình Database
1. Tạo database `websach` trên server
2. Import file `db/websach.sql`
3. Cập nhật thông tin database trong `config/environment.php` (phần production)

### Bước 3: Cấu hình Production
Chỉnh sửa file `config/environment.php`:

```php
} else {
    // Cấu hình cho môi trường production
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $domain = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . '://' . $domain);
    define('BASE_PATH', ''); // Không cần subfolder trên production
    define('DB_HOST', 'localhost'); // Thay đổi theo server của bạn
    define('DB_PORT', 3306); // Port mặc định của MySQL
    define('DB_USER', 'your_db_user'); // Thay đổi theo server
    define('DB_PASSWORD', 'your_db_password'); // Thay đổi theo server
    define('DB_NAME', 'websach');
}
```

### Bước 4: Cấu hình VNPay (nếu sử dụng)
1. Đăng ký tài khoản VNPay production
2. Cập nhật `vnp_TmnCode` và `vnp_HashSecret` trong `vnpay_php/config.php`
3. Thay đổi `vnp_Url` từ sandbox sang production

### Bước 5: Kiểm tra quyền file
```bash
chmod 755 assets/img/products/
chmod 644 config/environment.php
```

## ✅ Kiểm tra sau khi deploy

### 1. Kiểm tra đường dẫn
- Truy cập trang chủ: `https://yourdomain.com`
- Kiểm tra các API endpoints hoạt động
- Test chức năng đăng nhập/đăng ký

### 2. Kiểm tra upload file
- Test upload ảnh sản phẩm
- Kiểm tra quyền ghi file trong thư mục `assets/img/products/`

### 3. Kiểm tra thanh toán
- Test thanh toán VNPay (nếu có)
- Kiểm tra redirect sau thanh toán

## 🔍 Troubleshooting

### Lỗi 404 - File not found
- Kiểm tra cấu hình web server
- Đảm bảo đường dẫn rewrite đúng

### Lỗi Database connection
- Kiểm tra thông tin database trong `config/environment.php`
- Đảm bảo MySQL service đang chạy

### Lỗi upload file
- Kiểm tra quyền thư mục `assets/img/products/`
- Kiểm tra cấu hình PHP upload limits

### JavaScript errors
- Kiểm tra console browser
- Đảm bảo `path-manager.js` được load trước các file khác

## 📞 Hỗ trợ
Nếu gặp vấn đề trong quá trình deploy, hãy kiểm tra:
1. Logs của web server
2. Console browser (F12)
3. Cấu hình PHP và MySQL
4. Quyền file và thư mục

---
**Lưu ý**: Hệ thống sẽ tự động detect môi trường và sử dụng cấu hình phù hợp. Không cần thay đổi code khi chuyển từ local sang production.
