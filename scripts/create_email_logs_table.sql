-- Tạo bảng email_logs để theo dõi việc gửi email
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL,
  `email_type` varchar(50) NOT NULL COMMENT 'Loại email: payment_reminder, delivery_success, order_cancellation',
  `recipient_email` varchar(255) NOT NULL,
  `sent_at` datetime NOT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_email_type` (`email_type`),
  KEY `idx_sent_at` (`sent_at`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm comment cho bảng
ALTER TABLE `email_logs` COMMENT = 'Bảng log theo dõi việc gửi email';
