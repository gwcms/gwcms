SET sql_mode = "";
ALTER TABLE  `gw_todo` CHANGE  `insert_time`  `insert_time` DATETIME NULL ;UPDATE `gw_todo` SET `insert_time` = NULL WHERE `insert_time`=0;
ALTER TABLE  `gw_todo` CHANGE  `update_time`  `update_time` DATETIME NULL ;UPDATE `gw_todo` SET `update_time` = NULL WHERE `update_time`=0; 
ALTER TABLE  `gw_todo` CHANGE  `deadline`  `deadline` DATETIME NULL ;UPDATE `gw_todo` SET `deadline` = NULL WHERE `deadline`=0; 
