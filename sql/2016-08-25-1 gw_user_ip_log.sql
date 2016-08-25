-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 25, 2016 at 04:19 PM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 7.0.8-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `acs`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_user_ip_log`
--

CREATE TABLE `gw_user_ip_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `user_agent` varchar(200) NOT NULL,
  `insert_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gw_user_ip_log`
--

INSERT INTO `gw_user_ip_log` (`id`, `user_id`, `ip`, `user_agent`, `insert_time`) VALUES
(2, 9, '127.0.0.1', '', '2016-08-22 17:04:27'),
(3, 9, '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', '2016-08-22 17:13:32'),
(4, 9, '192.168.0.24', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', '2016-08-23 09:16:06'),
(5, 9, '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/51.0.2704.79 Chrome/51.0.2704.79 Safari/537.36', '2016-08-23 11:28:51'),
(6, 9, '192.168.0.24', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', '2016-08-24 08:36:32'),
(7, 9, '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', '2016-08-24 08:38:59'),
(8, 12, '192.168.0.24', '', '2016-08-24 08:50:15'),
(9, 187, '192.168.0.24', 'firefox', '2016-08-24 09:51:11'),
(10, 188, '192.168.0.24', 'firefox', '2016-08-24 09:52:34'),
(11, 189, '192.168.0.24', 'firefox', '2016-08-24 09:57:28'),
(12, 190, '192.168.0.24', 'firefox', '2016-08-24 09:57:52'),
(13, 191, '', 'firefox', '2016-08-24 10:15:40'),
(14, 192, '123.123.123.123', 'firefox', '2016-08-24 10:33:09'),
(15, 193, '123.123.123.123', 'firefox', '2016-08-24 10:35:53'),
(16, 194, '123.123.123.123', 'firefox', '2016-08-24 10:49:07'),
(17, 9, '192.168.0.24', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', '2016-08-24 12:08:58'),
(18, 9, '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', '2016-08-24 13:45:13'),
(19, 9, '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/51.0.2704.79 Chrome/51.0.2704.79 Safari/537.36', '2016-08-24 13:52:16'),
(20, 9, '192.168.0.24', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', '2016-08-24 13:55:43'),
(21, 31232, '123.123.123.123', 'firefox', '2016-08-24 15:44:09'),
(22, 9, '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/51.0.2704.79 Chrome/51.0.2704.79 Safari/537.36', '2016-08-24 16:49:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_user_ip_log`
--
ALTER TABLE `gw_user_ip_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_user_ip_log`
--
ALTER TABLE `gw_user_ip_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
