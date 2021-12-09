
-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 10, 2021 at 01:13 AM
-- Server version: 8.0.27-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `books.webshop24.lt`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_payuniversal_log`
--

CREATE TABLE `gw_payuniversal_log` (
  `id` int NOT NULL,
  `method` varchar(20) NOT NULL,
  `order_id` int NOT NULL,
  `remote_id` varchar(25) NOT NULL,
  `received_amount` float NOT NULL,
  `data` text NOT NULL,
  `processed` tinyint NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_payuniversal_log`
--
ALTER TABLE `gw_payuniversal_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_payuniversal_log`
--
ALTER TABLE `gw_payuniversal_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
