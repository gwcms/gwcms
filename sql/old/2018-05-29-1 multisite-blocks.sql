
ALTER TABLE `gw_sitemap` ADD `site_id` INT NOT NULL AFTER `id`;
update `gw_sitemap` SET site_id=1;

-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 29, 2018 at 08:36 PM
-- Server version: 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_sites`
--

DROP TABLE IF EXISTS `gw_sites`;
CREATE TABLE `gw_sites` (
  `id` int(11) NOT NULL,
  `title_lt` varchar(255) NOT NULL,
  `title_ru` varchar(255) NOT NULL COMMENT 'copy from title_lt',
  `title_en` varchar(255) NOT NULL COMMENT 'copy from title_lt',
  `active` int(11) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `hosts` varchar(255) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gw_sites`
--

INSERT INTO `gw_sites` (`id`, `title_lt`, `title_ru`, `title_en`, `active`, `admin_email`, `hosts`, `insert_time`, `update_time`) VALUES
(1, 'ArtistDB', '', '', 1, '', '*', '2018-03-13 16:33:11', '2018-05-28 12:41:38'),
(2, 'M. K. Čiurlionio konkursai', '', '', 1, '', 'ciurlionis.link,ciurlionis.dev', '2018-03-13 16:37:29', '2018-05-29 12:04:21');

-- --------------------------------------------------------

--
-- Table structure for table `gw_site_blocks`
--

DROP TABLE IF EXISTS `gw_site_blocks`;
CREATE TABLE `gw_site_blocks` (
  `id` int(11) NOT NULL,
  `site_id` tinyint(4) NOT NULL,
  `name` varchar(100) NOT NULL,
  `path_filter` varchar(255) NOT NULL,
  `contents_type` tinyint(4) NOT NULL,
  `contents` text NOT NULL,
  `ln` varchar(2) NOT NULL,
  `active` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `preload` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gw_site_blocks`
--

INSERT INTO `gw_site_blocks` (`id`, `site_id`, `name`, `path_filter`, `contents_type`, `contents`, `ln`, `active`, `priority`, `insert_time`, `update_time`, `preload`) VALUES
(1, 1, 'theme', '', 1, 'main', '*', 1, 0, '2018-05-29 17:20:46', '2018-05-29 19:42:41', 1),
(2, 2, 'theme', '', 1, 'universal', '*', 1, 0, '2018-05-29 18:56:39', '2018-05-29 19:42:53', 1),
(6, 2, 'theme_color', '', 1, 'aqua', '*', 1, 0, '2018-05-29 19:12:36', '2018-05-29 19:42:58', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_sites`
--
ALTER TABLE `gw_sites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gw_site_blocks`
--
ALTER TABLE `gw_site_blocks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_sites`
--
ALTER TABLE `gw_sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `gw_site_blocks`
--
ALTER TABLE `gw_site_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;