UPDATE `gw_form_elements`
SET `config`='{"default_value_src":"user_title"}'
WHERE `owner_id`=31 AND `fieldname`='client_title';

UPDATE `gw_form_elements`
SET `config`='{"default_value_src":"user_phone"}'
WHERE `owner_id`=31 AND `fieldname`='client_phone';

UPDATE `gw_form_elements`
SET `config`='{"default_value_src":"user_email"}'
WHERE `owner_id`=31 AND `fieldname`='client_email';
