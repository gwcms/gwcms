-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 29, 2014 at 03:40 AM
-- Server version: 5.5.38
-- PHP Version: 5.5.9-1ubuntu4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bulksms_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_adm_messages`
--

CREATE TABLE IF NOT EXISTS `gw_adm_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sender` varchar(255) NOT NULL,
  `seen` tinyint(4) NOT NULL DEFAULT '0',
  `group_cnt` smallint(5) unsigned NOT NULL,
  `insert_time` varchar(2555) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `gw_adm_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `gw_adm_permissions`
--

CREATE TABLE IF NOT EXISTS `gw_adm_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `access_level` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gid` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=595 ;

--
-- Dumping data for table `gw_adm_permissions`
--

INSERT INTO `gw_adm_permissions` (`id`, `group_id`, `path`, `access_level`) VALUES
(565, 7, 'sitemap/templates/tplvars', 1),
(564, 7, 'sitemap/templates', 1),
(563, 7, 'sitemap/pages', 1),
(562, 7, 'sitemap', 1),
(561, 7, 'gallery/items', 1),
(560, 7, 'gallery/config', 1),
(559, 7, 'gallery', 1),
(593, 10, 'gallery/config', 1),
(594, 10, 'gallery/items', 1),
(592, 10, 'gallery', 1),
(591, 10, 'adm_users/users', 1),
(586, 10, 'adm_users', 1),
(587, 10, 'adm_users/config', 1),
(588, 10, 'adm_users/groups', 1),
(589, 10, 'adm_users/login', 1),
(590, 10, 'adm_users/profile', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gw_adm_sitemap`
--

CREATE TABLE IF NOT EXISTS `gw_adm_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '-1',
  `path` varchar(255) NOT NULL,
  `pathname` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_lt` varchar(255) NOT NULL,
  `title_no` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `fields` text NOT NULL,
  `public` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `in_menu` tinyint(4) NOT NULL DEFAULT '1',
  `priority` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `sync_time` datetime NOT NULL,
  `notes` text NOT NULL,
  `views` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=139 ;

--
-- Dumping data for table `gw_adm_sitemap`
--

INSERT INTO `gw_adm_sitemap` (`id`, `parent_id`, `path`, `pathname`, `title_en`, `title_lt`, `title_no`, `info`, `fields`, `public`, `active`, `in_menu`, `priority`, `insert_time`, `update_time`, `sync_time`, `notes`, `views`) VALUES
(126, 0, 'articles', 'articles', 'Articles', 'Straipsniai', 'Artikler', 'a:1:{s:5:"model";s:10:"GW_Article";}', '', 0, 1, 1, 1, '2014-09-07 00:59:35', '2014-09-07 00:59:35', '2014-09-07 00:59:35', '', ''),
(127, 126, 'articles/groups', 'groups', 'Groups', 'Grupės', 'Grupper', 'a:1:{s:5:"model";s:17:"GW_Articles_Group";}', '', 0, 1, 1, 3, '2014-09-07 00:59:35', '2014-09-07 00:59:35', '2014-09-07 00:59:35', '', ''),
(128, 0, 'config', 'config', 'Configuration', 'Configuration', 'Configuration', '', '', 0, 1, 1, 2, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(129, 128, 'config/modules', 'modules', 'Module Configuration', 'Module Configuration', 'Module Configuration', '', '', 0, 1, 1, 14, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(130, 128, 'config/tools', 'tools', 'Tools', 'Tools', 'Tools', '', '', 0, 1, 1, 15, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(131, 128, 'config/dbqueries', 'dbqueries', 'DB Queries', 'DB Queries', 'DB Queries', 'a:1:{s:5:"model";s:13:"GW_DB_Queries";}', '', 0, 1, 1, 16, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(132, 128, 'config/logwatch', 'logwatch', 'Log Watch', 'Log Watch', 'Log Watch', 'a:1:{s:5:"model";s:12:"GW_Log_Watch";}', '', 0, 1, 1, 17, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(133, 128, 'config/tasks', 'tasks', 'Tasks', 'Tasks', 'Tasks', 'a:1:{s:5:"model";s:7:"GW_Task";}', '', 0, 1, 1, 18, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(134, 128, 'config/crontasks', 'crontasks', 'Cron Tasks', 'Cron Tasks', 'Cron Tasks', 'a:1:{s:5:"model";s:11:"GW_CronTask";}', '', 0, 1, 1, 19, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(135, 128, 'config/messages', 'messages', 'Messages', '', '', 'a:1:{s:5:"model";s:14:"GW_Adm_Message";}', '', 0, 1, 1, 7, '2014-09-07 00:59:41', '2014-09-07 00:59:41', '2014-09-07 00:59:41', '', ''),
(14, 0, 'gallery', 'gallery', 'Gallery', 'Galerija', 'Galleri', '', '', 0, 1, 1, 7, '2010-03-23 20:28:08', '2014-09-07 00:25:15', '2014-09-07 00:25:15', '', ''),
(15, 14, 'gallery/items', 'items', 'Gallery', 'Galerija', 'Galleri', 'a:1:{s:5:"model";s:15:"GW_Gallery_Item";}', '', 0, 1, 1, 5, '2010-03-23 20:28:08', '2014-09-07 00:25:15', '2014-09-07 00:25:15', '', ''),
(16, 14, 'gallery/config', 'config', 'Configuration', 'Nustatymai', 'Instillinger', '', '', 0, 1, 1, 6, '2010-03-23 20:28:08', '2014-09-07 00:25:15', '2014-09-07 00:25:15', '', ''),
(17, 0, 'sitemap', 'sitemap', 'Structure & Content', 'Struktūra ir tekstai', 'Struktur og Innhold', '', '', 0, 1, 1, 8, '2010-03-23 20:28:08', '2014-09-07 00:25:15', '2014-09-07 00:25:15', '', ''),
(18, 17, 'sitemap/pages', 'pages', 'Sitemap', 'Svetainės medis', 'Sitemap', 'a:1:{s:5:"model";s:7:"GW_Page";}', '', 0, 1, 1, 7, '2010-03-23 20:28:08', '2014-09-07 00:25:15', '2014-09-07 00:25:15', '', ''),
(19, 17, 'sitemap/templates', 'templates', 'Templates', 'Šablonai', 'Mal', 'a:1:{s:5:"model";s:11:"GW_Template";}', '', 0, 1, 1, 8, '2010-03-23 20:28:08', '2014-09-07 00:25:15', '2014-09-07 00:25:15', '', ''),
(20, 19, 'sitemap/templates/tplvars', 'tplvars', 'Template vars', 'Šablono įvestys', 'Mal variabler', 'a:1:{s:5:"model";s:9:"GW_TplVar";}', '', 0, 1, 1, 9, '2010-03-23 20:28:08', '2014-09-07 00:25:15', '2014-09-07 00:25:15', '', ''),
(138, 0, 'mass_messages', 'mass_messages', '%NOT SPECIFIED%', 'Masinės žinutės', '%NOT SPECIFIED%', 'a:1:{s:5:"model";s:15:"GW_Mass_Message";}', '', 0, 1, 1, 0, '2014-09-08 06:59:53', '2014-09-13 12:36:32', '2014-09-13 12:36:32', '', ''),
(115, 114, 'customers/users', 'users', 'Users', 'Svetainės vartotojai', '%NOT SPECIFIED%', 'a:1:{s:5:"model";s:7:"GW_User";}', 'a:7:{i:0;s:1:"0";s:2:"id";s:1:"1";s:8:"username";s:1:"1";s:4:"name";s:1:"1";s:5:"email";s:1:"1";s:11:"insert_time";s:1:"0";s:11:"update_time";s:1:"0";}', 0, 1, 1, 3, '2014-09-07 00:59:12', '2014-10-05 19:09:13', '2014-09-13 12:44:49', '', ''),
(114, 0, 'customers', 'customers', 'Customer Database', 'Svetainės vartotojai', '%NOT SPECIFIED%', '', '', 0, 1, 1, 3, '2014-09-07 00:59:12', '2014-09-13 12:44:49', '2014-09-13 12:44:49', '', ''),
(118, 0, 'todo', 'todo', 'Todo', 'Darbai', 'Jobb boka', '', '', 0, 1, 1, 4, '2014-09-07 00:59:24', '2014-09-07 00:59:24', '2014-09-07 00:59:24', '', ''),
(119, 118, 'todo/items', 'items', 'Todo list', 'Darbų sąrašas', 'Jobb liste', 'a:1:{s:5:"model";s:9:"todo_item";}', '', 0, 1, 1, 3, '2014-09-07 00:59:24', '2014-09-07 00:59:24', '2014-09-07 00:59:24', '', ''),
(121, 120, 'adm_users/users', 'users', 'Users', 'Vartotojai', '%NOT SPECIFIED%', 'a:1:{s:5:"model";s:11:"GW_Adm_User";}', '', 0, 1, 1, 11, '2014-09-07 00:59:29', '2014-09-07 00:59:29', '2014-09-07 00:59:29', '', ''),
(122, 120, 'adm_users/profile', 'profile', 'My profile', 'Mano profilis', 'Min Profil', '', '', 0, 1, 0, 12, '2014-09-07 00:59:29', '2014-09-07 00:59:29', '2014-09-07 00:59:29', '', ''),
(123, 120, 'adm_users/login', 'login', 'User identification', 'Vartotojo identifikavimas', '%NOT SPECIFIED%', '', '', 1, 1, 0, 13, '2014-09-07 00:59:29', '2014-09-07 00:59:29', '2014-09-07 00:59:29', '', ''),
(124, 120, 'adm_users/groups', 'groups', 'Groups', 'Grupės', '%NOT SPECIFIED%', 'a:1:{s:5:"model";s:18:"GW_Adm_Users_Group";}', '', 0, 1, 1, 14, '2014-09-07 00:59:29', '2014-09-07 00:59:29', '2014-09-07 00:59:29', '', ''),
(125, 120, 'adm_users/config', 'config', 'Configuration', 'Nustatymai', '%NOT SPECIFIED%', '', '', 0, 1, 1, 15, '2014-09-07 00:59:29', '2014-09-07 00:59:29', '2014-09-07 00:59:29', '', ''),
(70, 0, 'products', 'products', 'Products', 'Prekės', '%NOT SPECIFIED%', 'a:1:{s:5:"model";s:10:"GW_Product";}', '', 0, 1, 1, 5, '2012-12-06 00:30:43', '2012-12-06 00:30:43', '2012-12-06 00:30:43', '', ''),
(120, 0, 'adm_users', 'adm_users', 'Users', 'Vartotojai', '%NOT SPECIFIED%', '', '', 0, 1, 1, 6, '2014-09-07 00:59:29', '2014-09-07 00:59:29', '2014-09-07 00:59:29', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `gw_adm_users`
--

CREATE TABLE IF NOT EXISTS `gw_adm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `pass` varchar(256) DEFAULT NULL,
  `rights` varchar(255) NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `login_time` datetime NOT NULL,
  `login_count` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL,
  `session_validity` int(11) NOT NULL DEFAULT '30' COMMENT 'in minutes',
  `last_request_time` datetime NOT NULL,
  `info` text NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `removed` tinyint(4) NOT NULL DEFAULT '0',
  `insert_time` datetime NOT NULL COMMENT 'auto field',
  `update_time` datetime NOT NULL COMMENT 'auto field',
  PRIMARY KEY (`id`),
  KEY `login` (`username`,`pass`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `gw_adm_users`
--

INSERT INTO `gw_adm_users` (`id`, `username`, `pass`, `rights`, `name`, `email`, `login_time`, `login_count`, `last_ip`, `session_validity`, `last_request_time`, `info`, `active`, `removed`, `insert_time`, `update_time`) VALUES
(9, 'root', '$1$OPqPRwOn$wyiwvLNLQHM7DHbQedg0q1', '', 'Vidmantass', 'root@gw.lt', '2014-10-22 00:49:36', 821, '127.0.0.1', -1, '2014-10-22 00:51:15', 'a:1:{s:9:"autologin";a:1:{s:32:"84fe593141c7390bf4c927e85948481f";s:19:"2014-11-02 17:56:27";}}', 1, 0, '2011-09-08 18:16:36', '2014-10-22 00:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `gw_adm_users_groups`
--

CREATE TABLE IF NOT EXISTS `gw_adm_users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `gw_adm_users_groups`
--

INSERT INTO `gw_adm_users_groups` (`id`, `title`, `description`, `active`, `insert_time`, `update_time`) VALUES
(1, 'God Mode', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 'demo', 'haha', 1, '2011-09-27 15:50:41', '2014-09-07 21:17:51');

-- --------------------------------------------------------

--
-- Table structure for table `gw_articles`
--

CREATE TABLE IF NOT EXISTS `gw_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `gw_articles`
--

INSERT INTO `gw_articles` (`id`, `title`, `text`, `active`, `insert_time`, `update_time`) VALUES
(44, 'test AsdAS D', '<p>\r\n	lalala rwarA AsdAS</p>\r\n', 0, '2014-09-07 20:54:13', '2014-09-29 05:56:57'),
(42, 'test', '<p>\r\n	lalala</p>\r\n', 0, '2014-09-07 20:52:33', '2014-09-08 06:54:23'),
(43, 'test', '<p>\r\n	lalala</p>\r\n', 0, '2014-09-07 20:53:22', '2014-09-08 06:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `gw_articles_groups`
--

CREATE TABLE IF NOT EXISTS `gw_articles_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `gw_articles_groups`
--

INSERT INTO `gw_articles_groups` (`id`, `title`, `description`, `active`, `insert_time`, `update_time`) VALUES
(9, 'gg', 'gg', 1, '2011-09-28 10:49:21', '2014-09-07 20:55:03'),
(10, 'dsg', 'sdfgsdfg', 1, '2014-09-07 20:54:58', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `gw_config`
--

CREATE TABLE IF NOT EXISTS `gw_config` (
  `id` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gw_config`
--

INSERT INTO `gw_config` (`id`, `value`, `time`) VALUES
('gallery/id', '', '2010-03-14 22:17:12'),
('gallery/store_size', '1600x1200', '2010-03-14 23:20:25'),
('gallery/thunmbnails_size', '50x50', '2011-01-30 23:15:00'),
('gallery/display_size', '1000x1000', '2010-03-14 23:20:25'),
('gallery/page_by', '', '2010-03-14 22:17:12'),
('gallery/enable_description', '1', '2011-01-19 15:40:33'),
('gallery/enable_author', '1', '2011-01-19 15:40:33'),
('gallery/adm_thunmbnails_size', '16x16', '2010-03-15 14:25:29'),
('gallery/adm_list_style', '1', '2014-09-07 21:35:57'),
('gw_adm_users/id', '', '2010-06-02 00:53:09'),
('gw_adm_users/autologin', '1', '2014-09-07 21:17:57'),
('system_app/pid', '2167', '2014-04-09 16:20:05'),
('ctask ....-..-.. ..:3.:.. 30', '2014-04-11 15:30:05', '2014-04-11 15:30:05'),
('tasks/bamba_read/last_ids', 'a:3:{s:5:"n1675";i:220438;s:5:"n1604";s:6:"737725";s:6:"n19901";i:687165;}', '2012-05-18 09:50:03'),
('system/log_watch_config', '{"php_error_cached.log":{"last_offset":925086,"expanded":false,"area":false}}', '2014-09-07 21:01:58'),
('ctask ....-..-.. ..:00:.. 60', '2012-03-01 04:00:01', '2012-03-01 04:00:01'),
('ctask ....-..-.. 03:..:.. 1439', '2014-04-11 03:00:01', '2014-04-11 03:00:01'),
('ctask ....-..-.. ..:.[50]:.. 5', '2014-04-11 15:30:05', '2014-04-11 15:30:05'),
('ctask ....-..-.[05] 12:..:.. 1440', '2014-04-10 12:00:01', '2014-04-10 12:00:01'),
('new_sms/sms_sender', 'Paskola23', '2014-09-23 22:57:29');

-- --------------------------------------------------------

--
-- Table structure for table `gw_crontasks`
--

CREATE TABLE IF NOT EXISTS `gw_crontasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `time_match` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `params` text NOT NULL COMMENT 'json',
  `title` varchar(255) NOT NULL,
  `last_output` text NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `time_match` (`time_match`),
  KEY `active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `gw_crontasks`
--

INSERT INTO `gw_crontasks` (`id`, `active`, `time_match`, `name`, `params`, `title`, `last_output`, `insert_time`, `update_time`) VALUES
(3, 1, '..:3.:..#30', 'tasks_health', '', 'Užduočių sanitaras', '', '2012-02-28 17:50:02', '2012-02-28 17:51:11');

-- --------------------------------------------------------

--
-- Table structure for table `gw_db_queries`
--

CREATE TABLE IF NOT EXISTS `gw_db_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sql` text NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `gw_db_queries`
--

INSERT INTO `gw_db_queries` (`id`, `name`, `sql`, `active`, `insert_time`, `update_time`) VALUES
(2, 'Show tables', 'SHOW TABLES;', 1, '2010-01-28 21:53:56', '2010-01-28 22:04:39'),
(3, 'Show users', 'SELECT * FROM gw_adm_users', 1, '2010-01-28 22:05:47', '2010-01-28 22:05:50'),
(4, 'Delete all tables', 'SELECT "You beter don''t! San Ov A Bič!!!!111"', 1, '2010-01-28 22:27:22', '2010-01-28 22:28:10'),
(5, 'view_tom_order_items', 'DROP VIEW IF EXISTS `view_tom_order_items`;\r\nCREATE ALGORITHM = UNDEFINED VIEW view_tom_order_items AS \r\nSELECT \r\n	oi.*, \r\n	o.state AS order_state, \r\n	o.deadline AS order_deadline,\r\n	p.title AS product_title,\r\n	p.type AS product_type,\r\n	s.id AS school_id,\r\n	s.title AS school_title,\r\n	s.material AS school_material,\r\n	(\r\n		SELECT pmo.title \r\n		FROM tom_product_modif pm,tom_order_item_opt oio,tom_product_modif_opt pmo \r\n		WHERE pm.product_id=p.id AND pm.title="dydis" AND oio.id=oi.id AND oio.id1=pmo.id\r\n	) \r\n	AS dydis\r\nFROM \r\n	tom_order_items oi\r\n		LEFT JOIN tom_orders o ON oi.order_id = o.id \r\n		LEFT JOIN tom_products p ON oi.product_id = p.id\r\n		LEFT JOIN tom_schools s ON o.school_id = s.id\r\n\r\n\r\n', 1, '2010-01-29 15:59:59', '2010-02-04 17:27:19'),
(6, 'Show config', 'SELECT * FROM gw_config', 1, '2012-02-28 18:15:11', '2012-02-28 18:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `gw_gallery_items`
--

CREATE TABLE IF NOT EXISTS `gw_gallery_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '-1',
  `type` tinyint(4) NOT NULL COMMENT '0 - image, 1 - folder',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `priority` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `gw_gallery_items`
--

INSERT INTO `gw_gallery_items` (`id`, `parent_id`, `type`, `title`, `description`, `author`, `active`, `priority`, `insert_time`, `update_time`) VALUES
(1, -1, 0, 'a', 'aaa', 'asdf', 1, 1, '2013-04-25 14:48:19', '2014-09-07 21:35:41'),
(2, -1, 0, 'b', 'b', 'asdf', 1, 0, '2013-04-25 14:48:42', '2014-09-07 21:35:51'),
(3, -1, 1, 'Katalogas', '', '', 1, 0, '2013-04-25 14:49:00', '0000-00-00 00:00:00'),
(4, 3, 0, 'd', 'd', '', 1, 0, '2013-04-25 14:49:22', '2014-09-07 21:35:23'),
(5, 3, 0, 'f', 'f', '', 1, 0, '2013-04-25 14:49:38', '2014-09-07 21:36:12'),
(6, 3, 0, 'g', 'g', 'g', 1, 0, '2013-04-25 14:49:54', '2014-09-07 21:36:25'),
(7, -1, 1, 'Dar vienas katalogas', 'test', 'test', 1, 0, '2013-04-25 14:50:24', '0000-00-00 00:00:00'),
(8, 7, 0, 'Serverine', '', '', 1, 0, '2013-04-25 14:50:40', '2014-09-07 21:36:43'),
(9, 7, 0, 'Kietas diskas', '', '', 1, 0, '2013-04-25 14:50:54', '2014-09-07 21:37:03'),
(10, -1, 0, 'testas', 'testas', '', 1, 0, '2014-09-07 21:23:28', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `gw_images`
--

CREATE TABLE IF NOT EXISTS `gw_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `gw_images`
--

INSERT INTO `gw_images` (`id`, `key`, `owner`, `filename`, `width`, `height`, `size`, `original_filename`, `insert_time`) VALUES
(13, '2279740aa77aa30af89fe7c02954eab4', 'GW_Gallery_Item_1_image', '13_GW_Gallery_Item_1_image.jpg', 852, 640, 51639, '10626497_10204806823237541_8501101631157710197_n.jpg', '2014-09-07 21:35:41'),
(14, '62f18c873be558167f780fcdd3e56a59', 'GW_Gallery_Item_2_image', '14_GW_Gallery_Item_2_image.jpg', 1600, 1200, 325386, '20140815_132814.jpg', '2014-09-07 21:35:51'),
(12, '1e508ea65b28cc79dca9d980660f4c6d', 'GW_Gallery_Item_4_image', '12_GW_Gallery_Item_4_image.jpg', 804, 960, 106342, '10620662_681415288600363_8190965674003422179_n.jpg', '2014-09-07 21:35:23'),
(15, 'bf3382049adab49dd2588fed7fbdfeb3', 'GW_Gallery_Item_5_image', '15_GW_Gallery_Item_5_image.jpg', 852, 640, 51076, '10480627_10204806823117538_365685291594958374_n.jpg', '2014-09-07 21:36:12'),
(16, '2fc34042d11fdd87a00e65632b2ddd0e', 'GW_Gallery_Item_6_image', '16_GW_Gallery_Item_6_image.jpg', 720, 960, 80912, '10426098_10204806848118163_2578796072942415775_n.jpg', '2014-09-07 21:36:25'),
(17, '2152ac263bdafd9db9447ff921fc375f', 'GW_Gallery_Item_8_image', '17_GW_Gallery_Item_8_image.jpg', 960, 720, 64475, '10153920_4120309663603_4944620175840698335_n.jpg', '2014-09-07 21:36:43'),
(18, '80efad59bfd3ae1a8b47e4a30f5f21ff', 'GW_Gallery_Item_9_image', '18_GW_Gallery_Item_9_image.jpg', 960, 720, 81459, '10259825_4120309783606_6627878119241071408_n.jpg', '2014-09-07 21:37:03'),
(20, '643f7412c957e9e74d188bb8aa60cd49', 'GW_Article_42_image', '20_GW_Article_42_image.jpg', 800, 600, 65826, '10153920_4120309663603_4944620175840698335_n.jpg', '2014-09-08 06:54:23'),
(19, 'ee001d68010d5f48045eefb2a775982e', 'GW_Article_43_image', '19_GW_Article_43_image.jpg', 800, 600, 82371, '10259825_4120309783606_6627878119241071408_n.jpg', '2014-09-08 06:54:03'),
(35, '7b6428cfc2ba19acf7bed0efe0bbdde1', 'GW_Article_44_image', '35_GW_Article_44_image.jpg', 800, 533, 105349, '1525758_10201363736275138_1120402494_n.jpg', '2014-09-29 05:56:57'),
(11, '09f00c0ff42c07aaa27b0cfdb0b7bf7f', 'GW_Gallery_Item_10_image', '11_GW_Gallery_Item_10_image.jpg', 804, 960, 106342, '10620662_681415288600363_8190965674003422179_n.jpg', '2014-09-07 21:23:28');

-- --------------------------------------------------------

--
-- Table structure for table `gw_link_user_groups`
--

CREATE TABLE IF NOT EXISTS `gw_link_user_groups` (
  `id` int(11) NOT NULL,
  `id1` int(11) NOT NULL,
  KEY `id` (`id`,`id1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gw_link_user_groups`
--

INSERT INTO `gw_link_user_groups` (`id`, `id1`) VALUES
(9, 1),
(10, 10),
(11, 10);

-- --------------------------------------------------------

--
-- Table structure for table `gw_log`
--

CREATE TABLE IF NOT EXISTS `gw_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `type` enum('modem','user','proc.ctrl','mysql') DEFAULT NULL,
  `action` varchar(20) CHARACTER SET ascii DEFAULT NULL,
  `status` varchar(20) CHARACTER SET ascii DEFAULT NULL,
  `msg` varchar(255) NOT NULL,
  `add_info` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `gw_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `gw_sitemap`
--

CREATE TABLE IF NOT EXISTS `gw_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '-1',
  `template_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `pathname` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-page,1-link to first child, 2-link',
  `title_en` varchar(255) NOT NULL,
  `title_lt` varchar(255) NOT NULL,
  `title_no` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `meta_description` text NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `in_menu_lt` tinyint(4) NOT NULL DEFAULT '1',
  `in_menu_en` tinyint(4) NOT NULL,
  `priority` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `visit_count` int(11) NOT NULL DEFAULT '0',
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Dumping data for table `gw_sitemap`
--

INSERT INTO `gw_sitemap` (`id`, `parent_id`, `template_id`, `path`, `pathname`, `type`, `title_en`, `title_lt`, `title_no`, `link`, `meta_description`, `active`, `in_menu_lt`, `in_menu_en`, `priority`, `user_id`, `visit_count`, `insert_time`, `update_time`) VALUES
(53, -1, 18, 'usr', 'usr', 1, '', 'Vartotoju zona', '', '', '', 1, 1, 0, 0, 0, 0, '2014-09-13 22:18:46', '2014-09-23 20:17:42'),
(54, 53, 19, 'usr/new', 'new', 0, '', 'Nauja sms', '', '', '', 1, 1, 0, 0, 0, 0, '2014-09-13 22:51:42', '2014-09-23 20:18:20'),
(58, 53, 24, 'usr/user', 'user', 0, '', 'Sąskaitos balansas', '', '', '', 1, 1, 0, 2, 0, 0, '2014-09-23 20:24:40', '2014-10-22 00:51:13'),
(56, -1, 0, 'sys', 'sys', 1, '', 'Sisteminis meniu', '', '', '', 0, 1, 0, 0, 0, 0, '2014-09-23 20:12:30', '2014-09-23 20:12:45'),
(57, 56, 18, 'sys/user', 'user', 0, '', 'Vartotojai', '', '', '', 1, 1, 0, 0, 0, 0, '2014-09-23 20:14:53', '2014-10-19 18:18:33'),
(59, 53, 22, 'usr/list', 'list', 0, '', 'Žinučių sąrašas', '', '', '', 1, 1, 0, 1, 0, 0, '2014-09-23 20:45:33', '2014-09-24 07:36:48');

-- --------------------------------------------------------

--
-- Table structure for table `gw_sitemap_data`
--

CREATE TABLE IF NOT EXISTS `gw_sitemap_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ln` char(2) NOT NULL,
  `key` varchar(255) NOT NULL,
  `page_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `gw_sitemap_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `gw_tasks`
--

CREATE TABLE IF NOT EXISTS `gw_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `arguments` text NOT NULL COMMENT 'json',
  `error_code` int(11) NOT NULL DEFAULT '-1',
  `error_msg` text NOT NULL,
  `output` text NOT NULL,
  `insert_time` datetime NOT NULL,
  `halt_time` datetime NOT NULL COMMENT 'execution time limit',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `finish_time` datetime NOT NULL,
  `running` int(11) NOT NULL DEFAULT '-1' COMMENT 'pid',
  `speed` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `running` (`running`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `gw_tasks`
--


-- --------------------------------------------------------

--
-- Table structure for table `gw_templates`
--

CREATE TABLE IF NOT EXISTS `gw_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `gw_templates`
--

INSERT INTO `gw_templates` (`id`, `title`, `path`, `active`, `insert_time`, `update_time`) VALUES
(22, 'Žinučių sąrašas', 'mass_messages/mass_messages/list', 1, '2014-09-24 07:35:58', '0000-00-00 00:00:00'),
(14, 'Paprastas tekstas', 'templates/text.tpl', 1, '2011-04-16 09:30:04', '2012-05-17 07:28:54'),
(18, 'Vartotojo profilis', 'users/users', 1, '2014-09-13 22:17:19', '2014-09-23 20:25:11'),
(19, 'Nauja sms', 'mass_messages/mass_messages/form', 1, '2014-09-13 22:52:24', '2014-09-15 08:04:41'),
(21, 'Prisijungimo puslapis', 'users/users/login', 1, '2014-09-23 20:13:57', '2014-09-23 20:16:47'),
(23, 'Slaptažodžio keitimas', 'users/users/passchange', 1, '2014-10-19 17:53:05', '0000-00-00 00:00:00'),
(24, 'Sąskaitos balansas', 'users/users/accountbalance', 1, '2014-10-22 00:50:14', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `gw_template_vars`
--

CREATE TABLE IF NOT EXISTS `gw_template_vars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `note` varchar(255) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `gw_template_vars`
--

INSERT INTO `gw_template_vars` (`id`, `template_id`, `title`, `type`, `note`, `insert_time`, `update_time`) VALUES
(1, 0, 'text', 'textarea', '', '2010-03-12 14:58:12', '0000-00-00 00:00:00'),
(2, 4, 'text', 'textarea', '', '2010-03-12 15:07:44', '2010-03-19 21:20:14'),
(29, 16, 'text', 'htmlarea', '', '2011-09-11 04:12:33', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `gw_todo`
--

CREATE TABLE IF NOT EXISTS `gw_todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `user_create` int(11) NOT NULL,
  `user_exec` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `job_type` varchar(255) NOT NULL,
  `title` varchar(2255) NOT NULL,
  `description` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `deadline` datetime NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=98 ;

--
-- Dumping data for table `gw_todo`
--

INSERT INTO `gw_todo` (`id`, `parent_id`, `user_create`, `user_exec`, `type`, `job_type`, `title`, `description`, `state`, `priority`, `deadline`, `insert_time`, `update_time`) VALUES
(95, 0, 9, 0, 2, '', '', '', 0, 0, '0000-00-00 00:00:00', '2012-08-20 00:47:46', '0000-00-00 00:00:00'),
(96, 0, 9, 0, 2, '', '', 'test', 0, 0, '0000-00-00 00:00:00', '2014-10-05 18:21:00', '0000-00-00 00:00:00'),
(97, -1, 9, 9, 0, '', 'tset', 'tsetasdfasdf', 5, 1, '2016-01-01 00:00:00', '2014-10-05 19:09:45', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `gw_users`
--

CREATE TABLE IF NOT EXISTS `gw_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `pass` varchar(256) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `second_name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `login_time` datetime NOT NULL,
  `login_count` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(18) NOT NULL,
  `session_validity` int(11) NOT NULL DEFAULT '30',
  `last_request_time` datetime NOT NULL,
  `info` text NOT NULL,
  `desc` text NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `removed` tinyint(4) NOT NULL DEFAULT '0',
  `key` varchar(255) DEFAULT NULL,
  `last_sms_sender` varchar(255) NOT NULL,
  `passchange` varchar(255) NOT NULL,
  `credit` float NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;


CREATE TABLE IF NOT EXISTS `gw_pricing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `country` char(3) NOT NULL,
  `price` float NOT NULL,
  `price_percent` float NOT NULL,
  `user_id` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gw_pricing`
--

INSERT INTO `gw_pricing` (`id`, `name`, `country`, `price`, `price_percent`, `user_id`, `insert_time`, `update_time`) VALUES
(1, 'gate1', 'LT', 0.04, 0, 0, '2014-10-29 04:05:21', '2014-10-29 04:26:04'),
(2, 'gate1', 'LT', 0.03, 0, 39, '0000-00-00 00:00:00', '2014-10-29 04:25:21');



ALTER TABLE `gw_users` CHANGE `credit` `funds` FLOAT NOT NULL;
ALTER TABLE `gw_users` ADD `allow_credit` TINYINT NOT NULL AFTER `funds` ;

