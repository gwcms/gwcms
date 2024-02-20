ALTER TABLE `gw_support_messages` ADD `date` DATE NOT NULL AFTER `message`, ADD `time` TIME NOT NULL AFTER `date`;

ALTER TABLE `gw_support_messages` CHANGE `subject` `subject` VARCHAR(70) NOT NULL;