ALTER TABLE  `gw_todo_projects` ADD  `color` VARCHAR( 7 ) NOT NULL AFTER  `description` ;
ALTER TABLE  `gw_todo_projects` ADD  `fcolor` VARCHAR( 7 ) NOT NULL COMMENT  'font-color' AFTER  `color` ;
