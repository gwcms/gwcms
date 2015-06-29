CREATE TABLE IF NOT EXISTS `gw_nl_sent_messages` (
  `message_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`,`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;