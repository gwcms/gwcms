-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2015 at 04:37 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `gwcms_gwcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_hits`
--

CREATE TABLE IF NOT EXISTS `gw_nl_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `debug` varchar(300) NOT NULL,
  `browser` varchar(100) NOT NULL,
  `referer` varchar(300) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=155 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_links`
--

CREATE TABLE IF NOT EXISTS `gw_nl_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_id` int(11) NOT NULL,
  `link` varchar(400) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_messages`
--

CREATE TABLE IF NOT EXISTS `gw_nl_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `lang` char(3) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `replyto` varchar(200) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `body_prepared` text NOT NULL,
  `body_editor_size` char(9) NOT NULL,
  `groups` text NOT NULL,
  `recipients` text NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `recipients_count` int(11) NOT NULL,
  `recipients_data` text NOT NULL,
  `sent_count` int(11) NOT NULL,
  `sent_info` text NOT NULL,
  `sent_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_sent_messages`
--

CREATE TABLE IF NOT EXISTS `gw_nl_sent_messages` (
  `message_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`,`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_subscribers`
--

CREATE TABLE IF NOT EXISTS `gw_nl_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lang` char(3) NOT NULL,
  `unsubscribed` int(11) NOT NULL,
  `unsubscribe_note` varchar(200) NOT NULL,
  `active` int(11) NOT NULL,
  `confirm_code` int(10) unsigned DEFAULT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38543 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_subscribers_maza`
--

CREATE TABLE IF NOT EXISTS `gw_nl_subscribers_maza` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lang` char(3) NOT NULL,
  `unsubscribed` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_subscribers_test`
--

CREATE TABLE IF NOT EXISTS `gw_nl_subscribers_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lang` char(3) NOT NULL,
  `unsubscribed` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38523 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_subs_bind_groups`
--

CREATE TABLE IF NOT EXISTS `gw_nl_subs_bind_groups` (
  `subscriber_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gw_nl_subs_groups`
--

CREATE TABLE IF NOT EXISTS `gw_nl_subs_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
