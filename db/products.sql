-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2018 at 12:48 PM
-- Server version: 5.6.25
-- PHP Version: 5.5.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `angular_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `p_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_slug` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `is_submenu` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`p_id`, `product_name`, `product_slug`, `parent_id`, `is_submenu`) VALUES
(1, 'Mobile & Accessories', '', 0, 'Y'),
(2, 'Mobile', 'mobile', 1, 'N'),
(3, 'Chargers', 'chargers', 1, 'N'),
(4, 'Cases covers', 'cases-covers', 1, 'N'),
(5, 'Memory Cards', 'memory-cards', 1, 'N'),
(6, 'Laptops & Accessories', '', 0, 'Y'),
(7, 'Laptops', 'laptops', 6, 'N'),
(8, 'Printers & inks', 'printers-and-inks', 6, 'N'),
(9, 'Wireless Speakers', 'wireless-speakers', 6, 'N'),
(10, 'Cameras & DSLRs', 'camera', 0, 'N'),
(11, 'LCD & LED TVs', 'tv', 0, 'N'),
(12, 'Electronics', 'electronics', 0, 'N'),
(13, 'Men', 'men', 0, 'N'),
(14, 'Women', 'women', 0, 'N');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`p_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
