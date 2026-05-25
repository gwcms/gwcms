-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 25, 2026 at 11:59 PM
-- Server version: 8.0.45-0ubuntu0.24.04.1
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `artistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_chat_attachments`
--

CREATE TABLE `gw_chat_attachments` (
  `id` bigint NOT NULL,
  `message_id` bigint NOT NULL DEFAULT '0',
  `room_id` int NOT NULL DEFAULT '0',
  `uploader_id` int NOT NULL DEFAULT '0',
  `storage` enum('local','remote') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local',
  `kind` enum('image','file') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file',
  `original_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `stored_filename` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `relpath` varchar(700) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `mime` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `size` int UNSIGNED NOT NULL DEFAULT '0',
  `public_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `thumb_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gw_chat_events`
--

CREATE TABLE `gw_chat_events` (
  `id` bigint NOT NULL,
  `room_id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `event_type` varchar(32) NOT NULL,
  `ref_id` bigint NOT NULL DEFAULT '0',
  `payload_json` text,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gw_chat_messages`
--

CREATE TABLE `gw_chat_messages` (
  `id` bigint NOT NULL,
  `room_id` int NOT NULL,
  `sender_id` int NOT NULL DEFAULT '0',
  `message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` enum('web','backend','system','ai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `mentions_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reply_to_message_id` bigint NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gw_chat_message_reactions`
--

CREATE TABLE `gw_chat_message_reactions` (
  `id` bigint NOT NULL,
  `message_id` bigint NOT NULL,
  `room_id` int NOT NULL,
  `user_id` int NOT NULL,
  `reaction` varchar(16) NOT NULL,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `last_event_id` bigint NOT NULL DEFAULT '0',
  `last_event_time` datetime DEFAULT NULL,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
  `last_seen_event_id` bigint NOT NULL DEFAULT '0',
  `last_seen_time` datetime DEFAULT NULL,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_chat_attachments`
--
ALTER TABLE `gw_chat_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `room_id_message_id` (`room_id`,`message_id`),
  ADD KEY `stored_filename` (`stored_filename`),
  ADD KEY `uploader_id` (`uploader_id`);

--
-- Indexes for table `gw_chat_events`
--
ALTER TABLE `gw_chat_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id_id` (`room_id`,`id`),
  ADD KEY `room_id_insert_time` (`room_id`,`insert_time`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_type` (`event_type`);

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
-- Indexes for table `gw_chat_message_reactions`
--
ALTER TABLE `gw_chat_message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_message_user` (`message_id`,`user_id`),
  ADD KEY `idx_room_message` (`room_id`,`message_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `gw_chat_rooms`
--
ALTER TABLE `gw_chat_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `last_message_time` (`last_message_time`),
  ADD KEY `direct_key` (`direct_key`),
  ADD KEY `last_event_time` (`last_event_time`);

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
-- AUTO_INCREMENT for table `gw_chat_attachments`
--
ALTER TABLE `gw_chat_attachments`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gw_chat_events`
--
ALTER TABLE `gw_chat_events`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gw_chat_messages`
--
ALTER TABLE `gw_chat_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gw_chat_message_reactions`
--
ALTER TABLE `gw_chat_message_reactions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gw_chat_rooms`
--
ALTER TABLE `gw_chat_rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gw_chat_room_users`
--
ALTER TABLE `gw_chat_room_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
