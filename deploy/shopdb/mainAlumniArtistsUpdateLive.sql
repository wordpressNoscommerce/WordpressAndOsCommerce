use ShopDB;

-- reentrant updates!!!

update ShopDB.manufacturers m
set m.manufacturers_label = concat(m.manufacturers_label,'|244')
where m.manufacturers_label not like '%244%'
AND m.manufacturers_id in 
(12, 14, 20, 22, 26, 30, 33, 69, 74, 77, 79, 82, 86, 178, 240, 254, 292, 298);

-- also with mains
update ShopDB.manufacturers m
set m.manufacturers_label = concat(m.manufacturers_label,'|245')
where m.manufacturers_label not like '%245%'
AND m.manufacturers_id in 
(10, 16, 24, 28, 74, 215, 239, 308, 322, 328, 350, 352, 362);


INSERT INTO `categories` 
(`categories_id`,`categories_image`,`parent_id`,`sort_order`,`date_added`,`last_modified`) 
VALUES 
 (244,'',0,1000,'2012-05-14 17:30:37',NULL),
 (245,'',0,1001,'2012-05-14 17:31:14',NULL);

INSERT INTO `categories_description` 
(`categories_id`,`language_id`,`categories_name`,`categories_description`,`categories_htc_title_tag`,`categories_htc_desc_tag`,`categories_htc_keywords_tag`,`categories_htc_description`) 
VALUES 
 (244,2,'Main','Main shitkatapult artists','Main','Main','Main',''),
 (245,2,'Alumni','shitkatapult Alumni Artists','Alumni','Alumni','Alumni','');
 