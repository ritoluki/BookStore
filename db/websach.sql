-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3308
-- Thời gian đã tạo: Th9 30, 2025 lúc 08:09 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `websach`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `book_reviews`
--

CREATE TABLE `book_reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` varchar(20) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `book_reviews`
--

INSERT INTO `book_reviews` (`id`, `user_id`, `product_id`, `order_id`, `rating`, `content`, `image`, `created_at`, `updated_at`) VALUES
(1, 34, 3, NULL, 4, 'test', NULL, '2025-05-26 12:36:04', '2025-05-26 12:36:04'),
(2, 1, 3, NULL, 5, 'test', NULL, '2025-05-26 12:38:09', '2025-05-26 12:38:09'),
(3, 1, 8, NULL, 5, '', NULL, '2025-05-26 12:48:12', '2025-05-26 12:48:12'),
(4, 1, 21, NULL, 5, 'Rất hay', NULL, '2025-05-26 14:00:03', '2025-05-26 14:00:03'),
(5, 1, 12, NULL, 3, 'Tân', NULL, '2025-06-03 15:31:44', '2025-06-03 15:31:44'),
(6, 1, 2, NULL, 4, '123', NULL, '2025-08-01 16:51:52', '2025-08-01 16:51:52'),
(7, 1, 1, NULL, 4, '1', NULL, '2025-08-01 16:54:03', '2025-08-01 16:54:03'),
(8, 1, 11, NULL, 5, 'testing 19/08', NULL, '2025-08-19 13:15:08', '2025-08-19 13:15:08'),
(9, 1, 9, NULL, 3, '25/08', NULL, '2025-08-25 17:26:52', '2025-08-25 17:26:52'),
(10, 1, 36, NULL, 4, '28', NULL, '2025-08-28 22:20:29', '2025-08-28 22:20:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `idcart` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sách Hay', 's-ch-hay', '', 1, 1, '2025-08-28 15:37:56', '2025-08-28 15:52:51'),
(2, 'Khoa học', 'khoa-hoc', NULL, 2, 1, '2025-08-28 15:37:56', '2025-08-28 16:24:15'),
(3, 'Tiểu Thuyết', 'ti-u-thuy-t', '', 6, 1, '2025-08-28 15:37:56', '2025-08-28 16:34:13'),
(4, 'Thiếu nhi', 'thieu-nhi', NULL, 4, 1, '2025-08-28 15:37:56', '2025-08-28 16:24:15'),
(9, 'Văn học', 'v-n-h-c', 'Sách văn học trong và ngoài nước', 7, 1, '2025-08-28 15:56:33', '2025-08-28 16:34:20'),
(10, 'Kinh tế', 'kinh-t-', 'Sách về kinh tế, kinh doanh', 3, 1, '2025-08-28 15:56:33', '2025-08-28 16:33:42'),
(13, 'Kỹ năng sống', 'ky-nang-song', 'Sách phát triển bản thân', 5, 0, '2025-08-28 15:56:33', '2025-08-28 15:56:33'),
(14, 'Danh mục test', 'danh-m-c-test', 'Mô tả test', 0, 0, '2025-08-28 16:01:56', '2025-08-28 16:33:16'),
(15, 'we are c', 'we-are-c', '', 8, 0, '2025-08-28 16:07:39', '2025-08-28 16:34:30'),
(26, 'quảng bá', 'qu-ng-b-', '', 12, 1, '2025-09-03 09:57:16', '2025-09-03 09:57:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Tên chương trình giảm giá',
  `description` text DEFAULT NULL COMMENT 'Mô tả chương trình',
  `discount_type` enum('percentage','fixed_amount') NOT NULL DEFAULT 'percentage' COMMENT 'Loại giảm giá: % hoặc số tiền cố định',
  `discount_value` decimal(10,2) NOT NULL COMMENT 'Giá trị giảm (nếu % thì nhập 20 = 20%, nếu tiền thì nhập 50000)',
  `min_order_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Giá sản phẩm tối thiểu để được áp dụng giảm giá',
  `start_date` datetime NOT NULL COMMENT 'Ngày bắt đầu giảm giá',
  `end_date` datetime NOT NULL COMMENT 'Ngày kết thúc giảm giá',
  `max_uses` int(11) DEFAULT 0 COMMENT 'Số lượng tối đa có thể sử dụng (0 = không giới hạn)',
  `current_uses` int(11) DEFAULT 0 COMMENT 'Số lượng đã sử dụng',
  `status` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái: 1 = active, 0 = inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `discounts`
--

INSERT INTO `discounts` (`id`, `name`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `start_date`, `end_date`, `max_uses`, `current_uses`, `status`, `created_at`, `updated_at`) VALUES
(18, 'aaaa', 'aa', 'percentage', 50.00, 0.00, '2025-08-19 14:03:00', '2025-08-22 14:03:00', 0, 21, 1, '2025-08-20 07:03:23', '2025-08-22 02:57:34'),
(20, 'Test discount', '', 'percentage', 20.00, 0.00, '2025-08-24 08:55:00', '2025-08-26 08:55:00', 0, 24, 1, '2025-08-25 01:56:21', '2025-08-26 01:18:33'),
(21, 'Giảm giá 2/9', 'Giảm giá mừng 2/9', 'percentage', 20.00, 0.00, '2025-09-02 09:02:00', '2025-09-05 09:02:00', 0, 40, 0, '2025-09-03 02:02:53', '2025-09-03 08:32:59'),
(22, 'test aa', 'a', 'fixed_amount', 20000.00, 0.00, '2025-09-01 15:33:00', '2025-09-05 15:33:00', 0, 12, 0, '2025-09-03 08:33:52', '2025-09-03 08:42:39'),
(23, 'TEST', '', 'percentage', 20.00, 0.00, '2025-09-03 20:35:00', '2025-09-05 20:35:00', 0, 0, 1, '2025-09-03 13:35:43', '2025-09-03 13:35:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discount_products`
--

CREATE TABLE `discount_products` (
  `id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL COMMENT 'ID của chương trình giảm giá',
  `product_id` int(11) DEFAULT NULL COMMENT 'ID sản phẩm (NULL nếu áp dụng cho category)',
  `category` varchar(100) DEFAULT NULL COMMENT 'Tên category (NULL nếu áp dụng cho sản phẩm cụ thể)',
  `quantity_limit` int(11) DEFAULT NULL COMMENT 'Giới hạn số lượng sản phẩm được giảm giá',
  `quantity_sold` int(11) DEFAULT 0 COMMENT 'Số lượng đã bán với giá giảm',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `discount_products`
--

INSERT INTO `discount_products` (`id`, `discount_id`, `product_id`, `category`, `quantity_limit`, `quantity_sold`, `created_at`) VALUES
(254, 18, 35, NULL, NULL, 0, '2025-08-22 02:57:34'),
(255, 18, 36, NULL, NULL, 0, '2025-08-22 02:57:34'),
(256, 18, 37, NULL, NULL, 0, '2025-08-22 02:57:34'),
(257, 18, 38, NULL, NULL, 0, '2025-08-22 02:57:34'),
(258, 18, 39, NULL, NULL, 0, '2025-08-22 02:57:34'),
(259, 18, 40, NULL, NULL, 0, '2025-08-22 02:57:34'),
(260, 18, 41, NULL, NULL, 0, '2025-08-22 02:57:34'),
(261, 18, 42, NULL, NULL, 0, '2025-08-22 02:57:34'),
(262, 18, 43, NULL, NULL, 0, '2025-08-22 02:57:34'),
(263, 18, 44, NULL, NULL, 0, '2025-08-22 02:57:34'),
(264, 18, 45, NULL, NULL, 0, '2025-08-22 02:57:34'),
(265, 18, 46, NULL, NULL, 0, '2025-08-22 02:57:34'),
(266, 18, 47, NULL, NULL, 0, '2025-08-22 02:57:34'),
(267, 18, 48, NULL, NULL, 0, '2025-08-22 02:57:34'),
(268, 18, 49, NULL, NULL, 0, '2025-08-22 02:57:34'),
(269, 18, 50, NULL, NULL, 0, '2025-08-22 02:57:34'),
(270, 18, 51, NULL, NULL, 0, '2025-08-22 02:57:34'),
(271, 18, 52, NULL, NULL, 0, '2025-08-22 02:57:34'),
(272, 18, 53, NULL, NULL, 0, '2025-08-22 02:57:34'),
(273, 18, 54, NULL, NULL, 0, '2025-08-22 02:57:34'),
(298, 20, 30, NULL, NULL, 0, '2025-08-25 01:56:21'),
(323, 21, 55, NULL, NULL, 0, '2025-09-03 08:32:59'),
(324, 21, 56, NULL, NULL, 0, '2025-09-03 08:32:59'),
(325, 21, 57, NULL, NULL, 0, '2025-09-03 08:32:59'),
(326, 21, 58, NULL, NULL, 0, '2025-09-03 08:32:59'),
(327, 21, 59, NULL, NULL, 0, '2025-09-03 08:32:59'),
(328, 21, 60, NULL, NULL, 0, '2025-09-03 08:32:59'),
(329, 21, 61, NULL, NULL, 0, '2025-09-03 08:32:59'),
(330, 21, 62, NULL, NULL, 0, '2025-09-03 08:32:59'),
(332, 22, 55, NULL, NULL, 0, '2025-09-03 08:42:39'),
(333, 23, 55, NULL, NULL, 0, '2025-09-03 13:35:43'),
(334, 23, 56, NULL, NULL, 0, '2025-09-03 13:35:43'),
(335, 23, 57, NULL, NULL, 0, '2025-09-03 13:35:43'),
(336, 23, 58, NULL, NULL, 0, '2025-09-03 13:35:43'),
(337, 23, 59, NULL, NULL, 0, '2025-09-03 13:35:43'),
(338, 23, 60, NULL, NULL, 0, '2025-09-03 13:35:43'),
(339, 23, 61, NULL, NULL, 0, '2025-09-03 13:35:43'),
(340, 23, 62, NULL, NULL, 0, '2025-09-03 13:35:43'),
(341, 23, 78, NULL, NULL, 0, '2025-09-03 13:35:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `email_type` varchar(50) NOT NULL COMMENT 'Loại email: payment_reminder, delivery_success, order_cancellation',
  `recipient_email` varchar(255) NOT NULL,
  `sent_at` datetime NOT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng log theo dõi việc gửi email';

--
-- Đang đổ dữ liệu cho bảng `email_logs`
--

INSERT INTO `email_logs` (`id`, `order_id`, `email_type`, `recipient_email`, `sent_at`, `status`, `error_message`, `created_at`) VALUES
(1, 'DH36', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-21 10:34:28', 'success', NULL, '2025-08-21 03:34:28'),
(2, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-21 10:40:23', 'success', NULL, '2025-08-21 03:40:23'),
(3, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-21 10:40:36', 'success', NULL, '2025-08-21 03:40:36'),
(4, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-21 10:42:29', 'success', NULL, '2025-08-21 03:42:29'),
(5, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-21 10:42:59', 'success', NULL, '2025-08-21 03:42:59'),
(6, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-21 10:44:15', 'success', NULL, '2025-08-21 03:44:15'),
(7, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-21 10:50:19', 'success', NULL, '2025-08-21 03:50:19'),
(8, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-22 08:32:03', 'success', NULL, '2025-08-22 01:32:03'),
(9, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-22 08:33:57', 'success', NULL, '2025-08-22 01:33:57'),
(10, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-22 08:39:10', 'success', NULL, '2025-08-22 01:39:10'),
(11, 'DH12', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-22 08:43:18', 'success', NULL, '2025-08-22 01:43:18'),
(12, 'DH41', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-25 08:45:32', 'failed', 'Không thể gửi email nhắc nhở thanh toán', '2025-08-25 01:45:32'),
(13, 'DH46', 'payment_reminder', 'ritoluki@gmail.com', '2025-08-26 07:48:27', 'success', NULL, '2025-08-26 00:48:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order`
--

CREATE TABLE `order` (
  `id` varchar(255) NOT NULL,
  `khachhang` varchar(255) NOT NULL,
  `hinhthucgiao` varchar(255) NOT NULL,
  `ngaygiaohang` varchar(255) NOT NULL,
  `thoigiangiao` varchar(255) NOT NULL,
  `ghichu` text DEFAULT NULL,
  `tenguoinhan` varchar(255) NOT NULL,
  `sdtnhan` varchar(20) NOT NULL,
  `diachinhan` varchar(255) NOT NULL,
  `thoigiandat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tongtien` int(225) NOT NULL,
  `trangthai` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `payment_status` tinyint(4) DEFAULT 0,
  `cancel_reason` text DEFAULT NULL COMMENT 'Lý do hủy đơn hàng',
  `cancelled_by` varchar(20) DEFAULT NULL COMMENT 'Người hủy đơn: admin hoặc customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order`
--

INSERT INTO `order` (`id`, `khachhang`, `hinhthucgiao`, `ngaygiaohang`, `thoigiangiao`, `ghichu`, `tenguoinhan`, `sdtnhan`, `diachinhan`, `thoigiandat`, `tongtien`, `trangthai`, `payment_method`, `payment_status`, `cancel_reason`, `cancelled_by`) VALUES
('DH11', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:33:41 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:33:43', 125000, 1, 'online', 1, NULL, NULL),
('DH12', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:37:04 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:37:05', 130000, 1, 'online', 0, NULL, NULL),
('DH13', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:52:21 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:52:22', 175000, 3, 'online', 1, NULL, NULL),
('DH14', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:56:39 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:56:40', 140000, 1, 'online', 0, NULL, NULL),
('DH16', '1', 'Giao tận nơi', 'Sun Jun 08 2025 07:13:38 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 07:13:40', 140000, 3, 'online', 1, NULL, NULL),
('DH17', '1', 'Giao tận nơi', 'Tue Aug 19 2025 11:16:30 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 11:16:33', 175000, 3, 'cod', 1, NULL, NULL),
('DH18', '1', 'Giao tận nơi', 'Sun Jun 08 2025 07:59:26 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 07:59:30', 160000, 3, 'online', 1, NULL, NULL),
('DH19', '1', 'Giao tận nơi', 'Tue Aug 19 2025 11:22:31 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 11:22:34', 175000, 1, 'VNPay', 1, NULL, NULL),
('DH20', '28', 'Giao tận nơi', 'Sat Jun 14 2025 09:49:55 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Tân test mail', '1234123412', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-14 09:50:09', 905000, 1, 'online', 0, NULL, NULL),
('DH21', '1', 'Giao tận nơi', 'Sat Jun 14 2025 10:08:38 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-14 10:08:43', 228000, 1, 'online', 0, NULL, NULL),
('DH22', '1', 'Giao tận nơi', 'Sat Jun 14 2025 10:13:21 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-14 10:13:27', 155000, 3, 'cod', 1, NULL, NULL),
('DH23', '1', 'Giao tận nơi', 'Tue Jul 29 2025 10:10:16 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-07-29 10:10:30', 205000, 4, 'online', 0, NULL, 'unknown'),
('DH24', '1', 'Giao tận nơi', 'Fri Aug 01 2025 15:24:25 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-01 15:24:27', 165000, 1, 'online', 0, NULL, NULL),
('DH25', '1', 'Giao tận nơi', 'Tue Aug 19 2025 09:56:23 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 09:56:38', 175000, 3, 'online', 1, NULL, NULL),
('DH26', '1', 'Giao tận nơi', 'Tue Aug 19 2025 10:42:03 GMT+0700 (Indochina Time)', '15:00', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 10:42:09', 205000, 3, 'online', 1, NULL, NULL),
('DH27', '1', 'Giao tận nơi', 'Tue Aug 19 2025 10:56:50 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 10:56:55', 180000, 4, 'online', 0, 'Khách hàng yêu cầu hủy', 'admin'),
('DH28', '1', 'Giao tận nơi', 'Wed Aug 20 2025 13:46:54 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-20 13:46:59', 175000, 4, 'cod', 0, 'Khách hàng yêu cầu hủy', 'admin'),
('DH29', '1', 'Giao tận nơi', 'Wed Aug 20 2025 13:49:45 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-20 13:49:51', 871500, 4, 'cod', 0, 'Khách hàng yêu cầu hủy', 'admin'),
('DH30', '1', 'Giao tận nơi', 'Thu Aug 21 2025 08:26:19 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', 'đặt hàng test', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-21 08:26:37', 605000, 4, 'online', 1, 'Khách hàng yêu cầu hủy', 'admin'),
('DH31', '39', 'Giao tận nơi', 'Wed Aug 20 2025 10:00:22 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'tân 20/08', '0987687611', 'Hà Nội', '2025-08-20 10:00:38', 226200, 3, 'cod', 0, NULL, NULL),
('DH32', '1', 'Giao tận nơi', 'Thu Aug 21 2025 08:33:06 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-21 08:33:11', 772500, 4, 'cod', 0, 'Đơn hàng bị trùng lặp', 'admin'),
('DH33', '1', 'Giao tận nơi', 'Thu Aug 21 2025 08:41:33 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-21 08:41:39', 499200, 4, 'cod', 0, 'Vấn đề về thanh toán', 'admin'),
('DH34', '1', 'Giao tận nơi', 'Thu Aug 21 2025 08:47:38 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-21 08:47:41', 1905000, 3, 'cod', 1, NULL, NULL),
('DH35', '1', 'Giao tận nơi', 'Thu Aug 21 2025 10:21:40 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-21 10:21:43', 465000, 4, 'cod', 0, NULL, NULL),
('DH36', '1', 'Giao tận nơi', 'Thu Aug 21 2025 10:22:17 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-21 10:22:19', 405000, 3, 'cod', 0, NULL, NULL),
('DH37', '1', 'Giao tận nơi', 'Fri Aug 22 2025 08:52:16 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-22 08:52:18', 205000, 3, 'cod', 1, NULL, NULL),
('DH38', '1', 'Giao tận nơi', 'Fri Aug 22 2025 09:13:40 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-22 09:13:44', 155000, 1, 'online', 1, NULL, NULL),
('DH39', '1', 'Giao tận nơi', 'Fri Aug 22 2025 09:15:11 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-22 09:15:15', 190000, 1, 'cod', 0, NULL, NULL),
('DH40', '1', 'Giao tận nơi', 'Fri Aug 22 2025 09:23:54 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-22 09:23:56', 205000, 4, 'cod', 0, 'Thông tin giao hàng không chính xác', 'admin'),
('DH41', '1', 'Giao tận nơi', 'Fri Aug 22 2025 09:33:53 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-22 09:33:54', 170000, 0, 'cod', 0, NULL, NULL),
('DH42', '1', 'Giao tận nơi', 'Mon Aug 25 2025 08:47:48 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-25 08:47:52', 155000, 4, 'cod', 0, NULL, NULL),
('DH43', '1', 'Giao tận nơi', 'Mon Aug 25 2025 08:56:44 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-25 08:56:50', 1680000, 1, 'online', 1, NULL, NULL),
('DH44', '1', 'Giao tận nơi', 'Mon Aug 25 2025 09:06:27 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-25 09:06:28', 162000, 0, 'cod', 0, NULL, NULL),
('DH45', '1', 'Giao tận nơi', 'Mon Aug 25 2025 09:11:12 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-25 09:11:15', 1955000, 3, 'VNPay', 1, NULL, NULL),
('DH46', '1', 'Giao tận nơi', 'Mon Aug 25 2025 09:42:58 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-25 09:43:01', 155000, 4, 'VNPay', 2, 'Khách hàng yêu cầu hủy', 'admin'),
('DH47', '1', 'Giao tận nơi', 'Mon Aug 25 2025 09:52:25 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-25 09:52:32', 175000, 3, 'VNPay', 1, NULL, NULL),
('DH48', '41', 'Giao tận nơi', 'Tue Aug 26 2025 08:18:17 GMT+0700 (Indochina Time)', '08:00', '', 'nva', '1010101010', 'Hoài đức, Hà Nội', '2025-08-26 08:18:33', 162000, 3, 'VNPay', 1, NULL, NULL),
('DH49', '1', 'Giao tận nơi', 'Thu Aug 28 2025 23:18:05 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-28 23:18:09', 205000, 0, 'cod', 0, NULL, NULL),
('DH50', '1', 'Giao tận nơi', 'Wed Sep 03 2025 09:03:48 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-09-03 09:03:55', 1534800, 3, 'VNPay', 1, NULL, NULL),
('DH51', '1', 'Giao tận nơi', 'Wed Sep 03 2025 20:28:05 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-09-03 20:28:43', 540000, 1, 'VNPay', 1, NULL, NULL),
('DH52', '42', 'Giao tận nơi', 'Wed Sep 03 2025 15:40:17 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Nguyễn Văn A', '0934567890', '1', '2025-09-03 15:40:28', 444000, 0, 'cod', 0, NULL, NULL),
('DH8', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:21:05 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:21:07', 140000, 3, 'online', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orderdetails`
--

CREATE TABLE `orderdetails` (
  `id` int(11) NOT NULL,
  `madon` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `note` varchar(255) NOT NULL,
  `product_price` int(11) NOT NULL,
  `soluong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orderdetails`
--

INSERT INTO `orderdetails` (`id`, `madon`, `product_id`, `note`, `product_price`, `soluong`) VALUES
(268725, 'DH8', 12, 'Không có ghi chú', 140000, 1),
(268728, 'DH11', 6, 'Không có ghi chú', 125000, 1),
(268729, 'DH12', 11, 'Không có ghi chú', 130000, 1),
(268730, 'DH13', 7, 'Không có ghi chú', 175000, 1),
(268731, 'DH14', 12, 'Không có ghi chú', 140000, 1),
(268733, 'DH16', 12, 'Không có ghi chú', 140000, 1),
(268735, 'DH18', 11, 'Không có ghi chú', 130000, 1),
(268737, 'DH20', 7, 'Không có ghi chú', 175000, 5),
(268738, 'DH21', 56, 'Không có ghi chú', 99000, 2),
(268739, 'DH22', 6, 'Không có ghi chú', 125000, 1),
(268740, 'DH23', 7, 'Không có ghi chú', 175000, 1),
(268741, 'DH24', 36, 'Không có ghi chú', 135000, 1),
(268742, 'DH25', 2, 'Không có ghi chú', 145000, 1),
(268743, 'DH26', 7, 'Không có ghi chú', 175000, 1),
(268744, 'DH27', 1, 'Không có ghi chú', 150000, 1),
(268745, 'DH17', 2, 'Không có ghi chú', 145000, 1),
(268746, 'DH19', 2, 'Không có ghi chú', 145000, 1),
(268752, 'DH31', 6, 'Không có ghi chú', 125000, 1),
(268753, 'DH31', 55, 'Không có ghi chú', 71200, 1),
(268762, 'DH28', 2, 'Không có ghi chú', 145000, 1),
(268763, 'DH29', 16, 'Không có ghi chú', 140250, 6),
(268765, 'DH30', 37, 'Không có ghi chú', 57500, 10),
(268766, 'DH32', 36, 'Không có ghi chú', 67500, 11),
(268767, 'DH33', 59, 'Không có ghi chú', 78200, 6),
(268768, 'DH34', 6, 'Không có ghi chú', 125000, 15),
(268769, 'DH35', 2, 'Không có ghi chú', 145000, 3),
(268770, 'DH36', 6, 'Không có ghi chú', 125000, 3),
(268771, 'DH37', 7, 'Không có ghi chú', 175000, 1),
(268772, 'DH38', 6, 'Không có ghi chú', 125000, 1),
(268773, 'DH39', 5, 'Không có ghi chú', 160000, 1),
(268774, 'DH40', 7, 'Không có ghi chú', 175000, 1),
(268775, 'DH41', 12, 'Không có ghi chú', 140000, 1),
(268776, 'DH42', 6, 'Không có ghi chú', 125000, 1),
(268777, 'DH43', 30, 'Không có ghi chú', 165000, 10),
(268778, 'DH44', 30, 'Không có ghi chú', 132000, 1),
(268779, 'DH45', 7, 'Không có ghi chú', 175000, 11),
(268780, 'DH46', 6, 'Không có ghi chú', 125000, 1),
(268781, 'DH47', 2, 'Không có ghi chú', 145000, 1),
(268782, 'DH48', 30, 'Không có ghi chú', 132000, 1),
(268783, 'DH49', 7, 'Không có ghi chú', 175000, 1),
(268784, 'DH50', 56, 'Không có ghi chú', 79200, 19),
(268786, 'DH52', 55, 'Không có ghi chú', 69000, 6),
(268787, 'DH51', 2, 'Không có ghi chú', 145000, 1),
(268788, 'DH51', 8, 'Không có ghi chú', 180000, 1),
(268789, 'DH51', 9, 'Không có ghi chú', 185000, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` int(225) NOT NULL,
  `describes` text DEFAULT NULL,
  `soluong` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `status`, `title`, `img`, `category`, `price`, `describes`, `soluong`) VALUES
(1, 1, 'Muôn Kiếp Nhân Sinh (Tập 2)', 'http://localhost/bookstore_datn/assets/img/products/sachhay/Bia-Sach-Muon-Kiep-Nhan-Sinh.jpg', 'Sách Hay', 150000, NULL, 10),
(2, 1, 'Muôn Kiếp Nhân Sinh (Tập 1)', 'http://localhost/bookstore_datn/assets/img/products/sachhay/Bia-Sach-Muon-Kiep-Nhan-Sinh-1.jpg', 'Sách Hay', 145000, '', 2),
(3, 1, 'Cây Cam Ngọt Của Tôi', './assets/img/products/sachhay/cay-cam-ngot-cua-toi.jpg', 'Sách Hay', 110000, 'Tiểu thuyết nổi tiếng của José Mauro de Vasconcelos', 0),
(4, 1, 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', './assets/img/products/sachhay/Cho-toi-xin-mot-ve-di-tuoi-tho.jpg', 'Sách Hay', 95000, 'Tác phẩm nổi tiếng của Nguyễn Nhật Ánh', 0),
(5, 1, 'Chủ Nghĩa Khắc Kỷ', './assets/img/products/sachhay/Chu-Nghia-Khac-Ky-Phong-Cach-Song-Ban-Linh-Va-Binh-Than.jpg', 'Sách Hay', 160000, 'Sách về triết học và lối sống khắc kỷ', 29),
(6, 1, 'Đại Dương Đen', './assets/img/products/sachhay/Dai-duong-den.jpg', 'Sách Hay', 125000, 'Tiểu thuyết về những bí ẩn của đại dương', 1982),
(7, 1, 'Chiến Tranh Tiền Tệ (Tập 1)', './assets/img/products/sachhay/Ebook-Chien-tranh-tien-te-Tap-1-Ai-that-su-la-nguoi-giau-nhat-the-gioi.jpg', 'Sách Hay', 175000, 'Phần 1 của bộ sách nổi tiếng về tài chính và quyền lực', 211),
(8, 1, 'Chiến Tranh Tiền Tệ (Tập 2)', './assets/img/products/sachhay/Ebook-Chien-tranh-tien-te-Tap-2-Su-thong-tri-cua-quyen-luc-tai-chinh.jpg', 'Sách Hay', 180000, 'Phần 2 của bộ sách về tài chính và quyền lực', 14),
(9, 1, 'Chiến Tranh Tiền Tệ (Tập 3)', './assets/img/products/sachhay/Ebook-Chien-tranh-tien-te-Tap-3-Bien-gioi-tien-te-nhan-to-bi-an-trong-cac-cuoc-chien-kinh-te.jpg', 'Sách Hay', 185000, 'Phần 3 của bộ sách về tài chính toàn cầu', 18),
(10, 1, 'Dám Nghĩ Lại', './assets/img/products/sachhay/Ebook-Dam-nghi-lai.jpg', 'Sách Hay', 120000, 'Sách về tư duy đổi mới và phát triển bản thân', 10),
(11, 1, 'Kẻ Khôn Đi Lối Khác', './assets/img/products/sachhay/Ebook-Ke-khon-di-loi-khac.jpg', 'Sách Hay', 130000, 'Sách về chiến lược sống và thành công', 250),
(12, 1, 'Không Diệt Không Sinh Đừng Sợ Hãi', './assets/img/products/sachhay/Ebook-Khong-diet-khong-sinh-dung-so-hai.jpg', 'Sách Hay', 140000, 'Sách của Thiền sư Thích Nhất Hạnh', 2803),
(13, 1, 'Nghệ Thuật Tư Duy Chiến Lược', './assets/img/products/sachhay/Ebook-Nghe-thuat-tu-duy-chien-luoc.jpg', 'Sách Hay', 155000, 'Sách về phương pháp tư duy và lập chiến lược', 39),
(14, 1, 'Nói chuyện là bản năng', './assets/img/products/sachhay/ebook-Noi-Chuyen-La-Ban-Nang-Giu-Mieng-La-Tu-Duong-Im-Lang-La-Tri-Tue-Truong-Tieu-Hang.jpg', 'Sách Hay', 115000, 'Sách về nghệ thuật giao tiếp và đối nhân xử thế', 41),
(15, 1, 'Sức Mạnh Của Ngôn Từ', './assets/img/products/sachhay/Ebook-Suc-manh-cua-ngon-tu.jpg', 'Sách Hay', 100000, 'Sách về nghệ thuật giao tiếp hiệu quả', 37),
(16, 1, 'Thay Đổi Cuộc Sống Với Nhân Số Học', './assets/img/products/sachhay/Ebook-Thay-doi-cuoc-song-voi-nhan-so-hoc.jpg', 'Sách Hay', 165000, 'Sách về nhân số học và ứng dụng trong cuộc sống', 14),
(17, 1, 'Tư Duy Ngược Dịch Chuyển Thế Giới', './assets/img/products/sachhay/Ebook-Tu-duy-nguoc-dich-chuyen-the-gioi.jpg', 'Sách Hay', 145000, 'Sách về tư duy đột phá và sáng tạo', 44),
(18, 1, 'Muốn An Được An', './assets/img/products/sachhay/Muon-an-duoc-an.jpg', 'Sách Hay', 110000, 'Sách về lòng biết ơn và sự trân trọng', 35),
(19, 0, 'Ngày Xưa Có Một Chuyện Tình', './assets/img/products/sachhay/Ngay-Xua-Co-Mot-Chuyen-Tinh.jpg', 'Sách Hay', 120000, 'Tiểu thuyết lãng mạn', 39),
(20, 1, 'Rừng Nauy', './assets/img/products/sachhay/Rung-nauy.jpg', 'Sách Hay', 135000, 'Tiểu thuyết nổi tiếng của Haruki Murakami', 40),
(21, 1, 'Tết Ở Làng Địa Ngục', './assets/img/products/sachhay/Tet-o-lang-dia-nguc.jpg', 'Sách Hay', 105000, 'Tác phẩm văn học Việt Nam', 31),
(22, 1, 'Thảo Túng Tâm Lý', './assets/img/products/sachhay/thao-tung-tam-ly.jpg', 'Sách Hay', 140000, 'Sách về tâm lý học và các kỹ thuật thao túng', 32),
(23, 1, 'Thế Giới Bên Trong Cái Ác', './assets/img/products/sachhay/The-gioi-ben-trong-cai-ac.jpg', 'Sách Hay', 150000, 'Sách về tâm lý học tội phạm', 15),
(24, 1, 'Tĩnh Lặng', 'http://localhost/bookstore_datn/assets/img/products/sachhay/tinh-lang.jpg', 'Sách Hay', 95000, '', 21),
(25, 1, 'Đánh Thức Tài Năng Toán Học 01', './assets/img/products/khoahoc/ebook-danh-thuc-tai-nang-toan-hoc-01-7-8-tuoi.jpg', 'Khoa Học', 85000, 'Sách phát triển tư duy toán học cho trẻ 7-8 tuổi', 9),
(26, 1, 'Đánh Thức Tài Năng Toán Học 02 ', './assets/img/products/khoahoc/ebook-danh-thuc-tai-nang-toan-hoc-02-8-9-tuoi.jpg', 'Khoa Học', 85000, 'Sách phát triển tư duy toán học cho trẻ 8-9 tuổi', 24),
(27, 1, 'Đánh Thức Tài Năng Toán Học 03 ', './assets/img/products/khoahoc/ebook-danh-thuc-tai-nang-toan-hoc-03-9-10-tuoi.jpg', 'Khoa Học', 85000, 'Sách phát triển tư duy toán học cho trẻ 9-10 tuổi', 41),
(28, 1, 'Não Bộ Kể Gì Về Bạn', './assets/img/products/khoahoc/Ebook-Nao-bo-ke-gi-ve-ban.jpg', 'Khoa Học', 140000, 'Sách về khoa học não bộ và tâm lý học', 37),
(29, 1, 'Thám Hóa Khí Hậu', './assets/img/products/khoahoc/Ebook-Tham-hoa-khi-hau.jpg', 'Khoa Học', 155000, 'Sách về biến đổi khí hậu và tác động đến môi trường', 11),
(30, 1, 'Vật Lý Của Những Điều Tưởng Chừng Bất Khả', './assets/img/products/khoahoc/Ebook-Vat-ly-cua-nhung-dieu-tuong-chung-bat-kha.jpg', 'Khoa Học', 165000, 'Sách về các hiện tượng vật lý phức tạp và ứng dụng', 18),
(31, 1, 'Vật Lý Của Tương Lai', './assets/img/products/khoahoc/Ebook-Vat-ly-cua-tuong-lai.jpg', 'Khoa Học', 170000, 'Sách về các xu hướng phát triển của ngành vật lý hiện đại', 0),
(32, 1, 'Lịch Sử Vạn Vật', './assets/img/products/khoahoc/lich-su-van-vat_1.jpg', 'Khoa Học', 180000, 'Sách về lịch sử phát triển của vạn vật trong vũ trụ', 15),
(33, 1, 'Thế Giới Lượng Tử Kỳ Bí', './assets/img/products/khoahoc/The-gioi-luong-tu-ky-bi.jpg', 'Khoa Học', 150000, 'Sách về vật lý lượng tử và những bí ẩn của thế giới vi mô', 6),
(34, 1, 'Thuyết Tương Đối Cho Mọi Người ', './assets/img/products/Khoa Học/683400127d046_4510cb11f908b9f0.jpg', 'Khoa Học', 145000, 'Sách giải thích thuyết tương đối của Einstein dành cho độc giả phổ thông', 25),
(35, 1, 'Cây Cam Ngọt Của Tôi', './assets/img/products/tieuthuyet/cay-cam-ngot-cua-toi.jpg', 'Tiểu Thuyết', 120000, 'Tiểu thuyết nổi tiếng của José Mauro de Vasconcelos', 36),
(36, 1, 'Đồi Gió Hú', './assets/img/products/tieuthuyet/Doi-gio-hu.jpg', 'Tiểu Thuyết', 135000, 'Tiểu thuyết kinh điển của Emily Brontë', 22),
(37, 1, 'Đời Nhẹ Khôn Kham', './assets/img/products/tieuthuyet/Doi-Nhe-Khon-Kham.jpg', 'Tiểu Thuyết', 115000, 'Tác phẩm văn học cảm động về tình người', 38),
(38, 1, 'Mùa Lá Rụng Trong Vườn', './assets/img/products/tieuthuyet/Ebook-Mua-la-rung-trong-vuon.jpg', 'Tiểu Thuyết', 110000, 'Tiểu thuyết lãng mạn đầy cảm xúc', 20),
(39, 1, 'Mù Lòa', './assets/img/products/tieuthuyet/Ebook-Mu-loa.jpg', 'Tiểu Thuyết', 125000, 'Tiểu thuyết triết lý sâu sắc về cuộc sống', 29),
(40, 1, 'Số 31 Đường Giác Mơ', './assets/img/products/tieuthuyet/Ebook-So-31-duong-Giac-Mo.jpg', 'Tiểu Thuyết', 130000, 'Tiểu thuyết huyền bí với những bí mật được hé lộ', 32),
(41, 1, 'Hai Số Phận', './assets/img/products/tieuthuyet/Hai-So-Phan.jpg', 'Tiểu Thuyết', 160000, 'Tiểu thuyết nổi tiếng của Jeffrey Archer', 25),
(42, 1, 'Mắt Biếc - Nguyễn Nhật Ánh', './assets/img/products/tieuthuyet/Mat-Biec-Nguyen-Nhat-Anh-min.jpg', 'Tiểu Thuyết', 105000, 'Tác phẩm nổi tiếng của nhà văn Nguyễn Nhật Ánh', 21),
(43, 1, 'Ngày Xưa Có Một Chuyện Tình', './assets/img/products/tieuthuyet/Ngay-Xua-Co-Mot-Chuyen-Tinh.jpg', 'Tiểu Thuyết', 115000, 'Câu chuyện tình yêu đầy xúc động', 29),
(44, 1, 'Những Người Khốn Khổ', './assets/img/products/tieuthuyet/Nhung-nguoi-khon-kho.jpg', 'Tiểu Thuyết', 175000, 'Tác phẩm kinh điển của đại văn hào Victor Hugo', 29),
(45, 1, 'Nửa Kia Của Hitler', './assets/img/products/tieuthuyet/Nua-Kia-Cua-Hitler-Eric-Emmanuel-Schmitt.jpg', 'Tiểu Thuyết', 140000, 'Tiểu thuyết táo bạo về lịch sử thế giới', 10),
(46, 1, 'Quán Gò Đi Lên', './assets/img/products/tieuthuyet/Quan-Gio-Di-Len.jpg', 'Tiểu Thuyết', 120000, 'Câu chuyện về những số phận đan xen', 48),
(47, 1, 'Sáu Người Đi Khắp Thế Gian', './assets/img/products/tieuthuyet/Sau-nguoi-di-khap-the-gian.jpg', 'Tiểu Thuyết', 145000, 'Tiểu thuyết phiêu lưu đầy cảm hứng', 24),
(48, 1, 'Tắt Đèn', './assets/img/products/tieuthuyet/tat-den.jpg', 'Tiểu Thuyết', 95000, 'Tác phẩm văn học Việt Nam kinh điển của Ngô Tất Tố', 17),
(49, 1, 'The Hobbit', './assets/img/products/tieuthuyet/the-hobbit.jpg', 'Tiểu Thuyết', 150000, 'Tiểu thuyết giả tưởng nổi tiếng của J.R.R. Tolkien', 8),
(50, 1, 'Thép Là Tôi Thế Đấy', './assets/img/products/tieuthuyet/thep-da-toi-the-day-minh-thang-scaled.jpg', 'Tiểu Thuyết', 135000, 'Tiểu thuyết kinh điển của Nikolai Ostrovsky', 32),
(51, 1, 'Thủy Hử', './assets/img/products/tieuthuyet/Thuy-Hu.jpg', 'Tiểu Thuyết', 185000, 'Tiểu thuyết cổ điển Trung Quốc nổi tiếng', 40),
(52, 1, 'Tiếng Gọi Nơi Hoang Dã', './assets/img/products/tieuthuyet/Tieng-goi-noi-hoang-da.jpg', 'Tiểu Thuyết', 110000, 'Tác phẩm nổi tiếng của Jack London', 8),
(53, 1, 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', './assets/img/products/tieuthuyet/Toi-thay-hoa-vang-tren-co-xanh.jpg', 'Tiểu Thuyết', 105000, 'Tiểu thuyết được yêu thích của Nguyễn Nhật Ánh', 6),
(54, 1, 'Xứ Cát', './assets/img/products/tieuthuyet/xucat-e1693798884621.jpg', 'Tiểu Thuyết', 125000, 'Tiểu thuyết hiện đại đầy ấn tượng', 47),
(55, 1, 'Chuyện con mèo dạy hải âu bay', './assets/img/products/thieunhi/chuyen-con-meo-day-hai-au-bay.jpg', 'Thiếu Nhi', 89000, 'Câu chuyện cảm động giữa mèo và chim hải âu', 40),
(56, 1, 'Dế Mèn Phiêu Lưu Ký', './assets/img/products/thieunhi/de-men-phieu-luu-ky.jpg', 'Thiếu Nhi', 99000, 'Tác phẩm thiếu nhi kinh điển của Tô Hoài', 31),
(57, 1, 'Cuộc đời và những cuộc phiêu lưu của Santa Claus', './assets/img/products/thieunhi/Ebook-Cuoc-doi-va-nhung-cuoc-phieu-luu-cua-Santa-Claus.jpg', 'Thiếu Nhi', 79000, 'Khám phá cuộc đời kỳ thú của ông già Noel', 20),
(58, 1, 'Năm đứa trẻ và điều ước kỳ lạ', './assets/img/products/thieunhi/Ebook-Nam-dua-tre-va-no-Five-children-and-It.jpg', 'Thiếu Nhi', 86000, 'Một hành trình kỳ diệu đầy phép màu', 16),
(59, 1, 'Peter Pan', './assets/img/products/thieunhi/Ebook-peter-pan.jpg', 'Thiếu Nhi', 92000, 'Chuyến phiêu lưu tới Neverland bất tận', 28),
(60, 1, 'Wolfgang Amadeus Mozart là ai', './assets/img/products/thieunhi/Ebook-Wolfgang-Amadeus-Mozart-la-ai.jpg', 'Thiếu Nhi', 85000, 'Tiểu sử hấp dẫn về thiên tài âm nhạc Mozart', 44),
(61, 1, 'Hoàng tử bé', './assets/img/products/thieunhi/hoang-tu-be.jpg', 'Thiếu Nhi', 78000, 'Tác phẩm triết lý sâu sắc dưới dạng truyện thiếu nhi', 37),
(62, 1, 'Lâu đài bay của pháp sư Howl', './assets/img/products/thieunhi/lau-dai-bay-cua-phap-su-howl.jpg', 'Thiếu Nhi', 98000, 'Câu chuyện phép thuật ly kỳ và cảm động', 50),
(63, 0, 'Tắt đèn', './assets/img/products/67f108c29267d_a4f5a2d965481841.jpg', 'Sách Hay', 12000, 'Tác phẩm kinh điển của Ngô Tất Tố', 40),
(64, 1, 'Thế giới bên trong cái ác', './assets/img/products/default/6829aeffac0f2_528e7d30de122f3a.jpg', 'Sách Hay', 90000, 'Test import ', 48),
(66, 1, 'Thế giới lượng tử kỳ bí', 'http://localhost/bookstore_datn/assets/img/products/default/6829ab5d75cf5_0cedcd07ab238c92.jpg', 'Khoa Học', 100000, 'Tôi mong muốn khám phá sâu hơn cấu trúc ẩn sau những hành vi tự nhiên, để hiểu rõ hơn về bí ẩn của thế giới chúng ta sống. Khi tôi còn 15 tuổi, sự tò mò và khát khao tìm hiểu không ngừng đã dẫn tôi đến với lĩnh vực vật lý lượng tử qua các bài giảng và tài liệu khoa học. Điều này đã mở ra cửa hiểu biết đầu tiên về vũ trụ tuyệt diệu này.\r\n\r\nNiềm say mê với vật lý lượng tử ngày càng trở nên mạnh mẽ hơn. Tôi đã bắt đầu đặt ra những câu hỏi thách thức và đòi hỏi sự giải đáp. Tuy nhiên, không ai có thể đưa ra những câu trả lời mà tôi đang tìm kiếm. Tôi đã dành hai năm để nghiên cứu về vật lý lượng tử, và sau khi thấu hiểu sâu hơn, tôi cảm nhận một áp lực về việc sắp xếp lại những kiến thức đã thu thập.\r\n\r\nTừ ý tưởng này, tôi quyết định chia sẻ kiến thức của mình thông qua văn bản, tạo ra một tác phẩm mà tôi hy vọng sẽ truyền tải được các chủ đề quan trọng và các hiệu ứng đáng chú ý trong vật lý lượng tử dưới góc nhìn của riêng tôi. Tôi rất hào hứng khi bắt đầu dựng lên một cấu trúc mới, tạo ra một cuốn sách giáo khoa độc đáo, nối liền khoảng trống giữa những tài liệu khoa học phổ biến và các tài liệu nghiên cứu chính thống.\r\n\r\nĐó là lý do tại sao tôi tự hào giới thiệu cuốn sách “Thế Giới Lượng Tử Kỳ Bí”. Chúng tôi hy vọng rằng, thông qua cuốn sách này, bạn cũng sẽ có cơ hội khám phá những điều tuyệt diệu về vật lý lượng tử và thế giới xung quanh chúng ta.', 23),
(69, 0, 'Bước đường cùng', './assets/img/products/Không Phân Loại/68ac310c72b07_7873fd7d64875977.png', 'Không Phân Loại', 100000, '', 0),
(70, 0, '1', './assets/img/products/Tiểu Thuyết/68ac2eeb7d397_b70e65549f5277b2.jpg', 'Tiểu Thuyết', 1, NULL, 0),
(71, 0, 'test', 'assets/img/products/Sách Hay/68ac345ceb8a0_cb5627bdcaa64595.jpg', 'Sách Hay', 123, '', 0),
(72, 1, 'Bước đường cùng', 'http://localhost/Bookstore_DATN/assets/img/products/68ac34867f495_631ca0cbc74fc9c9.png', 'Sách Hay', 1, '', 1),
(73, 0, 'Đừng bao giờ đi ăn một mình', 'assets/img/products/Sách Hay/68ac353d729d5_8ea2c03a053bcbc4.png', 'Sách Hay', 10000, '', 0),
(74, 0, 'Đừng bao giờ đi ăn một mình', 'assets/img/products/Sách Hay/68ac3571a03f8_e0e086335f3cca63.png', 'Sách Hay', 111111, '', 0),
(75, 1, 'Bàn có năm chỗ ngồi', 'http://localhost/Bookstore_DATN/assets/img/products/S%C3%A1ch%20Hay/68ac35d6e4fb0_493f2ce16e7909d7.jpg', 'Sách Hay', 123456, '', 12),
(76, 0, 'Bàn có năm chỗ ngồi', 'assets/img/products/Sách Hay/68ac367f93a35_47fac4b28e121996.jpg', 'Sách Hay', 123, '', 0),
(77, 1, 'Đàn Ông Sao Hỏa, Đàn Bà Sao Kim', 'http://localhost/bookstore_datn/assets/img/products//68ac372756847_ceef7e3a00e8c77e.jpg', 'Sách Hay', 290000, '', 1),
(78, 1, 'Vương quốc bí ẩn', 'http://localhost/bookstore_datn/assets/img/products/S%C3%A1ch%20Hay/68b7fa8c2a5e1_b50729f2ef7d65f7.jpg', 'Thiếu Nhi', 111, '“Vương Quốc Bí Ẩn” của Jenny Nimmo không chỉ là một cuốn sách thiếu nhi thông thường mà là một hành trình phiêu lưu đầy màu sắc và ẩn chứa nhiều điều bí ẩn. Trong thế giới đầy phép thuật của tác phẩm, chúng ta gặp Charlie Bone, một cậu bé 12 tuổi có khả năng đặc biệt – khả năng nhìn thấy linh hồn.', 12),
(79, 1, 'Số 31 Đường Giấc Mơ', 'http://localhost/bookstore_datn/assets/img/products/S%C3%A1ch%20Hay/68b7fc0559323_77c2795600ecea8b.jpg', 'Tiểu Thuyết', 129000, 'Trong Số 31 Đường Giấc Mơ ngòi bút tài hoa của Lisa Jewell đã kết hợp sự căng thẳng của trinh thám và sâu lắng của tâm hồn con người. Câu chuyện không chỉ xoá mờ ranh giới giữa hiện tại và quá khứ, mà còn đưa ta vào những tư duy về tình yêu, mất mát và hành trình tìm thấy sự thấu hiểu về bản thân.\r\n\r\nTrong sự hấp dẫn đầy nguy hiểm của Số 31 Đường Giấc Mơ bạn sẽ bước vào một thế giới đầy kỳ bí, đong đầy tình cảm và bí ẩn. Cuốn sách sẽ đánh thức những cảm xúc sâu thẳm và khiến bạn suy tư về những khía cạnh ẩn giấu của cuộc sống và con người.', 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_categories`
--

INSERT INTO `product_categories` (`id`, `product_id`, `category_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 18, 1),
(19, 19, 1),
(20, 20, 1),
(21, 21, 1),
(22, 22, 1),
(23, 23, 1),
(24, 24, 1),
(25, 25, 2),
(26, 26, 2),
(27, 27, 2),
(28, 28, 2),
(29, 29, 2),
(30, 30, 2),
(31, 31, 2),
(32, 32, 2),
(33, 33, 2),
(34, 34, 2),
(35, 35, 3),
(36, 36, 3),
(37, 37, 3),
(38, 38, 3),
(39, 39, 3),
(40, 40, 3),
(41, 41, 3),
(42, 42, 3),
(43, 43, 3),
(44, 44, 3),
(45, 45, 3),
(46, 46, 3),
(47, 47, 3),
(48, 48, 3),
(49, 49, 3),
(50, 50, 3),
(51, 51, 3),
(52, 52, 3),
(53, 53, 3),
(54, 54, 3),
(55, 55, 4),
(56, 56, 4),
(57, 57, 4),
(58, 58, 4),
(59, 59, 4),
(60, 60, 4),
(61, 61, 4),
(62, 62, 4),
(63, 63, 1),
(64, 64, 1),
(65, 66, 2),
(66, 70, 3),
(67, 71, 1),
(68, 72, 1),
(69, 73, 1),
(70, 74, 1),
(71, 75, 1),
(72, 76, 1),
(73, 77, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `join_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `userType` int(12) DEFAULT 0,
  `reset_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `phone`, `password`, `address`, `email`, `status`, `join_date`, `userType`, `reset_token`) VALUES
(1, 'Phan Nhật Tân', '0123456789', '123456', '62 Hậu Ái, Hoài Đức, Hà Nội.', 'ritoluki@gmail.com', 1, '2025-03-05 03:08:37', 1, 'bc83d6599956785bdac63a8a52a5ba229c4de06b7621acdfc6a1352e33ca7934'),
(27, 'Tân Tân', '123456', '123456', '', '', 1, '2025-04-02 02:11:04', 0, NULL),
(28, 'Tân test mail', '1234123412', 'Tan123@@', '62 Hậu Ái, Hoài Đức, Hà Nội.', '3najchuoj9@gmail.com', 1, '2025-04-02 03:40:58', 0, '8df25cbeb385f19a69eba695998ce66a95957197fada08525cfe28b5071f99bb'),
(29, 'Tân test Form', '1234567890', '123123', '', '123@ww.com', 1, '2025-04-04 22:37:47', 0, NULL),
(34, 'Nguyễn Văn A', '0842717777', '123456', '', 'iyi55272@toaik.com', 0, '2025-05-18 03:10:24', 0, NULL),
(35, 'PHAN NHAT TAN', '0842717778', '123456', '', '20212270@eaut.edu.vn', 1, '2025-05-18 03:33:43', 0, NULL),
(38, 'Tan', '0842717889', 'Tan123@@', '', 'ritoluki1@gmail.com', 1, '2025-07-18 01:52:32', 0, NULL),
(39, 'tân 20/08', '0987687611', '123123', '', 'tss@1231.c', 1, '2025-08-19 19:59:55', 0, NULL),
(41, 'nva', '1010101010', '123123', '', 'ibf44565@jioso.com', 1, '2025-08-25 18:17:37', 0, NULL),
(42, 'Nguyễn Văn A', '0934567890', '123456', '', 'admin@gmail.com', 0, '2025-09-03 01:39:47', 0, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `book_reviews`
--
ALTER TABLE `book_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`idcart`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `discount_products`
--
ALTER TABLE `discount_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `category` (`category`);

--
-- Chỉ mục cho bảng `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_email_type` (`email_type`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `madon` (`madon`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_category` (`product_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `book_reviews`
--
ALTER TABLE `book_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `idcart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `discount_products`
--
ALTER TABLE `discount_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=342;

--
-- AUTO_INCREMENT cho bảng `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268790;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT cho bảng `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `book_reviews`
--
ALTER TABLE `book_reviews`
  ADD CONSTRAINT `book_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `book_reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `book_reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`);

--
-- Các ràng buộc cho bảng `discount_products`
--
ALTER TABLE `discount_products`
  ADD CONSTRAINT `discount_products_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`madon`) REFERENCES `order` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
