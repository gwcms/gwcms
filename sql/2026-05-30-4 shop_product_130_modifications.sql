UPDATE `shop_products`
SET
	`contract_id` = 24,
	`min_price` = 90,
	`max_price` = 90,
	`mod_count` = 4,
	`active` = 1,
	`update_time` = NOW()
WHERE `id` = 130;

INSERT INTO `shop_products`
	(`id`, `parent_id`, `title`, `type`, `modif_title`, `language_id`, `vendor`, `qty`, `price_scheme`, `price`, `min_price`, `max_price`, `mod_count`, `oldprice`, `weight`, `priority`, `vat_group`, `active`, `insert_time`, `update_time`, `komisas`, `author`, `ticket_tpl`, `date`, `start_time`, `end_time`, `after_buy_email_tpl`, `contracts`, `seller_id`, `contract_id`)
SELECT
	131, 130, '', p.`type`, 'birželio 15 – liepos 14 d., 9-13 m. vaikams', p.`language_id`, p.`vendor`, 100, '', 90, 0, 0, 0, 0, 0, 40, p.`vat_group`, 1, NOW(), NOW(), p.`komisas`, p.`author`, p.`ticket_tpl`, p.`date`, p.`start_time`, p.`end_time`, p.`after_buy_email_tpl`, 0, p.`seller_id`, 0
FROM `shop_products` p
WHERE p.`id` = 130
ON DUPLICATE KEY UPDATE
	`parent_id` = VALUES(`parent_id`),
	`modif_title` = VALUES(`modif_title`),
	`price` = VALUES(`price`),
	`priority` = VALUES(`priority`),
	`active` = VALUES(`active`),
	`contract_id` = VALUES(`contract_id`),
	`update_time` = NOW();

INSERT INTO `shop_products`
	(`id`, `parent_id`, `title`, `type`, `modif_title`, `language_id`, `vendor`, `qty`, `price_scheme`, `price`, `min_price`, `max_price`, `mod_count`, `oldprice`, `weight`, `priority`, `vat_group`, `active`, `insert_time`, `update_time`, `komisas`, `author`, `ticket_tpl`, `date`, `start_time`, `end_time`, `after_buy_email_tpl`, `contracts`, `seller_id`, `contract_id`)
SELECT
	132, 130, '', p.`type`, 'birželio 15 – liepos 14 d., 12-17 m. vaikams', p.`language_id`, p.`vendor`, 100, '', 90, 0, 0, 0, 0, 0, 30, p.`vat_group`, 1, NOW(), NOW(), p.`komisas`, p.`author`, p.`ticket_tpl`, p.`date`, p.`start_time`, p.`end_time`, p.`after_buy_email_tpl`, 0, p.`seller_id`, 0
FROM `shop_products` p
WHERE p.`id` = 130
ON DUPLICATE KEY UPDATE
	`parent_id` = VALUES(`parent_id`),
	`modif_title` = VALUES(`modif_title`),
	`price` = VALUES(`price`),
	`priority` = VALUES(`priority`),
	`active` = VALUES(`active`),
	`contract_id` = VALUES(`contract_id`),
	`update_time` = NOW();

INSERT INTO `shop_products`
	(`id`, `parent_id`, `title`, `type`, `modif_title`, `language_id`, `vendor`, `qty`, `price_scheme`, `price`, `min_price`, `max_price`, `mod_count`, `oldprice`, `weight`, `priority`, `vat_group`, `active`, `insert_time`, `update_time`, `komisas`, `author`, `ticket_tpl`, `date`, `start_time`, `end_time`, `after_buy_email_tpl`, `contracts`, `seller_id`, `contract_id`)
SELECT
	133, 130, '', p.`type`, 'Liepos 15 – rugpjūčio 14 d., 9-13 m. vaikams', p.`language_id`, p.`vendor`, 100, '', 90, 0, 0, 0, 0, 0, 20, p.`vat_group`, 1, NOW(), NOW(), p.`komisas`, p.`author`, p.`ticket_tpl`, p.`date`, p.`start_time`, p.`end_time`, p.`after_buy_email_tpl`, 0, p.`seller_id`, 0
FROM `shop_products` p
WHERE p.`id` = 130
ON DUPLICATE KEY UPDATE
	`parent_id` = VALUES(`parent_id`),
	`modif_title` = VALUES(`modif_title`),
	`price` = VALUES(`price`),
	`priority` = VALUES(`priority`),
	`active` = VALUES(`active`),
	`contract_id` = VALUES(`contract_id`),
	`update_time` = NOW();

INSERT INTO `shop_products`
	(`id`, `parent_id`, `title`, `type`, `modif_title`, `language_id`, `vendor`, `qty`, `price_scheme`, `price`, `min_price`, `max_price`, `mod_count`, `oldprice`, `weight`, `priority`, `vat_group`, `active`, `insert_time`, `update_time`, `komisas`, `author`, `ticket_tpl`, `date`, `start_time`, `end_time`, `after_buy_email_tpl`, `contracts`, `seller_id`, `contract_id`)
SELECT
	134, 130, '', p.`type`, 'Liepos 15 – rugpjūčio 14 d., 12-17 m. vaikams', p.`language_id`, p.`vendor`, 100, '', 90, 0, 0, 0, 0, 0, 10, p.`vat_group`, 1, NOW(), NOW(), p.`komisas`, p.`author`, p.`ticket_tpl`, p.`date`, p.`start_time`, p.`end_time`, p.`after_buy_email_tpl`, 0, p.`seller_id`, 0
FROM `shop_products` p
WHERE p.`id` = 130
ON DUPLICATE KEY UPDATE
	`parent_id` = VALUES(`parent_id`),
	`modif_title` = VALUES(`modif_title`),
	`price` = VALUES(`price`),
	`priority` = VALUES(`priority`),
	`active` = VALUES(`active`),
	`contract_id` = VALUES(`contract_id`),
	`update_time` = NOW();
