-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 08, 2018 at 08:32 PM
-- Server version: 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_mail_templates`
--

DROP TABLE IF EXISTS `gw_mail_templates`;
CREATE TABLE `gw_mail_templates` (
  `id` int(11) NOT NULL,
  `owner_type` varchar(255) NOT NULL,
  `owner_field` varchar(100) NOT NULL,
  `idname` varchar(100) NOT NULL,
  `admin_title` varchar(255) NOT NULL,
  `custom_sender` tinyint(255) NOT NULL DEFAULT '0',
  `sender_lt` varchar(200) NOT NULL,
  `sender_en` varchar(200) NOT NULL,
  `sender_ru` varchar(200) NOT NULL,
  `subject_lt` varchar(255) NOT NULL,
  `subject_en` varchar(255) NOT NULL,
  `subject_ru` varchar(255) NOT NULL,
  `body_lt` text NOT NULL,
  `body_en` text NOT NULL,
  `body_ru` text NOT NULL,
  `recipients_lt` text NOT NULL,
  `recipients_en` text NOT NULL,
  `recipients_ru` text NOT NULL,
  `ln_enabled_lt` tinyint(4) NOT NULL,
  `ln_enabled_en` tinyint(4) NOT NULL,
  `ln_enabled_ru` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `format_texts` tinyint(4) NOT NULL DEFAULT '0',
  `body_editor` tinyint(4) DEFAULT '0',
  `body_editor_height` varchar(10) NOT NULL,
  `variables` text NOT NULL,
  `recipients_count` int(11) NOT NULL,
  `recipients_data` text NOT NULL,
  `config` text NOT NULL,
  `sent_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_mail_templates`
--
ALTER TABLE `gw_mail_templates`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_mail_templates`
--
ALTER TABLE `gw_mail_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;