-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 22, 2025 at 10:19 PM
-- Server version: 8.0.32-0ubuntu0.22.04.2
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `bulksms`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_uni_schema`
--

CREATE TABLE `gw_uni_schema` (
  `id` int NOT NULL,
  `type` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `str` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_lithuanian_ci NOT NULL,    
  `insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_lithuanian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_uni_schema`
--
ALTER TABLE `gw_uni_schema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_2` (`type`,`str`),
  ADD KEY `type` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_uni_schema`
--
ALTER TABLE `gw_uni_schema`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
