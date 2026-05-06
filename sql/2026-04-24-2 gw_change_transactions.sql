CREATE TABLE `gw_change_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action_type` varchar(64) NOT NULL,
  `context_obj_type` varchar(64) NOT NULL,
  `context_obj_id` int NOT NULL,
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` varchar(32) NOT NULL,
  `note` varchar(255) NOT NULL,
  `meta` text NOT NULL,
  `insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `action_type` (`action_type`),
  KEY `context_obj` (`context_obj_type`,`context_obj_id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
