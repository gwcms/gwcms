ALTER TABLE `gw_outg_sms` ADD `gw` VARCHAR(5) NOT NULL AFTER `remote_id`;
ALTER TABLE `gw_outg_sms` ADD `country` CHAR(3) NOT NULL AFTER `number`;

ALTER TABLE `gw_outg_sms` CHANGE `remote_id` `remote_id` VARCHAR(40) NOT NULL;