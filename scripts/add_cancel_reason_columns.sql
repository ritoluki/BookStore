-- Thêm cột lý do hủy đơn và người hủy vào bảng order
ALTER TABLE `order` 
ADD COLUMN `cancel_reason` TEXT NULL COMMENT 'Lý do hủy đơn hàng',
ADD COLUMN `cancelled_by` VARCHAR(20) NULL COMMENT 'Người hủy đơn: admin hoặc customer';

-- Cập nhật các đơn hàng đã hủy trước đó (nếu có)
UPDATE `order` 
SET `cancelled_by` = 'unknown' 
WHERE `trangthai` = 4 AND `cancelled_by` IS NULL;
