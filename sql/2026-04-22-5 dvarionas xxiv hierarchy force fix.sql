SET @has_subsubcat := (
	SELECT COUNT(*)
	FROM information_schema.COLUMNS
	WHERE TABLE_SCHEMA = DATABASE()
	  AND TABLE_NAME = 'ipmc_competitions'
	  AND COLUMN_NAME = 'subsubcat_id'
);

SET @sql := IF(
	@has_subsubcat = 0,
	'ALTER TABLE `ipmc_competitions` ADD COLUMN `subsubcat_id` int NOT NULL DEFAULT 0 AFTER `subcat_id`',
	'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `adb_competcats`
	(`id`, `type`, `group_id`, `key`, `title_lt`, `title_en`, `title_ru`, `color`, `svgparams`, `insert_time`, `update_time`)
VALUES
	(36, 'subcat', 0, 'saviraiska', 'Saviraiška', 'Self-expression', 'Самовыражение', '#c7a27a', '', NOW(), NOW()),
	(37, 'subcat', 0, 'profesine', 'Profesinė', 'Professional', 'Профессиональная', '#8ba89a', '', NOW(), NOW())
ON DUPLICATE KEY UPDATE
	`type` = VALUES(`type`),
	`group_id` = VALUES(`group_id`),
	`key` = VALUES(`key`),
	`title_lt` = VALUES(`title_lt`),
	`title_en` = VALUES(`title_en`),
	`title_ru` = VALUES(`title_ru`),
	`color` = VALUES(`color`),
	`svgparams` = VALUES(`svgparams`),
	`update_time` = NOW();

UPDATE `ipmc_competitions`
SET
	`subcat_id` = CASE
		WHEN `id` IN (151,152,153,154,155,163) THEN 36
		WHEN `id` IN (156,157,158,160,161,162) THEN 37
		ELSE `subcat_id`
	END,
	`subsubcat_id` = CASE
		WHEN `id` IN (152,155,158,162) THEN 12
		WHEN `id` IN (151,154,157,161) THEN 13
		WHEN `id` IN (153,156,160,163) THEN 14
		ELSE `subsubcat_id`
	END
WHERE `id` IN (151,152,153,154,155,156,157,158,160,161,162,163);
