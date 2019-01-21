
CREATE TABLE IF NOT EXISTS `mt_seller` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,

  `active` TINYINT(1) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `mt_seller`  ADD PRIMARY KEY (`id`);
ALTER TABLE `mt_seller`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;			
			