
-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 06, 2021 at 03:12 PM
-- Server version: 8.0.23-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `beach`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_order_group`
--

CREATE TABLE `gw_order_group` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `pay_confirm_id` int NOT NULL,
  `pay_test` tinyint NOT NULL,
  `payment_status` tinyint NOT NULL,
  `amount` float NOT NULL,
  `status` varchar(50) NOT NULL,
  `adm_processed` tinyint NOT NULL,
  `invoicevars` text NOT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `insert_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gw_order_item`
--

CREATE TABLE `gw_order_item` (
  `id` int NOT NULL,
  `group_id` int NOT NULL,
  `obj_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `qty` tinyint NOT NULL,
  `unit_price` float NOT NULL,
  `obj_id` int NOT NULL,
  `context_obj_id` int NOT NULL,
  `context_obj_type` varchar(50) NOT NULL,
  `user_id` int NOT NULL,
  `payd` tinyint NOT NULL,
  `insert_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_order_group`
--
ALTER TABLE `gw_order_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gw_order_item`
--
ALTER TABLE `gw_order_item`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_order_group`
--
ALTER TABLE `gw_order_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gw_order_item`
--
ALTER TABLE `gw_order_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
