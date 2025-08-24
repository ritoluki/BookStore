# Bookstore DATN - Hệ thống bán sách trực tuyến

## Tổng quan dự án
Hệ thống bán sách trực tuyến được phát triển bằng PHP với database MySQL (local) và PostgreSQL (Heroku production).

## Cấu trúc database
- **Local Development**: MySQL 
- **Production (Heroku)**: PostgreSQL
- **Database Migration**: Tự động chuyển đổi giữa MySQL và PostgreSQL

## Hướng dẫn Deploy lên Heroku

### Bước 1: Chuẩn bị
```bash
# 1. Cài đặt Heroku CLI
# Download từ: https://devcenter.heroku.com/articles/heroku-cli

# 2. Đăng nhập Heroku
heroku login

# 3. Tạo app Heroku
heroku create bookstore-datn-rito

# 4. Set buildpack PHP
heroku buildpacks:set heroku/php --app bookstore-datn-rito
```

### Bước 2: Cấu hình Database
```bash
# 1. Thêm PostgreSQL add-on
heroku addons:create heroku-postgresql:essential-0 --app bookstore-datn-rito

# 2. Lấy thông tin database
heroku config:get DATABASE_URL --app bookstore-datn-rito
# Kết quả: postgres://username:password@host:port/database
```

### Bước 3: Deploy Code
```bash
# 1. Add changes
git add .

# 2. Commit changes 
git commit -m "Prepare for Heroku deployment"

# 3. Deploy branch lên Heroku main
git push heroku copilot-fix:main
```

### Bước 4: Migration Database
**Sử dụng DBeaver để migrate từ MySQL sang PostgreSQL:**

#### A. Kết nối Source (MySQL Local):
- **Host**: localhost
- **Port**: 3308
- **Database**: websach
- **Username**: root
- **Password**: (để trống)

#### B. Kết nối Target (PostgreSQL Heroku):
```bash
# Lấy thông tin từ DATABASE_URL
heroku config:get DATABASE_URL --app bookstore-datn-rito
```
- **Host**: cer3tutrbi7n1t.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com
- **Port**: 5432
- **Database**: dd8h6q47p5eua4
- **Username**: ua0jkfhl3p1fia
- **Password**: p756f0c958d7908f1665f4437396fdee30f12dd3ee3eb8f59bb1798dd108c51f2

#### C. Thực hiện Data Transfer:
1. Right-click vào database MySQL → **Tools** → **Data Transfer**
2. **Source**: MySQL websach
3. **Target**: PostgreSQL dd8h6q47p5eua4
4. **Chọn tất cả tables**
5. **Settings**:
   - ✅ Transfer table structure
   - ✅ Transfer table data  
   - ✅ Transfer auto-generated columns
   - ✅ Use transactions
6. **Start Transfer**

### Bước 5: Cấu hình Dual Database

#### File Structure:
```
config/
├── config.php          # Auto-detect environment
├── config.local.php     # MySQL cho local
├── config.heroku.php    # PostgreSQL cho Heroku
└── db_helper.php        # Universal database functions
```

#### config/config.php:
```php
<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include database helper functions
require_once __DIR__ . '/db_helper.php';

// Auto-detect environment and load appropriate config
if (getenv('DATABASE_URL') || getenv('DB_HOST')) {
    // Heroku environment - use PostgreSQL
    require_once 'config.heroku.php';
} else {
    // Local environment - use MySQL
    require_once 'config.local.php';
}
?>
```

#### config/config.local.php:
```php
<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Database configuration for LOCAL MySQL
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'websach';
$port = 3308;

// Create MySQL connection
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

// Set time zone for MySQL
mysqli_query($conn, "SET time_zone = '+07:00'");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

#### config/config.heroku.php:
```php
<?php
// Set default timezone for PHP
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Database configuration for HEROKU PostgreSQL
$servername = getenv('DB_HOST') ?: 'cer3tutrbi7n1t.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com';
$username = getenv('DB_USERNAME') ?: 'ua0jkfhl3p1fia';
$password = getenv('DB_PASSWORD') ?: 'p756f0c958d7908f1665f4437396fdee30f12dd3ee3eb8f59bb1798dd108c51f2';
$dbname = getenv('DB_NAME') ?: 'dd8h6q47p5eua4';
$port = getenv('DB_PORT') ?: 5432;

// Create PostgreSQL connection
try {
    $dsn = "pgsql:host=$servername;port=$port;dbname=$dbname;user=$username;password=$password";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set timezone for PostgreSQL
    $pdo->exec("SET timezone = '+07:00'");
    
    // For backward compatibility, keep $conn variable
    $conn = $pdo;
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

### Bước 6: Universal Database Functions

#### config/db_helper.php:
```php
<?php
// Universal database helper functions for MySQL & PostgreSQL compatibility

function isPostgreSQL($conn) {
    return $conn instanceof PDO;
}

function db_query($conn, $sql) {
    if (isPostgreSQL($conn)) {
        return $conn->query($sql);
    } else {
        return mysqli_query($conn, $sql);
    }
}

function db_fetch_assoc($result) {
    if ($result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_ASSOC);
    } else {
        return mysqli_fetch_assoc($result);
    }
}

function db_num_rows($result) {
    if ($result instanceof PDOStatement) {
        return $result->rowCount();
    } else {
        return mysqli_num_rows($result);
    }
}

function db_insert_id($conn) {
    if (isPostgreSQL($conn)) {
        return $conn->lastInsertId();
    } else {
        return mysqli_insert_id($conn);
    }
}

function db_escape_string($conn, $string) {
    if (isPostgreSQL($conn)) {
        return $conn->quote($string);
    } else {
        return "'" . mysqli_real_escape_string($conn, $string) . "'";
    }
}

function db_close($conn) {
    if (isPostgreSQL($conn)) {
        $conn = null;
    } else {
        mysqli_close($conn);
    }
}
?>
```

### Bước 7: Sửa Database Syntax Issues

#### Thay thế MySQL Backticks với PostgreSQL:
```php
// ❌ MySQL syntax (không hoạt động với PostgreSQL)
$sql = "SELECT * FROM `order` WHERE id = 1";

// ✅ PostgreSQL compatible
$sql = "SELECT * FROM \"order\" WHERE id = 1";
```

#### Sử dụng db_helper functions:
```php
// ❌ MySQL specific
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    // process data
}

// ✅ Universal (works with both MySQL & PostgreSQL)
$result = db_query($conn, $sql);
while ($row = db_fetch_assoc($result)) {
    // process data
}
```

### Bước 8: Files cần thiết cho Heroku

#### composer.json:
```json
{
    "require": {
        "php": "^7.4|^8.0",
        "phpmailer/phpmailer": "^6.9",
        "ralouphie/getallheaders": "^3.0",
        "ext-pdo": "*",
        "ext-pdo_pgsql": "*"
    },
    "require-dev": {
        "heroku/heroku-buildpack-php": "*"
    },
    "scripts": {
        "post-install-cmd": [
            "php -r \"copy('apache_app.conf', 'apache_app.conf');\""
        ]
    },
    "config": {
        "optimize-autoloader": true
    }
}
```

#### Procfile:
```
web: vendor/bin/heroku-php-apache2
```

#### .htaccess:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Bước 9: Kiểm tra và Debug

#### Xem logs:
```bash
# Xem logs realtime
heroku logs --tail --app bookstore-datn-rito

# Xem logs cũ  
heroku logs -n 100 --app bookstore-datn-rito
```

#### Restart app:
```bash
heroku restart --app bookstore-datn-rito
```

#### Mở website:
```bash
heroku open --app bookstore-datn-rito
```

## Các vấn đề thường gặp và giải pháp

### 1. Lỗi PostgreSQL Syntax
**Vấn đề**: `syntax error at or near "`
**Nguyên nhân**: PostgreSQL không hỗ trợ backticks MySQL
**Giải pháp**: Thay `` `table` `` bằng `"table"` hoặc `table`

### 2. Lỗi mysqli functions với PDO
**Vấn đề**: `Argument #1 ($mysql) must be of type mysqli, PDO given`
**Nguyên nhân**: Code dùng mysqli functions với PDO connection
**Giải pháp**: Sử dụng db_helper universal functions

### 3. Heroku CLI không nhận diện
**Vấn đề**: `heroku command not found`
**Giải pháp**: 
- Cài đặt Heroku CLI từ trang chính thức
- Restart PowerShell sau khi cài

### 4. Procfile parse error
**Vấn đề**: `cannot parse Procfile`
**Nguyên nhân**: File encoding không đúng
**Giải pháp**: 
```bash
echo "web: vendor/bin/heroku-php-apache2" | Out-File -FilePath Procfile -Encoding ASCII
```

### 5. .htaccess BOM error
**Vấn đề**: `Invalid command '\xff\xfeR'`
**Nguyên nhân**: File có Byte Order Mark (BOM)
**Giải pháp**: Tạo lại file với encoding ASCII

## Tính năng chính

- ✅ Quản lý sách và danh mục
- ✅ Giỏ hàng và thanh toán
- ✅ Quản lý đơn hàng
- ✅ Hệ thống đánh giá sách
- ✅ Tìm kiếm và lọc sản phẩm
- ✅ Admin panel
- ✅ Dual database support (MySQL/PostgreSQL)

## Demo
- **Local**: http://localhost/Bookstore_DATN
- **Production**: https://bookstore-datn-rito-cd227f7b2037.herokuapp.com/

## Lưu ý quan trọng

1. **Database**: Luôn backup database trước khi migrate
2. **Environment**: Test kỹ trên local trước khi deploy
3. **Security**: Không commit database passwords vào Git
4. **Performance**: PostgreSQL có syntax khác MySQL, cần optimize queries
5. **Monitoring**: Thường xuyên check Heroku logs để phát hiện lỗi sớm

## Liên hệ
- **Developer**: ritoluki@gmail.com
- **GitHub**: https://github.com/username/Bookstore_DATN
- **Heroku App**: bookstore-datn-rito