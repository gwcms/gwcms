-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 25, 2020 at 09:09 PM
-- Server version: 8.0.20-0ubuntu0.20.04.1
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
-- Table structure for table `gw_faq`
--

CREATE TABLE `gw_faq` (
  `id` int NOT NULL,
  `title_lt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `title_en` varchar(255) NOT NULL COMMENT 'copy from title_lt',
  `answer_lt` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `answer_en` text NOT NULL COMMENT 'copy from answer_lt',
  `group_id` int NOT NULL,
  `priority` int NOT NULL,
  `active` tinyint NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_faq`
--
ALTER TABLE `gw_faq`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_faq`
--
ALTER TABLE `gw_faq`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
