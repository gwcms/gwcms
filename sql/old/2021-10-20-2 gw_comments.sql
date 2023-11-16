-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 20, 2021 at 05:42 PM
-- Server version: 8.0.26-0ubuntu0.20.04.2
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `events.ltf.lt`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_comments`
--

CREATE TABLE `gw_comments` (
  `id` int NOT NULL,
  `obj_type` varchar(25) NOT NULL,
  `obj_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `insert_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_comments`
--
ALTER TABLE `gw_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`obj_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_comments`
--
ALTER TABLE `gw_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
