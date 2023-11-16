ALTER TABLE `gw_form_elements` CHANGE `type` `type` ENUM('file','text','textarea','number','radio','select','checkbox','birthdate','date','htmlarea','code_smarty','infotext') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


ALTER TABLE `gw_classificators` ADD `key` VARCHAR(50) NOT NULL AFTER `id`;

ALTER TABLE `gw_form_elements` CHANGE `note_lt` `note_lt` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_form_elements` CHANGE `note_en` `note_en` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `gw_form_elements` CHANGE `note_ru` `note_ru` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
