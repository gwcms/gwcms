
DROP TABLE IF EXISTS `tom_material_balance`;
CREATE TABLE IF NOT EXISTS `tom_material_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `ordered_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `comment` varchar(100) NOT NULL,
  `quantity` float NOT NULL,
  `update_time` datetime NOT NULL,
  `insert_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ordered_item_id_2` (`ordered_item_id`,`material_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;
