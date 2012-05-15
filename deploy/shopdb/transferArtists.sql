truncate wordpress.wp_gigpress_artists;
insert into wordpress.wp_gigpress_artists
(SELECT manufacturers_id, manufacturers_name, 0 FROM ShopDB.manufacturers )
;
