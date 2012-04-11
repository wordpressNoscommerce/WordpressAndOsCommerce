TRUNCATE wordpress.wp_oscommerce;
-- set shopdb connection
INSERT into wordpress.wp_oscommerce ( vchShopName, vchUrl, vchUsername, vchPassword, vchDbName, vchHost)
VALUES ('shopkatapult', 'www.shopkatapult.com', 'shopdb', 'shopdb', 'shopdb', 'dbhost');

-- UPDATE  wordpress.wp_options SET  option_value =  '' WHERE  option_name = '';

UPDATE  wordpress.wp_options 
SET  option_value = '/wp-content/themes/sight/images/shitkatapult.png' WHERE  option_name = 'logourl';
UPDATE  wordpress.wp_options SET  option_value =  'New Shitkatapult using WordPress' WHERE  option_name = 'blogdescription';
UPDATE  wordpress.wp_options SET  option_value =  'Shitkatapult Relaunch' WHERE  option_name = 'blogname';
UPDATE  wordpress.wp_options SET  option_value =  '#ffffff' WHERE  option_name = 'bg_color';

UPDATE  wordpress.wp_options SET  option_value =  'Sight' WHERE  option_name = 'current_theme';
UPDATE  wordpress.wp_options SET  option_value =  'sight' WHERE  option_name = 'stylesheet';
UPDATE  wordpress.wp_options SET  option_value =  'sight' WHERE  option_name = 'template';

UPDATE  wordpress.wp_options SET  option_value =  '/%post_id%/' WHERE  option_name = 'permalink_structure';
UPDATE  wordpress.wp_options SET  option_value =  'ajax' WHERE  option_name = 'paging_mode';

UPDATE  wordpress.wp_options 
SET  option_value =  'a:48:{s:11:"enable_tabs";s:2:"on";s:14:"enable_widgets";s:2:"on";s:15:"selected_styles";s:0:"";s:10:"tab_scheme";s:10:"wpui-light";s:18:"jqui_custom_themes";s:102:"{"shit-theme":"http://mywebsite:8080/wp-content/uploads/wp-ui/shit-theme/jquery-ui-1.8.18.custom.css"}";s:10:"custom_css";s:0:"";s:12:"dialog_width";s:0:"";s:6:"tabsfx";s:9:"slideDown";s:8:"fx_speed";s:0:"";s:11:"tabs_rotate";s:7:"disable";s:10:"tabs_event";s:5:"click";s:12:"accord_event";s:5:"click";s:13:"accord_easing";s:5:"false";s:16:"mouse_wheel_tabs";s:5:"false";s:17:"tab_nav_prev_text";s:0:"";s:17:"tab_nav_next_text";s:0:"";s:17:"spoiler_show_text";s:0:"";s:17:"spoiler_hide_text";s:0:"";s:14:"excerpt_length";s:4:"more";s:11:"post_widget";a:4:{s:5:"title";s:0:"";s:4:"type";s:7:"popular";s:6:"number";s:0:"";s:7:"per_row";s:1:"2";}s:22:"post_default_thumbnail";a:3:{s:3:"url";s:0:"";s:5:"width";s:0:"";s:6:"height";s:0:"";}s:15:"post_template_1";s:337:"<h2 class="wpui-post-title">{$title}</h2>
<div class="wpui-post-meta">{$date} |  {$author}</div>
<div class="wpui-post-thumbnail">{$thumbnail}</div>
<div class="wpui-post-content">{$excerpt}</div>
<p class="wpui-readmore"><a class="ui-button ui-widget ui-corner-all" href="{$url}" title="Read more from {$title}">Read More...</a></p>";s:15:"post_template_2";s:239:"<div class="wpui-post-meta">{$date}</div>
<div class="wpui-post-thumbnail">{$thumbnail}</div>
<div class="wpui-post-content">{$excerpt}</div>
<p class="wpui-readmore"><a href="{$url}" title="Read more from {$title}">Read More...</a></p>";s:15:"jquery_disabled";s:2:"on";s:19:"script_conditionals";s:0:"";s:12:"docwrite_fix";s:2:"on";s:6:"submit";s:12:"Save Options";s:16:"enable_accordion";s:3:"off";s:15:"enable_spoilers";s:3:"off";s:14:"enable_dialogs";s:3:"off";s:17:"enable_pagination";s:3:"off";s:24:"enable_quicktags_buttons";s:3:"off";s:6:"topnav";s:3:"off";s:9:"bottomnav";s:3:"off";s:19:"enable_tinymce_menu";s:3:"off";s:18:"enable_post_widget";s:3:"off";s:12:"enable_cache";s:3:"off";s:15:"load_all_styles";s:3:"off";s:16:"collapsible_tabs";s:3:"off";s:17:"accord_autoheight";s:3:"off";s:18:"accord_collapsible";s:3:"off";s:13:"bleeding_edge";s:3:"off";s:6:"alt_sc";s:3:"off";s:22:"load_scripts_on_demand";s:3:"off";s:11:"use_cookies";s:3:"off";s:15:"linking_history";s:3:"off";s:14:"relative_times";s:3:"off";s:7:"version";s:5:"0.8.2";}'
-- 'a:48:{s:11:"enable_tabs";s:2:"on";s:14:"enable_widgets";s:2:"on";s:15:"selected_styles";s:0:"";s:10:"tab_scheme";s:10:"wpui-light";s:18:"jqui_custom_themes";s:0:"";s:10:"custom_css";s:0:"";s:12:"dialog_width";s:0:"";s:6:"tabsfx";s:9:"slideDown";s:8:"fx_speed";s:0:"";s:11:"tabs_rotate";s:7:"disable";s:10:"tabs_event";s:5:"click";s:12:"accord_event";s:5:"click";s:13:"accord_easing";s:5:"false";s:16:"mouse_wheel_tabs";s:5:"false";s:17:"tab_nav_prev_text";s:0:"";s:17:"tab_nav_next_text";s:0:"";s:17:"spoiler_show_text";s:0:"";s:17:"spoiler_hide_text";s:0:"";s:14:"excerpt_length";s:0:"";s:11:"post_widget";a:4:{s:5:"title";s:0:"";s:4:"type";s:7:"popular";s:6:"number";s:0:"";s:7:"per_row";s:1:"2";}s:22:"post_default_thumbnail";a:3:{s:3:"url";s:0:"";s:5:"width";s:0:"";s:6:"height";s:0:"";}s:15:"post_template_1";s:0:"";s:15:"post_template_2";s:0:"";s:15:"jquery_disabled";s:2:"on";s:19:"script_conditionals";s:0:"";s:12:"docwrite_fix";s:2:"on";s:6:"submit";s:12:"Save Options";s:16:"enable_accordion";s:3:"off";s:15:"enable_spoilers";s:3:"off";s:14:"enable_dialogs";s:3:"off";s:17:"enable_pagination";s:3:"off";s:24:"enable_quicktags_buttons";s:3:"off";s:6:"topnav";s:3:"off";s:9:"bottomnav";s:3:"off";s:19:"enable_tinymce_menu";s:3:"off";s:18:"enable_post_widget";s:3:"off";s:12:"enable_cache";s:3:"off";s:15:"load_all_styles";s:3:"off";s:16:"collapsible_tabs";s:3:"off";s:17:"accord_autoheight";s:3:"off";s:18:"accord_collapsible";s:3:"off";s:13:"bleeding_edge";s:3:"off";s:6:"alt_sc";s:3:"off";s:22:"load_scripts_on_demand";s:3:"off";s:11:"use_cookies";s:3:"off";s:15:"linking_history";s:3:"off";s:14:"relative_times";s:3:"off";s:7:"version";s:5:"0.8.2";}' 
WHERE  option_name = 'wpUI_options';

UPDATE  wordpress.wp_options 
SET  option_value =  'a:4:{s:19:"wp_inactive_widgets";a:0:{}s:9:"sidebar-1";a:0:{}s:9:"sidebar-2";a:3:{i:0;s:14:"getconnected-2";i:1;s:10:"gigpress-2";i:2;s:11:"tag_cloud-2";}s:13:"array_version";i:3;}' 
WHERE  option_name = 'sidebars_widgets';

UPDATE  wordpress.wp_options
SET  option_value =  'a:5:{i:0;s:21:"gigpress/gigpress.php";i:1;s:25:"oscommerce/osCommerce.php";i:2;s:37:"wordpress-database-reset/wp-reset.php";i:3;s:41:"wordpress-importer/wordpress-importer.php";i:4;s:15:"wp-ui/wp-ui.php";}' 
WHERE  option_name = 'active_plugins';

UPDATE  wordpress.wp_options 
SET  option_value =  'http://www.facebook.com/shitkatapult.label' 
WHERE  wp_options.option_id = 'fb_url';

UPDATE  wordpress.wp_options 
SET  option_value =  'http://www.youtube.com/results?search_query=shitkatapult' 
WHERE  option_name ='youtube_url';

UPDATE  wordpress.wp_options 
SET  option_value =  'http://twitter.com/Shitkatapult' 
WHERE  option_name = 'twitter_url';
