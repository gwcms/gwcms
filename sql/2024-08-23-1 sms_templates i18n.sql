ALTER TABLE `gw_sms_templates` CHANGE `message` `message_lt` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;
ALTER TABLE `gw_sms_templates` ADD `message_en` TEXT NOT NULL AFTER `message_lt`;
ALTER TABLE `gw_sms_templates` CHANGE `message_lt` `body_lt` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;
ALTER TABLE `gw_sms_templates` CHANGE `message_en` `body_en` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;