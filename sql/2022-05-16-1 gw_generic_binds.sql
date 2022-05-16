
CREATE TABLE `gw_generic_binds` (
  `owner` enum('x') NOT NULL,
  `id` int NOT NULL,
  `id1` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_generic_binds`
--
ALTER TABLE `gw_generic_binds`
  ADD KEY `id` (`id`,`id1`),
  ADD KEY `owner` (`owner`);
COMMIT;