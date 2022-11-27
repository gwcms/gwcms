ALTER TABLE `gw_todo` ADD `body_editor` TINYINT NOT NULL AFTER `deadline`;
ALTER TABLE `gw_todo` ADD `body_editor_height` TINYINT NOT NULL AFTER `body_editor`;