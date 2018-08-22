
CREATE TABLE `gw_change_track` (
  `id` int(11) NOT NULL,
  `owner_type` varchar(50) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `field` varchar(30) NOT NULL,
  `new` text NOT NULL,
  `old` text NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `gw_change_track`
  ADD PRIMARY KEY (`id`);


--
ALTER TABLE `gw_change_track`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;