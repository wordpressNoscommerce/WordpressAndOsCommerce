-- get News posts
-- select * from wp_posts wp
-- join wp_term_relationships wtr on wtr.object_id = wp.id -- and wtr.term_taxonomy_id = 3
-- join wp_term_taxonomy wtt USING (term_taxonomy_id)
-- where wp.post_type = 'post' and wtt.taxonomy = 'category' and wtt.term_id = 4;    -- which is NEWS

-- ####################################################################################
-- REMOVE RECORDS
-- ####################################################################################
-- TODO consider using the post_parent relation for the attachments

-- the postmeta of the POST attachments
DELETE  pmdel.*
FROM wordpress.wp_postmeta pmdel, wordpress.wp_postmeta pm
JOIN wordpress.wp_term_relationships wtr ON wtr.object_id = pm.post_id
JOIN wordpress.wp_term_taxonomy wtt USING (term_taxonomy_id)
where wtt.taxonomy = 'category' and wtt.term_id = 4     -- which is NEWS
AND pm.meta_key = '_thumbnail_id'				-- the post attachment link
AND pmdel.post_id = pm.meta_value;    -- select the id of the attachments from postmeta

-- the attachment POSTs
DELETE  pmdel.*
FROM wordpress.wp_posts pmdel, wordpress.wp_postmeta pm
JOIN wordpress.wp_term_relationships wtr ON wtr.object_id = pm.post_id
JOIN wordpress.wp_term_taxonomy wtt USING (term_taxonomy_id)
where wtt.taxonomy = 'category' and wtt.term_id = 4     -- which is NEWS
AND pm.meta_key = '_thumbnail_id'				-- the post attachment link
AND pmdel.ID = pm.meta_value;    -- select the id of the attachments from postmeta

-- the postmeta of the POSTS
delete from wp_postmeta
where post_id in 	(select object_id
					 from wp_term_relationships wtr
					 join wp_term_taxonomy wtt USING (term_taxonomy_id)
					 where wtt.taxonomy = 'category' and wtt.term_id = 4
					);    -- which is NEWS

-- finally the POSTS
delete from wp_posts
where post_type = 'post'and id in
					(select object_id
					from wp_term_relationships wtr
					join wp_term_taxonomy wtt USING (term_taxonomy_id)
					where wtt.taxonomy = 'category' and wtt.term_id = 4 		-- which is NEWS
					);


-- delete the term relations also
DELETE FROM t1 USING wp_term_relationships AS t1 INNER JOIN wp_term_taxonomy AS t2
WHERE t1.term_taxonomy_id = t2.term_taxonomy_id
and t2.taxonomy = 'category' and t2.term_id = 4;


-- ####################################################################################
-- NEW RECORDS
-- ####################################################################################

-- SELECT
-- ID, post_author, post_date, post_date_gmt,
-- post_content, post_title, post_excerpt, post_status,
-- comment_status, ping_status, post_password, post_name, to_ping, pinged,
-- post_modified, post_modified_gmt, post_content_filtered, post_parent,
-- guid, menu_order, post_type, post_mime_type, comment_count
-- FROM wordpress.wp_posts w;

-- first batch of transfers doing the news...
-- ####################################################################################
INSERT INTO wordpress.wp_posts
SELECT
	null ID,
	1 post_author,
	adddate(n.news_date_added, interval 1 year) post_date,
	adddate(n.news_date_added, interval 1 year) post_date_gmt,
	IF (LENGTH(n.news_description) = 0, n.news_subtitel, n.news_description) post_content,
	n.news_titel post_title,
	n.news_subtitel post_excerpt,
	'publish' post_status,
	'open' comment_status,
	'open' ping_status,
	'' post_password,
	'post_name' post_name,
	'' to_ping,
	'' pinged,
	if (n.news_last_modified != 0, adddate(n.news_last_modified, interval 1 year),'0000-00-00') post_modified,
	if (n.news_last_modified != 0, adddate(n.news_last_modified, interval 1 year),'0000-00-00') post_modified_gmt,
	'' post_content_filtered,
	0 post_parent,
	n.news_link guid,				--  put link into guid for now ...
	n.news_id menu_order,
	'post' post_type,
	'' post_mime_type,
	0 comment_count
FROM shopdb.news n;

-- ####################################################################################
-- set posts into News category by inserting records into wp_term_relationships
insert into wp_term_relationships
select ID object_id,
  (SELECT term_taxonomy_id
  FROM wp_terms wt
  JOIN wp_term_taxonomy wtt USING (term_id)
  where wtt.taxonomy = 'category' and name = 'News'),
  0
from wp_posts wp0
where post_type = 'post' and post_status = 'publish'
and not exists (
select 1
from wp_term_relationships wtr
join wp_posts wp on wtr.object_id = wp.id
JOIN wp_term_taxonomy wtt USING (term_taxonomy_id)
where wtt.taxonomy = 'category' AND wp0.id = wtr.object_id
);

-- ####################################################################################
-- DEAL WITH SLIDESHOW OPTIONS
-- SELECT w.`meta_id`, w.`post_id`, w.`meta_key`, w.`meta_value` FROM wp_postmeta w;

INSERT INTO wp_postmeta(post_id, meta_key, meta_value)
SELECT ID post_id, 'sgt_slide' meta_key, 'on' meta_value
FROM wp_posts wp
join wp_term_relationships wtr on wtr.object_id = wp.id -- and wtr.term_taxonomy_id = 3
join wp_term_taxonomy wtt USING (term_taxonomy_id)
where wp.post_type = 'post' and wtt.taxonomy = 'category' and wtt.term_id = 4;    -- which is NEWS


-- ####################################################################################
-- CREATE FEATURED IMAGE FOR POSTS
INSERT INTO wordpress.wp_posts
SELECT
	null ID,
	1 post_author,
	adddate(n.news_date_added, interval 1 year) post_date,
	adddate(n.news_date_added, interval 1 year) post_date_gmt,
	'' post_content,
	n.news_image post_title,
	'' post_excerpt,
	'inherit' post_status,
	'open' comment_status,
	'open' ping_status,
	'' post_password,
	SUBSTR(n.news_image,0,20) post_name,
	'' to_ping,
	'' pinged,
	if (n.news_last_modified != 0, adddate(n.news_last_modified, interval 1 year),'0000-00-00') post_modified,
	if (n.news_last_modified != 0, adddate(n.news_last_modified, interval 1 year),'0000-00-00') post_modified_gmt,
	'' post_content_filtered,
	post.ID post_parent,			-- this has to point to the related post
	n.news_image guid,
	n.news_id menu_order,
	'attachment' post_type,
	'image/jpeg' post_mime_type,
	0 comment_count
FROM shopdb.news n
LEFT JOIN wordpress.wp_posts post ON post.menu_order = n.news_id
WHERE n.news_image <> '' AND post.post_type = 'post';

-- create meta entries
-- the file link TODO needs some fixing
INSERT INTO wp_postmeta(post_id, meta_key, meta_value)
SELECT ID post_id, '_wp_attached_file' meta_key, post_title meta_value
FROM wordpress.wp_posts
WHERE post_type = 'attachment' and post_mime_type = 'image/jpeg';

-- the metadata TODO IS IT REQUIRED?????
-- INSERT INTO wp_postmeta(post_id, meta_key, meta_value)
-- SELECT ID post_id, '_wp_attachment_metadata' meta_key, 'a:6:{s:5:"width";s:3:"236";s:6:"height";s:3:"236";s:14:"hwstring_small";s:22:"height='96' width='96'";s:4:"file";s:30:"2010/01/cover_strike100_gr.jpg";s:5:"sizes";a:1:{s:14:"mini-thumbnail";a:3:{s:4:"file";s:28:"cover_strike100_gr-50x50.jpg";s:5:"width";s:2:"50";s:6:"height";s:2:"50";}}s:10:"image_meta";a:10:{s:8:"aperture";s:1:"0";s:6:"credit";s:6:"bianca";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:26:"Shit_100_Cover_5Spine.indd";}}' meta_value
-- FROM wordpress.wp_posts
-- WHERE post_type = 'attachment' and post_mime_type = 'image/jpeg';

-- the link from post to attachement can be created from the attachment record
INSERT INTO wp_postmeta(post_id, meta_key, meta_value)
SELECT wp.post_parent post_id, '_thumbnail_id' meta_key, wp.ID meta_value
FROM wordpress.wp_posts wp
WHERE wp.post_type = 'attachment' and wp.post_mime_type = 'image/jpeg' AND wp.post_parent <> '';

