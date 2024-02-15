ALTER TABLE `gw_sitemap_data` ADD `user_id` INT NOT NULL AFTER `update_time`;


CREATE TABLE `gw_sitemap_data_versions` (
  `id` int NOT NULL,
  `ln` char(2) NOT NULL,
  `key` varchar(255) NOT NULL,
  `page_id` int NOT NULL,
  `content` text NOT NULL,
  `diff` blob NOT NULL,
  `time` timestamp NOT NULL,
  `user_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_sitemap_data_versions`
--
ALTER TABLE `gw_sitemap_data_versions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_sitemap_data_versions`
--
ALTER TABLE `gw_sitemap_data_versions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;