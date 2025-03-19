-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 12, 2025 at 07:02 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ch`
--

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 0, 5, 1, 300.00),
(2, 0, 2, 1, 500.00),
(3, 0, 2, 1, 500.00),
(4, 0, 1, 1, 200.00),
(5, 0, 2, 3, 500.00),
(6, 0, 1, 1, 200.00),
(7, 0, 2, 3, 500.00),
(8, 0, 1, 1, 200.00),
(9, 0, 2, 3, 500.00),
(10, 0, 1, 1, 200.00),
(11, 0, 2, 3, 500.00),
(12, 0, 1, 1, 200.00),
(13, 0, 2, 3, 500.00),
(14, 0, 1, 1, 200.00),
(15, 0, 2, 3, 500.00),
(16, 0, 1, 1, 200.00),
(17, 0, 2, 3, 500.00),
(18, 0, 1, 1, 200.00),
(19, 0, 1, 1, NULL),
(20, 0, 1, 1, NULL),
(21, 0, 1, 1, 200.00),
(22, 0, 1, 1, 200.00),
(23, 0, 3, 1, 250.00),
(24, 0, 3, 1, 250.00),
(25, 0, 1, 1, 200.00),
(26, 0, 1, 1, NULL),
(27, 0, 1, 1, 200.00),
(28, 0, 1, 1, 200.00),
(29, 0, 1, 1, 200.00),
(30, 0, 1, 1, 200.00),
(31, 0, 1, 1, 200.00),
(32, 0, 1, 1, 200.00),
(33, 0, 1, 1, 200.00),
(34, 0, 1, 1, 200.00),
(35, 0, 1, 1, 200.00),
(36, 0, 1, 1, 200.00),
(37, 0, 1, 1, 200.00),
(38, 0, 2, 1, 500.00),
(39, 0, 2, 1, 500.00),
(40, 0, 9, 1, 425.00),
(41, 0, 9, 1, 425.00),
(42, 0, 2, 1, 500.00),
(43, 0, 1, 1, 200.00),
(44, 0, 2, 1, 500.00),
(45, 0, 1, 9, 200.00);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
