select replace (post_content,'http://localhost:8080/wordpress','')
from wp_posts
where post_content like  '%http://localhost:8080/wordpress%';

update wp_posts
set post_content = replace (post_content,'http://localhost:8080/wordpress','')
where post_content like  '%http://localhost:8080/wordpress%'

update wp_posts
set guid = replace (guid,'http://192.168.99.100:8080/wordpress','')
where guid like  '%http://192.168.99.100:8080/wordpress%';

update  `wp_posts`
set post_content = replace(post_content , 'oscProductListing','oscReleaseListing')
where post_content like '%oscProductListing%'