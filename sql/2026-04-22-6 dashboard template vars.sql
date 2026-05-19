INSERT INTO `gw_template_vars`
	(`template_id`, `name`, `title`, `type`, `note`, `params`, `multilang`, `access`, `priority`, `insert_time`, `update_time`)
SELECT
	60,
	'dashboardblocks',
	'Dashboard blokai',
	'multiselect',
	'Galimi callable blokai: bystatus, byquestionnaire, byaccommodation, bycountry, bycategory, byregistrations, byorders',
	'{"options":{"statuses":"1. Dalyviu statusai","questionnaire":"2. Universaliojo klausimyno suvestine","accommodation":"3. Viesbuciu suvestine","country":"4. Dalyviai pagal salis","category":"5. Dalyviai pagal konkurso kategorija / konkursa","registrations":"6. Registraciju dinamika","orders":"7. Uzsakymu dinamika"},"value_format":"json1","options_fix":1}',
	0,
	11,
	10,
	NOW(),
	NOW()
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM `gw_template_vars`
	WHERE `template_id` = 60 AND `name` = 'dashboardblocks'
);

INSERT INTO `gw_template_vars`
	(`template_id`, `name`, `title`, `type`, `note`, `params`, `multilang`, `access`, `priority`, `insert_time`, `update_time`)
SELECT
	60,
	'dashboardcode',
	'Dashboard Smarty kodas',
	'code_smarty',
	'Jei uzpildyta, perima dashboard blokeliu renderinima. Galimos funkcijos: {call name=bystatus}, {call name=byquestionnaire}, {call name=byaccommodation}, {call name=bycountry}, {call name=bycategory}, {call name=byregistrations}, {call name=byorders}',
	'{"height":"260px","layout":"wide"}',
	0,
	11,
	11,
	NOW(),
	NOW()
FROM DUAL
WHERE NOT EXISTS (
	SELECT 1 FROM `gw_template_vars`
	WHERE `template_id` = 60 AND `name` = 'dashboardcode'
);
