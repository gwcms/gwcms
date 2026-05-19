-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 25, 2026 at 09:41 PM
-- Server version: 8.0.45-0ubuntu0.24.04.1
-- PHP Version: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `artistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_chat_messages`
--

CREATE TABLE `gw_chat_messages` (
  `id` bigint NOT NULL,
  `room_id` int NOT NULL,
  `sender_id` int NOT NULL DEFAULT '0',
  `message` mediumtext NOT NULL,
  `source` enum('web','backend','system','ai') NOT NULL DEFAULT 'web',
  `mentions_json` text,
  `reply_to_message_id` bigint NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `gw_chat_messages`
--

INSERT INTO `gw_chat_messages` (`id`, `room_id`, `sender_id`, `message`, `source`, `mentions_json`, `reply_to_message_id`, `is_deleted`, `insert_time`, `update_time`) VALUES
(1, 4, 9, 'PHP protocol message 1 1774455195', '', '', 0, 0, '2026-03-25 18:13:15', '2026-03-25 18:13:15'),
(3, 5, 9, 'PHP protocol message 2 1774455756', '', '', 0, 0, '2026-03-25 18:22:36', '2026-03-25 18:22:36'),
(4, 5, 9, 'PHP protocol message 3 1774455756', '', '', 0, 0, '2026-03-25 18:22:36', '2026-03-25 18:22:36'),
(5, 5, 9, 'PHP protocol message 4 1774455756', '', '', 0, 0, '2026-03-25 18:22:36', '2026-03-25 18:22:36'),
(7, 6, 9, 'PHP protocol message 2 1774455831', '', '', 0, 0, '2026-03-25 18:23:51', '2026-03-25 18:23:51'),
(8, 6, 9, 'PHP protocol message 3 1774455831', '', '', 0, 0, '2026-03-25 18:23:51', '2026-03-25 18:23:51'),
(9, 6, 9, 'PHP protocol message 4 1774455831', '', '', 0, 0, '2026-03-25 18:23:51', '2026-03-25 18:23:51'),
(10, 7, 9, 'PHP private protocol test 1774455831', '', '', 0, 0, '2026-03-25 18:23:51', '2026-03-25 18:23:51'),
(12, 8, 9, 'PHP protocol message 2 1774455987', 'backend', '', 0, 0, '2026-03-25 18:26:27', '2026-03-25 18:26:27'),
(13, 8, 9, 'PHP protocol message 3 1774455987', 'backend', '', 0, 0, '2026-03-25 18:26:27', '2026-03-25 18:26:27'),
(14, 8, 9, 'PHP protocol message 4 1774455987', 'backend', '', 0, 0, '2026-03-25 18:26:27', '2026-03-25 18:26:27'),
(15, 7, 9, 'PHP private protocol test 1774455987', 'backend', '', 0, 0, '2026-03-25 18:26:27', '2026-03-25 18:26:27'),
(17, 9, 9, 'PHP protocol message 2 1774456115', 'backend', '', 0, 0, '2026-03-25 18:28:35', '2026-03-25 18:28:35'),
(18, 9, 9, 'PHP protocol message 3 1774456115', 'backend', '', 0, 0, '2026-03-25 18:28:35', '2026-03-25 18:28:35'),
(19, 9, 9, 'PHP protocol message 4 1774456116', 'backend', '', 0, 0, '2026-03-25 18:28:36', '2026-03-25 18:28:36'),
(20, 7, 9, 'PHP private protocol test 1774456116', 'backend', '', 0, 0, '2026-03-25 18:28:36', '2026-03-25 18:28:36'),
(22, 10, 9, 'PHP protocol message 2 1774456482', 'backend', '', 0, 0, '2026-03-25 18:34:42', '2026-03-25 18:34:42'),
(23, 10, 9, 'PHP protocol message 3 1774456482', 'backend', '', 0, 0, '2026-03-25 18:34:42', '2026-03-25 18:34:42'),
(24, 10, 9, 'PHP protocol message 4 1774456482', 'backend', '', 0, 0, '2026-03-25 18:34:42', '2026-03-25 18:34:42'),
(25, 7, 9, 'PHP private protocol test 1774456482', 'backend', '', 0, 0, '2026-03-25 18:34:42', '2026-03-25 18:34:42'),
(26, 11, 9, 'Protocol self test 1774458721782', 'web', '', 0, 0, '2026-03-25 19:12:01', '2026-03-25 19:12:01'),
(27, 12, 9, 'Protocol self test 1774460220945', 'web', '', 0, 0, '2026-03-25 19:37:00', '2026-03-25 19:37:00'),
(28, 13, 9, 'Protocol self test 1774466015551', 'web', '', 0, 0, '2026-03-25 21:13:35', '2026-03-25 21:13:35'),
(29, 1, 9, 'labas', 'web', '', 0, 0, '2026-03-25 21:14:26', '2026-03-25 21:14:26'),
(30, 1, 9, 'kaip sekas', 'web', '', 0, 0, '2026-03-25 21:14:30', '2026-03-25 21:14:30'),
(31, 1, 9, 'nu ka zinau gerai', 'web', '', 0, 0, '2026-03-25 21:15:15', '2026-03-25 21:15:15'),
(32, 1, 9, 'bebras', 'web', '', 0, 0, '2026-03-25 21:15:20', '2026-03-25 21:15:20');

-- --------------------------------------------------------

--
-- Table structure for table `gw_chat_rooms`
--

CREATE TABLE `gw_chat_rooms` (
  `id` int NOT NULL,
  `type` enum('private','group') NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `direct_key` varchar(100) DEFAULT NULL COMMENT 'private rooms only, for example smallerUserId:largerUserId',
  `creator_id` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `room_history_limit` int DEFAULT NULL COMMENT 'NULL = unlimited, intended for private rooms; group rooms may set explicit limit',
  `last_message_id` bigint NOT NULL DEFAULT '0',
  `last_message_time` datetime DEFAULT NULL,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `gw_chat_rooms`
--

INSERT INTO `gw_chat_rooms` (`id`, `type`, `title`, `direct_key`, `creator_id`, `is_active`, `room_history_limit`, `last_message_id`, `last_message_time`, `insert_time`, `update_time`) VALUES
(1, 'group', 'General', NULL, 1, 1, 10000, 32, '2026-03-25 21:15:20', '2026-03-25 17:50:54', '2026-03-25 21:15:20'),
(2, 'group', 'PHP Protocol Test 2026-03-25 18:07:05', '', 9, 1, 3, 0, '0000-00-00 00:00:00', '2026-03-25 18:07:05', '2026-03-25 18:07:05'),
(4, 'group', 'PHP Protocol Test 2026-03-25 18:13:15', '', 9, 1, 3, 1, '2026-03-25 18:13:15', '2026-03-25 18:13:15', '2026-03-25 18:13:15'),
(5, 'group', 'PHP Protocol Test 2026-03-25 18:22:36', '', 9, 1, 3, 5, '2026-03-25 18:22:36', '2026-03-25 18:22:36', '2026-03-25 18:22:36'),
(6, 'group', 'PHP Protocol Test 2026-03-25 18:23:51', '', 9, 1, 3, 9, '2026-03-25 18:23:51', '2026-03-25 18:23:51', '2026-03-25 18:23:51'),
(7, 'private', '', '1:9', 9, 1, 0, 25, '2026-03-25 18:34:42', '2026-03-25 18:23:51', '2026-03-25 18:34:42'),
(8, 'group', 'PHP Protocol Test 2026-03-25 18:26:27', '', 9, 1, 3, 14, '2026-03-25 18:26:27', '2026-03-25 18:26:27', '2026-03-25 18:26:27'),
(9, 'group', 'PHP Protocol Test 2026-03-25 18:28:35', '', 9, 1, 3, 19, '2026-03-25 18:28:36', '2026-03-25 18:28:35', '2026-03-25 18:28:36'),
(10, 'group', 'PHP Protocol Test 2026-03-25 18:34:42', '', 9, 1, 3, 24, '2026-03-25 18:34:42', '2026-03-25 18:34:42', '2026-03-25 18:34:42'),
(11, 'group', 'Protocol Test 1774458721675', '', 9, 1, 100, 26, '2026-03-25 19:12:01', '2026-03-25 19:12:01', '2026-03-25 19:12:01'),
(12, 'group', 'Protocol Test 1774460220896', '', 9, 1, 100, 27, '2026-03-25 19:37:00', '2026-03-25 19:37:00', '2026-03-25 19:37:00'),
(13, 'group', 'Protocol Test 1774466015450', '', 9, 1, 100, 28, '2026-03-25 21:13:35', '2026-03-25 21:13:35', '2026-03-25 21:13:35');

-- --------------------------------------------------------

--
-- Table structure for table `gw_chat_room_users`
--

CREATE TABLE `gw_chat_room_users` (
  `id` int NOT NULL,
  `room_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('owner','member') NOT NULL DEFAULT 'member',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_seen_message_id` bigint NOT NULL DEFAULT '0',
  `last_seen_time` datetime DEFAULT NULL,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `gw_chat_room_users`
--

INSERT INTO `gw_chat_room_users` (`id`, `room_id`, `user_id`, `role`, `is_active`, `last_seen_message_id`, `last_seen_time`, `insert_time`, `update_time`) VALUES
(1, 2, 9, 'owner', 1, 0, '0000-00-00 00:00:00', '2026-03-25 18:07:05', '2026-03-25 18:07:05'),
(2, 4, 9, 'owner', 1, 0, '0000-00-00 00:00:00', '2026-03-25 18:13:15', '2026-03-25 18:13:15'),
(3, 5, 9, 'owner', 1, 5, '2026-03-25 18:22:36', '2026-03-25 18:22:36', '2026-03-25 18:22:36'),
(4, 6, 9, 'owner', 1, 9, '2026-03-25 18:23:51', '2026-03-25 18:23:51', '2026-03-25 18:23:51'),
(5, 7, 9, 'owner', 1, 0, '0000-00-00 00:00:00', '2026-03-25 18:23:51', '2026-03-25 18:34:42'),
(6, 7, 1, 'member', 1, 0, '0000-00-00 00:00:00', '2026-03-25 18:23:51', '2026-03-25 18:34:42'),
(7, 8, 9, 'owner', 1, 14, '2026-03-25 18:26:27', '2026-03-25 18:26:27', '2026-03-25 18:26:27'),
(8, 9, 9, 'owner', 1, 19, '2026-03-25 18:28:36', '2026-03-25 18:28:35', '2026-03-25 18:28:36'),
(9, 10, 9, 'owner', 1, 24, '2026-03-25 18:34:42', '2026-03-25 18:34:42', '2026-03-25 18:34:42'),
(10, 11, 9, 'owner', 0, 26, '2026-03-25 19:12:01', '2026-03-25 19:12:01', '2026-03-25 19:12:01'),
(11, 12, 9, 'owner', 0, 27, '2026-03-25 19:37:00', '2026-03-25 19:37:00', '2026-03-25 19:37:00'),
(12, 13, 9, 'owner', 0, 28, '2026-03-25 21:13:35', '2026-03-25 21:13:35', '2026-03-25 21:13:35'),
(13, 1, 9, 'member', 1, 32, '2026-03-25 21:15:20', '2026-03-25 21:14:22', '2026-03-25 21:15:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_chat_messages`
--
ALTER TABLE `gw_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id_id` (`room_id`,`id`),
  ADD KEY `room_id_insert_time` (`room_id`,`insert_time`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `reply_to_message_id` (`reply_to_message_id`);

--
-- Indexes for table `gw_chat_rooms`
--
ALTER TABLE `gw_chat_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `last_message_time` (`last_message_time`),
  ADD KEY `direct_key` (`direct_key`);

--
-- Indexes for table `gw_chat_room_users`
--
ALTER TABLE `gw_chat_room_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_user_active` (`room_id`,`user_id`,`is_active`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id_is_active` (`room_id`,`is_active`),
  ADD KEY `user_id_is_active` (`user_id`,`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_chat_messages`
--
ALTER TABLE `gw_chat_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `gw_chat_rooms`
--
ALTER TABLE `gw_chat_rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `gw_chat_room_users`
--
ALTER TABLE `gw_chat_room_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;
