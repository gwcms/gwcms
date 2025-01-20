ALTER TABLE `gw_classificators` ADD `user_id` INT NOT NULL AFTER `active`;



CREATE TABLE `gw_classificatorsext` (
  `id` int NOT NULL,
  `key` varchar(50) NOT NULL,
  `type` int NOT NULL,
  `title_lt` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `title_ru` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `title_en` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `text_lt` text NOT NULL,
  `text_en` text NOT NULL,
  `aka` varchar(100) NOT NULL COMMENT 'also known as',
  `count` int NOT NULL,
  `priority` int NOT NULL,
  `active` tinyint NOT NULL,
  `user_id` int NOT NULL,
  `insert_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_classificatorsext`
--
ALTER TABLE `gw_classificatorsext`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_classificatorsext`
--
ALTER TABLE `gw_classificatorsext`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
