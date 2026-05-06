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
SET `subsubcat_id` = CASE
	WHEN `subsubcat_id` <> 0 THEN `subsubcat_id`
	WHEN `subcat_id` NOT IN (36, 37) THEN `subcat_id`
	ELSE `subsubcat_id`
END
WHERE
	(`title_lt` LIKE '%Saviraiška%' OR `short_title_lt` LIKE '%Saviraiška%' OR
	 `title_lt` LIKE '%Profesinė%' OR `short_title_lt` LIKE '%Profesinė%' OR `short_title_lt` LIKE '%PRO%' OR
	 `title_en` LIKE '%Self-expression%' OR `short_title_en` LIKE '%Self-expression%' OR
	 `title_en` LIKE '%Professional%' OR `short_title_en` LIKE '%Professional%' OR `short_title_en` LIKE '%PRO%' OR
	 `title_ru` LIKE '%Самовыражен%' OR `short_title_ru` LIKE '%Самовыражен%' OR
	 `title_ru` LIKE '%Профессион%' OR `short_title_ru` LIKE '%Профессион%' OR `short_title_ru` LIKE '%PRO%');

UPDATE `ipmc_competitions`
SET `subcat_id` = CASE
	WHEN
		`title_lt` LIKE '%Saviraiška%' OR `short_title_lt` LIKE '%Saviraiška%' OR
		`title_en` LIKE '%Self-expression%' OR `short_title_en` LIKE '%Self-expression%' OR
		`title_ru` LIKE '%Самовыражен%' OR `short_title_ru` LIKE '%Самовыражен%'
	THEN 36
	WHEN
		`title_lt` LIKE '%Profesinė%' OR `short_title_lt` LIKE '%Profesinė%' OR `short_title_lt` LIKE '%PRO%' OR
		`title_en` LIKE '%Professional%' OR `short_title_en` LIKE '%Professional%' OR `short_title_en` LIKE '%PRO%' OR
		`title_ru` LIKE '%Профессион%' OR `short_title_ru` LIKE '%Профессион%' OR `short_title_ru` LIKE '%PRO%'
	THEN 37
	ELSE `subcat_id`
END
WHERE
	`title_lt` LIKE '%Saviraiška%' OR `short_title_lt` LIKE '%Saviraiška%' OR
	`title_lt` LIKE '%Profesinė%' OR `short_title_lt` LIKE '%Profesinė%' OR `short_title_lt` LIKE '%PRO%' OR
	`title_en` LIKE '%Self-expression%' OR `short_title_en` LIKE '%Self-expression%' OR
	`title_en` LIKE '%Professional%' OR `short_title_en` LIKE '%Professional%' OR `short_title_en` LIKE '%PRO%' OR
	`title_ru` LIKE '%Самовыражен%' OR `short_title_ru` LIKE '%Самовыражен%' OR
	`title_ru` LIKE '%Профессион%' OR `short_title_ru` LIKE '%Профессион%' OR `short_title_ru` LIKE '%PRO%';
