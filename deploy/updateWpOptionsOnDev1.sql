TRUNCATE wordpress.wp_oscommerce;
-- set shopdb connection
INSERT into wordpress.wp_oscommerce
( vchShopName, vchUrl, vchUsername, vchPassword, vchDbName, vchHost)
VALUES
('shopkatapult', 'www.shopkatapult.com', 'RandomNoizeShop', 'shitthat', 'ShopDb', 'localhost');

-- delete old one
delete from wordpress.wp_users where user_login = 'admin';

-- create new admin user for us
insert into wordpress.wp_users(
user_login, user_pass, user_nicename, user_email, user_url,
user_registered, user_activation_key, display_name)
values (
'Admin', 'db03f4c1470155a5aca50c94e1d795dc:07', 'admin', 'admin@trash-mail.com', '',
curdate(), '','admin');

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
