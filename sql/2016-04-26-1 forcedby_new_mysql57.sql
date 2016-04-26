ALTER TABLE `gw_adm_sitemap` CHANGE `title_en` `title_en` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `gw_adm_sitemap` CHANGE `title_no` `title_no` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `gw_adm_sitemap` CHANGE `fields` `fields` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `gw_adm_sitemap` CHANGE `update_time` `update_time` DATETIME NULL;
ALTER TABLE `gw_adm_sitemap` CHANGE `notes` `notes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `gw_adm_sitemap` CHANGE `views` `views` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `gw_adm_sitemap` CHANGE `orders` `orders` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `gw_adm_sitemap` CHANGE `info` `info` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `gw_adm_sitemap` CHANGE `priority` `priority` INT(11) NOT NULL DEFAULT '0';