ALTER TABLE `gw_forms` ADD `description_lt` TEXT NOT NULL AFTER `title_en`;
ALTER TABLE  `gw_forms` ADD  `description_ru` text NOT NULL  COMMENT  'copy from description_lt'  AFTER  `description_lt` ;
ALTER TABLE  `gw_forms` ADD  `description_en` text NOT NULL  COMMENT  'copy from description_lt'  AFTER  `description_lt` ;

ALTER TABLE `gw_outg_sms` ADD `retry` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `weight`;

SET GLOBAL sql_mode=''
ALTER TABLE `gw_form_elements` CHANGE `options` `options_src` INT NOT NULL;

ALTER TABLE `gw_classificators` CHANGE `title` `title_lt` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `gw_classificators` ADD  `title_en` varchar(100) NOT NULL  COMMENT  'copy from title_lt'  AFTER  `title_lt` ;
ALTER TABLE  `gw_classificators` ADD  `title_ru` varchar(100) NOT NULL  COMMENT  'copy from title_lt'  AFTER  `title_lt` ;

ALTER TABLE `gw_classificators` CHANGE `title_lt` `title_lt` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_classificators` CHANGE `title_en` `title_en` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_classificators` CHANGE `title_ru` `title_ru` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `gw_form_answers` ADD `auid` INT NOT NULL COMMENT 'gw_anonynous_user' AFTER `user_id`;
