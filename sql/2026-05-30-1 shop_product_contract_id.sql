ALTER TABLE `shop_products` MODIFY `contracts` TINYINT NULL;
ALTER TABLE `shop_products` ADD `contract_id` INT NULL AFTER `contracts`;

UPDATE `gw_adm_page_fields`
SET
	`title_lt` = 'Sutartys',
	`inp_type` = 'bool',
	`modpath` = '',
	`active` = 0
WHERE `parent` = 'shop_products' AND `fieldname` = 'contracts';

INSERT INTO `gw_adm_page_fields`
	(`parent`, `fieldset`, `fieldname`, `title_lt`, `title_en`, `title_ru`, `required`, `type`, `inp_type`, `options`, `config`, `size`, `i18n`, `hidden_note_lt`, `hidden_note_ru`, `hidden_note_en`, `note_lt`, `note_ru`, `note_en`, `placeholder_lt`, `placeholder_ru`, `placeholder_en`, `modpath`, `active`, `priority`, `insert_time`)
SELECT
	'shop_products', '', 'contract_id', 'Sutarties dokumentas', '', '', 0, 'optional', 'select_ajax', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 'docs/docs', 1, 1, NOW()
WHERE NOT EXISTS (
	SELECT 1 FROM `gw_adm_page_fields` WHERE `parent` = 'shop_products' AND `fieldname` = 'contract_id'
);

UPDATE `gw_adm_page_fields`
SET
	`title_lt` = 'Sutarties dokumentas',
	`inp_type` = 'select_ajax',
	`modpath` = 'docs/docs',
	`active` = 1
WHERE `parent` = 'shop_products' AND `fieldname` = 'contract_id';
