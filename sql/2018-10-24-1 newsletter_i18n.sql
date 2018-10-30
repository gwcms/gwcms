ALTER TABLE `gw_nl_messages` CHANGE `lang` `lang_lt` CHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_nl_messages` CHANGE `body` `body_lt` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_nl_messages` CHANGE `subject` `subject_lt` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_nl_messages` CHANGE `recipients` `recipients_lt` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_nl_messages` CHANGE `recipients_count` `recipients_count_lt` INT(11) NOT NULL;
ALTER TABLE `gw_nl_messages` CHANGE `sender` `sender_lt` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


ALTER TABLE  `gw_nl_messages` ADD  `subject_en` varchar(255) NOT NULL  COMMENT  'copy from subject_lt'  AFTER  `subject_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `body_en` text NOT NULL  COMMENT  'copy from body_lt'  AFTER  `body_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `sender_en` varchar(255) NOT NULL  COMMENT  'copy from sender_lt'  AFTER  `sender_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `recipients_count_en` int(11) NOT NULL  COMMENT  'copy from recipients_count_lt'  AFTER  `recipients_count_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `recipients_en` text NOT NULL  COMMENT  'copy from recipients_lt'  AFTER  `recipients_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `lang_en` char(3) NOT NULL  COMMENT  'copy from lang_lt'  AFTER  `lang_lt` ;

ALTER TABLE  `gw_nl_messages` ADD  `subject_ru` varchar(255) NOT NULL  COMMENT  'copy from subject_lt'  AFTER  `subject_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `body_ru` text NOT NULL  COMMENT  'copy from body_lt'  AFTER  `body_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `sender_ru` varchar(255) NOT NULL  COMMENT  'copy from sender_lt'  AFTER  `sender_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `recipients_count_ru` int(11) NOT NULL  COMMENT  'copy from recipients_count_lt'  AFTER  `recipients_count_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `recipients_ru` text NOT NULL  COMMENT  'copy from recipients_lt'  AFTER  `recipients_lt` ;;
 ALTER TABLE  `gw_nl_messages` ADD  `lang_ru` char(3) NOT NULL  COMMENT  'copy from lang_lt'  AFTER  `lang_lt` ;

SET GLOBAL sql_mode='';
ALTER TABLE `gw_nl_messages` CHANGE `lang_lt` `lang_lt` TINYINT NOT NULL;
ALTER TABLE `gw_nl_messages` CHANGE `lang_ru` `lang_ru` TINYINT NOT NULL COMMENT 'copy from lang_lt';
ALTER TABLE `gw_nl_messages` CHANGE `lang_en` `lang_en` TINYINT NOT NULL COMMENT 'copy from lang_lt';



