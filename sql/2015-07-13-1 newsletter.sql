ALTER TABLE `gw_nl_hits` ADD `debug` VARCHAR(400) NOT NULL AFTER `link`;
ALTER TABLE `gw_nl_hits` ADD `browser` VARCHAR(100) NOT NULL AFTER `debug`;
ALTER TABLE `gw_nl_hits` ADD `referer` VARCHAR(300) NOT NULL AFTER `browser`;



CREATE TABLE IF NOT EXISTS `gw_nl_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_id` int(11) NOT NULL,
  `link` varchar(400) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE  `gw_nl_messages` ADD  `body_prepared` TEXT NOT NULL AFTER  `body` ;
