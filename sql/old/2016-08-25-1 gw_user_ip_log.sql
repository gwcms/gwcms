
CREATE TABLE `gw_user_ip_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `user_agent` varchar(200) NOT NULL,
  `insert_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--


--
-- Indexes for table `gw_user_ip_log`
--
ALTER TABLE `gw_user_ip_log`
  ADD PRIMARY KEY (`id`);

--

ALTER TABLE `gw_user_ip_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

