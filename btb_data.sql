-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 04:06 PM
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
-- Database: `btb_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_username` varchar(50) NOT NULL,
  `admin_email` varchar(50) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_username`, `admin_email`, `admin_password`, `created_at`) VALUES
(1, 'test_admin', 'admin@btb.com', '$2y$10$wT0X8hL9A1Y2c3V4e5R6P.uFjD7Q8S9T0U1V2W3X4Y5Z6A7B8C9D', '2025-11-12 23:55:20');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `booking_time` datetime DEFAULT current_timestamp(),
  `seat_num` varchar(50) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_status` enum('CONFIRMED','CANCELED') DEFAULT 'CONFIRMED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `customer_id`, `schedule_id`, `booking_time`, `seat_num`, `total_price`, `booking_status`) VALUES
(1, 1, 1, '2025-11-13 00:58:02', '1 seat(s)', 30.00, 'CONFIRMED'),
(2, 2, 1, '2025-11-13 01:05:40', '2 seat(s)', 60.00, 'CONFIRMED'),
(3, 2, 1, '2025-11-13 09:39:20', '1 seat(s)', 30.00, 'CONFIRMED'),
(4, 3, 1, '2025-11-13 19:49:21', '3 seat(s)', 90.00, ''),
(6, 3, 8, '2025-11-14 08:36:28', '2 seat(s)', 200.00, ''),
(7, 3, 12, '2025-11-14 16:57:52', '3 seat(s)', 180.00, 'CONFIRMED'),
(8, 4, 10, '2025-11-14 17:26:59', '3 seat(s)', 120.00, 'CONFIRMED'),
(9, 5, 10, '2025-11-14 17:38:50', '1 seat(s)', 40.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_username` varchar(50) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_username`, `customer_email`, `customer_password`, `created_at`) VALUES
(1, 'im', 'im@gmail.com', '$2y$10$.aon.DaWYVFQ6GwvigscX.5P7MinzAR30IhDLj18otchd6hZb.DYC', '2025-11-13 00:37:27'),
(2, 'ui', 'ui@gmail.com', '$2y$10$2q0vDX1JxrEWeyRF5jW9bOlzGJ5JwB7/MpZ9oxM7yvcxGTUCSVSfy', '2025-11-13 01:05:07'),
(3, 'hi', 'hi@gmail.com', '$2y$10$DBEMM2n5k/YtgZwwct2gse41UQBiMSoqZ/nfYDeuIt5uFqOmV3cPC', '2025-11-13 19:48:46'),
(4, 'huh', 'huh@gmail.com', '$2y$10$9W75DcIxonnQbUQ9t8SIZ.M/8lK2hC2DcnMIfMRnJZ.803HVLuJ0W', '2025-11-14 17:26:10'),
(5, 'nay', 'nay@gmail.com', '$2y$10$b.c3YrckuY4Qr1QVB3R1COWA1NthIKr1SM21w1zP4Q6skctBoxJ7a', '2025-11-14 17:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(100) NOT NULL,
  `route_desc` varchar(300) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `route_name`, `route_desc`, `created_by`, `created_at`) VALUES
(1, 'KUALA LUMPUR to KUANTAN', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 00:53:06'),
(2, 'KUALA LUMPUR to JOHOR BAHRU', 'STOPS AT EVERY STOPS', 1, '2025-11-13 19:50:21'),
(4, 'SEREMBAN to KUALA TERENGGANU', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 21:08:00'),
(5, 'PORT DICKSON to IPOH', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 21:12:25'),
(6, 'KANGAR to JOHOR BAHRU', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 21:15:46'),
(7, 'KOTA BHARU to KUALA TERENGGANU', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 21:16:32'),
(8, 'PUTRAJAYA to PENDANG', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 21:17:12'),
(9, 'KUALA LUMPUR to KOTA BAHRU', 'LUXURY EN SUITE BUS', 1, '2025-11-13 21:18:02'),
(10, 'BENTONG to SERI ISKANDAR', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 21:18:39'),
(11, 'ALOR SETAR to SETIU', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-13 21:19:31'),
(12, 'SEREMBAN to SG PETANI', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-14 16:51:44'),
(15, 'JOHOR BAHRU to KL SENTRAL', 'STOP AT EVERY STOPS ALONG THE WAY', 1, '2025-11-14 17:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `depart_date` date NOT NULL,
  `depart_time` time NOT NULL,
  `max_seats` int(11) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `route_id`, `depart_date`, `depart_time`, `max_seats`, `available_seats`, `price`, `created_at`) VALUES
(1, 1, '2025-11-14', '19:00:00', 40, 33, 30.00, '2025-11-13 00:53:06'),
(2, 2, '2025-11-14', '22:00:00', 40, 40, 50.00, '2025-11-13 19:50:21'),
(4, 4, '2025-11-14', '23:07:00', 40, 40, 70.00, '2025-11-13 21:08:00'),
(5, 5, '2025-11-15', '09:15:00', 40, 40, 45.00, '2025-11-13 21:12:25'),
(6, 6, '2025-11-15', '12:15:00', 40, 40, 90.00, '2025-11-13 21:15:46'),
(7, 7, '2025-11-14', '23:15:00', 40, 40, 20.00, '2025-11-13 21:16:32'),
(8, 8, '2025-11-16', '00:30:00', 40, 38, 100.00, '2025-11-13 21:17:12'),
(9, 9, '2025-11-16', '00:45:00', 40, 40, 200.00, '2025-11-13 21:18:02'),
(10, 10, '2025-11-18', '14:30:00', 40, 36, 40.00, '2025-11-13 21:18:39'),
(11, 11, '2025-11-21', '23:20:00', 40, 40, 30.00, '2025-11-13 21:19:31'),
(12, 12, '2025-11-15', '19:55:00', 40, 37, 60.00, '2025-11-14 16:51:44'),
(13, 12, '2025-11-15', '12:55:00', 40, 40, 60.00, '2025-11-14 16:51:44'),
(15, 12, '2025-11-15', '17:20:00', 40, 40, 50.00, '2025-11-14 17:18:34'),
(16, 15, '2025-11-15', '17:40:00', 40, 40, 50.00, '2025-11-14 17:41:03'),
(17, 15, '2025-11-15', '18:40:00', 40, 40, 50.00, '2025-11-14 17:41:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_username` (`admin_username`),
  ADD UNIQUE KEY `admin_email` (`admin_email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_username` (`customer_username`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `route_id` (`route_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`);

--
-- Constraints for table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
