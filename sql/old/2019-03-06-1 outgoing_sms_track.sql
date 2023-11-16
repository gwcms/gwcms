-- belongs to module: datasources/sms

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `factory`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_outg_sms`
--

CREATE TABLE `gw_outg_sms` (
  `id` int(11) NOT NULL,
  `number` bigint(20) NOT NULL,
  `msg` varchar(400) CHARACTER SET utf8 COLLATE utf8_lithuanian_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `err` varchar(80) NOT NULL,
  `enc` tinyint(4) NOT NULL,
  `parts` tinyint(4) NOT NULL,
  `weight` float NOT NULL,
  `remote_id` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_outg_sms`
--
ALTER TABLE `gw_outg_sms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_outg_sms`
--
ALTER TABLE `gw_outg_sms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;