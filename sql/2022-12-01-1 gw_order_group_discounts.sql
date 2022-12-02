ALTER TABLE `gw_order_group` ADD `discount_id` INT NOT NULL AFTER `adm_message`;

ALTER TABLE `shop_discountcode` ADD `singleuse` TINYINT NOT NULL AFTER `create_order_id`, ADD `use_count` INT NOT NULL AFTER `singleuse`;

ALTER TABLE `gw_order_group` ADD `amount_coupon` FLOAT NOT NULL AFTER `amount_shipping`;
ALTER TABLE `gw_order_group` ADD `amount_discount` FLOAT NOT NULL AFTER `amount_coupon`;

ALTER TABLE `shop_discountcode` ADD `obj_type` ENUM('shop_products','gw_reservations','gw_membership','shop_subscription') NOT NULL AFTER `create_order_id`;

ALTER TABLE `gw_order_item` ADD `discount` FLOAT NOT NULL AFTER `unit_price`;

ALTER TABLE `shop_discountcode` ADD `valid_from` DATE NOT NULL AFTER `use_count`, ADD `expires` DATE NOT NULL AFTER `valid_from`;