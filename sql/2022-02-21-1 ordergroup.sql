ALTER TABLE `gw_order_group` ADD `itmcnt` TINYINT NOT NULL AFTER `active`


ALTER TABLE `gw_mail_queue` ADD `scheduled` DATETIME NULL AFTER `status`;