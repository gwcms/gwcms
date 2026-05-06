INSERT INTO `gw_templates`
	(`title`, `path`, `active`, `access`, `insert_time`, `update_time`)
SELECT
	'Konkurso kontaktai',
	'competitions/competitions/contacts',
	1,
	11,
	NOW(),
	NOW()
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM `gw_templates`
	WHERE `path` = 'competitions/competitions/contacts'
);

SET @contacts_template_id := (
	SELECT `id` FROM `gw_templates`
	WHERE `path` = 'competitions/competitions/contacts'
	LIMIT 1
);

INSERT INTO `gw_template_vars`
	(`template_id`, `name`, `title`, `type`, `note`, `params`, `multilang`, `access`, `priority`, `insert_time`, `update_time`)
SELECT
	@contacts_template_id,
	'contact_ids',
	'Kontaktai',
	'multiselect_ajax',
	'Pasirinkti vartotojai rodomi tokia tvarka, kokia sudeliota siame lauke.',
	'{"modpath":"users/usr","preload":1,"options":[],"sorting":1,"value_format":"json1"}',
	0,
	11,
	10,
	NOW(),
	NOW()
FROM DUAL
WHERE @contacts_template_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1 FROM `gw_template_vars`
		WHERE `template_id` = @contacts_template_id AND `name` = 'contact_ids'
	);

INSERT INTO `gw_template_vars`
	(`template_id`, `name`, `title`, `type`, `note`, `params`, `multilang`, `access`, `priority`, `insert_time`, `update_time`)
SELECT
	@contacts_template_id,
	'contact_roles',
	'Kontaktu roles',
	'code_json',
	'JSON objektas pagal user ID, pvz: {"123":"secretary","456":"coordinator"}. Reiksmes yra competition_contact_roles classificator key.',
	'{"height":"120px"}',
	0,
	11,
	20,
	NOW(),
	NOW()
FROM DUAL
WHERE @contacts_template_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1 FROM `gw_template_vars`
		WHERE `template_id` = @contacts_template_id AND `name` = 'contact_roles'
	);

INSERT INTO `gw_template_vars`
	(`template_id`, `name`, `title`, `type`, `note`, `params`, `multilang`, `access`, `priority`, `insert_time`, `update_time`)
SELECT
	@contacts_template_id,
	'infotext',
	'Informacinis tekstas',
	'htmlarea',
	'',
	'[]',
	1,
	11,
	30,
	NOW(),
	NOW()
FROM DUAL
WHERE @contacts_template_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1 FROM `gw_template_vars`
		WHERE `template_id` = @contacts_template_id AND `name` = 'infotext'
	);

INSERT INTO `gw_template_vars`
	(`template_id`, `name`, `title`, `type`, `note`, `params`, `multilang`, `access`, `priority`, `insert_time`, `update_time`)
SELECT
	@contacts_template_id,
	'listfullinfo',
	'Rodyti aprasymus sarase',
	'bool',
	'Viename puslapyje rodoma visa info, nuorodos i vidu isjungiamos.',
	'[]',
	0,
	11,
	40,
	NOW(),
	NOW()
FROM DUAL
WHERE @contacts_template_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1 FROM `gw_template_vars`
		WHERE `template_id` = @contacts_template_id AND `name` = 'listfullinfo'
	);

UPDATE `gw_template_vars`
SET `priority` = CASE `name`
	WHEN 'contact_ids' THEN 10
	WHEN 'contact_roles' THEN 20
	WHEN 'infotext' THEN 30
	WHEN 'listfullinfo' THEN 40
	ELSE `priority`
END
WHERE `template_id` = @contacts_template_id
	AND `name` IN ('contact_ids', 'contact_roles', 'infotext', 'listfullinfo');

UPDATE `gw_template_vars`
SET
	`title` = 'Kontaktu roles',
	`type` = 'code_json',
	`note` = 'JSON objektas pagal user ID, pvz: {"123":"secretary","456":"coordinator"}. Reiksmes yra competition_contact_roles classificator key.',
	`params` = '{"height":"120px"}'
WHERE `template_id` = @contacts_template_id
	AND `name` = 'contact_roles';

INSERT INTO `gw_classificator_types`
	(`key`, `title`, `aka`, `count`, `insert_time`, `update_time`)
SELECT
	'competition_contact_roles',
	'Konkurso kontaktu roles',
	'',
	0,
	NOW(),
	NOW()
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM `gw_classificator_types`
	WHERE `key` = 'competition_contact_roles'
);

SET @contact_roles_type_id := (
	SELECT `id` FROM `gw_classificator_types`
	WHERE `key` = 'competition_contact_roles'
	LIMIT 1
);

INSERT INTO `gw_classificators`
	(`key`, `type`, `title_lt`, `title_ru`, `title_en`, `aka`, `count`, `priority`, `active`, `user_id`, `insert_time`, `update_time`)
SELECT 'secretary', @contact_roles_type_id, 'Sekretorius', 'Secretary', 'Secretary', '', 0, 10, 1, 0, NOW(), NOW()
FROM DUAL
WHERE @contact_roles_type_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1 FROM `gw_classificators`
		WHERE `type` = @contact_roles_type_id AND `key` = 'secretary'
	);

INSERT INTO `gw_classificators`
	(`key`, `type`, `title_lt`, `title_ru`, `title_en`, `aka`, `count`, `priority`, `active`, `user_id`, `insert_time`, `update_time`)
SELECT 'organizer', @contact_roles_type_id, 'Organizatorius', 'Organizer', 'Organizer', '', 0, 20, 1, 0, NOW(), NOW()
FROM DUAL
WHERE @contact_roles_type_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1 FROM `gw_classificators`
		WHERE `type` = @contact_roles_type_id AND `key` = 'organizer'
	);

INSERT INTO `gw_classificators`
	(`key`, `type`, `title_lt`, `title_ru`, `title_en`, `aka`, `count`, `priority`, `active`, `user_id`, `insert_time`, `update_time`)
SELECT 'coordinator', @contact_roles_type_id, 'Koordinatorius', 'Coordinator', 'Coordinator', '', 0, 30, 1, 0, NOW(), NOW()
FROM DUAL
WHERE @contact_roles_type_id IS NOT NULL
	AND NOT EXISTS (
		SELECT 1 FROM `gw_classificators`
		WHERE `type` = @contact_roles_type_id AND `key` = 'coordinator'
	);
