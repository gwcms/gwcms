ALTER TABLE `tom_schools` ADD `city` VARCHAR(100) NOT NULL AFTER `id`;



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `gw_tometa`
--

-- --------------------------------------------------------

--
-- Table structure for table `tom_schools`
--

DROP TABLE IF EXISTS `tom_schools`;
CREATE TABLE `tom_schools` (
  `id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tom_schools`
--

INSERT INTO `tom_schools` (`id`, `city`, `title`, `address`, `active`, `insert_time`, `update_time`) VALUES
(6, 'Klaipėdos', 'Aukuro gimnazija(Agutė)', '', 1, '2010-01-27 20:42:57', '2016-12-22 15:22:27'),
(134, 'Klaipėdos', 'Baltijos', '', 1, '2010-05-16 13:29:54', '2016-12-22 15:22:35'),
(3, 'Klaipėdos', 'Versmės progimnazija(Jandra)', '', 1, '2010-01-21 16:08:38', '2016-12-22 15:23:21'),
(132, 'Klaipėdos', 'Gedminų(Agutė)', '', 1, '2010-05-16 13:27:19', '2016-12-22 15:22:42'),
(4, 'Kauno', 'Dainavos vidurinė mokykla(Jolanta)', '', 1, '2010-01-21 20:05:22', '2016-12-22 15:16:21'),
(5, 'Kauno', 'Saulės gimnazija(Edita)', '', 1, '2010-01-27 20:41:33', '2016-12-22 15:19:33'),
(230, 'Vilkyčių', 'pagr.m-kla(Jandra)', '', 1, '2015-05-04 16:34:23', '2016-12-22 15:26:07'),
(7, 'Klaipėdos', 'Vyturio vid.mokykla(Agutė)', '', 1, '2010-01-27 20:44:56', '2016-12-22 15:23:36'),
(130, 'Klaipėdos', 'Varpo(Agutė)', '', 1, '2010-05-16 13:20:39', '2016-12-22 15:23:15'),
(8, 'Klaipėdos', 'Zudermano vid.mokykla(Agutė)', '', 1, '2010-01-27 20:45:47', '2016-12-22 15:23:46'),
(131, 'Klaipėdos', 'Smeltės(Agutė)', '', 1, '2010-05-16 13:24:32', '2016-12-22 15:23:10'),
(9, 'Kauno', 'Ryto pradinė mokykla(Edita)', '', 1, '2010-01-27 20:47:00', '2016-12-22 15:19:21'),
(160, 'Kėdainių raj.', 'Akademijos vid.m-kla(Edita)', '', 1, '2010-06-16 19:13:36', '2016-12-22 15:22:06'),
(11, 'Kauno', 'T. Ivanausko vid.mokykla(Edita)', '', 1, '2010-01-27 20:49:34', '2016-12-22 15:16:00'),
(14, 'Kauno', 'Santaros gimnazija(Edita)', '', 1, '2010-01-27 20:52:31', '2016-12-22 15:19:27'),
(15, 'Kauno', 'Milikonių vid.mokykla(Jolanta)', '', 1, '2010-01-27 20:53:33', '2016-12-22 15:17:36'),
(16, 'Kauno', 'Aušros gimnazija(Ina)', '', 1, '2010-01-27 20:54:15', '2016-12-22 15:16:04'),
(17, 'Kauno', 'Dariaus ir Girėno gimnazija(Jolanta)', '', 1, '2010-01-27 20:55:29', '2016-12-22 15:16:26'),
(256, 'Jonavos', 'Senamiesčio g-ja(Jolanta)', '', 1, '2016-06-23 15:13:59', '2016-12-22 15:15:24'),
(18, 'Kauno', 'Kuprevičiaus vid.mokykla(Edita)', '', 1, '2010-01-27 20:57:05', '2016-12-22 15:17:12'),
(19, 'Kauno', 'Gedimino sporto ir sveikatingumo m-kla(Ina)', '', 1, '2010-01-27 20:57:46', '2016-12-22 15:16:35'),
(20, 'Kauno raj.', 'Jonučių vid.mokykla(Edita)', '', 1, '2010-01-27 20:59:41', '2016-12-22 15:18:47'),
(21, 'Kauno', 'Lukšos gimnazija(Jolanta)', '', 1, '2010-01-27 21:00:13', '2016-12-22 15:17:21'),
(22, 'Raseinių raj.', 'Viduklės gimnazija(Edita)', '', 1, '2010-01-27 21:01:54', '2016-12-22 15:24:51'),
(23, 'Kauno raj.', 'Tirkiliškių pradinė mokykla(Ina)', '', 1, '2010-01-27 21:02:49', '2016-12-22 15:19:07'),
(24, 'Kauno', 'Rasos gimnazija(Jolanta)', '', 1, '2010-01-27 21:03:40', '2016-12-22 15:19:17'),
(25, 'Kauno', 'Paparčio pradinė mokykla(Ina)', '', 1, '2010-01-27 21:04:38', '2016-12-22 15:18:04'),
(26, 'Kauno', 'Purienų vid.mokykla(Ina)', '', 1, '2010-01-27 21:05:22', '2016-12-22 15:18:18'),
(27, 'Kauno', 'Griniaus vid.mokykla(Ina)', '', 1, '2010-01-27 21:05:55', '2016-12-22 15:16:41'),
(28, 'Kauno', 'Jablonskio gimnazija(Ina)', '', 1, '2010-01-27 21:06:49', '2016-12-22 15:16:53'),
(30, 'Klaipėdos', 'Santarvės gimnazija(Jandra)', '', 1, '2010-01-27 21:10:11', '2016-12-22 15:23:00'),
(31, 'Kauno', 'Urbšio vid.mokykla(Jolanta)', '', 1, '2010-01-27 21:14:41', '2016-12-22 15:20:07'),
(150, 'Vilkijos', 'gimnazija(Jolanta)', '', 1, '2010-05-20 15:27:38', '2016-12-22 15:26:03'),
(32, 'Kauno', 'Veršvų vid.mokykla(Ina)', '', 1, '2010-01-27 21:15:20', '2016-12-22 15:21:26'),
(144, 'Klaipėdos', 'L.Stulpino progimnazija(Jandra)', '', 1, '2010-05-16 14:03:33', '2016-12-22 15:22:45'),
(231, 'Dovilų', 'pagr.m-kla(Jandra)', '', 1, '2015-05-04 16:43:28', '2016-12-22 15:20:31'),
(33, 'Kauno', 'Smetonos g-ja(Jolanta)', '', 1, '2010-01-27 21:16:16', '2016-12-22 15:19:46'),
(34, 'Kėdainių', 'Ryto vid.mokykla(Edita)', '', 1, '2010-01-27 21:17:03', '2016-12-22 15:22:10'),
(135, 'Klaipėdos', 'Aitvaro(Jandra)', '', 1, '2010-05-16 13:30:59', '2016-12-22 15:22:22'),
(136, 'Klaipėdos', 'Ąžuolyno(Jandra)', '', 1, '2010-05-16 13:33:56', '2016-12-22 15:22:31'),
(35, 'Kėdainių', 'Paukštelio gimnazija(Edita)', '', 1, '2010-01-27 21:18:13', '2016-12-22 15:21:59'),
(137, 'Klaipėdos', 'Vydūno gimnazija(Jandra)', '', 1, '2010-05-16 13:35:35', '2016-12-22 15:23:29'),
(36, 'Kauno raj.', 'Babtų vid.mokykla(Jolanta)', '', 1, '2010-01-27 21:18:41', '2016-12-22 15:18:35'),
(37, 'Kauno', 'Jėzuitų gimnazija(Ina)', '', 1, '2010-01-27 21:20:04', '2016-12-22 15:16:59'),
(38, 'Kretingos', 'Pranciškonų gimnazija(Jandra)', '', 1, '2010-01-27 21:21:51', '2016-12-22 15:23:53'),
(133, 'Klaipėdos', 'Mašioto(Agutė)', '', 1, '2010-05-16 13:28:49', '2016-12-22 15:22:55'),
(39, 'Klaipėdos', 'Vytauto Didžiojo gimnazija(Agutė)', '', 1, '2010-01-27 21:23:19', '2016-12-22 15:23:33'),
(40, 'Šilutės', 'pirmoji gimnazija(Jandra)', '', 1, '2010-01-27 21:25:26', '2016-12-22 15:25:33'),
(128, 'Klaipėdos', 'Vėtrungės gimnazija(Agutė)', '', 1, '2010-05-11 08:06:32', '2016-12-22 15:23:26'),
(41, 'Kauno', 'Neveronių vid.mokykla(Ina)', '', 1, '2010-01-27 21:27:24', '2016-12-22 15:17:59'),
(42, 'Kauno', 'Basanavičiaus gimnazija(Edita)', '', 1, '2010-02-09 10:59:32', '2016-12-22 15:16:15'),
(43, 'Kauno', 'Dobkevičiaus vid.mokykla(Ina)', '', 1, '2010-02-10 10:10:03', '2016-12-22 15:16:31'),
(143, 'Priekulės', 'I.Simonaitytės gimnazija(Jandra)', '', 1, '2010-05-16 14:01:10', '2016-12-22 15:24:33'),
(51, 'Kauno raj.', 'Vaišvydavos vid.mokykla(Jolanta)', '', 1, '2010-04-12 15:53:33', '2016-12-22 15:19:12'),
(48, 'Kauno', 'Žemaičio progimnazija(Edita)', '', 1, '2010-04-12 15:47:19', '2016-12-22 15:21:41'),
(170, 'Rumšiškių', 'vid.m-kla(Ina)', '', 1, '2010-09-02 16:13:42', '2016-12-22 15:25:03'),
(254, 'Elektrėnų', 'pradinė m-kla(Jolanta)', '', 1, '2016-06-13 08:48:13', '2016-12-22 15:20:27'),
(50, 'Kauno raj.', 'Rokų vid.mokykla(Jolanta)', '', 1, '2010-04-12 15:50:36', '2016-12-22 15:18:23'),
(167, 'Domeikavos', 'vid.m-kla(Ina)', '', 1, '2010-08-02 09:09:36', '2016-12-22 15:27:23'),
(52, 'Kauno', 'Atžalyno vid.mokykla(Jolanta)', '', 1, '2010-04-12 15:54:55', '2016-12-22 15:15:55'),
(54, 'Kauno', 'Petrašiūnų humanitarinė vid.mokykla(Jolanta)', '', 1, '2010-04-12 15:58:56', '2016-12-22 15:18:10'),
(56, 'Kauno', 'Lozoraičio vid.mokykla(Ina)', '', 1, '2010-04-12 16:03:18', '2016-12-22 15:17:16'),
(57, 'Kauno raj.', 'Raudondvario vid.mokykla(Jolanta)', '', 1, '2010-04-12 16:04:21', '2016-12-22 15:18:58'),
(58, 'Kauno', 'Varpo gimnazija(Ina)', '', 1, '2010-04-12 16:06:16', '2016-12-22 15:21:20'),
(59, 'Kauno', 'V.Kudirkos vid.mokykla(Ina)', '', 1, '2010-04-12 16:07:36', '2016-12-22 15:20:03'),
(60, 'Kauno', 'Žiburio vid.mokykla(Ina)', '', 1, '2010-04-12 16:09:17', '2016-12-22 15:21:51'),
(61, 'Kauno', 'Šančių vid.mokykla(Jolanta)', '', 1, '2010-04-12 16:11:30', '2016-12-22 15:19:24'),
(62, 'Kauno raj.', 'Ariogalos vid.mokykla(Jolanta)', '', 1, '2010-04-12 16:13:49', '2016-12-22 15:18:32'),
(166, 'Kauno', 'Varpelio pradinė m-kla(Ina)', '', 1, '2010-07-28 08:35:03', '2016-12-22 15:20:13'),
(63, 'Kauno', 'Maironio gimnazija(Ina)', '', 1, '2010-04-12 16:14:51', '2016-12-22 15:17:26'),
(64, 'Kauno', 'Šilo pradinė mokykla(Edita)', '', 1, '2010-04-12 16:15:52', '2016-12-22 15:19:42'),
(66, 'Kauno', 'Sargėnų vid.mokykla(Ina)', '', 1, '2010-04-12 16:20:27', '2016-12-22 15:19:30'),
(232, 'Ketvergių', 'pagr.m-kla(Jandra)', '', 1, '2015-05-04 16:51:23', '2016-12-22 15:22:17'),
(67, 'Vievio', 'pradinė mokykla(Ina)', '', 1, '2010-04-12 16:21:42', '2016-12-22 15:26:00'),
(233, 'Saugų', 'J.Mikšo pagr.m-kla(Jandra)', '', 1, '2015-05-04 16:55:06', '2016-12-22 15:25:18'),
(68, 'Kauno', 'Žaliakalnio (Bacevičiaus) pradinė mokykla(Ina)', '', 1, '2010-04-12 16:23:32', '2016-12-22 15:21:33'),
(140, 'Klaipėdos', 'Žemynos gimnazija(Jandra)', '', 1, '2010-05-16 13:54:57', '2016-12-22 15:23:43'),
(141, 'Klaipėdos', 'Žaliakalnio gimnazija(Jandra)', '', 1, '2010-05-16 13:56:08', '2016-12-22 15:23:39'),
(69, 'Kauno raj.', 'Ringaudų prad.mokykla(Ina)', '', 1, '2010-04-12 16:24:40', '2016-12-22 15:19:02'),
(71, '', 'Naujamiesčio vidurinė mokykla(Jolanta)', '', 1, '2010-04-12 16:29:01', '2016-04-26 15:28:42'),
(228, 'Kauno', 'J.Pauliaus katalikiška g-ja(Edita)', '', 1, '2014-03-12 09:58:08', '2016-12-22 15:16:48'),
(169, 'Kauno', 'KTU progimnazija (Edita)', '', 1, '2010-08-31 20:58:28', '2016-12-22 15:17:08'),
(249, 'Kretingos', 'S.Daukanto progimnazija(Jandra)', '', 1, '2016-05-12 08:46:34', '2016-12-22 15:23:57'),
(250, 'Seredžiaus', 'S.Šimkaus mokykla(Jolanta)', '', 1, '2016-05-17 20:02:48', '2016-12-22 15:25:22'),
(251, 'Išlaužo', 'vid.m-kla(Jolanta)', '', 1, '2016-05-26 14:54:44', '2016-12-22 15:27:20'),
(257, 'Kretingos', 'Daujoto progimnazija(Agutė)', '', 1, '2016-08-25 08:46:33', '2016-12-22 15:23:50'),
(163, 'Klaipėdos', 'Tauralaukio pagr.m-kla(Jandra)', '', 1, '2010-06-23 12:52:06', '2016-12-22 15:23:13'),
(159, 'Alytaus raj.', 'Butrimonių vid.m-kla(Ina)', '', 1, '2010-06-09 13:18:08', '2016-12-22 15:14:53'),
(154, 'Šilutės', 'Žibų pradinė mokykla(Jandra)', '', 1, '2010-06-05 21:12:49', '2016-12-22 15:25:36'),
(148, '', 'Buračo  vid.mokykla(Edita)', '', 1, '2010-05-18 20:51:50', '2016-04-27 12:12:05'),
(146, 'Subačiaus', 'vid.mokykla(Jolanta)', '', 1, '2010-05-17 08:14:49', '2016-12-22 15:25:42'),
(241, 'Jurbarko', 'V.Didžiojo pagr.m-kla(Jolanta)', '', 1, '2015-05-29 12:10:32', '2016-12-22 15:15:34'),
(125, 'Kauno', 'Kovo 11-osios vid.mokykla(Edita)', '', 1, '2010-04-23 15:21:12', '2016-12-22 15:17:03'),
(245, 'Palangos', 'Senoji gimnazija(Jandra)', '', 1, '2015-07-16 15:21:11', '2016-12-22 15:24:19'),
(165, 'Vydmantų', 'vid.m-kla(Jandra)', '', 1, '2010-07-18 18:53:47', '2016-12-22 15:26:11'),
(161, 'Kauno', 'Vyturio vid.m-kla(Ina)', '', 1, '2010-06-21 09:33:50', '2016-12-22 15:21:30'),
(153, 'Kauno raj.', 'Kulautuvos vid.mokykla(Jolanta)', '', 1, '2010-05-31 16:07:06', '2016-12-22 15:18:53'),
(152, 'Kėdainių', 'Atžalyno(Edita)', '', 1, '2010-05-26 08:21:15', '2016-12-22 15:21:55'),
(138, 'Klaipėdos', 'Gabijos(Jandra)', '', 1, '2010-05-16 13:52:42', '2016-12-22 15:22:38'),
(171, 'Kauno', 'Pilėnų vid.m-kla(Ina)', '', 1, '2010-10-18 15:57:51', '2016-12-22 15:18:14'),
(172, 'Kauno', 'mokykla-darželis ŠVIESA(Edita)', '', 1, '2011-05-04 12:19:10', '2016-12-22 15:17:48'),
(174, 'Radviliškio raj.', 'Šiaulėnų gimnazija(Edita)', '', 1, '2011-05-12 18:13:55', '2016-12-22 15:24:40'),
(175, 'Klaipėdos', 'Saulėtekio pagr.m-kla(Jandra)', '', 1, '2011-05-12 18:20:04', '2016-12-22 15:23:03'),
(176, 'Kauno raj.', 'Ežerėlio vid.m-kla(Ina)', '', 1, '2011-05-16 16:42:09', '2016-12-22 15:18:40'),
(177, 'Kauno', 'mokykla-darželis Rūtelė(Jolanta)', '', 1, '2011-05-23 08:57:18', '2016-12-22 15:17:43'),
(179, 'Klaipėdos', 'Sendvario pagrindinė m-kla(Agutė)', '', 1, '2011-05-24 12:19:40', '2016-12-22 15:23:06'),
(180, 'Jonavos', 'Neries vid.m-kla(Jolanta)', '', 1, '2011-05-25 16:51:31', '2016-12-22 15:15:04'),
(226, 'Kauno', 'B.Brazdžionio pagr.m-kla(Jolanta)', '', 1, '2014-03-12 09:07:36', '2016-12-22 15:16:09'),
(181, 'Šilutės', 'Pamario pagrindinė  mokykla(Jandra)', '', 1, '2011-06-11 17:57:10', '2016-12-22 15:25:29'),
(182, 'Kartenos', 'pagr.m-kla(Jandra)', '', 1, '2011-06-17 13:01:32', '2016-12-22 15:15:42'),
(192, 'Kauno raj.', 'Garliavos Mitkaus vid.mokykla(Jolanta)', '', 1, '2011-08-01 20:44:57', '2016-12-22 15:18:44'),
(255, 'Veliuonos', 'Gimnazija(Ina)', '', 1, '2016-06-14 14:35:46', '2016-12-22 15:25:56'),
(185, 'Nidos', 'vid.m-kla(Jandra)', '', 1, '2011-07-02 15:10:53', '2016-12-22 15:24:13'),
(191, 'Kauno raj.', 'Ugnės Karvelis gimnazija(Ina)', '', 1, '2011-07-25 20:33:41', '2016-12-22 15:18:27'),
(239, 'Kurmaičių', 'pradinė m-kla(Jandra)', '', 1, '2015-05-21 12:13:12', '2016-12-22 15:23:59'),
(240, 'Rūdaičių', 'pradinė m-kla(Agutė)', '', 1, '2015-05-28 14:02:15', '2016-12-22 15:25:00'),
(196, 'Kauno', 'Žaliakalnio progimnazija(Jolanta)', '', 1, '2011-09-09 14:21:18', '2016-12-22 15:21:37'),
(243, 'Romainių', 'pradinė m-kla(Ina)', '', 1, '2015-06-18 15:03:52', '2016-12-22 15:24:56'),
(199, 'Žiežmarių', 'gimnazija(Jolanta)', '', 1, '2011-11-09 12:29:01', '2016-12-22 15:26:48'),
(200, 'Marijampolės', 'Saulės pradinė m-kla(Edita)', '', 1, '2011-11-15 19:42:59', '2016-12-22 15:24:02'),
(201, 'Prienų', 'Revuonos mokykla(Edita)', '', 1, '2012-03-21 17:18:11', '2016-12-22 15:24:36'),
(202, 'Kazlų Rūdos', 'K.Griniaus gimnazija(Edita)', '', 1, '2012-04-18 17:36:28', '2016-12-22 15:21:47'),
(203, 'Jonavos', 'pradinė m-kla(Jolanta)', '', 0, '2012-05-17 17:37:52', '2016-12-22 15:15:19'),
(205, 'Šakių raj.', 'Plokščių vid.m-kla(Jolanta)', '', 1, '2012-06-02 11:41:08', '2016-12-22 15:25:07'),
(206, 'Kėdainių', 'Šviesioji gimnazija(Edita)', '', 1, '2012-06-18 16:52:47', '2016-12-22 15:22:13'),
(235, 'Žiežmarių', 'pradinė m-kla(Jolanta)', '', 1, '2015-05-18 08:53:42', '2016-12-22 15:26:52'),
(236, 'Klaipėdos', 'M.Mažvydo pagr.m-kla(Agutė)', '', 1, '2015-05-19 09:33:48', '2016-12-22 15:22:52'),
(237, 'Žemaičių Kalvarijos', 'pagr.m-kla(Agutė)', '', 1, '2015-05-19 09:36:05', '2016-12-22 15:26:44'),
(214, 'Ramučių', '(Buračo)pradinė(Edita)', '', 1, '2012-09-21 15:23:11', '2016-12-22 15:24:47'),
(215, 'Jokūbavo', 'Stulginskio vid.m-kla(Jandra)', '', 1, '2013-05-10 13:07:26', '2016-12-22 15:27:07'),
(244, 'Kauno', 'Šv.Mato (Edita)', '', 1, '2015-06-23 12:45:06', '2016-12-22 15:19:53'),
(217, 'Švėkšnos', 'Saulės gimnazija(Jandra)', '', 1, '2013-05-21 16:48:19', '2016-12-22 15:25:46'),
(218, 'Kauno', 'Senamiesčio progimnazija(Jolanta)', '', 1, '2013-05-22 12:59:42', '2016-12-22 15:19:39'),
(219, '', 'Ž. Naumiesčio gimnazija(Jandra)', '', 1, '2013-05-29 18:50:18', '2016-04-26 16:27:09'),
(220, 'Plungės', 'Senamiesčio vid.m-kla(Agutė)', '', 1, '2013-05-30 16:09:13', '2016-12-22 15:24:30'),
(221, 'Kauno', 'Kauno Stulginskio vid.m-kla(Jolanta)', '', 1, '2013-06-14 13:30:15', '2016-12-22 15:19:56'),
(222, 'Kauno', 'Mažvydo vid.m-kla(Edita)', '', 1, '2013-06-28 16:15:38', '2016-12-22 15:17:31'),
(238, 'Šilutės', 'M.Jankaus pagr.m-kla(Jandra)', '', 1, '2015-05-21 12:06:29', '2016-12-22 15:25:26'),
(253, 'Šventosios', 'pagrindinė m-kla(Agutė)', '', 1, '2016-06-01 14:10:57', '2016-12-22 15:25:52'),
(248, 'Klaipėdos', 'Litorinos m-kla(Jandra)', '', 1, '2016-05-12 08:39:04', '2016-12-22 15:22:49'),
(242, 'Plungės', 'Babrungo pagrindinė m-kla(Agutė)', '', 1, '2015-06-11 15:51:54', '2016-12-22 15:24:25'),
(234, 'Kauno', 'Montesori mokykla darželis ŽIBURĖLIS(Edita)', '', 1, '2015-05-15 08:53:43', '2016-12-22 15:17:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tom_schools`
--
ALTER TABLE `tom_schools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`(1));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tom_schools`
--
ALTER TABLE `tom_schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;