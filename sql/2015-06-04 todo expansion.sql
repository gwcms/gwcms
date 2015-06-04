ALTER TABLE  `gw_todo` ADD  `time_have` INT NOT NULL AFTER  `deadline` ;
UPDATE gw_todo SET time_have=-1;

ALTER TABLE  `gw_todo` ADD  `project_id` INT NOT NULL AFTER  `parent_id` ;


CREATE TABLE IF NOT EXISTS `gw_todo_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;
