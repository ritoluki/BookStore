-- Cập nhật cơ sở dữ liệu để hỗ trợ thanh toán VNPay
-- Chạy các lệnh SQL này nếu bảng order chưa có các cột payment_method và payment_status

-- Kiểm tra và thêm cột payment_method nếu chưa có
ALTER TABLE `order` ADD COLUMN IF NOT EXISTS `payment_method` varchar(50) DEFAULT 'COD' AFTER `trangthai`;

-- Kiểm tra và thêm cột payment_status nếu chưa có
ALTER TABLE `order` ADD COLUMN IF NOT EXISTS `payment_status` tinyint(4) DEFAULT 0 AFTER `payment_method`;

-- Cập nhật các đơn hàng hiện có để có giá trị mặc định
UPDATE `order` SET `payment_method` = 'COD' WHERE `payment_method` IS NULL;
UPDATE `order` SET `payment_status` = 0 WHERE `payment_status` IS NULL;

-- Thêm index để tối ưu truy vấn
ALTER TABLE `order` ADD INDEX IF NOT EXISTS `idx_payment_status` (`payment_status`);
ALTER TABLE `order` ADD INDEX IF NOT EXISTS `idx_payment_method` (`payment_method`);

-- Cập nhật comment cho các cột
ALTER TABLE `order` MODIFY COLUMN `payment_method` varchar(50) DEFAULT 'COD' COMMENT 'Phương thức thanh toán: COD, VNPay, etc.';
ALTER TABLE `order` MODIFY COLUMN `payment_status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái thanh toán: 0=chưa thanh toán, 1=đã thanh toán, 2=thất bại';
