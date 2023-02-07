

ALTER TABLE `gw_order_item` ADD `vat_group` TINYINT NOT NULL AFTER `qty_range`;



-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 24, 2023 at 03:47 PM
-- Server version: 8.0.31-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `badminton.lt`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_vatgroups`
--

CREATE TABLE `gw_vatgroups` (
  `id` int NOT NULL,
  `title` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `percent` tinyint NOT NULL,
  `key` varchar(25) NOT NULL,
  `note` varchar(255) NOT NULL,
  `active` tinyint NOT NULL,
  `access` tinyint NOT NULL DEFAULT '3',
  `priority` int NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `gw_vatgroups`
--

INSERT INTO `gw_vatgroups` (`id`, `title`, `percent`, `key`, `note`, `active`, `access`, `priority`, `insert_time`, `update_time`) VALUES
(1, '0%', 0, '0', '', 1, 3, 0, '2023-01-23 15:55:25', '2023-01-23 15:55:28'),
(2, '21%', 21, '21', '', 1, 3, 1, '2023-01-23 15:57:24', '2023-01-23 19:51:06');

ALTER TABLE `gw_vatgroups` CHANGE `access` `access` TINYINT NOT NULL DEFAULT '3';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_vatgroups`
--
ALTER TABLE `gw_vatgroups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unikalus` (`title`,`percent`,`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_vatgroups`
--
ALTER TABLE `gw_vatgroups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

ALTER TABLE `shop_products` ADD `vat_group` TINYINT NOT NULL AFTER `priority`;