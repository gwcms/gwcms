ALTER TABLE `gw_form_answers` ADD `sign_time` DATETIME NOT NULL AFTER `signature`;
ALTER TABLE `gw_form_answers` CHANGE `sign_time` `sign_time` DATETIME NULL;

ALTER TABLE `gw_form_answers` ADD `secret` VARCHAR(100) NOT NULL AFTER `signature`;