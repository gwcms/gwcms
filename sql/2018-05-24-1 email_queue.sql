-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 24, 2018 at 03:20 PM
-- Server version: 10.2.14-MariaDB-10.2.14+maria~xenial-log
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_mail_queue`
--

CREATE TABLE `gw_mail_queue` (
  `id` int(11) NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` varchar(255) NOT NULL,
  `subject` varchar(400) NOT NULL,
  `body` text NOT NULL,
  `body_id` int(11) NOT NULL,
  `args` text NOT NULL,
  `plain` tinyint(4) NOT NULL DEFAULT 0,
  `error` varchar(255) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_mail_queue`
--
ALTER TABLE `gw_mail_queue`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_mail_queue`
--
ALTER TABLE `gw_mail_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
