ALTER TABLE `gw_adm_page_views` ADD `group_by` VARCHAR(255) NOT NULL AFTER `page_by`;
ALTER TABLE `gw_adm_page_views` ADD `select` VARCHAR(255) NOT NULL AFTER `title_short`;
-- ALTER TABLE `gw_adm_page_views` ADD `fields_diff` TEXT NOT NULL AFTER `fields`;
ALTER TABLE `gw_adm_page_views` ADD `access` TINYINT NOT NULL DEFAULT '3' AFTER `active`;