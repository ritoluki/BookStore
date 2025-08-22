# Hướng dẫn Deploy Bookstore_DATN lên Heroku

## Bước 1: Chuẩn bị môi trường

### 1.1 Cài đặt Heroku CLI
```bash
# Windows (sử dụng Chocolatey)
choco install heroku

# Hoặc tải từ: https://devcenter.heroku.com/articles/heroku-cli
```

### 1.2 Đăng nhập Heroku
```bash
heroku login
```

### 1.3 Cài đặt Git (nếu chưa có)
```bash
# Kiểm tra git
git --version

# Nếu chưa có, tải từ: https://git-scm.com/
```

## Bước 2: Chuẩn bị dự án

### 2.1 Khởi tạo Git repository (nếu chưa có)
```bash
cd /c:/Xampp/htdocs/Bookstore_DATN
git init
git add .
git commit -m "Initial commit for Heroku deployment"
```

### 2.2 Cài đặt dependencies
```bash
composer install
```

## Bước 3: Tạo ứng dụng trên Heroku

### 3.1 Tạo ứng dụng mới
```bash
heroku create your-bookstore-app-name
```

### 3.2 Thêm buildpack PHP
```bash
heroku buildpacks:set heroku/php
```

### 3.3 Thêm PostgreSQL database
```bash
heroku addons:create heroku-postgresql:mini
```

## Bước 4: Cấu hình biến môi trường

### 4.1 Lấy thông tin database
```bash
heroku config:get DATABASE_URL
```

### 4.2 Cấu hình biến môi trường
```bash
heroku config:set DB_HOST=your-db-host
heroku config:set DB_USERNAME=your-db-username
heroku config:set DB_PASSWORD=your-db-password
heroku config:set DB_NAME=your-db-name
heroku config:set DB_PORT=5432
```

## Bước 5: Deploy ứng dụng

### 5.1 Push code lên Heroku
```bash
git add .
git commit -m "Deploy to Heroku"
git push heroku main
```

### 5.2 Kiểm tra logs
```bash
heroku logs --tail
```

### 5.3 Mở ứng dụng
```bash
heroku open
```

## Bước 6: Cấu hình database

### 6.1 Kết nối database
```bash
heroku pg:psql
```

### 6.2 Import database schema
```bash
# Trong psql shell
\i scripts/websach.sql
```

## Bước 7: Kiểm tra và test

### 7.1 Kiểm tra trạng thái
```bash
heroku ps
```

### 7.2 Kiểm tra logs
```bash
heroku logs --tail
```

### 7.3 Test ứng dụng
- Mở trình duyệt và truy cập URL của ứng dụng
- Kiểm tra các chức năng chính
- Kiểm tra kết nối database

## Lưu ý quan trọng

### 1. Database
- Heroku sử dụng PostgreSQL, không phải MySQL
- Cần chuyển đổi database schema nếu cần
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

## Troubleshooting

### Lỗi thường gặp:
1. **Buildpack error**: Kiểm tra buildpack PHP
2. **Database connection**: Kiểm tra biến môi trường
3. **File permissions**: Kiểm tra quyền file
4. **Memory limit**: Tăng dyno size nếu cần

### Lệnh hữu ích:
```bash
# Restart app
heroku restart

# Check config
heroku config

# Check buildpacks
heroku buildpacks

# Check addons
heroku addons
```

## Liên hệ hỗ trợ
- Heroku Support: https://help.heroku.com/
- Heroku Dev Center: https://devcenter.heroku.com/
- Heroku Status: https://status.heroku.com/
