ALTER TABLE `gw_permissions` CHANGE `access_level` `access_level` TINYINT UNSIGNED NOT NULL;
UPDATE `gw_permissions` SET access_level=15;