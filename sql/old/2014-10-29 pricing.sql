CREATE TABLE IF NOT EXISTS `gw_pricing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `country` char(3) NOT NULL,
  `price` float NOT NULL,
  `price_percent` float NOT NULL,
  `user_id` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gw_pricing`
--

INSERT INTO `gw_pricing` (`id`, `name`, `country`, `price`, `price_percent`, `user_id`, `insert_time`, `update_time`) VALUES
(1, 'gate1', 'LT', 0.04, 0, 0, '2014-10-29 04:05:21', '2014-10-29 04:26:04'),
(2, 'gate1', 'LT', 0.03, 0, 39, '0000-00-00 00:00:00', '2014-10-29 04:25:21');


ALTER TABLE `gw_mass_messages` ADD `credit` FLOAT NOT NULL AFTER `parts_count` ;

ALTER TABLE `gw_users` CHANGE `credit` `funds` FLOAT NOT NULL;
ALTER TABLE `gw_users` ADD `allow_credit` TINYINT NOT NULL AFTER `funds` ;