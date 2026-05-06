-- nacionalinis.dvarionas.link / b/ branch
-- Switch active subsite template vars from XXIII Dvarionas competition ids
-- to XXIV Dvarionas competition ids.
--
-- Mapping:
-- pianopro:   85,86,87   -> 156,157,158
-- stringspro: 88,89,90   -> 160,161,162
-- pianosav:   91,92,93   -> 153,154,155
-- stringssav: 94,95,96   -> 163,151,152

UPDATE gw_sitemap_data SET content='["156"]' WHERE page_id=2588 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["157"]' WHERE page_id=2589 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["158"]' WHERE page_id=2590 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["156","157","158"]' WHERE page_id=2592 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["158","157","156"]' WHERE page_id=2593 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='156' WHERE page_id=2595 AND ln='' AND `key`='competition_id';

UPDATE gw_sitemap_data SET content='["160","161","162"]' WHERE page_id=2598 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["160"]' WHERE page_id=2600 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["161"]' WHERE page_id=2601 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["162"]' WHERE page_id=2602 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["160","161","162"]' WHERE page_id=2603 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='160' WHERE page_id=2606 AND ln='' AND `key`='competition_id';

UPDATE gw_sitemap_data SET content='["153","154"]' WHERE page_id=2566 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["154"]' WHERE page_id=2565 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["155"]' WHERE page_id=2564 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["153","154","155"]' WHERE page_id=2571 AND ln='' AND `key`='contests';

UPDATE gw_sitemap_data SET content='["163"]' WHERE page_id=2610 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["151"]' WHERE page_id=2612 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["152"]' WHERE page_id=2611 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["163","151","152"]' WHERE page_id=2614 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='["163","152","151"]' WHERE page_id=2615 AND ln='' AND `key`='contests';
UPDATE gw_sitemap_data SET content='163' WHERE page_id=2617 AND ln='' AND `key`='competition_id';

-- Shared jury helper pages under b/jury/*
UPDATE gw_sitemap_data SET content='155' WHERE page_id=1712 AND ln='' AND `key`='competition_id';
UPDATE gw_sitemap_data SET content='156' WHERE page_id=1714 AND ln='' AND `key`='competition_id';
UPDATE gw_sitemap_data SET content='163' WHERE page_id=1719 AND ln='' AND `key`='competition_id';
UPDATE gw_sitemap_data SET content='160' WHERE page_id=1713 AND ln='' AND `key`='competition_id';

-- Main b/index shortcuts
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=85', 'competition_id=156') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=86', 'competition_id=157') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=87', 'competition_id=158') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=88', 'competition_id=160') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=89', 'competition_id=161') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=90', 'competition_id=162') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=91', 'competition_id=153') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=92', 'competition_id=154') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=93', 'competition_id=155') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=94', 'competition_id=163') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=95', 'competition_id=151') WHERE page_id=2561 AND ln='' AND `key`='code';
UPDATE gw_sitemap_data SET content=REPLACE(content, 'competition_id=96', 'competition_id=152') WHERE page_id=2561 AND ln='' AND `key`='code';

-- Active b/*/schedule pages
-- old performance schedule ids:
-- pianopro   343,345,347 -> 574,577,580
-- pianosav   356,358,360 -> 566,569,572
-- stringspro 350,352,354 -> 587,590,593
-- stringssav 362,395,398 -> 596,560,563
UPDATE gw_sitemap_data SET content='["574","577","580"]' WHERE page_id=2591 AND ln='' AND `key`='ids2';
UPDATE gw_sitemap_data SET content='566,569,572' WHERE page_id=2568 AND ln='' AND `key`='ids';
UPDATE gw_sitemap_data SET content='["587","590","593"]' WHERE page_id=2599 AND ln='' AND `key`='ids2';
UPDATE gw_sitemap_data SET content='["596","560","563"]' WHERE page_id=2613 AND ln='' AND `key`='ids2';

REPLACE INTO gw_translations (`module`,`key`,`value_lt`,`value_ru`,`value_en`,`priority`,`insert_time`)
VALUES ('M/competitions','SCHEDULES_NOT_PUBLISHED_YET','Tvarkaraštis dar nepaskelbtas.','Расписание еще не опубликовано.','The schedule has not been published yet.','0',NOW());
