ALTER TABLE `gw_users` ADD `last_user_agent` VARCHAR(100) NOT NULL AFTER `last_ip`;
ALTER TABLE `gw_users` CHANGE `admin_access` `is_admin` TINYINT(4) NOT NULL;