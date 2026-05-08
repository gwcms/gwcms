-- Repair partially imported chat rooms schema.
-- The old SQL importer splits by semicolon, so phpMyAdmin dumps can leave
-- gw_chat_rooms without indexes/AUTO_INCREMENT and create room id 0.

SET SESSION sql_mode = '';

SET @next_room_id := (
	SELECT GREATEST(
		COALESCE((SELECT MAX(id) FROM gw_chat_rooms), 0),
		COALESCE((SELECT MAX(room_id) FROM gw_chat_room_users), 0),
		COALESCE((SELECT MAX(room_id) FROM gw_chat_messages), 0),
		COALESCE((SELECT MAX(room_id) FROM gw_chat_events), 0),
		COALESCE((SELECT MAX(room_id) FROM gw_chat_attachments), 0),
		COALESCE((SELECT MAX(room_id) FROM gw_chat_message_reactions), 0)
	) + 1
);

UPDATE gw_chat_rooms
SET id = @next_room_id
WHERE id = 0
  AND NOT EXISTS (
	SELECT 1
	FROM (SELECT id FROM gw_chat_rooms) AS existing_rooms
	WHERE existing_rooms.id = @next_room_id
  );

UPDATE gw_chat_room_users
SET room_id = @next_room_id
WHERE room_id = 0
  AND EXISTS (
	SELECT 1
	FROM (SELECT id FROM gw_chat_rooms) AS existing_rooms
	WHERE existing_rooms.id = @next_room_id
  );

UPDATE gw_chat_rooms
SET last_message_time = NULL
WHERE last_message_time = '0000-00-00 00:00:00';

UPDATE gw_chat_rooms
SET last_event_time = NULL
WHERE last_event_time = '0000-00-00 00:00:00';

ALTER TABLE `gw_chat_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `last_message_time` (`last_message_time`),
  ADD KEY `direct_key` (`direct_key`),
  ADD KEY `last_event_time` (`last_event_time`);

ALTER TABLE `gw_chat_rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
