ALTER TABLE `gw_change_track` ADD `last` TINYINT NOT NULL AFTER `update_time`, ADD `undone` TINYINT NOT NULL AFTER `last`;
