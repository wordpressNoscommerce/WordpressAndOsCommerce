SELECT * FROM wp_terms w;


SELECT * FROM wp_term_taxonomy w;


SELECT * FROM wp_term_relationships w;


-- get posts which have category
select * from wp_posts wp
join wp_term_relationships wtr on wtr.object_id = wp.id -- and wtr.term_taxonomy_id = 3
join wp_term_taxonomy wtt USING (term_taxonomy_id)
where wp.post_type = 'post' and wtt.taxonomy = 'category';

-- select the term for NEWS category
SELECT *
FROM wp_term_relationships wtr
JOIN wp_term_taxonomy wtt USING (term_taxonomy_id)
JOIN wp_terms wt USING (term_id)
where wtt.taxonomy = 'category'
and wt.name = 'News';


-- select records to be inserted into wp_term_relationships
-- AND INSERT
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
)