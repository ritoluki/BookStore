# 🚀 HƯỚNG DẪN SỬ DỤNG ENV FILES

## ✅ ĐÃ HOÀN THÀNH MIGRATION

Dự án đã được chuyển đổi từ `environment.php` sang sử dụng `.env` files.

---

## 📁 CẤU TRÚC FILES

```
Bookstore_DATN/
├── .env.local          ← Config cho local (XAMPP)
├── .env.production     ← Config cho production server
├── .env.example        ← Template (commit lên Git)
├── config/
│   ├── env_loader.php  ← Auto-detect và load .env file
│   └── config.php      ← Sử dụng env_loader
├── diagnostic.php      ← Test system
├── deploy.sh           ← Deploy script (Bash)
└── deploy.ps1          ← Deploy script (PowerShell)
```

---

## 🎯 CÁCH HOẠT ĐỘNG

### **Auto-Detection**
```php
// Tự động detect môi trường dựa trên HTTP_HOST
localhost/127.0.0.1 → Load .env.local
Anything else       → Load .env.production
```

### **Trên Local (XAMPP)**
- File sử dụng: `.env.local`
- Database: localhost:3308
- Debug: Enabled

### **Trên Production Server**
- File sử dụng: `.env.production` (copy thành `.env`)
- Database: localhost:3306
- Debug: Disabled

---

## 🧪 KIỂM TRA HỆ THỐNG

### **1. Chạy Diagnostic Tool**

**Qua Browser:**
```
http://localhost/Bookstore_DATN/diagnostic.php
```

**Qua Command Line:**
```bash
php diagnostic.php
```

Diagnostic sẽ kiểm tra:
- ✅ Environment detection
- ✅ Database connection
- ✅ PHP configuration
- ✅ Required extensions
- ✅ File permissions
- ✅ Composer dependencies
- ✅ Environment files

---

## 📝 SỬ DỤNG ENVIRONMENT VARIABLES

### **Trong PHP Code**

```php
// Load env config
$env_config = require 'config/env_loader.php';

// Access config
$db_host = $env_config['db_config']['host'];
$base_url = $env_config['base_url'];

// Hoặc dùng helper functions
$url = getBaseUrl();
$path = getBasePath();
$api = getApiUrl('get_products.php');
```

### **Helper Functions Có Sẵn**

```php
env($key, $default)         // Get env variable
isProduction()              // Check if production
isLocal()                   // Check if local
isDebug()                   // Check debug mode
getBaseUrl()                // Get base URL
getBasePath()               // Get base path
getAssetUrl($path)          // Get asset URL
getApiUrl($endpoint)        // Get API URL
logMessage($msg, $level)    // Log custom message
```

---

## 🚀 DEPLOY LÊN PRODUCTION

### **Lần Đầu Tiên**

**1. Push code lên GitHub:**
```bash
git add .
git commit -m "Migrate to .env configuration"
git push origin local-code
```

**2. SSH vào server:**
```bash
ssh root@178.128.127.19
```

**3. Clone hoặc pull code:**
```bash
cd /var/www/html
git clone -b local-code https://github.com/YOUR_USERNAME/BookStore.git Bookstore_DATN
cd Bookstore_DATN
```

**4. Install dependencies:**
```bash
composer install --no-dev --optimize-autoloader
```

**5. Copy .env.production thành .env:**
```bash
cp .env.production .env
chmod 600 .env
```

**6. Set permissions:**
```bash
chown -R www-data:www-data .
chmod -R 775 logs assets/img/products
```

**7. Test:**
```bash
php diagnostic.php
```

---

### **Các Lần Deploy Sau**

**Cách 1: Dùng Deploy Script (Khuyến nghị)**

**Windows PowerShell:**
```powershell
cd C:\Xampp\htdocs\Bookstore_DATN
.\deploy.ps1
```

**Git Bash:**
```bash
cd c:/Xampp/htdocs/Bookstore_DATN
chmod +x deploy.sh
./deploy.sh
```

**Cách 2: Manual**
```bash
# 1. Local: Push to GitHub
git add .
git commit -m "Your message"
git push origin local-code

# 2. Server: Pull changes
ssh root@178.128.127.19
cd /var/www/html/Bookstore_DATN
git pull origin local-code
composer install --no-dev --optimize-autoloader
cp .env.production .env
systemctl restart apache2
```

---

## 🔒 BẢO MẬT

### **QUAN TRỌNG: Files KHÔNG BAO GIỜ commit lên Git**
```
.env
.env.local
.env.production
```

### **File NÊN commit lên Git**
```
.env.example  ← Template cho team members
```

### **Permissions trên Production**
```bash
chmod 600 .env .env.production    # Chỉ owner đọc được
chmod 644 .env.example            # Public readable
```

---

## 🐛 TROUBLESHOOTING

### **❌ Lỗi: "Environment file not found"**
```bash
# Check file exists
ls -la .env*

# Nếu thiếu, copy từ template
cp .env.example .env.local     # Cho local
cp .env.example .env.production # Cho production
```

### **❌ Lỗi: "Missing required environment variables"**
```bash
# Check file content
cat .env.local

# Ensure all required vars are set:
# APP_ENV, APP_URL, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME
```

### **❌ Lỗi: Database connection failed**
```bash
# Check credentials in .env file
nano .env.local  # hoặc .env.production

# Test connection
php diagnostic.php
```

### **❌ Lỗi: "Class 'Dotenv\Dotenv' not found"**
```bash
# Install composer packages
composer install
```

---

## 📊 SO SÁNH: CŨ vs MỚI

| Aspect | Old (environment.php) | New (.env files) |
|--------|----------------------|------------------|
| **Security** | ❌ Credentials in PHP | ✅ Separate .env files |
| **Git Safety** | ⚠️ Easy to commit | ✅ .gitignore protected |
| **Flexibility** | ❌ Hard to change | ✅ Easy to modify |
| **Validation** | ❌ No validation | ✅ Auto validation |
| **Multi-env** | ⚠️ If/else logic | ✅ Separate files |
| **Best Practice** | ❌ Not recommended | ✅ Industry standard |

---

## 📚 TÀI LIỆU THAM KHẢO

- [PHP dotenv Documentation](https://github.com/vlucas/phpdotenv)
- [Twelve-Factor App Methodology](https://12factor.net/config)

---

## ✅ CHECKLIST

**Setup Local:**
- [x] Cài package `vlucas/phpdotenv`
- [x] Tạo file `.env.local`
- [x] Cập nhật `config/config.php`
- [x] Test với `diagnostic.php`
- [x] Website chạy OK trên local

**Setup Production:**
- [ ] Push code lên GitHub
- [ ] SSH vào server
- [ ] Clone/pull code
- [ ] `composer install`
- [ ] Copy `.env.production` → `.env`
- [ ] Set permissions
- [ ] Test với `diagnostic.php`
- [ ] Website chạy OK trên production

---

## 🎉 HOÀN THÀNH!

Dự án của bạn giờ đã sử dụng `.env` files - một cách **an toàn và chuyên nghiệp** để quản lý configuration.

**Câu hỏi?** Check `diagnostic.php` hoặc xem logs tại `logs/php-errors.log`
