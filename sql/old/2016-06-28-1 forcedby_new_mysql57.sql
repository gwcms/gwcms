SET sql_mode = "";
ALTER TABLE  `gw_users_groups` CHANGE  `insert_time`  `insert_time` DATETIME NULL ;
ALTER TABLE  `gw_users_groups` CHANGE  `update_time`  `update_time` DATETIME NULL ;
UPDATE `gw_users_groups` SET `insert_time` = NULL WHERE `insert_time`=0;
UPDATE `gw_users_groups` SET `update_time` = NULL WHERE `update_time`=0; 



SET sql_mode = "";
ALTER TABLE  `gw_users` CHANGE  `insert_time`  `insert_time` DATETIME NULL ; UPDATE `gw_users` SET `insert_time` = NULL WHERE `insert_time`=0;
ALTER TABLE  `gw_users` CHANGE  `update_time`  `update_time` DATETIME NULL ; UPDATE `gw_users` SET `update_time` = NULL WHERE `update_time`=0;
ALTER TABLE  `gw_users` CHANGE  `last_request_time`  `last_request_time` DATETIME NULL ; UPDATE `gw_users` SET `last_request_time` = NULL WHERE `last_request_time`=0;
ALTER TABLE  `gw_users` CHANGE  `login_time`  `login_time` DATETIME NULL ; UPDATE `gw_users` SET `login_time` = NULL WHERE `login_time`=0;

ALTER TABLE  `gw_users` CHANGE  `rights`  `rights` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE  `gw_users` CHANGE  `company_name`  `company_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE  `gw_users` CHANGE  `last_ip`  `last_ip` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE  `gw_users` CHANGE  `info`  `info` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE  `gw_users` CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  'notes about user';
ALTER TABLE  `gw_users` CHANGE  `admin_access`  `admin_access` TINYINT( 4 ) NULL ;
ALTER TABLE  `gw_users` CHANGE  `site_verif_key`  `site_verif_key` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE  `gw_users` CHANGE  `site_passchange`  `site_passchange` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;






 