ALTER TABLE  `ipmc_composers` ADD  `title_lt` VARCHAR( 100 ) NOT NULL AFTER  `title` ;
ALTER TABLE  `ipmc_composers` ADD  `title_en` VARCHAR( 100 ) NOT NULL AFTER  `title_lt` ;
ALTER TABLE  `ipmc_composers` ADD  `title_ru` VARCHAR( 100 ) NOT NULL AFTER  `title_en` ;


ALTER TABLE  `ipmc_composers` ADD  `short_title_lt` VARCHAR( 40 ) NOT NULL AFTER  `title_ru` ;
ALTER TABLE  `ipmc_composers` ADD  `short_title_en` VARCHAR( 40 ) NOT NULL AFTER  `short_title_lt` ;
ALTER TABLE  `ipmc_composers` ADD  `short_title_ru` VARCHAR( 40 ) NOT NULL AFTER  `short_title_en` ;

SET sql_mode =  '';

UPDATE ipmc_composers SET title_lt=title, title_en=title, title_ru=title, short_title_en=title, short_title_ru=title, short_title_lt=title;

ALTER TABLE  `ipmc_composers` ADD  `approved` TINYINT NULL AFTER  `short_title_ru` ;
