ALTER TABLE `gw_order_group`
  ADD `payd_amount` DECIMAL(12,2) NOT NULL DEFAULT '0.00' AFTER `amount_total`,
  ADD `balance_amount` DECIMAL(12,2) NOT NULL DEFAULT '0.00' AFTER `payd_amount`,
  ADD `ledger_status` VARCHAR(20) NOT NULL DEFAULT '' AFTER `balance_amount`;

CREATE TABLE `gw_order_payment_confirmation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `direction` enum('payment','refund') NOT NULL DEFAULT 'payment',
  `status` enum('confirmed','pending','failed','canceled') NOT NULL DEFAULT 'confirmed',
  `source` varchar(30) NOT NULL DEFAULT '',
  `source_log_table` varchar(60) NOT NULL DEFAULT '',
  `source_log_id` int NOT NULL DEFAULT '0',
  `unique_key` varchar(190) NOT NULL DEFAULT '',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` char(3) NOT NULL DEFAULT 'EUR',
  `received_at` datetime DEFAULT NULL,
  `bank_account` varchar(100) NOT NULL DEFAULT '',
  `reference` varchar(190) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `created_by` int NOT NULL DEFAULT '0',
  `change_transaction_id` int NOT NULL DEFAULT '0',
  `test` tinyint NOT NULL DEFAULT '0',
  `insert_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`unique_key`),
  KEY `order_id` (`order_id`),
  KEY `source_log` (`source_log_table`,`source_log_id`),
  KEY `received_at` (`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `gw_classificator_types` (`key`, `title`, `aka`, `count`, `insert_time`, `update_time`)
SELECT 'own_bank_accounts', 'Mano bankinės sąskaitos', '', 0, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `gw_classificator_types` WHERE `key`='own_bank_accounts'
);

INSERT INTO `gw_classificators` (`key`, `type`, `title_lt`, `title_ru`, `title_en`, `aka`, `count`, `priority`, `active`, `user_id`, `insert_time`, `update_time`)
SELECT 'swedbank', t.id, 'Mano SWEDBANK', '', 'My SWEDBANK', '', 0, 10, 1, 0, NOW(), NOW()
FROM `gw_classificator_types` t
WHERE t.`key`='own_bank_accounts'
  AND NOT EXISTS (SELECT 1 FROM `gw_classificators` c WHERE c.`type`=t.id AND c.`key`='swedbank');

INSERT INTO `gw_classificators` (`key`, `type`, `title_lt`, `title_ru`, `title_en`, `aka`, `count`, `priority`, `active`, `user_id`, `insert_time`, `update_time`)
SELECT 'artea', t.id, 'Mano ARTEA', '', 'My ARTEA', '', 0, 20, 1, 0, NOW(), NOW()
FROM `gw_classificator_types` t
WHERE t.`key`='own_bank_accounts'
  AND NOT EXISTS (SELECT 1 FROM `gw_classificators` c WHERE c.`type`=t.id AND c.`key`='artea');

INSERT INTO `gw_classificators` (`key`, `type`, `title_lt`, `title_ru`, `title_en`, `aka`, `count`, `priority`, `active`, `user_id`, `insert_time`, `update_time`)
SELECT 'seb', t.id, 'Mano SEB', '', 'My SEB', '', 0, 30, 1, 0, NOW(), NOW()
FROM `gw_classificator_types` t
WHERE t.`key`='own_bank_accounts'
  AND NOT EXISTS (SELECT 1 FROM `gw_classificators` c WHERE c.`type`=t.id AND c.`key`='seb');

INSERT INTO `gw_classificators` (`key`, `type`, `title_lt`, `title_ru`, `title_en`, `aka`, `count`, `priority`, `active`, `user_id`, `insert_time`, `update_time`)
SELECT 'xx', t.id, 'Mano XX', '', 'My XX', '', 0, 40, 1, 0, NOW(), NOW()
FROM `gw_classificator_types` t
WHERE t.`key`='own_bank_accounts'
  AND NOT EXISTS (SELECT 1 FROM `gw_classificators` c WHERE c.`type`=t.id AND c.`key`='xx');
