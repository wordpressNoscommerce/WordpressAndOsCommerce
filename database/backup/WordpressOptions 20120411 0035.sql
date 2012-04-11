-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.1.39-community-log


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema wordpress
--

CREATE DATABASE IF NOT EXISTS wordpress;
USE wordpress;

--
-- Definition of table `wp_options`
--

DROP TABLE IF EXISTS `wp_options`;
CREATE TABLE `wp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL DEFAULT '0',
  `option_name` varchar(64) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wp_options`
--

/*!40000 ALTER TABLE `wp_options` DISABLE KEYS */;
INSERT INTO `wp_options` (`option_id`,`blog_id`,`option_name`,`option_value`,`autoload`) VALUES 
 (1,0,'siteurl','http://mywebsite:8080','yes'),
 (2,0,'blogname','Shitkatapult Relaunch','yes'),
 (3,0,'blogdescription','The Shitkatapult Relaunch as a WordPress site','yes'),
 (4,0,'users_can_register','0','yes'),
 (5,0,'admin_email','admin@trash-mail.com','yes'),
 (6,0,'start_of_week','1','yes'),
 (7,0,'use_balanceTags','0','yes'),
 (8,0,'use_smilies','1','yes'),
 (9,0,'require_name_email','1','yes'),
 (10,0,'comments_notify','1','yes'),
 (11,0,'posts_per_rss','10','yes'),
 (12,0,'rss_use_excerpt','0','yes'),
 (13,0,'mailserver_url','mail.example.com','yes'),
 (14,0,'mailserver_login','login@example.com','yes'),
 (15,0,'mailserver_pass','password','yes'),
 (16,0,'mailserver_port','110','yes'),
 (17,0,'default_category','1','yes'),
 (18,0,'default_comment_status','open','yes'),
 (19,0,'default_ping_status','open','yes'),
 (20,0,'default_pingback_flag','0','yes'),
 (21,0,'default_post_edit_rows','20','yes'),
 (22,0,'posts_per_page','12','yes'),
 (23,0,'date_format','F j, Y','yes'),
 (24,0,'time_format','g:i a','yes'),
 (25,0,'links_updated_date_format','F j, Y g:i a','yes'),
 (26,0,'links_recently_updated_prepend','<em>','yes'),
 (27,0,'links_recently_updated_append','</em>','yes'),
 (28,0,'links_recently_updated_time','120','yes'),
 (29,0,'comment_moderation','0','yes'),
 (30,0,'moderation_notify','1','yes'),
 (31,0,'permalink_structure','/%post_id%/','yes'),
 (32,0,'gzipcompression','0','yes'),
 (33,0,'hack_file','0','yes'),
 (34,0,'blog_charset','UTF-8','yes'),
 (35,0,'moderation_keys','','no'),
 (36,0,'active_plugins','a:5:{i:0;s:21:\"gigpress/gigpress.php\";i:1;s:25:\"oscommerce/osCommerce.php\";i:2;s:37:\"wordpress-database-reset/wp-reset.php\";i:3;s:41:\"wordpress-importer/wordpress-importer.php\";i:4;s:15:\"wp-ui/wp-ui.php\";}','yes'),
 (37,0,'home','http://mywebsite:8080','yes'),
 (38,0,'category_base','','yes'),
 (39,0,'ping_sites','http://rpc.pingomatic.com/','yes'),
 (40,0,'advanced_edit','0','yes'),
 (41,0,'comment_max_links','2','yes'),
 (42,0,'gmt_offset','0','yes'),
 (43,0,'default_email_category','1','yes'),
 (44,0,'recently_edited','','no'),
 (45,0,'template','sight','yes'),
 (46,0,'stylesheet','sight','yes'),
 (47,0,'comment_whitelist','1','yes'),
 (48,0,'blacklist_keys','','no'),
 (49,0,'comment_registration','0','yes'),
 (50,0,'rss_language','en','yes'),
 (51,0,'html_type','text/html','yes'),
 (52,0,'use_trackback','0','yes'),
 (53,0,'default_role','subscriber','yes'),
 (54,0,'db_version','18226','yes'),
 (55,0,'uploads_use_yearmonth_folders','1','yes'),
 (56,0,'upload_path','','yes'),
 (57,0,'blog_public','0','yes'),
 (58,0,'default_link_category','2','yes'),
 (59,0,'show_on_front','posts','yes'),
 (60,0,'tag_base','','yes'),
 (61,0,'show_avatars','1','yes'),
 (62,0,'avatar_rating','G','yes'),
 (63,0,'upload_url_path','','yes'),
 (64,0,'thumbnail_size_w','290','yes'),
 (65,0,'thumbnail_size_h','290','yes'),
 (66,0,'thumbnail_crop','1','yes'),
 (67,0,'medium_size_w','300','yes'),
 (68,0,'medium_size_h','300','yes'),
 (69,0,'avatar_default','mystery','yes'),
 (70,0,'enable_app','0','yes'),
 (71,0,'enable_xmlrpc','0','yes'),
 (72,0,'large_size_w','1024','yes'),
 (73,0,'large_size_h','1024','yes'),
 (74,0,'image_default_link_type','file','yes'),
 (75,0,'image_default_size','','yes'),
 (76,0,'image_default_align','','yes'),
 (77,0,'close_comments_for_old_posts','0','yes'),
 (78,0,'close_comments_days_old','14','yes'),
 (79,0,'thread_comments','1','yes'),
 (80,0,'thread_comments_depth','5','yes'),
 (81,0,'page_comments','0','yes'),
 (82,0,'comments_per_page','50','yes'),
 (83,0,'default_comments_page','newest','yes'),
 (84,0,'comment_order','asc','yes'),
 (85,0,'sticky_posts','a:0:{}','yes'),
 (86,0,'widget_categories','a:2:{i:2;a:4:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:12:\"hierarchical\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}','yes'),
 (87,0,'widget_text','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (88,0,'widget_rss','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (89,0,'timezone_string','','yes'),
 (90,0,'embed_autourls','1','yes'),
 (91,0,'embed_size_w','','yes'),
 (92,0,'embed_size_h','600','yes'),
 (93,0,'page_for_posts','0','yes'),
 (94,0,'page_on_front','0','yes'),
 (95,0,'default_post_format','0','yes'),
 (96,0,'wp_user_roles','a:5:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:71:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:9:\"add_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:24:\"NextGEN Gallery overview\";b:1;s:19:\"NextGEN Use TinyMCE\";b:1;s:21:\"NextGEN Upload images\";b:1;s:22:\"NextGEN Manage gallery\";b:1;s:19:\"NextGEN Manage tags\";b:1;s:29:\"NextGEN Manage others gallery\";b:1;s:18:\"NextGEN Edit album\";b:1;s:20:\"NextGEN Change style\";b:1;s:22:\"NextGEN Change options\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}}','yes'),
 (97,0,'_transient_random_seed','08ed4fd624f71846f54477a2f9493356','yes'),
 (98,0,'widget_pages','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (99,0,'widget_calendar','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (100,0,'widget_archives','a:2:{i:2;a:3:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}','yes'),
 (101,0,'widget_links','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (102,0,'widget_meta','a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}','yes'),
 (103,0,'widget_search','a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}','yes'),
 (104,0,'widget_recent-posts','a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}','yes'),
 (105,0,'widget_recent-comments','a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}','yes'),
 (106,0,'widget_tag_cloud','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (107,0,'widget_nav_menu','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (108,0,'cron','a:5:{i:1334139126;a:2:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1334139127;a:1:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1334182352;a:1:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1334182521;a:1:{s:26:\"importer_scheduled_cleanup\";a:1:{s:32:\"84876a92125bf5a9eac9aeec5f7615f8\";a:2:{s:8:\"schedule\";b:0;s:4:\"args\";a:1:{i:0;i:3;}}}}s:7:\"version\";i:2;}','yes'),
 (109,0,'_site_transient_update_core','O:8:\"stdClass\":3:{s:7:\"updates\";a:1:{i:0;O:8:\"stdClass\":9:{s:8:\"response\";s:7:\"upgrade\";s:8:\"download\";s:40:\"http://wordpress.org/wordpress-3.3.1.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":4:{s:4:\"full\";s:40:\"http://wordpress.org/wordpress-3.3.1.zip\";s:10:\"no_content\";s:51:\"http://wordpress.org/wordpress-3.3.1-no-content.zip\";s:11:\"new_bundled\";s:52:\"http://wordpress.org/wordpress-3.3.1-new-bundled.zip\";s:7:\"partial\";b:0;}s:7:\"current\";s:5:\"3.3.1\";s:11:\"php_version\";s:5:\"5.2.4\";s:13:\"mysql_version\";s:3:\"5.0\";s:11:\"new_bundled\";s:3:\"3.2\";s:15:\"partial_version\";s:0:\"\";}}s:12:\"last_checked\";i:1334095932;s:15:\"version_checked\";s:5:\"3.2.1\";}','yes'),
 (110,0,'sidebars_widgets','a:8:{s:19:\"wp_inactive_widgets\";a:0:{}s:19:\"primary-widget-area\";a:6:{i:0;s:8:\"search-2\";i:1;s:14:\"recent-posts-2\";i:2;s:17:\"recent-comments-2\";i:3;s:10:\"archives-2\";i:4;s:12:\"categories-2\";i:5;s:6:\"meta-2\";}s:21:\"secondary-widget-area\";a:0:{}s:24:\"first-footer-widget-area\";a:0:{}s:25:\"second-footer-widget-area\";a:0:{}s:24:\"third-footer-widget-area\";a:0:{}s:25:\"fourth-footer-widget-area\";a:0:{}s:13:\"array_version\";i:3;}','yes'),
 (112,0,'gigpress_settings','a:39:{s:16:\"age_restrictions\";s:40:\"All Ages | All Ages/Licensed | No Minors\";s:15:\"alternate_clock\";i:0;s:12:\"artist_label\";s:6:\"Artist\";s:15:\"autocreate_post\";i:0;s:16:\"category_exclude\";i:0;s:12:\"country_view\";s:4:\"long\";s:16:\"date_format_long\";s:9:\"l, F jS Y\";s:11:\"date_format\";s:5:\"m/d/y\";s:10:\"db_version\";s:3:\"1.5\";s:15:\"default_country\";s:2:\"US\";s:12:\"default_date\";s:10:\"2012-04-10\";s:12:\"default_time\";s:8:\"00:00:01\";s:13:\"default_title\";s:28:\"%artist% in %city% on %date%\";s:12:\"default_tour\";s:0:\"\";s:11:\"disable_css\";i:0;s:10:\"disable_js\";i:0;s:21:\"display_subscriptions\";i:1;s:15:\"display_country\";i:1;s:11:\"load_jquery\";i:1;s:6:\"nopast\";s:28:\"No shows in the archive yet.\";s:10:\"noupcoming\";s:30:\"No shows booked at the moment.\";s:16:\"related_category\";i:1;s:15:\"related_heading\";s:12:\"Related show\";s:16:\"related_position\";s:5:\"after\";s:7:\"related\";s:13:\"Related post.\";s:12:\"related_date\";s:3:\"now\";s:16:\"relatedlink_city\";i:0;s:16:\"relatedlink_date\";i:0;s:17:\"relatedlink_notes\";i:1;s:8:\"rss_head\";i:1;s:8:\"rss_list\";i:1;s:9:\"rss_title\";s:14:\"Upcoming shows\";s:10:\"shows_page\";s:0:\"\";s:12:\"sidebar_link\";i:0;s:12:\"target_blank\";i:0;s:11:\"time_format\";s:4:\"g:ia\";s:10:\"tour_label\";s:4:\"Tour\";s:10:\"user_level\";s:10:\"edit_posts\";s:7:\"welcome\";s:3:\"yes\";}','yes'),
 (113,0,'uninstall_plugins','a:2:{i:0;b:0;s:21:\"gigpress/gigpress.php\";s:18:\"gigpress_uninstall\";}','yes'),
 (114,0,'_transient_doing_cron','1334096180','yes'),
 (115,0,'_site_transient_update_plugins','O:8:\"stdClass\":3:{s:12:\"last_checked\";i:1334095932;s:7:\"checked\";a:12:{s:21:\"gigpress/gigpress.php\";s:6:\"2.1.14\";s:21:\"json-api/json-api.php\";s:5:\"1.0.7\";s:47:\"monkeyman-rewrite-analyzer/rewrite-analyzer.php\";s:3:\"1.0\";s:25:\"oscommerce/osCommerce.php\";s:3:\"1.0\";s:26:\"permalink-editor/index.php\";s:6:\"0.2.12\";s:37:\"wordpress-database-reset/wp-reset.php\";s:3:\"2.0\";s:41:\"wordpress-importer/wordpress-importer.php\";s:3:\"0.6\";s:29:\"wordpress-logger/wplogger.php\";s:3:\"0.3\";s:35:\"wordpress-reset/wordpress-reset.php\";s:5:\"1.3.2\";s:35:\"wp-memory-usage/wp-memory-usage.php\";s:5:\"1.2.1\";s:43:\"wp-htaccess-control/wp-htaccess-control.php\";s:3:\"2.7\";s:15:\"wp-ui/wp-ui.php\";s:5:\"0.8.2\";}s:8:\"response\";a:1:{s:43:\"wp-htaccess-control/wp-htaccess-control.php\";O:8:\"stdClass\":5:{s:2:\"id\";s:4:\"7509\";s:4:\"slug\";s:19:\"wp-htaccess-control\";s:11:\"new_version\";s:7:\"2.7.2.1\";s:3:\"url\";s:56:\"http://wordpress.org/extend/plugins/wp-htaccess-control/\";s:7:\"package\";s:69:\"http://downloads.wordpress.org/plugin/wp-htaccess-control.2.7.2.1.zip\";}}}','yes'),
 (116,0,'widget_gigpress','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (117,0,'_site_transient_timeout_theme_roots','1334103131','yes'),
 (118,0,'_site_transient_theme_roots','a:1:{s:5:\"sight\";s:7:\"/themes\";}','yes'),
 (119,0,'_site_transient_update_themes','O:8:\"stdClass\":3:{s:12:\"last_checked\";i:1334095932;s:7:\"checked\";a:1:{s:5:\"sight\";s:3:\"1.0\";}s:8:\"response\";a:0:{}}','yes'),
 (120,0,'can_compress_scripts','1','yes'),
 (123,0,'category_children','a:0:{}','yes'),
 (125,0,'_site_transient_timeout_wporg_theme_feature_list','1334106975','yes'),
 (126,0,'_site_transient_wporg_theme_feature_list','a:5:{s:6:\"Colors\";a:15:{i:0;s:5:\"black\";i:1;s:4:\"blue\";i:2;s:5:\"brown\";i:3;s:4:\"gray\";i:4;s:5:\"green\";i:5;s:6:\"orange\";i:6;s:4:\"pink\";i:7;s:6:\"purple\";i:8;s:3:\"red\";i:9;s:6:\"silver\";i:10;s:3:\"tan\";i:11;s:5:\"white\";i:12;s:6:\"yellow\";i:13;s:4:\"dark\";i:14;s:5:\"light\";}s:7:\"Columns\";a:6:{i:0;s:10:\"one-column\";i:1;s:11:\"two-columns\";i:2;s:13:\"three-columns\";i:3;s:12:\"four-columns\";i:4;s:12:\"left-sidebar\";i:5;s:13:\"right-sidebar\";}s:5:\"Width\";a:2:{i:0;s:11:\"fixed-width\";i:1;s:14:\"flexible-width\";}s:8:\"Features\";a:18:{i:0;s:8:\"blavatar\";i:1;s:10:\"buddypress\";i:2;s:17:\"custom-background\";i:3;s:13:\"custom-colors\";i:4;s:13:\"custom-header\";i:5;s:11:\"custom-menu\";i:6;s:12:\"editor-style\";i:7;s:21:\"featured-image-header\";i:8;s:15:\"featured-images\";i:9;s:20:\"front-page-post-form\";i:10;s:19:\"full-width-template\";i:11;s:12:\"microformats\";i:12;s:12:\"post-formats\";i:13;s:20:\"rtl-language-support\";i:14;s:11:\"sticky-post\";i:15;s:13:\"theme-options\";i:16;s:17:\"threaded-comments\";i:17;s:17:\"translation-ready\";}s:7:\"Subject\";a:3:{i:0;s:7:\"holiday\";i:1;s:13:\"photoblogging\";i:2;s:8:\"seasonal\";}}','yes'),
 (127,0,'current_theme','Sight','yes'),
 (128,0,'theme_mods_sight','a:1:{i:0;b:0;}','yes'),
 (129,0,'paging_mode','ajax','yes'),
 (130,0,'widget_getconnected','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (131,0,'widget_recentposts_thumbnail','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (132,0,'logo_url','/wp-content/themes/sight/images/shitkatapult.png','yes'),
 (133,0,'bg_color','','yes'),
 (134,0,'ss_disable','','yes'),
 (135,0,'ss_timeout','','yes'),
 (136,0,'ga','                  ','yes'),
 (137,0,'wpUI_options','a:48:{s:11:\"enable_tabs\";s:2:\"on\";s:14:\"enable_widgets\";s:2:\"on\";s:15:\"selected_styles\";s:0:\"\";s:10:\"tab_scheme\";s:10:\"wpui-light\";s:18:\"jqui_custom_themes\";s:0:\"\";s:10:\"custom_css\";s:0:\"\";s:12:\"dialog_width\";s:0:\"\";s:6:\"tabsfx\";s:9:\"slideDown\";s:8:\"fx_speed\";s:0:\"\";s:11:\"tabs_rotate\";s:7:\"disable\";s:10:\"tabs_event\";s:5:\"click\";s:12:\"accord_event\";s:5:\"click\";s:13:\"accord_easing\";s:5:\"false\";s:16:\"mouse_wheel_tabs\";s:5:\"false\";s:17:\"tab_nav_prev_text\";s:0:\"\";s:17:\"tab_nav_next_text\";s:0:\"\";s:17:\"spoiler_show_text\";s:0:\"\";s:17:\"spoiler_hide_text\";s:0:\"\";s:14:\"excerpt_length\";s:0:\"\";s:11:\"post_widget\";a:4:{s:5:\"title\";s:0:\"\";s:4:\"type\";s:7:\"popular\";s:6:\"number\";s:0:\"\";s:7:\"per_row\";s:1:\"2\";}s:22:\"post_default_thumbnail\";a:3:{s:3:\"url\";s:0:\"\";s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}s:15:\"post_template_1\";s:0:\"\";s:15:\"post_template_2\";s:0:\"\";s:15:\"jquery_disabled\";s:2:\"on\";s:19:\"script_conditionals\";s:0:\"\";s:12:\"docwrite_fix\";s:2:\"on\";s:6:\"submit\";s:12:\"Save Options\";s:16:\"enable_accordion\";s:3:\"off\";s:15:\"enable_spoilers\";s:3:\"off\";s:14:\"enable_dialogs\";s:3:\"off\";s:17:\"enable_pagination\";s:3:\"off\";s:24:\"enable_quicktags_buttons\";s:3:\"off\";s:6:\"topnav\";s:3:\"off\";s:9:\"bottomnav\";s:3:\"off\";s:19:\"enable_tinymce_menu\";s:3:\"off\";s:18:\"enable_post_widget\";s:3:\"off\";s:12:\"enable_cache\";s:3:\"off\";s:15:\"load_all_styles\";s:3:\"off\";s:16:\"collapsible_tabs\";s:3:\"off\";s:17:\"accord_autoheight\";s:3:\"off\";s:18:\"accord_collapsible\";s:3:\"off\";s:13:\"bleeding_edge\";s:3:\"off\";s:6:\"alt_sc\";s:3:\"off\";s:22:\"load_scripts_on_demand\";s:3:\"off\";s:11:\"use_cookies\";s:3:\"off\";s:15:\"linking_history\";s:3:\"off\";s:14:\"relative_times\";s:3:\"off\";s:7:\"version\";s:5:\"0.8.2\";}','yes'),
 (140,0,'widget_wpui_core','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (141,0,'widget_wpui-posts','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),
 (144,0,'rewrite_rules','a:72:{s:70:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:65:\"category/(.+?)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:67:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:62:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:68:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:63:\"type/([^/]+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:14:\".*wp-atom.php$\";s:19:\"index.php?feed=atom\";s:13:\".*wp-rdf.php$\";s:18:\"index.php?feed=rdf\";s:13:\".*wp-rss.php$\";s:18:\"index.php?feed=rss\";s:14:\".*wp-rss2.php$\";s:19:\"index.php?feed=rss2\";s:14:\".*wp-feed.php$\";s:19:\"index.php?feed=feed\";s:22:\".*wp-commentsrss2.php$\";s:34:\"index.php?feed=rss2&withcomments=1\";s:55:\"feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:50:\"(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:64:\"comments/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:59:\"comments/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:29:\"comments/page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:67:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:62:\"search/(.+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:70:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:65:\"author/([^/]+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:97:\"date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:92:\"date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:62:\"date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:44:\"date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:84:\"date/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:79:\"date/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:49:\"date/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:31:\"date/([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:71:\"date/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:66:\"date/([0-9]{4})/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:36:\"date/([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:18:\"date/([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:28:\"[0-9]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:38:\"[0-9]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:81:\"[0-9]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:76:\"[0-9]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"[0-9]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:21:\"([0-9]+)/trackback/?$\";s:28:\"index.php?p=$matches[1]&tb=1\";s:64:\"([0-9]+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:40:\"index.php?p=$matches[1]&feed=$matches[2]\";s:59:\"([0-9]+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:40:\"index.php?p=$matches[1]&feed=$matches[2]\";s:29:\"([0-9]+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?p=$matches[1]&paged=$matches[2]\";s:36:\"([0-9]+)/comment-page-([0-9]{1,})/?$\";s:41:\"index.php?p=$matches[1]&cpage=$matches[2]\";s:21:\"([0-9]+)(/[0-9]+)?/?$\";s:40:\"index.php?p=$matches[1]&page=$matches[2]\";s:17:\"[0-9]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:27:\"[0-9]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:70:\"[0-9]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:65:\"[0-9]+/([^/]+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:42:\"[0-9]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:25:\".+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:35:\".+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:78:\".+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:73:\".+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:50:\".+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:18:\"(.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:61:\"(.+?)/feed/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:56:\"(.+?)/(feed|rdf|rss|rss2|atom|gigpress|gigpress-ical)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:26:\"(.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:33:\"(.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:18:\"(.+?)(/[0-9]+)?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";}','yes');
/*!40000 ALTER TABLE `wp_options` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
