RENAME TABLE  `gw_users` TO  `gw_users_old` ;

ALTER TABLE  `gw_adm_users` ADD  `surname` VARCHAR( 50 ) NOT NULL AFTER  `name` ;
ALTER TABLE  `gw_adm_users` ADD  `company_name` VARCHAR( 50 ) NOT NULL AFTER  `surname` ;

ALTER TABLE  `gw_adm_users` ADD  `address` INT( 100 ) NOT NULL AFTER  `company_name` ,

ADD  `city` INT( 50 ) NOT NULL AFTER  `address` ,
ADD  `phone` INT( 50 ) NOT NULL AFTER  `city` ;

ALTER TABLE  `gw_adm_users` ADD  `admin_access` TINYINT NOT NULL AFTER  `info` ;


RENAME TABLE  `gw_adm_users` TO  `gw_users` ;


ALTER TABLE  `gw_users` CHANGE  `address`  `address` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE  `gw_users` ADD  `description` TEXT NOT NULL COMMENT  'notes about user' AFTER  `info` ;
ALTER TABLE  `gw_users` CHANGE  `city`  `city` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE  `gw_users` CHANGE  `phone`  `phone` BIGINT( 50 ) NOT NULL ;

RENAME TABLE  `gw_adm_users_groups` TO  `gw_users_groups` ;
RENAME TABLE  `gw_adm_permissions` TO  `gw_permissions` ;
RENAME TABLE  `gw_adm_messages` TO  `gw_messages`;






#tik smsams

ALTER TABLE  `gw_users` ADD  `site_verif_key` VARCHAR( 255 ) NOT NULL AFTER  `info` ,
ADD  `site_passchange` VARCHAR( 255 ) NOT NULL AFTER  `site_verif_key` ,
ADD  `sms_funds` FLOAT NOT NULL AFTER  `site_passchange` ,
ADD  `sms_allow_credit` TINYINT NOT NULL AFTER  `sms_funds` ,
ADD  `sms_gates` VARCHAR( 255 ) NOT NULL AFTER  `sms_allow_credit` ;

ALTER TABLE  `gw_users` ADD  `sms_pricing_plan` VARCHAR( 30 ) NOT NULL AFTER  `sms_gates` ;



