-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 27, 2017 at 06:25 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_adm_page_views`
--

CREATE TABLE `gw_adm_page_views` (
  `id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `type` enum('order') DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `title_short` varchar(100) NOT NULL,
  `condition` varchar(255) NOT NULL,
  `order` varchar(255) NOT NULL,
  `fields` text NOT NULL,
  `priority` int(11) NOT NULL,
  `page_by` int(11) NOT NULL,
  `default` tinyint(4) NOT NULL,
  `dropdown` tinyint(4) NOT NULL,
  `calculate` tinyint(4) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_adm_page_views`
--
ALTER TABLE `gw_adm_page_views`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_adm_page_views`
--
ALTER TABLE `gw_adm_page_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
