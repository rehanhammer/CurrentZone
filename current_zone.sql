-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2020 at 02:07 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `current_zone`
--

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_name` varchar(250) NOT NULL,
  `brand_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `category_id`, `brand_name`, `brand_status`) VALUES
(1, 1, 'Finibus', 'active'),
(2, 1, 'Lorem', 'active'),
(3, 1, 'Ipsum', 'active'),
(4, 8, 'Dolor', 'active'),
(5, 8, 'Amet', 'active'),
(6, 6, 'Aliquam', 'active'),
(7, 6, 'Maximus', 'active'),
(8, 10, 'Venenatis', 'active'),
(9, 10, 'Ligula', 'active'),
(10, 3, 'Vitae', 'active'),
(11, 3, 'Auctor', 'active'),
(12, 5, 'Luctus', 'active'),
(13, 5, 'Justo', 'active'),
(14, 2, 'Phasellus', 'active'),
(15, 2, 'Viverra', 'active'),
(16, 4, 'Elementum', 'active'),
(17, 4, 'Odio', 'active'),
(18, 7, 'Tellus', 'active'),
(19, 7, 'Curabitur', 'active'),
(20, 9, 'Commodo', 'active'),
(21, 9, 'Nullam', 'active'),
(22, 11, 'Quisques', 'active'),
(24, 11, 'XYZ', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(250) NOT NULL,
  `category_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_status`) VALUES
(1, 'LED Bulb', 'active'),
(2, 'LED Lights', 'active'),
(3, 'LED Down Lights', 'active'),
(4, 'LED Panel Light', 'active'),
(5, 'LED Lamp', 'active'),
(6, 'LED Concealed Light', 'active'),
(7, 'LED Spot Light', 'active'),
(8, 'LED Ceiling Light', 'active'),
(9, 'LED Tube Light', 'active'),
(10, 'LED Driver', 'active'),
(11, 'Led Floods Light', 'active'),
(13, 'LED Outdoor Lighting', 'active'),
(14, 'LED Indoor Lights', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_order`
--

CREATE TABLE `inventory_order` (
  `inventory_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `inventory_order_sale_price_total` double(10,2) NOT NULL,
  `inventory_order_base_price_total` double(10,2) NOT NULL,
  `order_cash_received` double(10,2) DEFAULT 0.00,
  `order_cash_receivable` double(10,2) NOT NULL DEFAULT 0.00,
  `inventory_order_date` date NOT NULL,
  `inventory_order_name` varchar(255) NOT NULL,
  `inventory_order_address` text NOT NULL,
  `payment_mode` enum('cash','credit') NOT NULL,
  `inventory_order_status` tinyint(4) NOT NULL,
  `inventory_order_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `inventory_order_udt` datetime DEFAULT NULL,
  `inventory_order_edt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_order_product`
--

CREATE TABLE `inventory_order_product` (
  `inventory_order_product_id` int(11) NOT NULL,
  `inventory_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `base_price` double(10,2) NOT NULL,
  `sale_price` double(10,2) NOT NULL,
  `inventory_order_product_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `inventory_order_product_edt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `product_name` varchar(300) NOT NULL,
  `product_description` text NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_quantity_remaining` int(11) DEFAULT 0,
  `product_quantity_sold` int(11) DEFAULT 0,
  `product_unit` varchar(150) NOT NULL,
  `product_base_price` double(10,2) NOT NULL,
  `product_total_amount` double(10,2) NOT NULL,
  `product_supplier_name` varchar(50) DEFAULT NULL,
  `product_supplier_contact_no` varchar(50) DEFAULT NULL,
  `product_enter_by` int(11) NOT NULL,
  `product_status` enum('active','inactive') NOT NULL,
  `product_date` date NOT NULL,
  `product_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `product_udt` datetime DEFAULT NULL,
  `product_edt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_credit_payment_history`
--

CREATE TABLE `tbl_credit_payment_history` (
  `history_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `credit_received` double(10,2) NOT NULL DEFAULT 0.00,
  `history_status` tinyint(4) NOT NULL DEFAULT 2,
  `history_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `history_udt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_id` int(11) NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `user_password` varchar(200) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `user_type` enum('master','user') NOT NULL,
  `user_status` enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_id`, `user_email`, `user_password`, `user_name`, `user_type`, `user_status`) VALUES
(1, 'abdullah@gmail.com', '$2y$10$pIFQYg2xmWORIntLwKo27OEMHprGW/3ucaRf9YyGfPwIvLOozXliW', 'Abdullah', 'master', 'Active'),
(2, 'saad@gmail.com', '$2y$10$QXI9wji7mzXByQVPwPrUfuaktxIL6P041qcHJWzPe1L5Am2n00sz2', 'Saad', 'user', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `inventory_order`
--
ALTER TABLE `inventory_order`
  ADD PRIMARY KEY (`inventory_order_id`);

--
-- Indexes for table `inventory_order_product`
--
ALTER TABLE `inventory_order_product`
  ADD PRIMARY KEY (`inventory_order_product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `tbl_credit_payment_history`
--
ALTER TABLE `tbl_credit_payment_history`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `inventory_order`
--
ALTER TABLE `inventory_order`
  MODIFY `inventory_order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_order_product`
--
ALTER TABLE `inventory_order_product`
  MODIFY `inventory_order_product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_credit_payment_history`
--
ALTER TABLE `tbl_credit_payment_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
