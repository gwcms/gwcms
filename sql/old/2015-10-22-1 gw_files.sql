CREATE TABLE IF NOT EXISTS `gw_files` (
`id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
