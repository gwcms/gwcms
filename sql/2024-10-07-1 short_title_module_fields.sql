ALTER TABLE `gw_adm_page_fields` ADD `short_title_lt` VARCHAR(100) NOT NULL AFTER `title_ru`;
ALTER TABLE `gw_adm_page_fields` ADD `short_title_en` VARCHAR(100) NOT NULL AFTER `short_title_lt`, ADD `short_title_ru` VARCHAR(100) NOT NULL AFTER `short_title_en`;

