update shopdb.manufacturers m
join artistupdate a using (manufacturers_id)
set m.manufacturers_label = concat(m.manufacturers_label,'|main')
where a.artist_set = 'M';# 13 row(s) affected.


update shopdb.manufacturers m
join artistupdate a using (manufacturers_id)
set m.manufacturers_label = concat(m.manufacturers_label,'|alumni')
where a.artist_set = 'A';# 17 row(s) affected.

-- set soundcloud id
update products p
set products_isrc = '42010527'
where products_parent = ''
and products_isrc = ''
and manufacturers_id = 16;

-- set youtube id
update products p
set p.products_upc = 'e9yZ-WTY8Yg'
where p.manufacturers_id = 10
and p.products_upc = ''
and p.products_parent = '';