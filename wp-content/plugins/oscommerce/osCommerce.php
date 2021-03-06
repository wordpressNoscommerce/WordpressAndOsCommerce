<?php
require_once ('debug.php');
/*
 Plugin Name: osCommerce
 Plugin URI: http://localhost/wordpress/wp-cpntent/plugins/index.php
 Description: Pulls the categories and products from an osCommerce system that has been defined in the admin section.
 Version: 1.0
 Author: Tal Orlik heavily modified by Uv Wildner
 Author URI: http://everything-about-everything-else.blogspot.com/2008/11/oscommerce-pulginwidget-for-wordpress.html
 Author URI:

 ------------------------------------------------------------------------------
 Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : talorlik@gmail.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 ------------------------------------------------------------------------------
 */
define('OSCOMMERCEVERS', 'Version: 1.0');
define('OSCOMMERCEPATH', ABSPATH. 'wp-content/plugins/oscommerce');
define('OSCOMMERCECLASSPATH', OSCOMMERCEPATH. '/classes');
define('OSCOMMERCEURL', get_option('siteurl'). '/wp-content/plugins/oscommerce');
define('OSCOMMERCEJPLAYERURL', OSCOMMERCEURL. '/jplayer');
define('OSCOMMERCEJSURL', OSCOMMERCEURL. '/js');
define('OSCOMMERCEVIDEOURL', OSCOMMERCEURL. '/video');
define('OSCOMMERCECSSURL', OSCOMMERCEURL. '/css');
define('OSCOMMERCEIMAGESURL', OSCOMMERCEURL. '/images');
define('ABSWPINCLUDE', ABSPATH.WPINC);
define('MIN_RECORDS', 10);

define('EMPTY_IMAGE', OSCOMMERCEIMAGESURL."/no_image.gif");

define('OSC_ARTIST_TAG','[oscArtistListing]');
define('OSC_RELEASE_TAG','[oscReleaseListing]');
define('OSC_SHOPPINGCART_TAG','[oscShoppingCart]');
define('OSC_SHOP_TAG','[oscShopListing]');
define('OSC_LABEL_TAG','[oscLabelListing]');

//$TAGLIST = array(OSC_ARTIST_TAG,OSC_RELEASE_TAG);
// TODO improve shopkatapult OSC location config
//define('OSCOMMERCE_DOC_ROOT', ABSPATH. '/../shopkatapult');

require_once(OSCOMMERCECLASSPATH .'/osc_db.class.php');
require_once(OSCOMMERCECLASSPATH .'/osc_widget.class.php');
require_once(OSCOMMERCECLASSPATH .'/osc_specials_sidebar_widget.class.php');
//require_once(OSCOMMERCECLASSPATH .'/osc_special_widget.class.php');
require_once(OSCOMMERCECLASSPATH .'/osc_management.class.php');

/* INIT LOCALISATION ----------------------------------------------------------*/
load_default_textdomain();
//require_once(ABSWPINCLUDE.'/locale.php'); // this breaks with duplicated code for some reason?!?!?!
load_plugin_textdomain('osCommerce', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/lang');
/*-----------------------------------------------------------------------------*/

if(!isset($_GET['paged']) || empty($_GET['paged']) || !is_numeric($_GET['paged']))
$_GET['paged'] = 1;

if(isset($_GET['osc_action']) && $_GET['osc_action'] == 'osc_delete')
{
	$db = new osc_db();

	$db->osc_delete($_GET['intID']);

	unset($db);
}

function osc_activate()
{
	// fbDebugBacktrace();
	$db = new osc_db();
	$db->create_tbl();
	unset($db);
}

function osc_init()
{
	$svr_uri = $_SERVER['REQUEST_URI'];
	$inadmin = strstr($svr_uri, 'wp-admin');

	if($inadmin)
	wp_enqueue_script('java_script', OSCOMMERCEJSURL.'/java_script.js');
	else {
		wp_enqueue_script('jquery.tmpl', OSCOMMERCEJSURL.'/jquery.tmpl.js', array('jquery'));
		wp_enqueue_script('jquery.updown', OSCOMMERCEJSURL.'/jquery.updown.js', array('jquery'));
		// inject jplayer
		wp_enqueue_script('jplayer', OSCOMMERCEJPLAYERURL.'/jquery.jplayer.js', array('jquery','jquery.tmpl'));
		wp_enqueue_script('jplayer.playlist', OSCOMMERCEJPLAYERURL.'/add-on/jplayer.playlist.js', array('jquery','jquery.tmpl','jplayer'));
		//        wp_enqueue_script('jplayer.playlist.inspector', OSCOMMERCEJPLAYERURL.'/add-on/jquery.jplayer.inspector.js', array('jquery','jquery.tmpl','jplayer'));

		// helper functions extracted
		wp_enqueue_script('helper', OSCOMMERCEJSURL.'/helper.js', array('jquery'));
		// our tabbed interface for products --  this has to depend on wpui-init for right inclusion order
		wp_enqueue_script('osc_tabbed_shop', OSCOMMERCEJSURL.'/osc_tabbed_shop.js', array('jquery','jquery-ui','jquery.tmpl', 'jplayer', 'wp-ui-min','helper'));
	}
	// register the widgets old school style (@see gigpress)
	if(!$inadmin || ($inadmin && strstr($svr_uri, 'widget')))
	{
		$widget     = new osc_widget();
		$management = new osc_management();

		if(!function_exists('register_sidebar_widget')) return;

		register_sidebar_widget(__('osCommerce', 'osCommerce'), array(&$widget, 'display'));
		register_widget_control(__('osCommerce', 'osCommerce'), array(&$management, 'widget_control'));

		unset($widget);
	}
	global $osc_labels;
	if (empty($osc_labels)) {
		$osc_products = new osc_products();
		$osc_labels = $osc_products->osc_get_labels();
	}
}

// action function for above hook
function osc_management_init()
{
	$management = new osc_management();
	add_menu_page(__('osCommerce', 'osCommerce'), __('osCommerce', 'osCommerce'), 8, 'osCommerce', array(&$management, 'display'));

	if(isset($_GET['page']) && strstr($_GET['page'], 'osCommerce'))
	{
		global $loc_lang;
		wp_enqueue_script('java_script', OSCOMMERCEJSURL.'/java_script.js');
		add_submenu_page('osCommerce', __('osCommerce', 'osCommerce'), __('Listing', 'osCommerce'), 8, 'osCommerce', array(&$management, 'osc_listing'));
		add_submenu_page('osCommerce', __('osCommerce','osCommerce'), __('Add Shop','osCommerce'), 8, 'osCommerce-add-form', array(&$management, 'osc_add_form'));

		if(isset($_GET['osc_action']) && $_GET['osc_action'] == 'osc_edit')
		add_submenu_page('osCommerce', __('osCommerce','osCommerce'), __('Edit Shop','osCommerce'), 8, 'osCommerce-edit-form', array(&$management, 'osc_edit_form'));
	}
}

function osCommerceHeaderScript()
{ ?>
<link type="text/css"
	rel="stylesheet" href="<?php echo OSCOMMERCECSSURL;?>/osc_front.css" />
<link
	type="text/css" rel="stylesheet" href="<?php echo OSCOMMERCEJPLAYERURL;?>/blue.monday/jplayer.blue.monday.css" />
<?php
}

function osCommerceAdminHeaderScript()
{
	if(isset($_GET['page']) && substr($_GET['page'], 0, 10) == 'osCommerce')
	{
		?>
<link type="text/css" rel="stylesheet" href="<?php echo OSCOMMERCECSSURL;?>/osc_management.css" />
		<?php
	}
}

function osc_strstr($haystack, $needle, $before_needle = false)
{
	if(($pos = strpos($haystack, $needle)) === false) return false;

	if($before_needle) return substr($haystack, 0, $pos);
	else return substr($haystack, $pos + strlen($needle));
}

/** replace release tag with funky tabbed UI **/
function filterOscReleaseListing($content)
{
	if(preg_match(OSC_RELEASE_TAG, $content))
	{
		$osc_match_filter = '['.OSC_RELEASE_TAG.']';
		$osc_products = new osc_products();

		// the text before the tag
		$before_product_listing = osc_strstr($content, $osc_match_filter, false);
		// is simply echoed
		echo $before_product_listing;
		// as is our tabbed interface
		$osc_products->osc_show_tabbed_products_page();

		$content = osc_strstr($content, $osc_match_filter, true);

		// TODO does this disconnect db session?
		unset($osc_products->osc_db);
		unset($osc_products);
	}

	return $content;
}

/** replace artist tag with funky tabbed UI **/
function filterOscArtistListing($content)
{
	//    fbDebugBacktrace(); // debug only when doing something
	if(preg_match(OSC_ARTIST_TAG, $content))
	{
		$osc_match_filter = '['.OSC_ARTIST_TAG.']';
		$osc_manufacturers = new osc_manufacturers();

		// the text before the tag
		$before_artist_listing = osc_strstr($content, $osc_match_filter, false);
		// is simply echoed
		echo $before_artist_listing;
		// as is our tabbed interface
		$osc_manufacturers->osc_show_tabbed_manufacturers_page();

		$content = osc_strstr($content, $osc_match_filter, true);

		// TODO does this disconnect db session?
		unset($osc_manufacturers->osc_db);
		unset($osc_manufacturers);
	}

	return $content;
}

/** replace shop tag with funky tabbed UI **/
function filterOscShopListing($content)
{
	//    fbDebugBacktrace(); // debug only when doing something
	if(preg_match(OSC_SHOP_TAG, $content))
	{
		$osc_match_filter = '['.OSC_SHOP_TAG.']';
		$osc_products = new osc_products();

		// the text before the tag
		$before_shop_listing = osc_strstr($content, $osc_match_filter, false);
		// is simply echoed
		echo $before_shop_listing;
		// as is our tabbed interface
		$osc_products->osc_show_shop_page();

		$content = osc_strstr($content, $osc_match_filter, true);

		// TODO does this disconnect db session?
		unset($osc_products->osc_db);
		unset($osc_products);
	}

	return $content;
}

/** inject shopping cart for tag **/
function filterOscShoppingCart($content)
{
	if(preg_match(OSC_SHOPPINGCART_TAG, $content))
	{
		$osc_products = new osc_products();
		$osc_match_filter = '['.OSC_SHOPPINGCART_TAG.']';
		$shop_id = $_GET['shopID'];
		if (is_null($shop_id)) $shop_id = 1;        // default is shop 1
		$oscSid  = $_GET['oscSid'];

		$content = osc_strstr($content, $osc_match_filter, true);
		$osc_products->osc_show_shopping_cart($oscSid);
	}

	return $content;
}
$osc_labels = 0;
/** inject label tabs for tag **/
function filterOscLabelTabs($content)
{
	if(preg_match(OSC_LABEL_TAG, $content))
	{
		global $osc_labels;
		$osc_match_filter = '['.OSC_LABEL_TAG.']';
		$shop_id = $_GET['shopID'];
		if (is_null($shop_id)) $shop_id = 1;        // default is shop 1
		$oscSid  = $_GET['oscSid'];

		// page content before
		$content = osc_strstr($content, $osc_match_filter, true);
		$content .= renderLabels($osc_labels);
		// page content after
		$content .= osc_strstr($content, $osc_match_filter);
	}

	return $content;
}

// return the label texts
function renderLabels($labels) {
	$labelText = '[wptabs]
';
	foreach ($labels as $label) {
		$labelText .= "[wptabtitle]$label->categories_name[/wptabtitle]
";
		$labelText .= "[wptabcontent]
		$label->categories_description
[/wptabcontent]
";
	}
	$labelText .= '
[/wptabs]
';
	return $labelText;
}

// convert
// http:///www.shopkatapult.com/product_info.php?products_id=4558
// http://mywebsite:8080/releases/?products_id=4961
function filterPostLink ($permalink, $post)
{
	if ($post->guid == '') {
		// try to get product ID from shortcode
		do_shortcode($post->post_content);
		global $osc_product_id;	// messy global return parameter!
		if ($osc_product_id !== '') {
			$releaseLink = '/releases/?products_id='.$osc_product_id;
			$osc_product_id = '';
			$post->guid = $releaseLink;	// keep in GUID
			return $releaseLink;			
		} 
	}
	$releaseLink = $post->guid;
	if (strpos($releaseLink, 'http://www.shopkatapult.com') !== false) {
		$releaseLink = str_replace('http://www.shopkatapult.com',get_option('siteurl'),$releaseLink);
		$releaseLink = str_replace('/product_info.php','/releases/',$releaseLink);
	} elseif (strpos($releaseLink, 'http://www.shitkatapult.com') !== false) {
		$releaseLink = str_replace('http://www.shitkatapult.com',get_option('siteurl'),$releaseLink);
		$releaseLink = str_replace('/index.php?page=releaseinfo&','/releases/?',$releaseLink);
	} else {
//		do_shortcode($post->content);
	}
	$releaseLink = str_replace('#038;','',$releaseLink);	// remove converted &
	return $releaseLink;
}

function oscommerce_template($tmpl) {

	// Look for our template in the following locations:
	// 1) Child theme directory
	// 2) Parent theme directory
	// 3) wp-content directory
	// 4) Default template directory

	if(file_exists(get_stylesheet_directory() . '/oscommerce-templates/' . $tmpl . '.php')) {
		$load = get_stylesheet_directory() . '/oscommerce-templates/' . $tmpl . '.php';
	} elseif(file_exists(get_template_directory() . '/oscommerce-templates/' . $tmpl . '.php')) {
		$load = get_template_directory() . '/oscommerce-templates/' . $tmpl . '.php';
	} elseif(file_exists(WP_CONTENT_DIR . '/oscommerce-templates/' . $tmpl . '.php')) {
		$load = WP_CONTENT_DIR . '/oscommerce-templates/' . $tmpl . '.php';
	} else {
		$load = WP_PLUGIN_DIR . '/oscommerce/templates/'  . $tmpl . '.php';
	}
	return $load;
}

// this filter simply reads a releaseid tag and adds its id as GUID to the post which is used to create the link to the post
function shortcode_product($atts) {
	global $wp_query;
	global $osc_product_id;
	$post = $wp_query->post;
	extract(shortcode_atts(array('id' => false), $atts));
	$osc_product_id = $post->guid = $atts['id'];
}


register_activation_hook(__FILE__, 'osc_activate');
// TODO osc_activate in register_deactivation_hook  ????
//register_deactivation_hook(__FILE__, 'osc_activate');
add_action('widgets_init', 'oscommerce_load_widgets');
add_action('plugins_loaded', 'osc_init');
add_action('admin_menu', 'osc_management_init');
add_action('wp_head', 'osCommerceHeaderScript');
add_action('admin_head', 'osCommerceAdminHeaderScript');
add_filter('the_content', 'filterOscReleaseListing');
add_filter('the_content', 'filterOscArtistListing');
add_filter('the_content', 'filterOscShopListing');
add_filter('the_content', 'filterOscShoppingCart');
add_filter('the_content', 'filterOscLabelTabs', 0);	
// run it first to place wptabs in there
add_filter('post_link', 'filterPostLink', 10, 2);

$shortcode = 'product';
$funcname='shortcode_product';
add_shortcode($shortcode, $funcname);
// if ( is_callable($funcname)) {
// 	global $shortcode_tags;
// 	$shortcode_tags[$shortcode] = $funcname;
// } else {
// 	echo 'hallo';
// }

?>
