ALTER TABLE `gw_faq` ADD `expanded` TINYINT NOT NULL AFTER `priority`;
ALTER TABLE `gw_faq` CHANGE `expanded` `expanded` TINYINT NOT NULL DEFAULT '1';