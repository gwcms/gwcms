
CREATE TABLE `gw_attachments` (
  `id` int(11) NOT NULL,
  `title_lt` varchar(200) NOT NULL,
  `title_ru` varchar(200) NOT NULL COMMENT 'copy from title_lt',
  `title_en` varchar(200) NOT NULL COMMENT 'copy from title_lt',
  `owner_type` varchar(50) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `owner_temp_id` varchar(15) NOT NULL,
  `field` varchar(30) NOT NULL,
  `content_cat` set('image','file') NOT NULL,
  `content_type` varchar(15) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `priority` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gw_attachments`
--

--
-- Indexes for table `gw_attachments`
--
ALTER TABLE `gw_attachments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_attachments`
--
ALTER TABLE `gw_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

ALTER TABLE `gw_attachments` CHANGE `content_type` `content_type` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;