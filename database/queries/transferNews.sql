-- get News posts
select * from wp_posts wp
join wp_term_relationships wtr on wtr.object_id = wp.id -- and wtr.term_taxonomy_id = 3
join wp_term_taxonomy wtt USING (term_taxonomy_id)
where wp.post_type = 'post' and wtt.taxonomy = 'category' and wtt.term_id = 4;    -- which is NEWS


delete from wp_posts
where post_type = 'post'and id in
(select object_id
from wp_term_relationships wtr
join wp_term_taxonomy wtt USING (term_taxonomy_id)
where wtt.taxonomy = 'category' and wtt.term_id = 4
);    -- which is NEWS


-- delete the term relations also
DELETE FROM t1 USING wp_term_relationships AS t1 INNER JOIN wp_term_taxonomy AS t2
WHERE t1.term_taxonomy_id = t2.term_taxonomy_id
and t2.taxonomy = 'category' and t2.term_id = 4;


-- SELECT
-- ID, post_author, post_date, post_date_gmt,
-- post_content, post_title, post_excerpt, post_status,
-- comment_status, ping_status, post_password, post_name, to_ping, pinged,
-- post_modified, post_modified_gmt, post_content_filtered, post_parent,
-- guid, menu_order, post_type, post_mime_type, comment_count
-- FROM wordpress.wp_posts w;

-- first batch of transfers doing the news...
-- TODO transfer links and images as additional posts

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
'' guid,
n.news_id menu_order,
'post' post_type,
'' post_mime_type,
0 comment_count
FROM shopdb.news n;

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