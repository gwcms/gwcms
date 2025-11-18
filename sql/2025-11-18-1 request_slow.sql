DROP TABLE IF EXISTS `request_slow`;
CREATE TABLE `request_slow` (
  `id` int NOT NULL,
  `url` varchar(255) NOT NULL,
  `endtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` INT NOT NULL,
  `ip` varchar(15) NOT NULL,
  `speed` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
