-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2022 at 06:02 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_train`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `type` varchar(8) NOT NULL,
  `multiplier` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `type`, `multiplier`) VALUES
(1, '1', '1.60'),
(2, '2', '1.20');

-- --------------------------------------------------------

--
-- Table structure for table `cologne_to_berlin`
--

CREATE TABLE `cologne_to_berlin` (
  `id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `starts` varchar(16) DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `path` varchar(8) DEFAULT NULL,
  `up` int(11) NOT NULL,
  `down` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cologne_to_berlin`
--

INSERT INTO `cologne_to_berlin` (`id`, `station_id`, `starts`, `price`, `path`, `up`, `down`) VALUES
(100, 2, 'cologne', 0, '1', 0, 80),
(101, 6, 'dortmund', 15, '1', 20, 60),
(102, 7, 'hannover', 30, '1', 40, 40),
(103, 4, 'berlin', 60, '1', 80, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cologne_to_hamburg`
--

CREATE TABLE `cologne_to_hamburg` (
  `id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `starts` varchar(16) DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `path` varchar(8) DEFAULT NULL,
  `up` int(11) NOT NULL DEFAULT 0,
  `down` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cologne_to_hamburg`
--

INSERT INTO `cologne_to_hamburg` (`id`, `station_id`, `starts`, `price`, `path`, `up`, `down`) VALUES
(100, 2, 'cologne', 0, '1', 0, 90),
(101, 6, 'dortmund', 15, '1', 30, 60),
(102, 7, 'hannover', 30, '1', 60, 30),
(103, 3, 'hamburg', 50, '1', 90, 0);

-- --------------------------------------------------------

--
-- Table structure for table `connection`
--

CREATE TABLE `connection` (
  `id` int(11) NOT NULL,
  `from_station` varchar(8) NOT NULL,
  `to_station` varchar(8) NOT NULL,
  `train_table` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `connection`
--

INSERT INTO `connection` (`id`, `from_station`, `to_station`, `train_table`) VALUES
(1, 'w', 'n', 'cologne_to_hamburg'),
(2, 'w', 'e', 'cologne_to_berlin'),
(3, 'n', 'e', 'hamburg_to_berlin'),
(4, 'c', 's', 'frankfurt_to_munich'),
(5, 'c', 'e', 'frankfurt_to_berlin'),
(6, 'c', 'n', 'frankfurt_to_hamburg'),
(7, 'c', 'w', 'frankfurt_to_cologne');

-- --------------------------------------------------------

--
-- Table structure for table `days_left`
--

CREATE TABLE `days_left` (
  `id` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `multiplier` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `days_left`
--

INSERT INTO `days_left` (`id`, `days`, `multiplier`) VALUES
(1, 1, '2.00'),
(2, 15, '1.75'),
(3, 30, '1.50'),
(4, 60, '1.00');

-- --------------------------------------------------------

--
-- Table structure for table `frankfurt_to_berlin`
--

CREATE TABLE `frankfurt_to_berlin` (
  `id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `starts` varchar(16) DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `path` varchar(8) DEFAULT NULL,
  `up` int(11) NOT NULL DEFAULT 0,
  `down` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `frankfurt_to_berlin`
--

INSERT INTO `frankfurt_to_berlin` (`id`, `station_id`, `starts`, `price`, `path`, `up`, `down`) VALUES
(100, 1, 'frankfurt', 0, '1', 0, 60),
(101, 11, 'erfurt', 25, '1', 20, 40),
(102, 12, 'leipzig', 40, '1', 40, 20),
(103, 4, 'berlin', 55, '1', 60, 0);

-- --------------------------------------------------------

--
-- Table structure for table `frankfurt_to_cologne`
--

CREATE TABLE `frankfurt_to_cologne` (
  `id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `starts` varchar(16) DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `path` varchar(8) DEFAULT NULL,
  `up` int(11) NOT NULL,
  `down` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `frankfurt_to_cologne`
--

INSERT INTO `frankfurt_to_cologne` (`id`, `station_id`, `starts`, `price`, `path`, `up`, `down`) VALUES
(1, 1, 'frankfurt', 0, '1', 0, 45),
(2, 2, 'colonge', 25, '1', 45, 0);

-- --------------------------------------------------------

--
-- Table structure for table `frankfurt_to_hamburg`
--

CREATE TABLE `frankfurt_to_hamburg` (
  `id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `starts` varchar(16) DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `path` varchar(8) DEFAULT NULL,
  `up` int(11) NOT NULL DEFAULT 0,
  `down` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `frankfurt_to_hamburg`
--

INSERT INTO `frankfurt_to_hamburg` (`id`, `station_id`, `starts`, `price`, `path`, `up`, `down`) VALUES
(100, 1, 'frankfurt', 0, '1', 0, 90),
(101, 7, 'hannover', 50, '1', 45, 45),
(102, 3, 'hamburg', 70, '1', 90, 0);

-- --------------------------------------------------------

--
-- Table structure for table `frankfurt_to_munich`
--

CREATE TABLE `frankfurt_to_munich` (
  `id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `starts` varchar(16) NOT NULL,
  `price` int(11) DEFAULT 0,
  `path` varchar(16) NOT NULL,
  `up` int(11) NOT NULL,
  `down` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `frankfurt_to_munich`
--

INSERT INTO `frankfurt_to_munich` (`id`, `station_id`, `starts`, `price`, `path`, `up`, `down`) VALUES
(100, 1, 'frankfurt', 0, '1,2', 0, 120),
(101, 8, 'stuttgart', 15, '1', 30, 90),
(102, 9, 'augsburg', 25, '1', 60, 60),
(103, 10, 'nurnburg', 25, '2', 90, 30),
(104, 5, 'munich', 50, '1,2', 120, 0);

-- --------------------------------------------------------

--
-- Table structure for table `hamburg_to_berlin`
--

CREATE TABLE `hamburg_to_berlin` (
  `id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `starts` varchar(16) DEFAULT NULL,
  `price` int(11) DEFAULT 0,
  `path` varchar(8) DEFAULT NULL,
  `up` int(11) NOT NULL,
  `down` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hamburg_to_berlin`
--

INSERT INTO `hamburg_to_berlin` (`id`, `station_id`, `starts`, `price`, `path`, `up`, `down`) VALUES
(100, 3, 'hamburg', 0, '1', 0, 90),
(101, 4, 'berlin', 30, '1', 90, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `uuid` varchar(64) NOT NULL,
  `train_time_id` int(11) NOT NULL,
  `from_station` int(11) NOT NULL,
  `to_station` int(11) NOT NULL,
  `train_date` date NOT NULL,
  `train_start_time` time NOT NULL,
  `train_end_time` time NOT NULL,
  `class` int(11) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `connecting` int(11) NOT NULL DEFAULT 0,
  `connecting_station_id` int(11) DEFAULT NULL,
  `connecting_train_time_id` int(11) DEFAULT NULL,
  `connecting_train_start_time` time DEFAULT NULL,
  `connecting_train_end_time` time DEFAULT NULL,
  `payment_status` varchar(16) DEFAULT 'INCOMPLETE',
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `no_of_passenger` int(11) NOT NULL DEFAULT 1,
  `special_service_flag` int(11) NOT NULL DEFAULT 0,
  `seats` int(11) NOT NULL DEFAULT 0,
  `seat_no` varchar(32) NOT NULL DEFAULT '0',
  `connecting_seat_no` varchar(32) DEFAULT NULL,
  `food_service` int(11) NOT NULL DEFAULT 0,
  `luggage_service` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`id`, `uuid`, `train_time_id`, `from_station`, `to_station`, `train_date`, `train_start_time`, `train_end_time`, `class`, `booking_time`, `connecting`, `connecting_station_id`, `connecting_train_time_id`, `connecting_train_start_time`, `connecting_train_end_time`, `payment_status`, `amount`, `no_of_passenger`, `special_service_flag`, `seats`, `seat_no`, `connecting_seat_no`, `food_service`, `luggage_service`) VALUES
(10001, 'ab123', 99, 11, 1, '2022-06-30', '14:00:00', '15:00:00', 2, '2022-06-27 15:23:06', 0, NULL, NULL, NULL, NULL, 'COMPLETE', '105.00', 2, 1, 2, '1,2', NULL, 1, 1),
(10002, 'abc4561', 99, 11, 5, '2022-06-30', '14:40:00', '15:00:00', 2, '2022-06-27 15:11:48', 1, 1, 9, '16:00:00', '18:00:00', 'INCOMPLETE', '588.00', 2, 1, 2, '3,4', '1,2', 0, 1),
(10003, 'abc123', 99, 11, 1, '2022-06-30', '14:40:00', '15:00:00', 2, '2022-06-27 11:30:26', 0, NULL, NULL, NULL, NULL, 'INCOMPLETE', '105.00', 3, 1, 3, '5,6,7', NULL, 1, 1),
(10004, 'abc456', 99, 11, 5, '2022-06-30', '14:40:00', '15:00:00', 2, '2022-06-27 11:30:47', 1, 1, 9, '16:00:00', '18:00:00', 'INCOMPLETE', '588.00', 2, 1, 2, '8,9', '3,4', 1, 1),
(10007, 'delete_test_123', 99, 11, 1, '2022-06-30', '14:40:00', '15:00:00', 2, '2022-06-27 15:18:01', 0, NULL, NULL, NULL, NULL, 'COMPLETE', '105.00', 3, 1, 3, '10,11,12', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stations`
--

CREATE TABLE `stations` (
  `station_id` int(11) NOT NULL,
  `station` varchar(16) NOT NULL,
  `main` int(11) NOT NULL,
  `via` int(11) NOT NULL,
  `region` varchar(1) NOT NULL,
  `start` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stations`
--

INSERT INTO `stations` (`station_id`, `station`, `main`, `via`, `region`, `start`) VALUES
(1, 'frankfurt', 1, 1, 'c', 3),
(2, 'cologne', 1, 1, 'w', 2),
(3, 'hamburg', 1, 1, 'n', 1),
(4, 'berlin', 1, 1, 'e', 0),
(5, 'munich', 1, 1, 's', 0),
(6, 'dortmund', 0, 0, 'w', 0),
(7, 'hannover', 0, 1, 'n', 0),
(8, 'stuttgart', 0, 0, 's', 0),
(9, 'augsburg', 0, 0, 's', 0),
(10, 'nurnburg', 0, 0, 's', 0),
(11, 'erfurt', 0, 0, 'c', 0),
(12, 'leipzig', 0, 0, 'e', 0);

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `train_id` int(11) NOT NULL,
  `train_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `train_id`, `train_time`) VALUES
(1, 1001, '09:00:00'),
(2, 1001, '15:00:00'),
(3, 1001, '20:00:00'),
(4, 1001, '00:00:00'),
(5, 1002, '03:00:00'),
(6, 1002, '06:00:00'),
(7, 1002, '08:00:00'),
(8, 1002, '12:00:00'),
(9, 1002, '16:00:00'),
(10, 1002, '18:00:00'),
(11, 1002, '22:00:00'),
(12, 1003, '09:00:00'),
(13, 1003, '07:00:00'),
(14, 1003, '13:00:00'),
(15, 1003, '18:00:00'),
(16, 1003, '22:00:00'),
(17, 1003, '06:00:00'),
(18, 1004, '06:00:00'),
(19, 1004, '09:00:00'),
(20, 1004, '12:00:00'),
(21, 1004, '14:00:00'),
(22, 1004, '16:00:00'),
(23, 1004, '18:00:00'),
(24, 1004, '20:00:00'),
(25, 1004, '22:00:00'),
(26, 1005, '07:00:00'),
(27, 1005, '09:00:00'),
(28, 1005, '13:00:00'),
(29, 1005, '17:00:00'),
(30, 1005, '21:00:00'),
(31, 1006, '06:00:00'),
(32, 1006, '08:00:00'),
(33, 1006, '10:00:00'),
(34, 1006, '12:00:00'),
(35, 1006, '14:00:00'),
(36, 1006, '16:00:00'),
(37, 1006, '18:00:00'),
(38, 1006, '20:00:00'),
(39, 1006, '22:00:00'),
(40, 1007, '06:00:00'),
(41, 1007, '08:00:00'),
(42, 1007, '14:00:00'),
(43, 1007, '18:00:00'),
(44, 1007, '22:00:00'),
(45, 1008, '07:00:00'),
(46, 1008, '09:00:00'),
(47, 1008, '11:00:00'),
(48, 1008, '13:00:00'),
(49, 1008, '15:00:00'),
(50, 1008, '17:00:00'),
(51, 1008, '19:00:00'),
(52, 1008, '21:00:00'),
(53, 1008, '23:00:00'),
(54, 1009, '07:00:00'),
(55, 1009, '09:00:00'),
(56, 1009, '13:00:00'),
(57, 1009, '17:00:00'),
(58, 1009, '21:00:00'),
(59, 1010, '06:00:00'),
(60, 1010, '08:00:00'),
(61, 1010, '10:00:00'),
(62, 1010, '12:00:00'),
(63, 1010, '14:00:00'),
(64, 1010, '16:00:00'),
(65, 1010, '18:00:00'),
(66, 1010, '20:00:00'),
(67, 1010, '22:00:00'),
(68, 1011, '06:00:00'),
(69, 1011, '08:00:00'),
(70, 1011, '14:00:00'),
(71, 1011, '18:00:00'),
(72, 1011, '22:00:00'),
(73, 1012, '07:00:00'),
(74, 1012, '09:00:00'),
(75, 1012, '11:00:00'),
(76, 1012, '13:00:00'),
(77, 1012, '15:00:00'),
(78, 1012, '17:00:00'),
(79, 1012, '19:00:00'),
(80, 1012, '21:00:00'),
(81, 1012, '23:00:00'),
(83, 1014, '07:00:00'),
(84, 1014, '09:00:00'),
(85, 1014, '13:00:00'),
(86, 1014, '17:00:00'),
(87, 1014, '21:00:00'),
(88, 1015, '06:00:00'),
(89, 1015, '08:00:00'),
(90, 1015, '10:00:00'),
(91, 1015, '12:00:00'),
(92, 1015, '14:00:00'),
(93, 1015, '16:00:00'),
(94, 1015, '18:00:00'),
(95, 1015, '20:00:00'),
(96, 1015, '22:00:00'),
(97, 1016, '06:00:00'),
(98, 1016, '08:00:00'),
(99, 1016, '14:00:00'),
(100, 1016, '18:00:00'),
(101, 1016, '22:00:00'),
(102, 1017, '07:00:00'),
(103, 1017, '09:00:00'),
(104, 1017, '11:00:00'),
(105, 1017, '13:00:00'),
(106, 1017, '15:00:00'),
(107, 1017, '17:00:00'),
(108, 1017, '19:00:00'),
(109, 1017, '21:00:00'),
(110, 1017, '23:00:00'),
(111, 1018, '07:00:00'),
(112, 1018, '09:00:00'),
(113, 1018, '13:00:00'),
(114, 1018, '17:00:00'),
(115, 1018, '21:00:00'),
(116, 1019, '06:00:00'),
(117, 1019, '08:00:00'),
(118, 1019, '10:00:00'),
(119, 1019, '12:00:00'),
(120, 1019, '14:00:00'),
(121, 1019, '16:00:00'),
(122, 1019, '18:00:00'),
(123, 1019, '20:00:00'),
(124, 1019, '22:00:00'),
(125, 1020, '06:00:00'),
(126, 1020, '08:00:00'),
(127, 1020, '14:00:00'),
(128, 1020, '18:00:00'),
(129, 1020, '22:00:00'),
(130, 1021, '07:00:00'),
(131, 1021, '09:00:00'),
(132, 1021, '11:00:00'),
(133, 1021, '13:00:00'),
(134, 1021, '15:00:00'),
(135, 1021, '17:00:00'),
(136, 1021, '19:00:00'),
(137, 1021, '21:00:00'),
(138, 1021, '23:00:00'),
(139, 1022, '07:00:00'),
(140, 1022, '09:00:00'),
(141, 1022, '13:00:00'),
(142, 1022, '17:00:00'),
(143, 1022, '21:00:00'),
(144, 1023, '06:00:00'),
(145, 1023, '08:00:00'),
(146, 1023, '10:00:00'),
(147, 1023, '12:00:00'),
(148, 1023, '14:00:00'),
(149, 1023, '16:00:00'),
(150, 1023, '18:00:00'),
(151, 1023, '20:00:00'),
(152, 1023, '22:00:00'),
(153, 1024, '06:00:00'),
(154, 1024, '08:00:00'),
(155, 1024, '14:00:00'),
(156, 1024, '18:00:00'),
(157, 1024, '22:00:00'),
(158, 1025, '07:00:00'),
(159, 1025, '09:00:00'),
(160, 1025, '11:00:00'),
(161, 1025, '13:00:00'),
(162, 1025, '15:00:00'),
(163, 1025, '17:00:00'),
(164, 1025, '19:00:00'),
(165, 1025, '21:00:00'),
(166, 1025, '23:00:00'),
(167, 1026, '07:00:00'),
(168, 1026, '09:00:00'),
(169, 1026, '13:00:00'),
(170, 1026, '17:00:00'),
(171, 1026, '21:00:00'),
(172, 1027, '06:00:00'),
(173, 1027, '08:00:00'),
(174, 1027, '10:00:00'),
(175, 1027, '12:00:00'),
(176, 1027, '14:00:00'),
(177, 1027, '16:00:00'),
(178, 1027, '18:00:00'),
(179, 1027, '20:00:00'),
(180, 1027, '22:00:00'),
(181, 1028, '06:00:00'),
(182, 1028, '08:00:00'),
(183, 1028, '14:00:00'),
(184, 1028, '18:00:00'),
(185, 1028, '22:00:00'),
(186, 1029, '07:00:00'),
(187, 1029, '09:00:00'),
(188, 1029, '11:00:00'),
(189, 1029, '13:00:00'),
(190, 1029, '15:00:00'),
(191, 1029, '17:00:00'),
(192, 1029, '19:00:00'),
(193, 1029, '21:00:00'),
(194, 1029, '23:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `train_list`
--

CREATE TABLE `train_list` (
  `train_id` int(11) NOT NULL,
  `from_station` int(11) NOT NULL,
  `to_station` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `reverse` int(11) NOT NULL,
  `seat` int(11) NOT NULL DEFAULT 60,
  `train_table` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `train_list`
--

INSERT INTO `train_list` (`train_id`, `from_station`, `to_station`, `type`, `reverse`, `seat`, `train_table`) VALUES
(1001, 1, 5, 1, 0, 60, 'frankfurt_to_munich'),
(1002, 1, 5, 2, 0, 60, 'frankfurt_to_munich'),
(1003, 5, 1, 1, 1, 60, 'frankfurt_to_munich'),
(1004, 5, 1, 2, 1, 60, 'frankfurt_to_munich'),
(1005, 1, 2, 1, 0, 60, 'frankfurt_to_cologne'),
(1006, 1, 2, 2, 0, 60, 'frankfurt_to_cologne'),
(1007, 2, 1, 1, 1, 60, 'frankfurt_to_cologne'),
(1008, 2, 1, 2, 1, 60, 'frankfurt_to_cologne'),
(1009, 1, 3, 1, 0, 60, 'frankfurt_to_hamburg'),
(1010, 1, 3, 2, 0, 60, 'frankfurt_to_hamburg'),
(1011, 3, 1, 1, 1, 60, 'frankfurt_to_hamburg'),
(1012, 3, 1, 2, 1, 60, 'frankfurt_to_hamburg'),
(1014, 1, 4, 1, 0, 60, 'frankfurt_to_berlin'),
(1015, 1, 4, 2, 0, 60, 'frankfurt_to_berlin'),
(1016, 4, 1, 1, 1, 60, 'frankfurt_to_berlin'),
(1017, 4, 1, 2, 0, 60, 'frankfurt_to_berlin'),
(1018, 2, 3, 1, 0, 60, 'cologne_to_hamburg'),
(1019, 2, 3, 2, 0, 60, 'cologne_to_hamburg'),
(1020, 3, 2, 1, 1, 60, 'cologne_to_hamburg'),
(1021, 3, 2, 2, 1, 60, 'cologne_to_hamburg'),
(1022, 2, 4, 1, 0, 60, 'cologne_to_berlin'),
(1023, 2, 4, 2, 0, 60, 'cologne_to_berlin'),
(1024, 4, 2, 1, 1, 60, 'cologne_to_berlin'),
(1025, 4, 2, 2, 1, 60, 'cologne_to_berlin'),
(1026, 3, 4, 1, 0, 60, 'hamburg_to_berlin'),
(1027, 3, 4, 2, 0, 60, 'hamburg_to_berlin'),
(1028, 4, 3, 1, 1, 60, 'hamburg_to_berlin'),
(1029, 4, 3, 2, 1, 60, 'hamburg_to_berlin');

-- --------------------------------------------------------

--
-- Table structure for table `train_type`
--

CREATE TABLE `train_type` (
  `train_type_id` int(11) NOT NULL,
  `type` varchar(8) NOT NULL,
  `multiplier` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `train_type`
--

INSERT INTO `train_type` (`train_type_id`, `type`, `multiplier`) VALUES
(1, 'ICE', '1.60'),
(2, 'RE/B', '1.20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cologne_to_berlin`
--
ALTER TABLE `cologne_to_berlin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `cologne_to_hamburg`
--
ALTER TABLE `cologne_to_hamburg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `connection`
--
ALTER TABLE `connection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `days_left`
--
ALTER TABLE `days_left`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frankfurt_to_berlin`
--
ALTER TABLE `frankfurt_to_berlin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `frankfurt_to_cologne`
--
ALTER TABLE `frankfurt_to_cologne`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `frankfurt_to_hamburg`
--
ALTER TABLE `frankfurt_to_hamburg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `frankfurt_to_munich`
--
ALTER TABLE `frankfurt_to_munich`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `hamburg_to_berlin`
--
ALTER TABLE `hamburg_to_berlin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `train_time_id` (`train_time_id`),
  ADD KEY `from_station` (`from_station`),
  ADD KEY `to_station` (`to_station`),
  ADD KEY `class` (`class`);

--
-- Indexes for table `stations`
--
ALTER TABLE `stations`
  ADD PRIMARY KEY (`station_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `train_id` (`train_id`);

--
-- Indexes for table `train_list`
--
ALTER TABLE `train_list`
  ADD PRIMARY KEY (`train_id`),
  ADD KEY `type` (`type`),
  ADD KEY `from_station` (`from_station`),
  ADD KEY `to_station` (`to_station`);

--
-- Indexes for table `train_type`
--
ALTER TABLE `train_type`
  ADD PRIMARY KEY (`train_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cologne_to_berlin`
--
ALTER TABLE `cologne_to_berlin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `cologne_to_hamburg`
--
ALTER TABLE `cologne_to_hamburg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `connection`
--
ALTER TABLE `connection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `days_left`
--
ALTER TABLE `days_left`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `frankfurt_to_berlin`
--
ALTER TABLE `frankfurt_to_berlin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `frankfurt_to_cologne`
--
ALTER TABLE `frankfurt_to_cologne`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `frankfurt_to_hamburg`
--
ALTER TABLE `frankfurt_to_hamburg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `frankfurt_to_munich`
--
ALTER TABLE `frankfurt_to_munich`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `hamburg_to_berlin`
--
ALTER TABLE `hamburg_to_berlin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10008;

--
-- AUTO_INCREMENT for table `stations`
--
ALTER TABLE `stations`
  MODIFY `station_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `train_list`
--
ALTER TABLE `train_list`
  MODIFY `train_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1033;

--
-- AUTO_INCREMENT for table `train_type`
--
ALTER TABLE `train_type`
  MODIFY `train_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cologne_to_berlin`
--
ALTER TABLE `cologne_to_berlin`
  ADD CONSTRAINT `cologne_to_berlin_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints for table `cologne_to_hamburg`
--
ALTER TABLE `cologne_to_hamburg`
  ADD CONSTRAINT `cologne_to_hamburg_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints for table `frankfurt_to_berlin`
--
ALTER TABLE `frankfurt_to_berlin`
  ADD CONSTRAINT `frankfurt_to_berlin_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints for table `frankfurt_to_cologne`
--
ALTER TABLE `frankfurt_to_cologne`
  ADD CONSTRAINT `frankfurt_to_cologne_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints for table `frankfurt_to_hamburg`
--
ALTER TABLE `frankfurt_to_hamburg`
  ADD CONSTRAINT `frankfurt_to_hamburg_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints for table `frankfurt_to_munich`
--
ALTER TABLE `frankfurt_to_munich`
  ADD CONSTRAINT `frankfurt_to_munich_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints for table `hamburg_to_berlin`
--
ALTER TABLE `hamburg_to_berlin`
  ADD CONSTRAINT `hamburg_to_berlin_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`train_time_id`) REFERENCES `timetable` (`id`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`from_station`) REFERENCES `stations` (`station_id`),
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`to_station`) REFERENCES `stations` (`station_id`),
  ADD CONSTRAINT `reservation_ibfk_4` FOREIGN KEY (`class`) REFERENCES `class` (`id`);

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`train_id`) REFERENCES `train_list` (`train_id`);

--
-- Constraints for table `train_list`
--
ALTER TABLE `train_list`
  ADD CONSTRAINT `train_list_ibfk_1` FOREIGN KEY (`type`) REFERENCES `train_type` (`train_type_id`),
  ADD CONSTRAINT `train_list_ibfk_2` FOREIGN KEY (`from_station`) REFERENCES `stations` (`station_id`),
  ADD CONSTRAINT `train_list_ibfk_3` FOREIGN KEY (`to_station`) REFERENCES `stations` (`station_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
