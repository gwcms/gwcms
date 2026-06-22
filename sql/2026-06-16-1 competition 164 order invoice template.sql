SET @competition_id := 164;
SET @invoice_tpl_id := 584;

CREATE TEMPORARY TABLE tmp_competition_order_invoice_tpl (
	`order_id` int NOT NULL,
	PRIMARY KEY (`order_id`)
) ENGINE=Memory;

INSERT IGNORE INTO tmp_competition_order_invoice_tpl (`order_id`)
SELECT DISTINCT oi.group_id
FROM gw_order_item AS oi
JOIN adb_participants AS p ON p.id = oi.obj_id
JOIN gw_order_group AS og ON og.id = oi.group_id
WHERE oi.obj_type = 'adb_participants'
	AND p.competition_id = @competition_id
	AND oi.group_id > 0;

INSERT IGNORE INTO tmp_competition_order_invoice_tpl (`order_id`)
SELECT DISTINCT CAST(jt.order_id AS UNSIGNED)
FROM adb_participants AS p
JOIN JSON_TABLE(
	CASE WHEN JSON_VALID(p.order_ids) THEN CAST(p.order_ids AS JSON) ELSE JSON_ARRAY() END,
	'$[*]' COLUMNS(order_id int PATH '$')
) AS jt
JOIN gw_order_group AS og ON og.id = CAST(jt.order_id AS UNSIGNED)
WHERE p.competition_id = @competition_id
	AND CAST(jt.order_id AS UNSIGNED) > 0;

INSERT IGNORE INTO tmp_competition_order_invoice_tpl (`order_id`)
SELECT DISTINCT CAST(jt.order_id AS UNSIGNED)
FROM adb_participants AS p
JOIN adb_participants_extended AS pe ON pe.owner_id = p.id AND pe.`key` = 'order_ids'
JOIN JSON_TABLE(
	CASE WHEN JSON_VALID(pe.`value`) THEN CAST(pe.`value` AS JSON) ELSE JSON_ARRAY() END,
	'$[*]' COLUMNS(order_id int PATH '$')
) AS jt
JOIN gw_order_group AS og ON og.id = CAST(jt.order_id AS UNSIGNED)
WHERE p.competition_id = @competition_id
	AND CAST(jt.order_id AS UNSIGNED) > 0;

UPDATE gw_generic_extended AS ge
JOIN tmp_competition_order_invoice_tpl AS oi ON oi.order_id = ge.owner_id
SET ge.`value` = @invoice_tpl_id,
	ge.update_time = NOW()
WHERE ge.own_table = 'gw_order_group'
	AND ge.`key` = 'invoice_tpl_id';

INSERT INTO gw_generic_extended (`own_table`, `owner_id`, `key`, `value`, `insert_time`)
SELECT 'gw_order_group', oi.order_id, 'invoice_tpl_id', @invoice_tpl_id, NOW()
FROM tmp_competition_order_invoice_tpl AS oi
WHERE NOT EXISTS (
	SELECT 1
	FROM gw_generic_extended AS ge
	WHERE ge.own_table = 'gw_order_group'
		AND ge.owner_id = oi.order_id
		AND ge.`key` = 'invoice_tpl_id'
);

DROP TEMPORARY TABLE tmp_competition_order_invoice_tpl;
