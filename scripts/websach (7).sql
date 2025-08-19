-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Aug 19, 2025 at 09:34 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `websach`
--

-- --------------------------------------------------------

--
-- Table structure for table `book_reviews`
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
-- Dumping data for table `book_reviews`
--

INSERT INTO `book_reviews` (`id`, `user_id`, `product_id`, `order_id`, `rating`, `content`, `image`, `created_at`, `updated_at`) VALUES
(1, 34, 3, NULL, 4, 'test', NULL, '2025-05-26 12:36:04', '2025-05-26 12:36:04'),
(2, 1, 3, NULL, 5, 'test', NULL, '2025-05-26 12:38:09', '2025-05-26 12:38:09'),
(3, 1, 8, NULL, 5, '', NULL, '2025-05-26 12:48:12', '2025-05-26 12:48:12'),
(4, 1, 21, NULL, 5, 'Rất hay', NULL, '2025-05-26 14:00:03', '2025-05-26 14:00:03'),
(5, 1, 12, NULL, 3, 'Tân', NULL, '2025-06-03 15:31:44', '2025-06-03 15:31:44'),
(6, 1, 2, NULL, 4, '123', NULL, '2025-08-01 16:51:52', '2025-08-01 16:51:52'),
(7, 1, 1, NULL, 4, '1', NULL, '2025-08-01 16:54:03', '2025-08-01 16:54:03'),
(8, 1, 11, NULL, 5, 'testing 19/08', NULL, '2025-08-19 13:15:08', '2025-08-19 13:15:08');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
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
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Tên chương trình giảm giá',
  `description` text DEFAULT NULL COMMENT 'Mô tả chương trình',
  `discount_type` enum('percentage','fixed_amount') NOT NULL DEFAULT 'percentage' COMMENT 'Loại giảm giá: % hoặc số tiền cố định',
  `discount_value` decimal(10,2) NOT NULL COMMENT 'Giá trị giảm (nếu % thì nhập 20 = 20%, nếu tiền thì nhập 50000)',
  `start_date` datetime NOT NULL COMMENT 'Ngày bắt đầu giảm giá',
  `end_date` datetime NOT NULL COMMENT 'Ngày kết thúc giảm giá',
  `max_uses` int(11) DEFAULT 0 COMMENT 'Số lượng tối đa có thể sử dụng (0 = không giới hạn)',
  `current_uses` int(11) DEFAULT 0 COMMENT 'Số lượng đã sử dụng',
  `min_order_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Giá trị đơn hàng tối thiểu để áp dụng',
  `max_discount_amount` decimal(10,2) DEFAULT NULL COMMENT 'Số tiền giảm tối đa (nếu giảm theo %)',
  `usage_limit` int(11) DEFAULT NULL COMMENT 'Giới hạn số lần sử dụng (NULL = không giới hạn)',
  `used_count` int(11) DEFAULT 0 COMMENT 'Số lần đã sử dụng',
  `status` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái: 1 = active, 0 = inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_products`
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

-- --------------------------------------------------------

--
-- Table structure for table `discount_usage`
--

CREATE TABLE `discount_usage` (
  `id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL COMMENT 'Số tiền được giảm',
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
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
  `payment_status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `khachhang`, `hinhthucgiao`, `ngaygiaohang`, `thoigiangiao`, `ghichu`, `tenguoinhan`, `sdtnhan`, `diachinhan`, `thoigiandat`, `tongtien`, `trangthai`, `payment_method`, `payment_status`) VALUES
('DH11', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:33:41 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:33:43', 125000, 1, 'online', 1),
('DH12', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:37:04 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:37:05', 130000, 1, 'online', 0),
('DH13', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:52:21 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:52:22', 175000, 3, 'online', 1),
('DH14', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:56:39 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:56:40', 140000, 1, 'online', 0),
('DH16', '1', 'Giao tận nơi', 'Sun Jun 08 2025 07:13:38 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 07:13:40', 140000, 3, 'online', 1),
('DH17', '1', 'Giao tận nơi', 'Tue Aug 19 2025 11:16:30 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 11:16:33', 175000, 3, 'cod', 1),
('DH18', '1', 'Giao tận nơi', 'Sun Jun 08 2025 07:59:26 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 07:59:30', 160000, 3, 'online', 1),
('DH19', '1', 'Giao tận nơi', 'Tue Aug 19 2025 11:22:31 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 11:22:34', 175000, 1, 'online', 0),
('DH20', '28', 'Giao tận nơi', 'Sat Jun 14 2025 09:49:55 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Tân test mail', '1234123412', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-14 09:50:09', 905000, 1, 'online', 0),
('DH21', '1', 'Giao tận nơi', 'Sat Jun 14 2025 10:08:38 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-14 10:08:43', 228000, 1, 'online', 0),
('DH22', '1', 'Giao tận nơi', 'Sat Jun 14 2025 10:13:21 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-14 10:13:27', 155000, 3, 'cod', 1),
('DH23', '1', 'Giao tận nơi', 'Tue Jul 29 2025 10:10:16 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-07-29 10:10:30', 205000, 4, 'online', 0),
('DH24', '1', 'Giao tận nơi', 'Fri Aug 01 2025 15:24:25 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-01 15:24:27', 165000, 1, 'online', 0),
('DH25', '1', 'Giao tận nơi', 'Tue Aug 19 2025 09:56:23 GMT+0700 (Indochina Time)', 'Giao ngay khi xong', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 09:56:38', 175000, 2, 'online', 1),
('DH26', '1', 'Giao tận nơi', 'Tue Aug 19 2025 10:42:03 GMT+0700 (Indochina Time)', '15:00', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 10:42:09', 205000, 1, 'online', 1),
('DH27', '1', 'Giao tận nơi', 'Tue Aug 19 2025 10:56:50 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 10:56:55', 180000, 1, 'online', 0),
('DH28', '1', 'Giao tận nơi', 'Tue Aug 19 2025 13:28:46 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 13:28:48', 730000, 0, 'cod', 0),
('DH29', '1', 'Giao tận nơi', 'Tue Aug 19 2025 13:30:54 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 13:30:56', 1830000, 0, 'cod', 0),
('DH30', '1', 'Giao tận nơi', 'Tue Aug 19 2025 13:31:10 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 13:31:11', 210000, 0, 'cod', 0),
('DH31', '1', 'Giao tận nơi', 'Tue Aug 19 2025 13:31:30 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 13:31:31', 730000, 4, 'cod', 0),
('DH32', '1', 'Giao tận nơi', 'Tue Aug 19 2025 13:42:33 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-08-19 13:42:35', 3770000, 0, 'cod', 0),
('DH8', '1', 'Giao tận nơi', 'Sun Jun 08 2025 06:21:05 GMT+0700 (Indochina Time)', '', '', 'Phan Nhật Tân', '0123456789', '62 Hậu Ái, Hoài Đức, Hà Nội.', '2025-06-08 06:21:07', 140000, 3, 'online', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
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
-- Dumping data for table `orderdetails`
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
(268747, 'DH28', 7, 'Không có ghi chú', 175000, 4),
(268748, 'DH29', 8, 'Không có ghi chú', 180000, 10),
(268749, 'DH30', 8, 'Không có ghi chú', 180000, 1),
(268750, 'DH31', 7, 'Không có ghi chú', 175000, 4),
(268751, 'DH32', 31, 'Không có ghi chú', 170000, 22);

-- --------------------------------------------------------

--
-- Table structure for table `products`
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
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `status`, `title`, `img`, `category`, `price`, `describes`, `soluong`) VALUES
(1, 1, 'Muôn Kiếp Nhân Sinh (Tập 2)', 'http://localhost/bookstore_datn/assets/img/products/sachhay/Bia-Sach-Muon-Kiep-Nhan-Sinh.jpg', 'Sách Hay', 150000, NULL, 9),
(2, 1, 'Muôn Kiếp Nhân Sinh (Tập 1)', 'http://localhost/bookstore_datn/assets/img/products/sachhay/Bia-Sach-Muon-Kiep-Nhan-Sinh-1.jpg', 'Sách Hay', 145000, 'Phần 1 của bộ sách về triết lý sống', 2),
(3, 1, 'Cây Cam Ngọt Của Tôi', './assets/img/products/sachhay/cay-cam-ngot-cua-toi.jpg', 'Sách Hay', 110000, 'Tiểu thuyết nổi tiếng của José Mauro de Vasconcelos', 0),
(4, 1, 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', './assets/img/products/sachhay/Cho-toi-xin-mot-ve-di-tuoi-tho.jpg', 'Sách Hay', 95000, 'Tác phẩm nổi tiếng của Nguyễn Nhật Ánh', 0),
(5, 1, 'Chủ Nghĩa Khắc Kỷ: Phong Cách Sống Bản Lĩnh Và Bình Thản', './assets/img/products/sachhay/Chu-Nghia-Khac-Ky-Phong-Cach-Song-Ban-Linh-Va-Binh-Than.jpg', 'Sách Hay', 160000, 'Sách về triết học và lối sống khắc kỷ', 30),
(6, 1, 'Đại Dương Đen', './assets/img/products/sachhay/Dai-duong-den.jpg', 'Sách Hay', 125000, 'Tiểu thuyết về những bí ẩn của đại dương', 2000),
(7, 1, 'Chiến Tranh Tiền Tệ (Tập 1): Ai Thật Sự Là Người Giàu Nhất Thế Giới', './assets/img/products/sachhay/Ebook-Chien-tranh-tien-te-Tap-1-Ai-that-su-la-nguoi-giau-nhat-the-gioi.jpg', 'Sách Hay', 175000, 'Phần 1 của bộ sách nổi tiếng về tài chính và quyền lực', 224),
(8, 1, 'Chiến Tranh Tiền Tệ (Tập 2): Sự Thống Trị Của Quyền Lực Tài Chính', './assets/img/products/sachhay/Ebook-Chien-tranh-tien-te-Tap-2-Su-thong-tri-cua-quyen-luc-tai-chinh.jpg', 'Sách Hay', 180000, 'Phần 2 của bộ sách về tài chính và quyền lực', 14),
(9, 1, 'Chiến Tranh Tiền Tệ (Tập 3): Biên Giới Tiền Tệ Nhân Tố Bí Ẩn Trong Các Cuộc Chiến Kinh Tế', './assets/img/products/sachhay/Ebook-Chien-tranh-tien-te-Tap-3-Bien-gioi-tien-te-nhan-to-bi-an-trong-cac-cuoc-chien-kinh-te.jpg', 'Sách Hay', 185000, 'Phần 3 của bộ sách về tài chính toàn cầu', 18),
(10, 1, 'Dám Nghĩ Lại', './assets/img/products/sachhay/Ebook-Dam-nghi-lai.jpg', 'Sách Hay', 120000, 'Sách về tư duy đổi mới và phát triển bản thân', 10),
(11, 1, 'Kẻ Khôn Đi Lối Khác', './assets/img/products/sachhay/Ebook-Ke-khon-di-loi-khac.jpg', 'Sách Hay', 130000, 'Sách về chiến lược sống và thành công', 250),
(12, 1, 'Không Diệt Không Sinh Đừng Sợ Hãi', './assets/img/products/sachhay/Ebook-Khong-diet-khong-sinh-dung-so-hai.jpg', 'Sách Hay', 140000, 'Sách của Thiền sư Thích Nhất Hạnh', 2804),
(13, 1, 'Nghệ Thuật Tư Duy Chiến Lược', './assets/img/products/sachhay/Ebook-Nghe-thuat-tu-duy-chien-luoc.jpg', 'Sách Hay', 155000, 'Sách về phương pháp tư duy và lập chiến lược', 39),
(14, 1, 'Nơi Chuyến Lá Ban Nắng Gió Mêng Là Tử Dương Im Lặng Lá Tri Tuệ Trường Tiêu Hạng', './assets/img/products/sachhay/ebook-Noi-Chuyen-La-Ban-Nang-Giu-Mieng-La-Tu-Duong-Im-Lang-La-Tri-Tue-Truong-Tieu-Hang.jpg', 'Sách Hay', 115000, 'Sách về nghệ thuật giao tiếp và đối nhân xử thế', 41),
(15, 1, 'Sức Mạnh Của Ngôn Từ', './assets/img/products/sachhay/Ebook-Suc-manh-cua-ngon-tu.jpg', 'Sách Hay', 100000, 'Sách về nghệ thuật giao tiếp hiệu quả', 37),
(16, 1, 'Thay Đổi Cuộc Sống Với Nhân Số Học', './assets/img/products/sachhay/Ebook-Thay-doi-cuoc-song-voi-nhan-so-hoc.jpg', 'Sách Hay', 165000, 'Sách về nhân số học và ứng dụng trong cuộc sống', 14),
(17, 1, 'Tư Duy Ngược Dịch Chuyển Thế Giới', './assets/img/products/sachhay/Ebook-Tu-duy-nguoc-dich-chuyen-the-gioi.jpg', 'Sách Hay', 145000, 'Sách về tư duy đột phá và sáng tạo', 44),
(18, 1, 'Muốn An Được An', './assets/img/products/sachhay/Muon-an-duoc-an.jpg', 'Sách Hay', 110000, 'Sách về lòng biết ơn và sự trân trọng', 35),
(19, 0, 'Ngày Xưa Có Một Chuyện Tình', './assets/img/products/sachhay/Ngay-Xua-Co-Mot-Chuyen-Tinh.jpg', 'Sách Hay', 120000, 'Tiểu thuyết lãng mạn', 39),
(20, 1, 'Rừng Nauy', './assets/img/products/sachhay/Rung-nauy.jpg', 'Sách Hay', 135000, 'Tiểu thuyết nổi tiếng của Haruki Murakami', 40),
(21, 1, 'Tết Ở Làng Địa Ngục', './assets/img/products/sachhay/Tet-o-lang-dia-nguc.jpg', 'Sách Hay', 105000, 'Tác phẩm văn học Việt Nam', 31),
(22, 1, 'Thảo Túng Tâm Lý', './assets/img/products/sachhay/thao-tung-tam-ly.jpg', 'Sách Hay', 140000, 'Sách về tâm lý học và các kỹ thuật thao túng', 32),
(23, 1, 'Thế Giới Bên Trong Cái Ác', './assets/img/products/sachhay/The-gioi-ben-trong-cai-ac.jpg', 'Sách Hay', 150000, 'Sách về tâm lý học tội phạm', 15),
(24, 1, 'Tĩnh Lặng', './assets/img/products/sachhay/tinh-lang.jpg', 'Sách Hay', 95000, 'Tiểu thuyết Việt Nam về tình yêu và đời sống', 21),
(25, 1, 'Đánh Thức Tài Năng Toán Học 01 (7-8 tuổi)', './assets/img/products/khoahoc/ebook-danh-thuc-tai-nang-toan-hoc-01-7-8-tuoi.jpg', 'Khoa Học', 85000, 'Sách phát triển tư duy toán học cho trẻ 7-8 tuổi', 9),
(26, 1, 'Đánh Thức Tài Năng Toán Học 02 (8-9 tuổi)', './assets/img/products/khoahoc/ebook-danh-thuc-tai-nang-toan-hoc-02-8-9-tuoi.jpg', 'Khoa Học', 85000, 'Sách phát triển tư duy toán học cho trẻ 8-9 tuổi', 24),
(27, 1, 'Đánh Thức Tài Năng Toán Học 03 (9-10 tuổi)', './assets/img/products/khoahoc/ebook-danh-thuc-tai-nang-toan-hoc-03-9-10-tuoi.jpg', 'Khoa Học', 85000, 'Sách phát triển tư duy toán học cho trẻ 9-10 tuổi', 41),
(28, 1, 'Não Bộ Kể Gì Về Bạn', './assets/img/products/khoahoc/Ebook-Nao-bo-ke-gi-ve-ban.jpg', 'Khoa Học', 140000, 'Sách về khoa học não bộ và tâm lý học', 37),
(29, 1, 'Thám Hóa Khí Hậu', './assets/img/products/khoahoc/Ebook-Tham-hoa-khi-hau.jpg', 'Khoa Học', 155000, 'Sách về biến đổi khí hậu và tác động đến môi trường', 11),
(30, 1, 'Vật Lý Của Những Điều Tưởng Chừng Bất Khả', './assets/img/products/khoahoc/Ebook-Vat-ly-cua-nhung-dieu-tuong-chung-bat-kha.jpg', 'Khoa Học', 165000, 'Sách về các hiện tượng vật lý phức tạp và ứng dụng', 30),
(31, 1, 'Vật Lý Của Tương Lai', './assets/img/products/khoahoc/Ebook-Vat-ly-cua-tuong-lai.jpg', 'Khoa Học', 170000, 'Sách về các xu hướng phát triển của ngành vật lý hiện đại', 0),
(32, 1, 'Lịch Sử Vạn Vật', './assets/img/products/khoahoc/lich-su-van-vat_1.jpg', 'Khoa Học', 180000, 'Sách về lịch sử phát triển của vạn vật trong vũ trụ', 15),
(33, 1, 'Thế Giới Lượng Tử Kỳ Bí', './assets/img/products/khoahoc/The-gioi-luong-tu-ky-bi.jpg', 'Khoa Học', 150000, 'Sách về vật lý lượng tử và những bí ẩn của thế giới vi mô', 6),
(34, 1, 'Thuyết Tương Đối Cho Mọi Người ', './assets/img/products/Khoa Học/683400127d046_4510cb11f908b9f0.jpg', 'Khoa Học', 145000, 'Sách giải thích thuyết tương đối của Einstein dành cho độc giả phổ thông', 25),
(35, 1, 'Cây Cam Ngọt Của Tôi', './assets/img/products/tieuthuyet/cay-cam-ngot-cua-toi.jpg', 'Tiểu Thuyết', 120000, 'Tiểu thuyết nổi tiếng của José Mauro de Vasconcelos', 12),
(36, 1, 'Đồi Gió Hú', './assets/img/products/tieuthuyet/Doi-gio-hu.jpg', 'Tiểu Thuyết', 135000, 'Tiểu thuyết kinh điển của Emily Brontë', 22),
(37, 1, 'Đời Nhẹ Khôn Kham', './assets/img/products/tieuthuyet/Doi-Nhe-Khon-Kham.jpg', 'Tiểu Thuyết', 115000, 'Tác phẩm văn học cảm động về tình người', 38),
(38, 1, 'Mùa Lá Rụng Trong Vườn', './assets/img/products/tieuthuyet/Ebook-Mua-la-rung-trong-vuon.jpg', 'Tiểu Thuyết', 110000, 'Tiểu thuyết lãng mạn đầy cảm xúc', 20),
(39, 1, 'Mù Lòa', './assets/img/products/tieuthuyet/Ebook-Mu-loa.jpg', 'Tiểu Thuyết', 125000, 'Tiểu thuyết triết lý sâu sắc về cuộc sống', 29),
(40, 1, 'Số 31 Đường Giác Mơ', './assets/img/products/tieuthuyet/Ebook-So-31-duong-Giac-Mo.jpg', 'Tiểu Thuyết', 130000, 'Tiểu thuyết huyền bí với những bí mật được hé lộ', 32),
(41, 1, 'Hai Số Phận', './assets/img/products/tieuthuyet/Hai-So-Phan.jpg', 'Tiểu Thuyết', 160000, 'Tiểu thuyết nổi tiếng của Jeffrey Archer', 25),
(42, 1, 'Mắt Biếc - Nguyễn Nhật Ánh', './assets/img/products/tieuthuyet/Mat-Biec-Nguyen-Nhat-Anh-min.jpg', 'Tiểu Thuyết', 105000, 'Tác phẩm nổi tiếng của nhà văn Nguyễn Nhật Ánh', 21),
(43, 1, 'Ngày Xưa Có Một Chuyện Tình', './assets/img/products/tieuthuyet/Ngay-Xua-Co-Mot-Chuyen-Tinh.jpg', 'Tiểu Thuyết', 115000, 'Câu chuyện tình yêu đầy xúc động', 29),
(44, 1, 'Những Người Khốn Khổ', './assets/img/products/tieuthuyet/Nhung-nguoi-khon-kho.jpg', 'Tiểu Thuyết', 175000, 'Tác phẩm kinh điển của đại văn hào Victor Hugo', 29),
(45, 1, 'Nửa Kia Của Hitler - Eric-Emmanuel Schmitt', './assets/img/products/tieuthuyet/Nua-Kia-Cua-Hitler-Eric-Emmanuel-Schmitt.jpg', 'Tiểu Thuyết', 140000, 'Tiểu thuyết táo bạo về lịch sử thế giới', 10),
(46, 1, 'Quán Gò Đi Lên', './assets/img/products/tieuthuyet/Quan-Gio-Di-Len.jpg', 'Tiểu Thuyết', 120000, 'Câu chuyện về những số phận đan xen', 48),
(47, 1, 'Sáu Người Đi Khắp Thế Gian', './assets/img/products/tieuthuyet/Sau-nguoi-di-khap-the-gian.jpg', 'Tiểu Thuyết', 145000, 'Tiểu thuyết phiêu lưu đầy cảm hứng', 24),
(48, 1, 'Tắt Đèn', './assets/img/products/tieuthuyet/tat-den.jpg', 'Tiểu Thuyết', 95000, 'Tác phẩm văn học Việt Nam kinh điển của Ngô Tất Tố', 17),
(49, 1, 'The Hobbit', './assets/img/products/tieuthuyet/the-hobbit.jpg', 'Tiểu Thuyết', 150000, 'Tiểu thuyết giả tưởng nổi tiếng của J.R.R. Tolkien', 8),
(50, 1, 'Thép Là Tôi Thế Đấy', './assets/img/products/tieuthuyet/thep-da-toi-the-day-minh-thang-scaled.jpg', 'Tiểu Thuyết', 135000, 'Tiểu thuyết kinh điển của Nikolai Ostrovsky', 32),
(51, 1, 'Thủy Hử', './assets/img/products/tieuthuyet/Thuy-Hu.jpg', 'Tiểu Thuyết', 185000, 'Tiểu thuyết cổ điển Trung Quốc nổi tiếng', 40),
(52, 1, 'Tiếng Gọi Nơi Hoang Dã', './assets/img/products/tieuthuyet/Tieng-goi-noi-hoang-da.jpg', 'Tiểu Thuyết', 110000, 'Tác phẩm nổi tiếng của Jack London', 8),
(53, 1, 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', './assets/img/products/tieuthuyet/Toi-thay-hoa-vang-tren-co-xanh.jpg', 'Tiểu Thuyết', 105000, 'Tiểu thuyết được yêu thích của Nguyễn Nhật Ánh', 6),
(54, 1, 'Xứ Cát', './assets/img/products/tieuthuyet/xucat-e1693798884621.jpg', 'Tiểu Thuyết', 125000, 'Tiểu thuyết hiện đại đầy ấn tượng', 47),
(55, 1, 'Chuyện con mèo dạy hải âu bay', './assets/img/products/thieunhi/chuyen-con-meo-day-hai-au-bay.jpg', 'Thiếu Nhi', 89000, 'Câu chuyện cảm động giữa mèo và chim hải âu', 30),
(56, 1, 'Dế Mèn Phiêu Lưu Ký', './assets/img/products/thieunhi/de-men-phieu-luu-ky.jpg', 'Thiếu Nhi', 99000, 'Tác phẩm thiếu nhi kinh điển của Tô Hoài', 48),
(57, 1, 'Cuộc đời và những cuộc phiêu lưu của Santa Claus', './assets/img/products/thieunhi/Ebook-Cuoc-doi-va-nhung-cuoc-phieu-luu-cua-Santa-Claus.jpg', 'Thiếu Nhi', 79000, 'Khám phá cuộc đời kỳ thú của ông già Noel', 14),
(58, 1, 'Năm đứa trẻ và điều ước kỳ lạ', './assets/img/products/thieunhi/Ebook-Nam-dua-tre-va-no-Five-children-and-It.jpg', 'Thiếu Nhi', 86000, 'Một hành trình kỳ diệu đầy phép màu', 16),
(59, 1, 'Peter Pan', './assets/img/products/thieunhi/Ebook-peter-pan.jpg', 'Thiếu Nhi', 92000, 'Chuyến phiêu lưu tới Neverland bất tận', 28),
(60, 1, 'Wolfgang Amadeus Mozart là ai', './assets/img/products/thieunhi/Ebook-Wolfgang-Amadeus-Mozart-la-ai.jpg', 'Thiếu Nhi', 85000, 'Tiểu sử hấp dẫn về thiên tài âm nhạc Mozart', 44),
(61, 1, 'Hoàng tử bé', './assets/img/products/thieunhi/hoang-tu-be.jpg', 'Thiếu Nhi', 78000, 'Tác phẩm triết lý sâu sắc dưới dạng truyện thiếu nhi', 37),
(62, 1, 'Lâu đài bay của pháp sư Howl', './assets/img/products/thieunhi/lau-dai-bay-cua-phap-su-howl.jpg', 'Thiếu Nhi', 98000, 'Câu chuyện phép thuật ly kỳ và cảm động', 50),
(63, 0, 'Tắt đèn', './assets/img/products/67f108c29267d_a4f5a2d965481841.jpg', 'Sách Hay', 12000, 'Tác phẩm kinh điển của Ngô Tất Tố', 40),
(64, 1, 'Thế giới bên trong cái ác', './assets/img/products/default/6829aeffac0f2_528e7d30de122f3a.jpg', 'Sách Hay', 90000, 'Test import ', 48),
(66, 1, 'Thế giới lượng tử kỳ bí', 'http://localhost/bookstore_datn/assets/img/products/default/6829ab5d75cf5_0cedcd07ab238c92.jpg', 'Khoa Học', 100000, 'Tôi mong muốn khám phá sâu hơn cấu trúc ẩn sau những hành vi tự nhiên, để hiểu rõ hơn về bí ẩn của thế giới chúng ta sống. Khi tôi còn 15 tuổi, sự tò mò và khát khao tìm hiểu không ngừng đã dẫn tôi đến với lĩnh vực vật lý lượng tử qua các bài giảng và tài liệu khoa học. Điều này đã mở ra cửa hiểu biết đầu tiên về vũ trụ tuyệt diệu này.\r\n\r\nNiềm say mê với vật lý lượng tử ngày càng trở nên mạnh mẽ hơn. Tôi đã bắt đầu đặt ra những câu hỏi thách thức và đòi hỏi sự giải đáp. Tuy nhiên, không ai có thể đưa ra những câu trả lời mà tôi đang tìm kiếm. Tôi đã dành hai năm để nghiên cứu về vật lý lượng tử, và sau khi thấu hiểu sâu hơn, tôi cảm nhận một áp lực về việc sắp xếp lại những kiến thức đã thu thập.\r\n\r\nTừ ý tưởng này, tôi quyết định chia sẻ kiến thức của mình thông qua văn bản, tạo ra một tác phẩm mà tôi hy vọng sẽ truyền tải được các chủ đề quan trọng và các hiệu ứng đáng chú ý trong vật lý lượng tử dưới góc nhìn của riêng tôi. Tôi rất hào hứng khi bắt đầu dựng lên một cấu trúc mới, tạo ra một cuốn sách giáo khoa độc đáo, nối liền khoảng trống giữa những tài liệu khoa học phổ biến và các tài liệu nghiên cứu chính thống.\r\n\r\nĐó là lý do tại sao tôi tự hào giới thiệu cuốn sách “Thế Giới Lượng Tử Kỳ Bí”. Chúng tôi hy vọng rằng, thông qua cuốn sách này, bạn cũng sẽ có cơ hội khám phá những điều tuyệt diệu về vật lý lượng tử và thế giới xung quanh chúng ta.', 23);

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `phone`, `password`, `address`, `email`, `status`, `join_date`, `userType`, `reset_token`) VALUES
(1, 'Phan Nhật Tân', '0123456789', '123456', '62 Hậu Ái, Hoài Đức, Hà Nội.', 'ritoluki@gmail.com', 1, '2025-03-05 03:08:37', 1, '4f7830fac6c47cf5bb3eb2e7bcc6390a7eb7cbbeded638deb465abfa7c23c54c'),
(27, 'Tân Tân', '123456', '123456', '', '', 1, '2025-04-02 02:11:04', 0, NULL),
(28, 'Tân test mail', '1234123412', 'Tan123@@', '62 Hậu Ái, Hoài Đức, Hà Nội.', '3najchuoj9@gmail.com', 1, '2025-04-02 03:40:58', 0, '8df25cbeb385f19a69eba695998ce66a95957197fada08525cfe28b5071f99bb'),
(29, 'Tân test Form', '1234567890', '123123', '', '123@ww.com', 1, '2025-04-04 22:37:47', 0, NULL),
(34, 'Nguyễn Văn A', '0842717777', '123456', '', 'iyi55272@toaik.com', 1, '2025-05-18 03:10:24', 0, NULL),
(35, 'PHAN NHAT TAN', '0842717778', '123456', '', '20212270@eaut.edu.vn', 1, '2025-05-18 03:33:43', 0, NULL),
(38, 'Tan', '0842717889', 'Tan123@@', '', 'ritoluki1@gmail.com', 1, '2025-07-18 01:52:32', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book_reviews`
--
ALTER TABLE `book_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`idcart`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_products`
--
ALTER TABLE `discount_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `discount_usage`
--
ALTER TABLE `discount_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `madon` (`madon`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book_reviews`
--
ALTER TABLE `book_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `idcart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_products`
--
ALTER TABLE `discount_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount_usage`
--
ALTER TABLE `discount_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268752;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_reviews`
--
ALTER TABLE `book_reviews`
  ADD CONSTRAINT `book_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `book_reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `book_reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`);

--
-- Constraints for table `discount_products`
--
ALTER TABLE `discount_products`
  ADD CONSTRAINT `discount_products_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discount_usage`
--
ALTER TABLE `discount_usage`
  ADD CONSTRAINT `discount_usage_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_usage_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_usage_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`madon`) REFERENCES `order` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
