ALTER TABLE `gw_articles` ADD `site_id` INT NOT NULL AFTER `id`;
ALTER TABLE `gw_articles_groups` ADD `site_id` INT NOT NULL AFTER `id`;

ALTER TABLE `gw_articles` ADD `datetime` DATETIME NOT NULL AFTER `priority`;