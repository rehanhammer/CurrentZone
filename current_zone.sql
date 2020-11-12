-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2020 at 09:56 AM
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
-- Table structure for table `inventory_order`
--

CREATE TABLE `inventory_order` (
  `inventory_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `inventory_order_total` double(10,2) NOT NULL,
  `inventory_order_actual_total` double(10,2) DEFAULT NULL,
  `order_cash_received` double(10,2) NOT NULL DEFAULT 0.00,
  `inventory_order_cash_receivable` double(10,2) DEFAULT 0.00,
  `inventory_order_date` date NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_mobile_no` text NOT NULL,
  `payment_status` enum('cash','credit') NOT NULL,
  `inventory_order_status` tinyint(4) NOT NULL COMMENT '1:Completed, 2:Inprogress, 3:InActive',
  `inventory_order_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `inventory_order_udt` datetime DEFAULT NULL,
  `inventory_order_edt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_order`
--

INSERT INTO `inventory_order` (`inventory_order_id`, `user_id`, `inventory_order_total`, `inventory_order_actual_total`, `order_cash_received`, `inventory_order_cash_receivable`, `inventory_order_date`, `customer_name`, `customer_mobile_no`, `payment_status`, `inventory_order_status`, `inventory_order_sdt`, `inventory_order_udt`, `inventory_order_edt`) VALUES
(1, 1, 9000.00, 7500.00, 6000.00, 3000.00, '2020-11-12', 'Rehan', '0347513924', 'credit', 2, '2020-11-12 13:38:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_order_product`
--

CREATE TABLE `inventory_order_product` (
  `inventory_order_product_id` int(11) NOT NULL,
  `inventory_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `weight` float NOT NULL,
  `base_price` double(10,2) DEFAULT NULL,
  `sale_price` double(10,2) NOT NULL,
  `product_amount` double(10,2) NOT NULL,
  `product_profit` double(10,2) DEFAULT 0.00,
  `inventory_order_product_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `inventory_order_product_udt` datetime DEFAULT NULL,
  `inventory_order_product_edt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_order_product`
--

INSERT INTO `inventory_order_product` (`inventory_order_product_id`, `inventory_order_id`, `product_id`, `weight`, `base_price`, `sale_price`, `product_amount`, `product_profit`, `inventory_order_product_sdt`, `inventory_order_product_udt`, `inventory_order_product_edt`) VALUES
(1, 1, 1, 50, 150.00, 180.00, 9000.00, 1500.00, '2020-11-12 13:38:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lutbl_month`
--

CREATE TABLE `lutbl_month` (
  `month_id` int(11) NOT NULL,
  `month_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lutbl_month`
--

INSERT INTO `lutbl_month` (`month_id`, `month_name`) VALUES
(1, 'January'),
(2, 'February'),
(3, 'March'),
(4, 'April'),
(5, 'May'),
(6, 'June'),
(7, 'July'),
(8, 'August'),
(9, 'September'),
(10, 'October'),
(11, 'November'),
(12, 'December');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(300) NOT NULL,
  `product_weight_remaining` float DEFAULT NULL,
  `product_total_weight` int(11) DEFAULT NULL,
  `product_weight_sold` float DEFAULT 0,
  `product_unit` varchar(150) NOT NULL,
  `product_base_price` double(10,2) NOT NULL COMMENT 'Rate',
  `product_total_cost` double(10,2) DEFAULT NULL,
  `product_cost_per_kg` float DEFAULT NULL,
  `product_enter_by` int(11) NOT NULL,
  `product_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:Active, 2:Inactive',
  `product_date` date NOT NULL,
  `product_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `product_udt` datetime DEFAULT NULL,
  `product_edt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_weight_remaining`, `product_total_weight`, `product_weight_sold`, `product_unit`, `product_base_price`, `product_total_cost`, `product_cost_per_kg`, `product_enter_by`, `product_status`, `product_date`, `product_sdt`, `product_udt`, `product_edt`) VALUES
(1, 'Dell Charger 3V', 360, 410, 50, 'Pieces', 150.00, 61500.00, 150, 1, 1, '2020-11-03', '2020-11-12 12:37:11', '2020-11-12 13:38:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ar_ap`
--

CREATE TABLE `tbl_ar_ap` (
  `ar_ap_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `ar_ap_customer_name` varchar(100) DEFAULT NULL,
  `ar_ap_description` longtext DEFAULT NULL,
  `cash_received` double(10,2) NOT NULL DEFAULT 0.00,
  `remaining_balance` double(10,2) DEFAULT 0.00,
  `insert_description` varchar(50) DEFAULT NULL,
  `ar_ap_date` date DEFAULT NULL,
  `ar_ap_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `ar_ap_udt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_ar_ap`
--

INSERT INTO `tbl_ar_ap` (`ar_ap_id`, `order_id`, `ar_ap_customer_name`, `ar_ap_description`, `cash_received`, `remaining_balance`, `insert_description`, `ar_ap_date`, `ar_ap_sdt`, `ar_ap_udt`) VALUES
(1, 1, 'Rehan', 'cash', 5000.00, 4000.00, 'Insert', '2020-11-12', '2020-11-12 13:38:21', NULL),
(2, 1, 'Rehan', 'cash at HBL', 1000.00, 3000.00, 'Update', '2020-11-12', '2020-11-12 13:52:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cash_book`
--

CREATE TABLE `tbl_cash_book` (
  `cash_book_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `cash_amount_received` double(10,2) DEFAULT NULL,
  `cash_received_from` varchar(250) DEFAULT NULL,
  `cash_amount_credit` double(10,2) DEFAULT NULL,
  `cash_amount_credit_to` varchar(50) DEFAULT NULL,
  `cash_action` tinyint(4) NOT NULL COMMENT '1:Debit, 2:Credit',
  `cash_received_date` date NOT NULL,
  `cash_book_status` tinyint(4) NOT NULL DEFAULT 1,
  `cash_book_sdt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_cash_book`
--

INSERT INTO `tbl_cash_book` (`cash_book_id`, `order_id`, `cash_amount_received`, `cash_received_from`, `cash_amount_credit`, `cash_amount_credit_to`, `cash_action`, `cash_received_date`, `cash_book_status`, `cash_book_sdt`) VALUES
(1, 1, 5000.00, 'Rehan', NULL, NULL, 1, '2020-11-12', 1, '2020-11-12 13:38:21'),
(2, 1, 1000.00, 'Rehan', NULL, NULL, 1, '2020-11-12', 1, '2020-11-12 13:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_expense`
--

CREATE TABLE `tbl_expense` (
  `expense_id` int(11) NOT NULL,
  `expense_name` varchar(50) NOT NULL,
  `expense_description` longtext NOT NULL,
  `expense_price` double(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `expense_created_by` int(11) NOT NULL,
  `expense_status` tinyint(4) NOT NULL DEFAULT 1,
  `expense_sdt` datetime NOT NULL DEFAULT current_timestamp(),
  `expense_udt` datetime DEFAULT NULL,
  `expense_edt` datetime DEFAULT NULL
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
(1, 'abdullah@gmail.com', '$2y$10$pIFQYg2xmWORIntLwKo27OEMHprGW/3ucaRf9YyGfPwIvLOozXliW', 'Abdullah', 'master', 'Active');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `lutbl_month`
--
ALTER TABLE `lutbl_month`
  ADD PRIMARY KEY (`month_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `tbl_ar_ap`
--
ALTER TABLE `tbl_ar_ap`
  ADD PRIMARY KEY (`ar_ap_id`);

--
-- Indexes for table `tbl_cash_book`
--
ALTER TABLE `tbl_cash_book`
  ADD PRIMARY KEY (`cash_book_id`);

--
-- Indexes for table `tbl_expense`
--
ALTER TABLE `tbl_expense`
  ADD PRIMARY KEY (`expense_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory_order`
--
ALTER TABLE `inventory_order`
  MODIFY `inventory_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_order_product`
--
ALTER TABLE `inventory_order_product`
  MODIFY `inventory_order_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lutbl_month`
--
ALTER TABLE `lutbl_month`
  MODIFY `month_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_ar_ap`
--
ALTER TABLE `tbl_ar_ap`
  MODIFY `ar_ap_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_cash_book`
--
ALTER TABLE `tbl_cash_book`
  MODIFY `cash_book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_expense`
--
ALTER TABLE `tbl_expense`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
