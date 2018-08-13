ALTER TABLE `gw_attachments` ADD `checksum` VARCHAR(255) NOT NULL AFTER `content_type`;
ALTER TABLE `gw_attachments` ADD `extra` TEXT NOT NULL AFTER `checksum`;