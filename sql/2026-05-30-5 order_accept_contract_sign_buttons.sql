UPDATE `gw_mail_templates`
SET `body_lt` = REPLACE(
	`body_lt`,
	'<p>Užsakymo identifikacija: <b>{$SECRET}</b></p>',
	'<p>Užsakymo identifikacija: <b>{$SECRET}</b></p>

{$CONTRACT_SIGN_BUTTONS}'
)
WHERE `id` = 48
	AND `body_lt` NOT LIKE '%{$CONTRACT_SIGN_BUTTONS}%';
