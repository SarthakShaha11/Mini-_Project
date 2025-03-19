-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 17, 2025 at 05:30 PM
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
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(225) DEFAULT NULL,
  `price` int DEFAULT NULL,
  `image` varchar(25) NOT NULL,
  `stock` int DEFAULT '200',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `price`, `image`, `stock`) VALUES
(1, 'Arabica Beans', 200, 'product-1.jpg', 200),
(2, 'Espresso Blend', 500, 'product-2.jpg', 200),
(3, 'Colombian Roast', 250, 'product-3.jpg', 200),
(4, 'French Roast', 100, 'product-4.jpg', 200),
(5, 'Decaf Coffee', 300, 'product-5.jpg', 200),
(6, 'Mocha Blend', 550, 'product-6.jpg', 200),
(7, 'Caramel Latte', 400, 'product-7.jpg', 200),
(8, 'Hazelnut Coffee', 150, 'product-8.jpg', 200),
(9, 'Hot coffee', 425, 'product-13.jpg', 200);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
