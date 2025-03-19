-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 15, 2025 at 07:16 PM
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
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `quantity` int NOT NULL DEFAULT '1',
  `Order_id` varchar(255) NOT NULL,
  `product_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_name` varchar(255) DEFAULT NULL,
  `is_cancelled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `product_name`, `status`, `quantity`, `Order_id`, `product_id`, `price`, `order_date`, `customer_name`, `is_cancelled`) VALUES
(1, 'Arabica Beans', 'Cancelled', 0, 'ORD_1741886128_9027', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(2, 'Arabica Beans', 'Pending', 1, 'ORD_1741886569_2506', 1, 200.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(3, 'Arabica Beans', 'Cancelled', 2, 'ORD_1741886962_2144', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(4, 'Arabica Beans', '', 2, 'ORD_1741886954_9480\r\n\r\n', 1, 200.00, '2025-03-14 17:08:58', 'Yash', 0),
(5, 'Arabica Beans', 'Pending', 2, 'ORD_1741887591_2727', 1, 200.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(6, 'Arabica Beans', '', 2, 'ORD_1741888730_8364', 1, 200.00, '2025-03-14 17:08:58', 'Yash', 0),
(7, 'Arabica Beans', 'Cancelled', 2, 'ORD_1741889081_1936', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(8, 'Arabica Beans', 'Pending', 2, 'ORD_1741889409_9422', 1, 200.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(9, 'Arabica Beans', '', 2, 'ORD_1741889614_7750', 1, 200.00, '2025-03-14 17:08:58', 'Yash', 0),
(10, 'Arabica Beans', 'Cancelled', 2, 'ORD_1741889727_3521', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(11, 'Arabica Beans', 'Pending', 2, 'ORD_1741889755_5144', 1, 200.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(12, 'Arabica Beans', '', 2, 'ORD_1741889793_8871', 1, 200.00, '2025-03-14 17:08:58', 'Yash', 0),
(13, 'Arabica Beans', 'Cancelled', 2, 'ORD_1741889846_3495', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(14, 'Arabica Beans', 'Pending', 2, 'ORD_1741889895_5375', 1, 200.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(15, 'Arabica Beans', '', 2, 'ORD_1741889936_3230', 1, 200.00, '2025-03-14 17:08:58', 'Yash', 0),
(16, 'Arabica Beans', 'Cancelled', 2, 'ORD_1741890191_2041', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(17, 'Arabica Beans', 'Pending', 2, 'ORD_1741890268_4436', 1, 200.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(18, 'Arabica Beans', '', 2, 'ORD_1741890507_1603', 1, 200.00, '2025-03-14 17:08:58', 'Yash', 0),
(19, 'Arabica Beans', 'Cancelled', 2, 'ORD_1741890631_7253', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(20, 'Espresso Blend', 'Pending', 2, 'ORD_1741894468_2300', 2, 500.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(21, 'Espresso Blend', '', 2, 'ORD_1741894536_6169', 2, 500.00, '2025-03-14 17:08:58', 'Yash', 0),
(22, 'Espresso Blend', 'Cancelled', 2, 'ORD_1741931089_4621', 2, 500.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(23, 'Espresso Blend', 'Pending', 2, 'ORD_1741933584_6697', 2, 500.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(24, 'Espresso Blend', '', 2, 'ORD_1741933852_5745', 2, 500.00, '2025-03-14 17:08:58', 'Yash', 0),
(25, 'Espresso Blend', 'Cancelled', 2, 'ORD_1741933893_7847', 2, 500.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(26, 'Espresso Blend', 'Pending', 2, 'ORD_1741934278_3221', 2, 500.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(27, 'Espresso Blend', '', 2, 'ORD_1741934349_4846', 2, 500.00, '2025-03-14 17:08:58', 'Yash', 0),
(28, 'Espresso Blend', 'Cancelled', 2, 'ORD_1741934418_3261', 2, 500.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(30, 'Espresso Blend', 'Pending', 2, 'ORD_1741935411_1696', 2, 500.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(31, 'Espresso Blend', '', 2, 'ORD_1741935450_2284', 2, 500.00, '2025-03-14 17:08:58', 'Yash', 0),
(32, 'Espresso Blend', 'Cancelled', 6, 'ORD_1741936170_7290', 2, 500.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(33, 'Arabica Beans', 'Cancelled', 10, 'ORD_1741936170_7290', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(34, 'Arabica Beans', '', 10, 'ORD_1741936204_4109', 1, 200.00, '2025-03-14 17:08:58', 'Yash', 0),
(35, 'Arabica Beans', 'Cancelled', 10, 'ORD_1741936236_6259', 1, 200.00, '2025-03-14 17:08:58', 'Bhumi', 0),
(36, 'Arabica Beans', 'Pending', 10, 'ORD_1741970372_6976', 1, 200.00, '2025-03-14 17:08:58', 'Sarthak Shaha', 0),
(37, 'Arabica Beans', '', 1, 'ORD_1742057448_4038', 1, 200.00, '2025-03-15 16:50:48', 'Yash', 0),
(38, 'Arabica Beans', 'Cancelled', 1, 'ORD_1742058427_7341', 1, 200.00, '2025-03-15 17:07:07', 'Bhumi', 0),
(52, 'Hot coffee', 'Pending', 2, 'ORD_1014886182_2790', 14, 425.00, '2025-03-15 17:12:20', 'Sarthak Shaha', 0),
(53, 'Hot coffee', '', 1, 'ORD_1015889027_6128', 15, 425.00, '2025-03-15 17:12:20', 'Yash', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
