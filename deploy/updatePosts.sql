select replace (post_content,'http://localhost:8080/wordpress','')
from wp_posts
where post_content like  '%http://localhost:8080/wordpress%';

update wp_posts
set post_content = replace (post_content,'http://localhost:8080/wordpress','')
where post_content like  '%http://localhost:8080/wordpress%'

update wp_posts
set guid = concat ('http://www.shopkatapult.com/',guid)
where post_type = 'attachment';

update  `wp_posts`
set post_content = replace(post_content , 'oscProductListing','oscReleaseListing')
where post_content like '%oscProductListing%';
