-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2022 at 04:46 PM
-- Server version: 8.0.29-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `natos`
--

-- --------------------------------------------------------

--
-- Table structure for table `shop_discountcode`
--

CREATE TABLE `shop_discountcode` (
  `id` int NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8_lithuanian_ci NOT NULL,
  `percent` float NOT NULL,
  `limit_amount` float NOT NULL,
  `used_amount` float NOT NULL,
  `used` tinyint NOT NULL,
  `user_id` int NOT NULL,
  `active` tinyint NOT NULL,
  `note` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8_lithuanian_ci NOT NULL,
  `products` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8_lithuanian_ci NOT NULL,
  `create_order_id` int NOT NULL,
  `update_time` datetime DEFAULT NULL,
  `insert_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_lithuanian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shop_discountcode`
--
ALTER TABLE `shop_discountcode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_2` (`code`),
  ADD KEY `code` (`code`(2)) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shop_discountcode`
--
ALTER TABLE `shop_discountcode`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
