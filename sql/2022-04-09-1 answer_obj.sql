ALTER TABLE `gw_form_answers` ADD `obj_type` VARCHAR(50) NOT NULL AFTER `signature`;
ALTER TABLE `gw_form_answers` ADD `obj_id` INT NOT NULL AFTER `obj_type`;