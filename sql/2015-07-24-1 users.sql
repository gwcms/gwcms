INSERT INTO  `gw_users` (
`id` ,
`username` 
)
VALUES (
'1',  'system'
);

UPDATE  `gw_users` SET  `active` =  '1' WHERE  `gw_users`.`id` =1;

ALTER TABLE  `gw_users` ADD  `parent_user_id` INT NOT NULL AFTER  `id` ;