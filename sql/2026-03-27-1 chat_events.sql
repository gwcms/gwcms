CREATE TABLE `gw_chat_events` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `event_type` varchar(32) NOT NULL,
  `ref_id` bigint NOT NULL DEFAULT '0',
  `payload_json` text,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id_id` (`room_id`,`id`),
  KEY `room_id_insert_time` (`room_id`,`insert_time`),
  KEY `user_id` (`user_id`),
  KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `gw_chat_rooms`
  ADD COLUMN `last_event_id` bigint NOT NULL DEFAULT '0' AFTER `last_message_time`,
  ADD COLUMN `last_event_time` datetime DEFAULT NULL AFTER `last_event_id`,
  ADD KEY `last_event_time` (`last_event_time`);

ALTER TABLE `gw_chat_room_users`
  ADD COLUMN `last_seen_event_id` bigint NOT NULL DEFAULT '0' AFTER `last_seen_message_id`;

UPDATE `gw_chat_rooms`
SET `last_event_id` = `last_message_id`,
    `last_event_time` = `last_message_time`
WHERE `last_message_id` > 0;

UPDATE `gw_chat_room_users`
SET `last_seen_event_id` = `last_seen_message_id`
WHERE `last_seen_message_id` > 0;
