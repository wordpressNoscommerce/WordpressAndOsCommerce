TRUNCATE wordpress.wp_oscommerce;
-- set shopdb connection
INSERT into wordpress.wp_oscommerce 
( vchShopName, vchUrl, vchUsername, vchPassword, vchDbName, vchHost)
VALUES 
('shopkatapult', 'www.shopkatapult.com', 'shopdb', 'shopdb', 'shopdb', 'localhost');


UPDATE  wordpress.wp_options SET  option_value =  'http://dev1.shitkatapult.com/' 
WHERE  option_name = 'siteurl';
UPDATE  wordpress.wp_options SET  option_value =  'http://dev1.shitkatapult.com/' 
WHERE  option_name = 'home';


