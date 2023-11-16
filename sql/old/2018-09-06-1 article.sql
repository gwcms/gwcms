ALTER TABLE `gw_articles` CHANGE `update_time` `update_time` DATETIME NULL;
ALTER TABLE `gw_articles` ADD `short` VARCHAR(500) NOT NULL AFTER `title`;