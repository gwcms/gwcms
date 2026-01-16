ALTER TABLE `gw_payuniversal_log` ADD `unique_key` VARCHAR(150) NOT NULL AFTER `remote_id`;
UPDATE `gw_payuniversal_log` SET `unique_key`=id;
ALTER TABLE `gw_payuniversal_log` ADD UNIQUE(`unique_key`);
