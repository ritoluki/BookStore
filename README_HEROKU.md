# 🚀 Deploy Bookstore_DATN lên Heroku

## 📋 Yêu cầu hệ thống

- Windows 10/11
- PowerShell (có sẵn trên Windows)
- Git
- Composer
- Heroku CLI

## 🛠️ Cài đặt công cụ cần thiết

### 1. Cài đặt Heroku CLI
```powershell
# Sử dụng Chocolatey (khuyến nghị)
choco install heroku

# Hoặc tải từ trang chủ
# https://devcenter.heroku.com/articles/heroku-cli
```

### 2. Cài đặt Git
```powershell
# Sử dụng Chocolatey
choco install git

# Hoặc tải từ: https://git-scm.com/
```

### 3. Cài đặt Composer
```powershell
# Tải từ: https://getcomposer.org/
# Chạy installer và làm theo hướng dẫn
```

## 🚀 Deploy tự động (Khuyến nghị)

### Bước 1: Mở PowerShell với quyền Administrator
- Nhấn `Win + X` → "Windows PowerShell (Admin)"

### Bước 2: Di chuyển đến thư mục dự án
```powershell
cd "C:\Xampp\htdocs\Bookstore_DATN"
```

### Bước 3: Chạy script deploy
```powershell
.\deploy-to-heroku.ps1
```

### Bước 4: Làm theo hướng dẫn
- Nhập tên ứng dụng Heroku (hoặc để trống để tự động tạo)
- Nhập tên database (mặc định: websach)
- Chờ quá trình hoàn tất

## 🔧 Deploy thủ công

### Bước 1: Đăng nhập Heroku
```powershell
heroku login
```

### Bước 2: Khởi tạo Git repository
```powershell
git init
git add .
git commit -m "Initial commit for Heroku"
```

### Bước 3: Tạo ứng dụng Heroku
```powershell
heroku create your-app-name
```

### Bước 4: Thêm buildpack PHP
```powershell
heroku buildpacks:set heroku/php
```

### Bước 5: Thêm PostgreSQL database
```powershell
heroku addons:create heroku-postgresql:mini
```

### Bước 6: Cấu hình biến môi trường
```powershell
# Lấy thông tin database
heroku config:get DATABASE_URL

# Cấu hình biến môi trường
heroku config:set DB_HOST=your-db-host
heroku config:set DB_USERNAME=your-db-username
heroku config:set DB_PASSWORD=your-db-password
heroku config:set DB_NAME=your-db-name
heroku config:set DB_PORT=5432
```

### Bước 7: Deploy ứng dụng
```powershell
git push heroku main
```

### Bước 8: Mở ứng dụng
```powershell
heroku open
```

## 📊 Kiểm tra và quản lý

### Xem logs
```powershell
heroku logs --tail
```

### Kiểm tra trạng thái
```powershell
heroku ps
```

### Xem cấu hình
```powershell
heroku config
```

### Khởi động lại ứng dụng
```powershell
heroku restart
```

### Xem addons
```powershell
heroku addons
```

## 🗄️ Cấu hình Database

### Kết nối database
```powershell
heroku pg:psql
```

### Import schema (nếu có)
```sql
-- Trong psql shell
\i scripts/websach.sql
```

## ⚠️ Lưu ý quan trọng

### 1. Database
- Heroku sử dụng PostgreSQL, không phải MySQL
- Cần chuyển đổi schema nếu cần thiết
- Sử dụng biến môi trường DATABASE_URL

### 2. File uploads
- Heroku có filesystem ephemeral
- Cần sử dụng cloud storage (AWS S3, Cloudinary) cho file uploads

### 3. Email
- Cấu hình SMTP cho Heroku
- Sử dụng SendGrid hoặc Mailgun addon

### 4. SSL
- Heroku tự động cung cấp SSL
- Không cần cấu hình thêm

## 🐛 Troubleshooting

### Lỗi thường gặp:

1. **Buildpack error**
   ```powershell
   heroku buildpacks:clear
   heroku buildpacks:set heroku/php
   ```

2. **Database connection**
   ```powershell
   heroku config:get DATABASE_URL
   heroku config:set DB_HOST=...
   ```

3. **File permissions**
   ```powershell
   git add .
   git commit -m "Fix permissions"
   git push heroku main
   ```

4. **Memory limit**
   ```powershell
   heroku ps:scale web=1
   ```

## 📞 Hỗ trợ

- **Heroku Support**: https://help.heroku.com/
- **Heroku Dev Center**: https://devcenter.heroku.com/
- **Heroku Status**: https://status.heroku.com/
- **Documentation**: Xem file `HEROKU_DEPLOYMENT_GUIDE.md`

## 🎯 Kết quả mong đợi

Sau khi deploy thành công:
- ✅ Ứng dụng chạy trên Heroku
- ✅ Database PostgreSQL được kết nối
- ✅ SSL tự động được cấu hình
- ✅ URL: `https://your-app-name.herokuapp.com`

---

**Chúc bạn deploy thành công! 🎉**
