# Tích hợp VNPay với Order Success Page

## Tổng quan
Hệ thống đã được cập nhật để thanh toán VNPay cũng trả về trang `order_success.php` như thanh toán COD, thay vì sử dụng trang `vnpay_return.php` riêng biệt.

## Các thay đổi đã thực hiện

### 1. Cập nhật config VNPay
- File: `vnpay_php/config.php`
- Thay đổi: `$vnp_Returnurl` từ `vnpay_return.php` thành `order_success.php`

### 2. Cập nhật order_success.php
- Thêm xử lý tham số VNPay (`vnp_TxnRef`, `vnp_ResponseCode`, `vnp_SecureHash`)
- Xác thực hash từ VNPay
- Cập nhật trạng thái đơn hàng trong database
- Hiển thị thông tin thanh toán VNPay khi cần thiết
- Thay đổi giao diện dựa trên trạng thái thanh toán

### 3. Cơ sở dữ liệu
- Sử dụng các cột có sẵn: `payment_method`, `payment_status`
- `payment_method`: COD, VNPay
- `payment_status`: 0 (chưa thanh toán), 1 (thành công), 2 (thất bại)

## Luồng hoạt động

### Thanh toán COD
1. Người dùng chọn thanh toán COD
2. Đơn hàng được tạo với `payment_method = 'COD'`
3. Chuyển hướng đến `order_success.php?order_id=DHxxx`
4. Hiển thị thông tin đơn hàng bình thường

### Thanh toán VNPay
1. Người dùng chọn thanh toán VNPay
2. Đơn hàng được tạo với `payment_method = 'VNPay'`
3. Chuyển hướng đến cổng thanh toán VNPay
4. Sau khi thanh toán, VNPay trả về `order_success.php` với các tham số:
   - `vnp_TxnRef`: Mã đơn hàng
   - `vnp_ResponseCode`: Mã phản hồi (00 = thành công)
   - `vnp_SecureHash`: Hash bảo mật
   - Các tham số khác: `vnp_Amount`, `vnp_BankCode`, `vnp_PayDate`, etc.
5. `order_success.php` xác thực hash và cập nhật trạng thái
6. Hiển thị thông tin đơn hàng + thông tin thanh toán VNPay

## Cấu trúc URL

### COD
```
order_success.php?order_id=DHxxx
```

### VNPay
```
order_success.php?vnp_TxnRef=DHxxx&vnp_ResponseCode=00&vnp_SecureHash=abc123&...
```

## Xử lý lỗi

### Hash không khớp
- `payment_status = 2` (thất bại)
- Hiển thị thông báo "Thanh toán thất bại"
- Nút "Thử lại thanh toán" thay vì "Tra cứu đơn hàng"

### ResponseCode khác 00
- `payment_status = 2` (thất bại)
- Hiển thị thông báo lỗi tương ứng

## Bảo mật

- Xác thực hash HMAC SHA512 từ VNPay
- Sử dụng `vnp_HashSecret` từ config
- Kiểm tra `vnp_ResponseCode` để xác định thành công/thất bại

## Giao diện

### Header
- **Thành công**: Xanh lá, icon ✓, tiêu đề "Đặt hàng thành công!"
- **Thất bại**: Đỏ, icon ✕, tiêu đề "Thanh toán thất bại!"
- **COD**: Vàng, icon ⏰, tiêu đề "Đặt hàng thành công!"

### Thông tin VNPay (nếu có)
- Số tiền thanh toán
- Ngân hàng
- Thời gian thanh toán
- Mã giao dịch
- Loại thẻ
- Trạng thái thanh toán

## Cài đặt

1. Chạy file SQL: `scripts/update_payment_columns.sql` (nếu cần)
2. Đảm bảo `vnpay_php/config.php` đã được cập nhật
3. Kiểm tra quyền ghi vào database

## Testing

### Test COD
1. Tạo đơn hàng với thanh toán COD
2. Kiểm tra URL: `order_success.php?order_id=DHxxx`
3. Xác nhận hiển thị thông tin COD

### Test VNPay
1. Tạo đơn hàng với thanh toán VNPay
2. Hoàn thành thanh toán trên sandbox
3. Kiểm tra redirect về `order_success.php`
4. Xác nhận hiển thị thông tin VNPay

## Troubleshooting

### Lỗi hash không khớp
- Kiểm tra `vnp_HashSecret` trong config
- Đảm bảo thứ tự sắp xếp tham số đúng
- Kiểm tra encoding của các tham số

### Không hiển thị thông tin VNPay
- Kiểm tra biến `$vnpay_data` có được tạo không
- Kiểm tra `$payment_method` có đúng 'VNPay' không
- Xem log lỗi PHP

### Lỗi database
- Kiểm tra quyền ghi
- Kiểm tra cấu trúc bảng `order`
- Chạy file SQL cập nhật nếu cần
