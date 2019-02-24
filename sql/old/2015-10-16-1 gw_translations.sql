CREATE TABLE IF NOT EXISTS `gw_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value_lt` text NOT NULL,
  `value_ru` text NOT NULL COMMENT 'copy from value_lt',
  `value_en` text NOT NULL COMMENT 'copy from value_lt',
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module` (`module`,`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;