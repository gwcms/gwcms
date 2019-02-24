
-- phpMyAdmin SQL Dump
-- version 4.4.13.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 26, 2016 at 11:40 PM
-- Server version: 5.6.28-0ubuntu0.15.10.1
-- PHP Version: 5.6.11-1ubuntu3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `datinggirls`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_support_messages`
--

CREATE TABLE IF NOT EXISTS `gw_support_messages` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `company` varchar(255) NOT NULL,
  `subject` int(100) NOT NULL,
  `message` text NOT NULL,
  `ip` varchar(30) NOT NULL,
  `seen` tinyint(4) NOT NULL DEFAULT '0',
  `insert_time` varchar(2555) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_support_messages`
--
ALTER TABLE `gw_support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_support_messages`
--
ALTER TABLE `gw_support_messages`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;