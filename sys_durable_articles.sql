-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 07:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sys_durable_articles`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_borrowing`
--

CREATE TABLE `tb_borrowing` (
  `borrowing_id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `durable_articles_id` int(11) NOT NULL,
  `status_of_use` enum('Borrowed','Free','Unavailable') NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `time_borrow` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_borrowing`
--

INSERT INTO `tb_borrowing` (`borrowing_id`, `member_id`, `durable_articles_id`, `status_of_use`, `room_id`, `time_borrow`) VALUES
(3, 1234568, 145002, 'Borrowed', 2212, '2025-03-25 13:54:14'),
(4, NULL, 145022, 'Free', 2207, '2025-03-24 13:54:14'),
(5, 1234567, 145012, 'Borrowed', 2207, '2025-03-25 13:54:58'),
(6, NULL, 145011, 'Free', 2207, '2025-03-24 13:54:58'),
(7, 1234569, 145011, 'Borrowed', 2307, '2025-03-25 13:54:58'),
(8, NULL, 145025, 'Free', 2301, '2025-03-24 13:54:58');

-- --------------------------------------------------------

--
-- Table structure for table `tb_durable_articles`
--

CREATE TABLE `tb_durable_articles` (
  `durable_articles_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `series` varchar(255) NOT NULL,
  `durable_articles_number` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) NOT NULL,
  `condition_of_use` enum('Working','Broken','Damaged','Sold') NOT NULL,
  `price` int(11) NOT NULL,
  `year_of_purchase` int(11) NOT NULL,
  `annual_warranty` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_durable_articles`
--

INSERT INTO `tb_durable_articles` (`durable_articles_id`, `name`, `brand`, `series`, `durable_articles_number`, `serial_number`, `condition_of_use`, `price`, `year_of_purchase`, `annual_warranty`, `description`, `note`) VALUES
(145001, 'CPU พร้อม DVD-RW ยี่ห้อ ไม่มี รุ่น CB 31005 S/N ไม่มี', 'HP', ' Pro Tower 280 G9', '7450-010-15778', '4CE430B6MP', 'Working', 16350, 2567, 3, 'จอภาพ ขนาด 21.5 นิ้ว		7450-010-15778(1)		  4,500 	HP	P22 G5	CNC4280LK9', ''),
(145002, 'CPU พร้อม DVD-RW ยี่ห้อ ไม่มี รุ่น CB 31005 S/N ไม่มี', 'HP', ' Pro Tower 280 G9', '7450-010-15779', '4CE430B6MZ', 'Broken', 16350, 2567, 3, 'จอภาพ ขนาด 21.5 นิ้ว		7450-010-15779(1)		  4,500 	HP	P22 G5	CNC4290RN0', ''),
(145003, 'CPU พร้อม DVD-RW ยี่ห้อ ไม่มี รุ่น CB 31005 S/N ไม่มี', 'HP', ' Pro Tower 280 G9', '7450-010-15780', '4CE430BKTT', 'Damaged', 16350, 2567, 3, 'จอภาพ ขนาด 21.5 นิ้ว		7450-010-15780(1)		  4,500 	HP	P22 G5	CNC4290RLS', ''),
(145004, 'CPU พร้อม DVD-RW ยี่ห้อ ไม่มี รุ่น CB 31005 S/N ไม่มี', 'HP', ' Pro Tower 280 G9', '7450-010-15781', '4CE430BKR7', 'Sold', 16350, 2567, 3, 'จอภาพ ขนาด 21.5 นิ้ว		7450-010-15781(1)		  4,500 	HP	P22 G5	CNC4280LKH', ''),
(145005, 'CPU พร้อม DVD-RW ยี่ห้อ ไม่มี รุ่น CB 31005 S/N ไม่มี', 'HP', ' Pro Tower 280 G9', '7450-010-15782', '4CE430BKT1', 'Working', 16350, 2567, 3, 'จอภาพ ขนาด 21.5 นิ้ว		7450-010-15782(1)		  4,500 	HP	P22 G5	CNC4290RMT', ''),
(145011, 'ไมค์', 'Shure', 'SM58', '7450-010-20001', 'MIC20240001', 'Working', 4500, 2566, 3, NULL, NULL),
(145012, 'ไมค์', 'Shure', 'SM58', '7450-010-20002', 'MIC20240002', 'Broken', 4500, 2568, 3, NULL, NULL),
(145013, 'ไมค์', 'Shure', 'SM58', '7450-010-20003', 'MIC20240003', 'Working', 4500, 2566, 3, NULL, NULL),
(145015, 'ไมค์', 'Shure', 'SM58', '7450-010-20005', 'MIC20240005', 'Working', 4500, 2566, 3, NULL, NULL),
(145019, 'ไมค์', 'Shure', 'SM58', '7450-010-20004', 'MIC20240004', 'Broken', 4500, 2568, 3, NULL, NULL),
(145021, 'ลำโพง', 'JBL', 'PartyBox 310', '7450-010-20006', 'SPK20240001', 'Working', 6500, 2568, 1, NULL, NULL),
(145022, 'ลำโพง', 'JBL', 'PartyBox 310', '7450-010-20007', 'SPK20240002', 'Sold', 6500, 2560, 1, NULL, NULL),
(145023, 'ลำโพง', 'JBL', 'PartyBox 310', '7450-010-20008', 'SPK20240003', 'Sold', 6500, 2560, 1, NULL, NULL),
(145024, 'ลำโพง', 'JBL', 'PartyBox 310', NULL, 'SPK20240006', 'Working', 6500, 2568, 1, NULL, NULL),
(145025, 'ลำโพง', 'JBL', 'PartyBox 310', NULL, 'SPK20240005', 'Working', 6500, 2568, 1, NULL, NULL),
(145026, 'ลำโพง', 'JBL', 'PartyBox 310', '7450-010-20009', 'SPK20240006', 'Working', 6500, 2568, 1, NULL, NULL),
(145027, 'ลำโพง', 'JBL', 'PartyBox 310', '7450-010-20010', 'SPK20240007', 'Sold', 6500, 2560, 1, NULL, NULL),
(145028, 'ลำโพง', 'JBL', 'PartyBox 310', '7450-010-20011', 'SPK20240008', 'Sold', 6500, 2560, 1, NULL, NULL),
(145029, 'ลำโพง', 'JBL', 'PartyBox 310', NULL, 'SPK20240009', 'Working', 6500, 2568, 1, NULL, NULL),
(145030, 'ลำโพง', 'JBL', 'PartyBox 310', NULL, 'SPK20240010', 'Working', 6500, 2568, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_member`
--

CREATE TABLE `tb_member` (
  `member_id` int(11) NOT NULL,
  `academic_ranks` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` enum('female','male','LGBTQ+') NOT NULL,
  `date_of_birth` date NOT NULL,
  `position_id` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_member`
--

INSERT INTO `tb_member` (`member_id`, `academic_ranks`, `first_name`, `last_name`, `gender`, `date_of_birth`, `position_id`, `phone_number`, `password`) VALUES
(1234567, 'อ.', 'วุฒิพงษ์', 'เรือนทอง', 'male', '0000-00-00', 1101, '090-0000000', 'wut030'),
(1234568, 'ดร.', 'ไกรศักดิ์', 'เกษร', 'LGBTQ+', '0000-00-00', 1104, '000-0000000', 'kai030'),
(1234569, 'ดร.', 'จันทร์จิรา', 'พยัคฆ์เพศ', 'female', '0000-00-00', 1103, '000-0000000', 'ji0300'),
(1234570, '', 'ปทุมมา', 'แก้วแดง', 'female', '0000-00-00', 1102, '000-0000000', 'pathum0');

-- --------------------------------------------------------

--
-- Table structure for table `tb_position`
--

CREATE TABLE `tb_position` (
  `position_id` int(11) NOT NULL,
  `name_position` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_position`
--

INSERT INTO `tb_position` (`position_id`, `name_position`, `description`) VALUES
(1101, 'หัวหน้าภาค', ''),
(1102, 'เจ้าหน้าที่พัสดุ', ''),
(1103, 'รองหัวหน้าภาควิชา', ''),
(1104, 'ผู้ช่วยหัวหน้าภาควิชา', '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_room`
--

CREATE TABLE `tb_room` (
  `room_id` int(11) NOT NULL,
  `number` varchar(11) NOT NULL,
  `max_occupancy` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `typ_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_room`
--

INSERT INTO `tb_room` (`room_id`, `number`, `max_occupancy`, `floor`, `typ_id`) VALUES
(2207, 'Sc2-207', 40, 2, 1303),
(2212, 'Sc2-212', 30, 2, 1301),
(2301, 'Sc2-301', 4, 3, 1305),
(2307, 'Sc2-307', 30, 3, 1304),
(2411, 'Sc2-411', 40, 4, 1304);

-- --------------------------------------------------------

--
-- Table structure for table `tb_room_typ`
--

CREATE TABLE `tb_room_typ` (
  `typ_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_room_typ`
--

INSERT INTO `tb_room_typ` (`typ_id`, `name`, `description`) VALUES
(1301, 'Meet_slope', ''),
(1302, 'Lecture', ''),
(1303, 'Meet', ''),
(1304, 'Lab', ''),
(1305, 'Professor\'s Office', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_borrowing`
--
ALTER TABLE `tb_borrowing`
  ADD PRIMARY KEY (`borrowing_id`),
  ADD KEY `durable_articles_id` (`durable_articles_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `tb_durable_articles`
--
ALTER TABLE `tb_durable_articles`
  ADD PRIMARY KEY (`durable_articles_id`);

--
-- Indexes for table `tb_member`
--
ALTER TABLE `tb_member`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `tb_position`
--
ALTER TABLE `tb_position`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `tb_room`
--
ALTER TABLE `tb_room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `typ_id` (`typ_id`);

--
-- Indexes for table `tb_room_typ`
--
ALTER TABLE `tb_room_typ`
  ADD PRIMARY KEY (`typ_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_borrowing`
--
ALTER TABLE `tb_borrowing`
  MODIFY `borrowing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_durable_articles`
--
ALTER TABLE `tb_durable_articles`
  MODIFY `durable_articles_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145033;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_borrowing`
--
ALTER TABLE `tb_borrowing`
  ADD CONSTRAINT `fk_du` FOREIGN KEY (`durable_articles_id`) REFERENCES `tb_durable_articles` (`durable_articles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_member` FOREIGN KEY (`member_id`) REFERENCES `tb_member` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_room` FOREIGN KEY (`room_id`) REFERENCES `tb_room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_member`
--
ALTER TABLE `tb_member`
  ADD CONSTRAINT `fk_position` FOREIGN KEY (`position_id`) REFERENCES `tb_position` (`position_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
