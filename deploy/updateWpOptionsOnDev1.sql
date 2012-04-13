TRUNCATE wordpress.wp_oscommerce;
-- set shopdb connection
INSERT into wordpress.wp_oscommerce
( vchShopName, vchUrl, vchUsername, vchPassword, vchDbName, vchHost)
VALUES
('shopkatapult', 'www.shopkatapult.com', 'RandomNoizeShop', 'shitthat', 'ShopDb', 'localhost');


-- SITEURLS
UPDATE  wordpress.wp_options SET  option_value =  'http://dev1.shitkatapult.com/'
WHERE  option_name = 'siteurl';
UPDATE  wordpress.wp_options SET  option_value =  'http://dev1.shitkatapult.com/'
WHERE  option_name = 'home';


-- fix links to images

UPDATE `wp_posts` SET
	post_title = replace (post_title, 'images/', 'shopkatapult/images/'),
	guid = replace (guid, 'images/', 'shopkatapult/images/')
where `post_type` = 'attachment'
AND post_title like 'images/%';
