CREATE TABLE `gw_chat_message_reactions` (
  `id` bigint NOT NULL,
  `message_id` bigint NOT NULL,
  `room_id` int NOT NULL,
  `user_id` int NOT NULL,
  `reaction` varchar(16) NOT NULL,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `gw_chat_message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_message_user` (`message_id`,`user_id`),
  ADD KEY `idx_room_message` (`room_id`,`message_id`),
  ADD KEY `idx_user` (`user_id`);

ALTER TABLE `gw_chat_message_reactions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;
