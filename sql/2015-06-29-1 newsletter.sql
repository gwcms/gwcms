CREATE TABLE IF NOT EXISTS `gw_nl_sent_messages` (
  `message_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`,`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `gw_nl_subscribers` ADD `unsubscribe_note` VARCHAR(200) NOT NULL AFTER `unsubscribed`;
ALTER TABLE `gw_nl_messages` ADD `replyto` VARCHAR(200) NOT NULL AFTER `sender`;