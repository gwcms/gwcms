
CREATE TABLE `gw_generic_extended` (
  `id` int NOT NULL,
  `own_table` varchar(50) NOT NULL,
  `owner_id` int NOT NULL,
  `key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_lithuanian_ci NOT NULL,
  `value` text NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_generic_extended`
--
ALTER TABLE `gw_generic_extended`
  ADD PRIMARY KEY (`id`),
  ADD KEY `field` (`key`),
  ADD KEY `owner_id` (`owner_id`) USING BTREE,
  ADD KEY `own_table` (`own_table`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_generic_extended`
--
ALTER TABLE `gw_generic_extended`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
