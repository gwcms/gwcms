ALTER TABLE `gw_users`
  DROP `post_index`,
  DROP `mob_phone`,
  DROP `news`,
  DROP `banned`;

ALTER TABLE `gw_users` ADD `username` VARCHAR( 255 ) NOT NULL AFTER `id` ;