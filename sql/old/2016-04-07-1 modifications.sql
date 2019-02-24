
ALTER TABLE `tom_product_modif` ADD `sys_field` ENUM('model') NULL AFTER `product_id`;
ALTER TABLE `tom_product_modif` ADD `priority` INT NOT NULL DEFAULT '-1' AFTER `type`;

ALTER TABLE `tom_product_modif` CHANGE `sys_field` `field_type` ENUM('custom','model') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `tom_product_modif` CHANGE `type` `opt_type` TINYINT(4) NOT NULL DEFAULT '0';