use shopdb;

drop table if exists artistUpdate;
create table if not exists artistUpdate
like manufacturers;

alter table artistUpdate
modify column manufacturers_id int(11),
add column dummy varchar (1),
add column artist_set varchar(1);

load data local infile "E:/Devel/wordpress/database/shopdb/manufacturersArtists2X.csv" 
into table artistUpdate 
fields terminated by ','
enclosed by '"'
lines terminated by '\n'
ignore 1 lines
(manufacturers_id,manufacturers_name,manufacturers_label,manufacturers_image,manufacturers_image_med,
manufacturers_press_image  ,date_added  ,last_modified, dummy, artist_set);

update shopdb.manufacturers m
join artistupdate a using (manufacturers_id)
set m.manufacturers_label = concat(m.manufacturers_label,'|main')
where a.artist_set = 'M';

update shopdb.manufacturers m
join artistupdate a using (manufacturers_id)
set m.manufacturers_label = concat(m.manufacturers_label,'|alumni')
where a.artist_set = 'A';
