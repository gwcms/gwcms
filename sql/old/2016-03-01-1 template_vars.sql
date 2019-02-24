ALTER TABLE `gw_template_vars` ADD `params` TEXT NOT NULL AFTER `type`;
ALTER TABLE `gw_template_vars` CHANGE `title` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_template_vars` ADD `title` VARCHAR(100) NOT NULL AFTER `name`;