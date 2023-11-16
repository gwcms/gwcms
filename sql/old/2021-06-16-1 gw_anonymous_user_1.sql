-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 17, 2021 at 01:09 AM
-- Server version: 8.0.25-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `badmintonocentras`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_anonymous_user`
--

CREATE TABLE `gw_anonymous_user` (
  `id` int NOT NULL,
  `idcookie` varchar(40) COLLATE utf8_lithuanian_ci NOT NULL,
  `lastip` varchar(15) COLLATE utf8_lithuanian_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_lithuanian_ci NOT NULL,
  `user_agent` int NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_lithuanian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_anonymous_user`
--
ALTER TABLE `gw_anonymous_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idcookie` (`idcookie`(3)) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_anonymous_user`
--
ALTER TABLE `gw_anonymous_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
