ALTER TABLE `gw_order_group` ADD `itmcnt` TINYINT NOT NULL AFTER `active`


ALTER TABLE `gw_mail_queue` ADD `scheduled` DATETIME NULL AFTER `status`;

UPDATE gw_order_group AS g SET itmcnt = (SELECT count(*) FROM gw_order_item AS i WHERE i.group_id=g.id);