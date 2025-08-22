# Hướng dẫn Deploy Bookstore_DATN lên Heroku

## Yêu cầu hệ thống
- Windows 10/11
- PHP 7.4+ hoặc 8.0+
- Composer
- Git
- Heroku CLI

## Bước 1: Cài đặt Heroku CLI

### Cách 1: Tải từ trang chủ
1. Truy cập: https://devcenter.heroku.com/articles/heroku-cli
2. Tải file .exe cho Windows
3. Chạy file cài đặt
4. Khởi động lại PowerShell

### Cách 2: Sử dụng winget
```powershell
winget install --id=Heroku.HerokuCLI
```

### Cách 3: Sử dụng Chocolatey
```powershell
choco install heroku
```

## Bước 2: Đăng nhập Heroku
```powershell
heroku login
```

## Bước 3: Chuẩn bị dự án
Dự án đã được chuẩn bị sẵn với các file:
- `Procfile` - Cấu hình Heroku
- `composer.json` - Dependencies PHP
- `.htaccess` - Apache rewrite rules
- `app.json` - Cấu hình ứng dụng

## Bước 4: Deploy tự động
Chạy script PowerShell:
```powershell
.\deploy-to-heroku.ps1
```

## Bước 5: Deploy thủ công (nếu cần)

### Tạo app Heroku
```powershell
heroku create your-app-name
```

### Thêm buildpack PHP
```powershell
heroku buildpacks:set heroku/php
```

### Thêm PostgreSQL database
```powershell
heroku addons:create heroku-postgresql:mini
```

### Deploy code
```powershell
git push heroku main
```

## Bước 6: Cấu hình database

### Xem thông tin database
```powershell
heroku config:get DATABASE_URL
```

### Cấu hình biến môi trường
```powershell
heroku config:set DB_HOST=your-host
heroku config:set DB_USERNAME=your-username
heroku config:set DB_PASSWORD=your-password
heroku config:set DB_NAME=your-database
heroku config:set DB_PORT=5432
```

## Bước 7: Kiểm tra ứng dụng

### Mở ứng dụng
```powershell
heroku open
```

### Xem logs
```powershell
heroku logs --tail
```

### Kiểm tra trạng thái
```powershell
heroku ps
```

## Lệnh hữu ích

### Quản lý app
```powershell
heroku apps                    # Liệt kê apps
heroku info                    # Thông tin app
heroku restart                 # Khởi động lại
```

### Quản lý database
```powershell
heroku pg:info                # Thông tin PostgreSQL
heroku pg:psql                # Kết nối database
heroku pg:backups             # Quản lý backup
```

### Quản lý logs
```powershell
heroku logs                    # Xem logs
heroku logs --tail            # Xem logs real-time
heroku logs --source app      # Chỉ xem logs app
```

## Xử lý lỗi thường gặp

### Lỗi buildpack
```powershell
heroku buildpacks:clear
heroku buildpacks:set heroku/php
```

### Lỗi database connection
- Kiểm tra DATABASE_URL
- Cấu hình lại biến môi trường
- Restart app

### Lỗi 500 Internal Server Error
- Kiểm tra logs: `heroku logs --tail`
- Kiểm tra cấu hình database
- Kiểm tra file .htaccess

## Cập nhật ứng dụng

### Deploy lại sau khi thay đổi code
```powershell
git add .
git commit -m "Update message"
git push heroku main
```

### Rollback về version cũ
```powershell
heroku rollback
```

## Tài nguyên tham khảo
- [Heroku PHP Documentation](https://devcenter.heroku.com/categories/php)
- [Heroku CLI Documentation](https://devcenter.heroku.com/articles/heroku-cli)
- [PostgreSQL on Heroku](https://devcenter.heroku.com/articles/heroku-postgresql)
