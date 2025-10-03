# 📧 Hướng dẫn sử dụng Email nhắc nhở thanh toán

## 🎯 **Tính năng**
Tích hợp email nhắc nhở thanh toán vào trang quản lý đơn hàng của admin.

## ✨ **Cách hoạt động**

### **1. Trong modal chi tiết đơn hàng:**
- **Trước đây**: Button "Chưa thanh toán" 
- **Bây giờ**: Button "Gửi nhắc nhở" với icon envelope

### **2. Khi admin nhấn button:**
1. Button chuyển thành trạng thái loading: "Đang gửi..."
2. Gửi email nhắc nhở thanh toán đến khách hàng
3. Button chuyển thành "Đã gửi nhắc nhở" (màu xanh)
4. Sau 5 giây, button trở về trạng thái ban đầu

## 🔧 **Cài đặt**

### **1. Chạy SQL để tạo bảng email_logs:**
```sql
-- Chạy file: scripts/create_email_logs_table.sql
```

### **2. Kiểm tra file đã được tạo:**
- ✅ `src/controllers/send_payment_reminder.php`
- ✅ `js/admin.js` - đã cập nhật function
- ✅ `assets/css/admin.css` - đã thêm CSS

## 📧 **Cấu trúc email nhắc nhở**

### **Nội dung email:**
- ⚠️ Tiêu đề: "Nhắc nhở thanh toán đơn hàng #[Mã đơn]"
- 📦 Chi tiết sản phẩm đã đặt
- 💰 Tổng tiền cần thanh toán
- 🔗 Link thanh toán trực tiếp
- 📱 Responsive design

### **Template:**
- Sử dụng function `sendPaymentReminderEmail()` từ `order_mail_helper.php`
- HTML và text version đều có sẵn
- Sử dụng màu sắc và icon phù hợp

## 🎨 **Giao diện**

### **Button states:**
1. **Bình thường**: "Gửi nhắc nhở" (màu vàng)
2. **Loading**: "Đang gửi..." (màu xám, có spinner)
3. **Thành công**: "Đã gửi nhắc nhở" (màu xanh)
4. **Lỗi**: Trở về trạng thái ban đầu

### **CSS classes:**
- `.payment-reminder` - Style cho button gửi nhắc nhở
- `.loading` - Style cho trạng thái loading
- Animation spin cho icon loading

## 📊 **Logging và theo dõi**

### **Bảng email_logs:**
- `order_id`: Mã đơn hàng
- `email_type`: Loại email (payment_reminder)
- `recipient_email`: Email người nhận
- `sent_at`: Thời gian gửi
- `status`: Trạng thái (success/failed)
- `error_message`: Thông báo lỗi (nếu có)

### **Xem log:**
```sql
SELECT * FROM email_logs WHERE email_type = 'payment_reminder' ORDER BY sent_at DESC;
```

## 🚀 **Sử dụng**

### **1. Admin mở modal chi tiết đơn hàng**
### **2. Nhấn button "Gửi nhắc nhở"**
### **3. Hệ thống tự động:**
- Kiểm tra trạng thái đơn hàng
- Lấy thông tin khách hàng
- Gửi email nhắc nhở
- Cập nhật giao diện
- Ghi log

## ⚠️ **Lưu ý**

### **Điều kiện gửi email:**
- Đơn hàng chưa thanh toán (`payment_status = 0`)
- Đơn hàng chưa bị hủy (`trangthai != 4`)
- Khách hàng có email hợp lệ

### **Bảo mật:**
- Chỉ admin mới có thể gửi email nhắc nhở
- Kiểm tra quyền truy cập
- Ghi log đầy đủ để theo dõi

## 🔮 **Phát triển tương lai**

### **1. Tự động hóa:**
- Cron job gửi email tự động sau 24h
- Email nhắc nhở định kỳ (3 ngày, 7 ngày)

### **2. Tùy chỉnh:**
- Template email có thể chỉnh sửa
- Thời gian gửi nhắc nhở linh hoạt
- Tùy chọn gửi SMS kết hợp

### **3. Analytics:**
- Thống kê tỷ lệ thanh toán sau nhắc nhở
- Báo cáo hiệu quả email marketing
- A/B testing cho nội dung email

## 📝 **Troubleshooting**

### **Email không gửi được:**
1. Kiểm tra cấu hình SMTP trong `send_mail.php`
2. Kiểm tra log lỗi trong `email_logs`
3. Kiểm tra quyền ghi file log

### **Button không hoạt động:**
1. Kiểm tra console browser
2. Kiểm tra network tab
3. Kiểm tra file `admin.js` đã được cập nhật

### **CSS không áp dụng:**
1. Clear cache browser
2. Kiểm tra file `admin.css` đã được cập nhật
3. Kiểm tra đường dẫn CSS

---
*Tính năng được phát triển bởi AI Assistant - Cập nhật lần cuối: 2024*
