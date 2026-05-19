ALTER TABLE `gw_chat_messages`
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `gw_chat_messages`
  MODIFY `message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY `mentions_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
