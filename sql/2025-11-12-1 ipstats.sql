DROP TABLE IF EXISTS `request_ip_verify`;
DROP TABLE IF EXISTS `request_ip_stats`;
DROP TABLE IF EXISTS `request_by_user_agent`;

CREATE TABLE request_ip_stats (
    year  SMALLINT UNSIGNED NOT NULL,
    month TINYINT UNSIGNED NOT NULL,
    day   TINYINT UNSIGNED NOT NULL,
    hour  TINYINT UNSIGNED NOT NULL,
    ip    INT UNSIGNED NOT NULL,
    cnt   INT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (year, month, day, hour, ip)
) ENGINE=InnoDB;


CREATE TABLE request_ip_verify (
  ip INT UNSIGNED NOT NULL PRIMARY KEY,
  state TINYINT NOT NULL DEFAULT 0,        -- -1 = whitelist, 0 = normal, 1 = must verify, 2 = verified
  expires DATETIME DEFAULT NULL,
  country CHAR(2) DEFAULT NULL,
  host VARCHAR(30) DEFAULT NULL,
  updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (state),
  INDEX (expires)
) ENGINE=InnoDB;

ALTER TABLE `request_ip_stats` ADD `ua` INT NOT NULL AFTER `cnt`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 13, 2025 at 05:04 PM
-- Server version: 8.0.43-0ubuntu0.24.04.2
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `badminton.lt`
--

-- --------------------------------------------------------

--
-- Table structure for table `request_by_user_agent`
--

CREATE TABLE `request_by_user_agent` (
  `date` date NOT NULL,
  `user_agent`  INT UNSIGNED NOT NULL,
  `cnt` smallint NOT NULL,
  `speed` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `request_by_user_agent`
--
ALTER TABLE `request_by_user_agent`
  ADD UNIQUE KEY `unikalus` (`date`,`user_agent`) USING BTREE;
COMMIT;


ALTER TABLE `request_ip_verify` ADD `tag` INT NOT NULL AFTER `ua`;