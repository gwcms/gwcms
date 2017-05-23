-- phpMyAdmin SQL Dump
-- version 4.4.13.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 11, 2016 at 08:17 PM
-- Server version: 5.6.27-0ubuntu1
-- PHP Version: 5.6.11-1ubuntu3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bulksms`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_temp_data`
--

CREATE TABLE IF NOT EXISTS `gw_temp_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `value` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `insert_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_temp_data`
--
ALTER TABLE `gw_temp_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group` (`group`),
  ADD KEY `name` (`name`(5)) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_temp_data`
--
ALTER TABLE `gw_temp_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;