CREATE TABLE `gw_config_change_track` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullkey` varchar(100) NOT NULL,
  `new` text NOT NULL,
  `old` text NOT NULL,
  `diff` text NOT NULL,
  `note` varchar(30) NOT NULL,
  `user_id` int NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fullkey` (`fullkey`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
