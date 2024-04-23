ALTER TABLE `gw_outg_sms` ADD `remote_status` TINYINT NOT NULL AFTER `status`;
ALTER TABLE `gw_outg_sms` ADD `send_time` DATETIME NOT NULL AFTER `remote_id`;

CREATE TABLE `gw_inco_sms` (
  `id` int NOT NULL,
  `number` bigint NOT NULL,
  `msg` varchar(400) CHARACTER SET utf8mb3 COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `remote_replyto` int NOT NULL,
  `remote_id` int NOT NULL,
  `time` timestamp NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_inco_sms`
--
ALTER TABLE `gw_inco_sms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `remote_id` (`remote_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_inco_sms`
--
ALTER TABLE `gw_inco_sms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;


