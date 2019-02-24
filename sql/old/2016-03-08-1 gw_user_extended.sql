CREATE TABLE IF NOT EXISTS `gw_user_extended` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `key` varchar(60) NOT NULL,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

ALTER TABLE  `gw_user_extended` ADD  `insert_time` DATETIME NOT NULL AFTER  `value` ;
ALTER TABLE  `gw_user_extended` ADD  `update_time` TIMESTAMP NOT NULL AFTER  `insert_time` ;
ALTER TABLE  `gw_user_extended` CHANGE  `value`  `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE  `gw_messages` ADD  `level` TINYINT NOT NULL COMMENT  '10-20 - siunciamas notificationas, 15-25 siunciama sms' AFTER  `sender` ;
