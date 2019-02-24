
DROP TABLE IF EXISTS `tom_product_modif_opt_school`;
CREATE TABLE IF NOT EXISTS `tom_product_modif_opt_school` (
  `opt_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tom_product_modif_opt_school`
--
ALTER TABLE `tom_product_modif_opt_school`
  ADD PRIMARY KEY (`opt_id`,`school_id`);