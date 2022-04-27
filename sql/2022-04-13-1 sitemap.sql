ALTER TABLE `gw_sitemap` ADD `display_cond` VARCHAR(255) NOT NULL AFTER `icon`;
ALTER TABLE `gw_sitemap` ADD `display_badge` VARCHAR(255) NOT NULL AFTER `display_cond`;
