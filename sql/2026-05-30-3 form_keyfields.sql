INSERT INTO `gw_generic_extended` (`own_table`, `owner_id`, `key`, `value`, `insert_time`)
SELECT 'gw_forms', 31, 'keyfields', '["child_title"]', NOW()
WHERE NOT EXISTS (
	SELECT 1 FROM `gw_generic_extended`
	WHERE `own_table`='gw_forms' AND `owner_id`=31 AND `key`='keyfields'
);

UPDATE `gw_generic_extended`
SET `value`='["child_title"]'
WHERE `own_table`='gw_forms' AND `owner_id`=31 AND `key`='keyfields';
