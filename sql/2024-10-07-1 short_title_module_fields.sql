ALTER TABLE `gw_adm_page_fields` ADD `short_title_lt` VARCHAR(30) NOT NULL AFTER `title_ru`;
ALTER TABLE `gw_adm_page_fields` ADD `short_title_en` VARCHAR(50) NOT NULL AFTER `short_title_lt`, ADD `short_title_ru` VARCHAR(50) NOT NULL AFTER `short_title_en`;