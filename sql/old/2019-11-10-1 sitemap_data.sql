#cleans non existing pages contents
DELETE a FROM  gw_sitemap_data AS a
LEFT JOIN gw_sitemap AS b ON a.page_id=b.id WHERE b.insert_time IS NULL;

#add index important will create duplicates
ALTER TABLE `gw_sitemap_data` ADD UNIQUE( `ln`, `key`, `page_id`);





#rodyt dublikatus
#SELECT a.*, b.insert_time
#FROM  gw_sitemap_data AS a
#LEFT JOIN gw_sitemap AS b ON a.page_id=b.id WHERE b.insert_time IS NULL

#rodyt dublikatus
#SELECT DISTINCT a.id, a.* FROM gw_sitemap_data AS a, gw_sitemap_data AS b WHERE a.ln=b.ln AND b.key=a.key AND a.page_id = b.page_id AND a.id!=b.id ORDER BY a.page_id, a.ln, a.`key`, a.update_time
