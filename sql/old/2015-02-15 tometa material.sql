ALTER TABLE `tom_product_school_ext` CHANGE `material` `material_id` INT NOT NULL;
ALTER TABLE `tom_product_school_ext` CHANGE `material_id` `material1_id` INT( 11 ) NOT NULL;
ALTER TABLE `tom_product_school_ext` ADD `usage` FLOAT NOT NULL AFTER `material_id` ;
ALTER TABLE `tom_product_school_ext` CHANGE `usage` `usage1` FLOAT NOT NULL;
ALTER TABLE `tom_product_school_ext` ADD `material2_id` INT NOT NULL AFTER `material1_id` ;
ALTER TABLE `tom_product_school_ext` ADD `usage2` FLOAT NOT NULL AFTER `usage1` ;



-- bus isskaiciuojamas dydis
ALTER TABLE `tom_order_items` DROP `dydis`;
ALTER TABLE `tom_schools` DROP `material`;
ALTER TABLE `tom_schools` DROP `model`;
ALTER TABLE `tom_material` DROP `quantity`;

ALTER TABLE `tom_order_items` ADD `material` VARCHAR( 100 ) NOT NULL AFTER `sizes` ;


