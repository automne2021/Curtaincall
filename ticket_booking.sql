-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 06, 2025 lúc 06:52 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ticket_booking`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `play_id` varchar(5) NOT NULL,
  `theater_id` varchar(3) NOT NULL,
  `seat_id` varchar(3) NOT NULL,
  `status` enum('Pending','Paid','Expired','Cancelled') DEFAULT 'Pending',
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `plays`
--

CREATE TABLE `plays` (
  `play_id` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `theater_id` varchar(3) NOT NULL,
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `plays`
--

INSERT INTO `plays` (`play_id`, `title`, `description`, `image`, `theater_id`, `views`) VALUES
('IDE00', 'Nhà Hát Kịch IDECAF: Đức Thượng Công Tả Quân LÊ VĂN DUYỆT', '...', 'public\\images\\plays\\LeVanDuyet.jpg', 'IDE', 0),
('IDE01', 'Nhà Hát Kịch IDECAF: Một Ngày Làm VUA', '- Tác giả: Viễn Hùng\r\n-\nĐạo diễn: Hùng Lâm - Đình Toàn\n- Diễn viên: Đình Toàn, Quốc Thịnh, Quang Thảo, Đại Nghĩa, Hồng Ánh, Mỹ Duyên, Cẩm Hò, Bạch Long, Tâm Anh, Quốc Tuấn, Bảo Cường, Phước Lộc, Thái Hiển, Trúc My, Hoài Trang, Phạm Hạnh, Bích Trâm.', 'public\\images\\plays\\MotNgayLamVua.jpg', 'IDE', 0),
('IDE02', 'Nhà Hát Kịch IDECAF: VÀNG ơi là VÀNG!', '...', 'public\\images\\plays\\VANG.jpg', 'IDE', 0),
('IDE03', 'Nhà Hát Kịch IDECAF: 12 Bà Mụ', '...', 'public\\images\\plays\\12BaMu.jpg', 'IDE', 0),
('IDE04', 'Nhà Hát Kịch IDECAF: LƯƠNG SƠN BÁ CHÚC ANH ĐÀI ngoại truyện', 'Tác giả: Trung Tín\r\nĐạo diễn: Đình Toàn\r\nDiễn viên: Đình Toàn - Trà Ngọc - Đại Nghĩa - Đông Hải - Cẩm Hò - Trịnh Minh Dũng - Quang Thảo - Mỹ Duyên - Quốc Tuấn - Phi Nga - Tâm Anh - Quách Bình - Trung Tín - Mai Phượng - Thiên Trang - Việt Trang.', 'public\\images\\plays\\LuongSonBa.jpg', 'IDE', 0),
('IDE05', 'Nhà Hát Kịch IDECAF: TẤM CÁM ĐẠI CHIẾN!', '- Tác giả: Bảo Ngọc - Hùng Lâm\n- Đạo diễn: Hùng Lâm\n- Diễn viên: Đình Toàn, Đại Nghĩa, Tuyền Mập, Mỹ Duyên, Hòa Hiệp, Bạch Long, Trịnh Minh Dũng, Đông Hải, Tâm Anh, Mai Phượng, Thiên Trang, Cẩm Hò, Việt Trang và Nhà Hát Thiếu Nhi Nụ Cười', 'public\\images\\plays\\TamCamDaiChien.jpg', 'IDE', 0),
('IDE06', 'Nhà Hát Kịch IDECAF: THUỐC ĐẮNG GIÃ TẬT', '...', 'public\\images\\plays\\ThuocDangGiaTat.jpg', 'IDE', 0),
('IDE07', 'Nhà Hát Kịch IDECAF: Dưới Bóng Giai Nhân', '...', 'public\\images\\plays\\DuoiBongGiaiNhan.jpg', 'IDE', 0),
('IDE08', 'Nhà Hát Kịch IDECAF: Cái gì Vui Vẻ thì mình Ưu Tiên', '...', 'public\\images\\plays\\CaigiVUIVE.jpg', 'IDE', 0),
('SKN00', 'Sân khấu 5B : Kịch thiếu nhi \"ĐẠI NÁO LONG CUNG\"', '..', 'public\\images\\plays\\DaiNaoLongCung.jpg', 'SKN', 0),
('SKN01', 'Sân khấu 5B : Kịch thiếu nhi \"CÂY BÚT THẦN\"', '...', 'public\\images\\plays\\CayButThan.jpg', 'SKN', 0),
('SKN02', 'Sân khấu 5B : Kịch thiếu nhi \"TRẠM CỨU HỘ ĐỘNG VẬT\"', '...', 'public\\images\\plays\\TramCuuHoDV.jpg', 'SKN', 0),
('THM00', 'SKNT TRƯƠNG HÙNG MINH : CẦU DỪA ĐỦ XÀI', '...', 'public\\images\\plays\\CauDuaDuXai.jpg', 'THM', 0),
('THM01', 'SKNT TRƯƠNG HÙNG MINH : NGÀY MAI NGƯỜI TA LẤY CHỒNG', '...', 'public\\images\\plays\\NgtaLayChong.jpg', 'THM', 0),
('THM02', 'SKNT TRƯƠNG HÙNG MINH : BỖNG DƯNG TRÚNG SỐ', '...', 'public\\images\\plays\\BongDungTrungSo.jpg', 'THM', 0),
('THN00', '[Nhà hát Thanh Niên] Hài Kịch: Đại Minh Tinh', '...', 'public\\images\\plays\\DaiMinhTinh.jpg', 'THN', 0),
('THN01', '[Nhà Hát THANH NIÊN] Hài Kịch: Đại Hội Yêu Quái - 7 Con Yêu Nhền Nhện', '...', 'public\\images\\plays\\7NhenNhen.jpg', 'THN', 0),
('THN02', '[Nhà Hát THANH NIÊN] Hài kịch: Thanh Xà Bạch Xà ngoại truyện', '...', 'public\\images\\plays\\ThanhXaBachXa.jpg', 'THN', 0),
('THN03', '[Nhà Hát THANH NIÊN] Hài kịch: Lạc lối ở BangKok', '- Tác giả: Nguyễn Bảo Ngọc\n- Đạo diễn: Hồng Ngọc\n- Diễn viên: Khương Ngọc, BB Trần, Ngọc Phước, Tuấn Kiệt, Long Chun, Bé 7, Mai Bảo Vinh, Duy Tiến, Huỳnh Thi, Tạ Lâm, Vương Chí Nam, Mạnh Lân, Phạm Gia Minh...', 'public\\images\\plays\\LacLoiBangkok.jpg', 'THN', 0),
('THN04', '[Nhà hát Thanh Niên] Hài Kịch: Tung Hoành Pattaya', 'Tác giả: Nguyễn Duy Xăng - Hiếu Nghĩa - Long Duy\nĐạo diễn:  Hồng Ngọc\nDiễn viên: Huỳnh Nhựt, Hải Triều, Tuấn Kiệt, Mai Bảo Vinh, Long Chun, Bé 7, Giỏi Lee, Duy Tiến, Huỳnh Thi, Huyền Duy, Lê Nghĩa và Khương Ngọc.', 'public\\images\\plays\\TungHoanhPattaya.jpg', 'THN', 0),
('THĐ00', 'SÂN KHẤU THIÊN ĐĂNG - VỞ KỊCH: CÔ GIÁO DUYÊN', '...', 'public\\images\\plays\\CoGiaoDuyen.jpg', 'THĐ', 0),
('THĐ01', 'SÂN KHẤU THIÊN ĐĂNG: NGŨ QUÝ TƯƠNG PHÙNG', 'Sân khấu Nghệ Thuật Thiên Đăng trân trọng giới thiệu đến quý khán giả vở hài kịch dân gian\n\"NGŨ QUÝ TƯƠNG PHÙNG\"   -- Phiên Bản Mới Nhất --\n\nTác giả: TUẤN KHÔI - HƯƠNG GIANG\nĐạo diễn: TUẤN KHÔI\nVới sự tham gia của các nghệ sĩ: NSƯT THÀNH LỘC, NSƯT HỮU CHÂU, LƯƠNG THẾ THÀNH, HƯƠNG GIANG, HUY TỨ, TRƯƠNG HẠ, QUỐC TRUNG, KIỀU NGÂN, TRANG TUYỀN, MẠNH HÙNG, HOÀNG KHANH\n\nTrân trọng kính mời quý khán giả.\nSự kiện có xuất VAT. Khách hàng lưu ý gửi thông tin cần thiết trong vòng 24h kể từ khi mua vé để được hỗ trợ. Sau thời gian trên, BTC xin phép được từ chối xuất hoá đơn.\nTrẻ em từ 8 tuổi trở lên sẽ có thể mua vé tham dự vờ diễn.', 'public\\images\\plays\\NguQuyTuongPhung.jpg', 'THĐ', 0),
('THĐ02', 'SÂN KHẤU THIÊN ĐĂNG: CHUYẾN ĐÒ ĐỊNH MỆNH', 'Sân khấu Kịch Thiên Đăng trân trọng giới thiệu vở diễn MỚI NHẨT\r\n\"CHUYẾN ĐÒ ĐỊNH MỆNH\"\nTác phẩm mới được dàn dựng bởi Đạo diễn - Nhà Giáo - NSND TRẦN MINH NGỌC\r\nTác giả : NGUYỄN HUY THIỆP\n\nVới sự tham gia của các diễn viên: NSUT THÀNH LỘC - NSUT HỮU CHÂU - NSƯT MẠNH HÙNG\nCác diễn viên: HƯƠNG GIANG, TRANG TUYỀN, LƯƠNG THẾ THÀNH, HỒNG NGỌC, HOÀNG KHANH\nSự kiện có xuất VAT. Khách hàng lưu ý gửi thông tin cần thiết trong vòng 24h kể từ khi mua vé để được hỗ trợ. Sau thời gian trên, BTC xin phép được từ chối xuất hoá đơn.\nTrẻ em từ 8 tuổi trở lên sẽ có thể mua vé tham dự vờ diễn.\n\nTrân trọng kính mời quý khách!', 'public\\images\\plays\\ChuyenDoDinhMenh.jpg', 'THĐ', 0),
('THĐ03', 'SÂN KHẤU THIÊN ĐĂNG: NHỮNG CON MA NHÀ HÁT', '...', 'public\\images\\plays\\MaNhaHat.jpg', 'THĐ', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `schedules`
--

CREATE TABLE `schedules` (
  `play_id` varchar(5) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `schedules`
--

INSERT INTO `schedules` (`play_id`, `date`, `start_time`, `end_time`) VALUES
('IDE00', '2025-06-09', '16:00:00', '19:00:00'),
('IDE01', '2025-06-11', '19:30:00', '22:30:00'),
('IDE02', '2025-05-13', '19:30:00', '22:30:00'),
('IDE03', '2025-05-14', '19:30:00', '22:30:00'),
('IDE04', '2025-05-15', '19:30:00', '22:30:00'),
('IDE05', '2025-06-16', '18:00:00', '21:00:00'),
('IDE06', '2025-06-20', '19:30:00', '22:30:00'),
('IDE07', '2025-05-22', '19:30:00', '22:30:00'),
('IDE08', '2025-04-23', '18:00:00', '21:00:00'),
('SKN00', '2025-05-15', '15:00:00', '17:00:00'),
('SKN01', '2025-04-23', '15:00:00', '17:00:00'),
('SKN02', '2025-04-29', '15:00:00', '17:00:00'),
('THM00', '2025-06-09', '19:30:00', '22:30:00'),
('THM01', '2025-05-22', '19:30:00', '22:30:00'),
('THM02', '2025-04-23', '19:30:00', '22:30:00'),
('THN01', '2025-04-28', '19:00:00', '21:00:00'),
('THN02', '2025-05-08', '19:00:00', '21:00:00'),
('THN03', '2025-06-12', '20:00:00', '23:00:00'),
('THN04', '2025-05-14', '19:00:00', '21:00:00'),
('THĐ00', '2025-07-01', '19:30:00', '22:30:00'),
('THĐ01', '2025-05-20', '19:30:00', '22:30:00'),
('THĐ02', '2025-06-22', '19:30:00', '22:00:00'),
('THĐ03', '2025-04-23', '18:00:00', '20:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seats`
--

CREATE TABLE `seats` (
  `theater_id` varchar(3) NOT NULL,
  `play_id` varchar(5) NOT NULL,
  `seat_id` varchar(3) NOT NULL,
  `status` enum('Available','Pending','Booked') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seat_maps`
--

CREATE TABLE `seat_maps` (
  `theater_id` varchar(3) NOT NULL,
  `seat_id` varchar(3) NOT NULL,
  `seat_type` enum('VIP','Regular') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seat_prices`
--

CREATE TABLE `seat_prices` (
  `theater_id` varchar(3) NOT NULL,
  `seat_type` enum('VIP','Regular') NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `seat_prices`
--

INSERT INTO `seat_prices` (`theater_id`, `seat_type`, `price`) VALUES
('IDE', 'VIP', 320000.00),
('IDE', 'Regular', 270000.00),
('SKN', 'Regular', 270000.00),
('THM', 'VIP', 350000.00),
('THM', 'Regular', 300000.00),
('THN', 'VIP', 350000.00),
('THN', 'Regular', 300000.00),
('THĐ', 'VIP', 400000.00),
('THĐ', 'Regular', 330000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theaters`
--

CREATE TABLE `theaters` (
  `theater_id` varchar(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `theaters`
--

INSERT INTO `theaters` (`theater_id`, `name`, `location`) VALUES
('IDE', 'Nhà Hát Kịch IDECAF', 'Số 28 Lê Thánh Tôn, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh'),
('SKN', 'Nhà hát Kịch Sân khấu Nhỏ', '5B, Võ Văn Tần, Phường 06, Quận 3, Thành Phố Hồ Chí Minh'),
('THM', 'SÂN KHẤU NGHỆ THUẬT TRƯƠNG HÙNG MINH', '22 VĨNH VIỄN, Phường 02, Quận 10, Thành Phố Hồ Chí Minh'),
('THN', 'Nhà Hát THANH NIÊN', '4 Phạm Ngọc Thạch, Bến Nghé, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh'),
('THĐ', 'Sân Khấu Thiên Đăng', 'Tầng 12B tòa nhà IMC - 62 Trần Quang Khải, Phường Tân Định, Quận 1, Thành Phố Hồ Chí Minh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `play_id` (`play_id`,`theater_id`,`seat_id`);

--
-- Chỉ mục cho bảng `plays`
--
ALTER TABLE `plays`
  ADD PRIMARY KEY (`play_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`play_id`,`date`,`start_time`);

--
-- Chỉ mục cho bảng `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`theater_id`,`play_id`,`seat_id`),
  ADD KEY `play_id` (`play_id`),
  ADD KEY `theater_id` (`theater_id`,`seat_id`);

--
-- Chỉ mục cho bảng `seat_maps`
--
ALTER TABLE `seat_maps`
  ADD PRIMARY KEY (`theater_id`,`seat_id`);

--
-- Chỉ mục cho bảng `seat_prices`
--
ALTER TABLE `seat_prices`
  ADD PRIMARY KEY (`theater_id`,`seat_type`);

--
-- Chỉ mục cho bảng `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`theater_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`play_id`,`theater_id`,`seat_id`) REFERENCES `seats` (`play_id`, `theater_id`, `seat_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `plays`
--
ALTER TABLE `plays`
  ADD CONSTRAINT `plays_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`theater_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `plays` (`play_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`play_id`) REFERENCES `plays` (`play_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seats_ibfk_2` FOREIGN KEY (`theater_id`,`seat_id`) REFERENCES `seat_maps` (`theater_id`, `seat_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seat_maps`
--
ALTER TABLE `seat_maps`
  ADD CONSTRAINT `seat_maps_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`theater_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seat_prices`
--
ALTER TABLE `seat_prices`
  ADD CONSTRAINT `seat_prices_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`theater_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
