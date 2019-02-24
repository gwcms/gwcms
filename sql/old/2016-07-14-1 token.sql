ALTER TABLE `gw_users` ADD `token` VARCHAR(30) NOT NULL AFTER `pass`;
ALTER TABLE `gw_users` ADD `person_id` VARCHAR(15) NOT NULL AFTER `username`;
