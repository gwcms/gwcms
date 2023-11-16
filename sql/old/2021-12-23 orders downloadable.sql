-- ecommerce 
ALTER TABLE `gw_order_group` ADD `downloadable` TINYINT NOT NULL AFTER `deliverable`;
ALTER TABLE `gw_order_item` ADD `downloadable` TINYINT NOT NULL AFTER `deliverable`;