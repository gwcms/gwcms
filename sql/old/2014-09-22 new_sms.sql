ALTER TABLE `gw_mass_messages` ADD `time_to_send` DATETIME NOT NULL AFTER `active` ;
ALTER TABLE `gw_mass_messages` CHANGE `time_to_send` `send_time` DATETIME NOT NULL;
ALTER TABLE `gw_users` ADD `last_sms_sender` VARCHAR( 255 ) NOT NULL AFTER `key` ;



DROP TABLE IF EXISTS `gw_templates`;
CREATE TABLE IF NOT EXISTS `gw_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `gw_templates`
--

INSERT INTO `gw_templates` (`id`, `title`, `path`, `active`, `insert_time`, `update_time`) VALUES
(22, 'Žinučių sąrašas', 'mass_messages/mass_messages/list', 1, '2014-09-24 07:35:58', '0000-00-00 00:00:00'),
(14, 'Paprastas tekstas', 'templates/text.tpl', 1, '2011-04-16 09:30:04', '2012-05-17 07:28:54'),
(18, 'Vartotojo profilis', 'users/users', 1, '2014-09-13 22:17:19', '2014-09-23 20:25:11'),
(19, 'Nauja sms', 'mass_messages/mass_messages/form', 1, '2014-09-13 22:52:24', '2014-09-15 08:04:41'),
(21, 'Prisijungimo puslapis', 'users/users/login', 1, '2014-09-23 20:13:57', '2014-09-23 20:16:47');




DROP TABLE IF EXISTS `gw_sitemap`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

--
-- Dumping data for table `gw_sitemap`
--

INSERT INTO `gw_sitemap` (`id`, `parent_id`, `template_id`, `path`, `pathname`, `type`, `title_en`, `title_lt`, `title_no`, `link`, `meta_description`, `active`, `in_menu_lt`, `in_menu_en`, `priority`, `user_id`, `visit_count`, `insert_time`, `update_time`) VALUES
(53, -1, 18, 'usr', 'usr', 1, '', 'Vartotoju zona', '', '', '', 1, 1, 0, 0, 0, 0, '2014-09-13 22:18:46', '2014-09-23 20:17:42'),
(54, 53, 19, 'usr/new', 'new', 0, '', 'Nauja sms', '', '', '', 1, 1, 0, 0, 0, 0, '2014-09-13 22:51:42', '2014-09-23 20:18:20'),
(58, 53, 18, 'usr/user', 'user', 0, '', 'Vartotojo profilis', '', '', '', 0, 1, 0, 2, 0, 0, '2014-09-23 20:24:40', '2014-09-23 20:51:29'),
(56, -1, 0, 'sys', 'sys', 1, '', 'Sisteminis meniu', '', '', '', 0, 1, 0, 0, 0, 0, '2014-09-23 20:12:30', '2014-09-23 20:12:45'),
(57, 56, 21, 'sys/login', 'login', 0, '', 'Prisijungimo puslapis', '', '', '', 1, 1, 0, 0, 0, 0, '2014-09-23 20:14:53', '2014-09-23 20:15:15'),
(59, 53, 22, 'usr/list', 'list', 0, '', 'Žinučių sąrašas', '', '', '', 1, 1, 0, 1, 0, 0, '2014-09-23 20:45:33', '2014-09-24 07:36:48');
