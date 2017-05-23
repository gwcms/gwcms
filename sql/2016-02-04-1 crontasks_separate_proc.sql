ALTER TABLE `gw_crontasks` ADD `separate_process` TINYINT NOT NULL AFTER `last_output`;
UPDATE gw_crontasks SET separate_process=1;