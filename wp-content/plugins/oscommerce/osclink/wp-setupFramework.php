<?php
/*
 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com
 Copyright (c) 201 osCommerce & UV
 Released under the GNU General Public License
 
 ADAPTED VERSION TO REPLACE application-top when called in wordpress
 */
if (!isset($HTTP_SERVER_VARS)) {
  $HTTP_GET_VARS = &$_GET;
  $HTTP_POST_VARS = &$_POST;
  $HTTP_COOKIE_VARS = &$_COOKIE;
  $HTTP_POST_FILES = &$_FILES;
  $HTTP_SERVER_VARS = &$_SERVER;
  $HTTP_ENV_VARS = &$_ENV;
}

// start the timer for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());

// set the level of error reporting
error_reporting(E_ALL & ~E_NOTICE  & ~E_DEPRECATED);

// Set the local configuration parameters - mainly for developers
//if (file_exists('includes/local/configure.php')) include('includes/local/configure.php');

// include server parameters
define ('FILENAME_WP_CREATE_ACCOUNT', 'wp-create_account.php');
define ('FILENAME_WP_CREATE_ACCOUNT_SUCCESS', 'wp-create_account_success.php');
define('OSCOMMERCE_OSCLINK_PATH', 'wp-content/plugins/oscommerce/osclink/');
define('DIR_FS_CATALOG', 'E:/Devel/shopkatapult/');
define('DIR_FS_CATALOG', '/var/www/vhosts/dev1.shitkatapult.com/httpdocs/wordpress/shopkatapult/');
define('DIR_FS_INCLUDES', DIR_FS_CATALOG. 'includes/');
define('DIR_WS_INCLUDES', DIR_FS_CATALOG. 'includes/');
define('DIR_FS_FUNCTIONS', DIR_FS_INCLUDES. 'functions/');
define('DIR_FS_CLASSES', DIR_FS_INCLUDES. 'classes/');
define('DIR_FS_LANGUAGES', DIR_FS_INCLUDES. 'languages/');
define('HTTP_SERVER', 'http://mywebsite:8080');
define('HTTPS_SERVER', 'https://mywebsite:8080');
define('HTTP_COOKIE_DOMAIN', 'mywebsite');
define('HTTPS_COOKIE_DOMAIN', 'mywebsite');
// define('HTTP_SERVER', 'http://dev1.shitkatapult.com');
// define('HTTPS_SERVER', 'https://dev1.shitkatapult.com');
// define('HTTP_COOKIE_DOMAIN', 'dev1.shitkatapult.com');
// define('HTTPS_COOKIE_DOMAIN', 'dev1.shitkatapult.com');

require(DIR_FS_INCLUDES.'configure.php');

// define the project version
define('PROJECT_VERSION', 'osCommerce Wordpress Integration 0.99');
// the ajax login page
define('WPLINK_LOGIN', '#action=login');

// some code to solve compatibility issues
require(DIR_FS_FUNCTIONS . 'compatibility.php');

// set the type of request (secure or not)
$request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

// set php_self in the local scope
if (!isset($PHP_SELF)) $PHP_SELF = $HTTP_SERVER_VARS['PHP_SELF'];

if ($request_type == 'NONSSL') {
  define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
} else {
  define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
}

// include the list of project filenames
require(DIR_FS_INCLUDES . 'filenames.php');

// include the list of project database tables
require(DIR_FS_INCLUDES . 'database_tables.php');

// customization for the design layout
define('BOX_WIDTH', 125); // how wide the boxes should be in pixels (default: 125)

// include the database functions
require(DIR_FS_FUNCTIONS. 'database.php');

// make a connection to the database... now
tep_db_connect() or die('Unable to connect to database server!');

// set the application parameters
$configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
while ($configuration = tep_db_fetch_array($configuration_query)) {
  define($configuration['cfgKey'], $configuration['cfgValue']);
}

// if gzip_compression is enabled, start to buffer the output
if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4') ) {
  if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
    ob_start('ob_gzhandler');
  } else {
    ini_set('zlib.output_compression_level', GZIP_LEVEL);
  }
}

// set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
  if (strlen(getenv('PATH_INFO')) > 1) {
    $GET_array = array();
    $PHP_SELF = str_replace(getenv('PATH_INFO'), '', $PHP_SELF);
    $vars = explode('/', substr(getenv('PATH_INFO'), 1));
    for ($i=0, $n=sizeof($vars); $i<$n; $i++) {
      if (strpos($vars[$i], '[]')) {
        $GET_array[substr($vars[$i], 0, -2)][] = $vars[$i+1];
      } else {
        $HTTP_GET_VARS[$vars[$i]] = $vars[$i+1];
      }
      $i++;
    }
    if (sizeof($GET_array) > 0) {
      while (list($key, $value) = each($GET_array)) {
        $HTTP_GET_VARS[$key] = $value;
      }
    }
  }
}

// define general functions used application-wide
require(DIR_FS_FUNCTIONS . 'general.php');
require(DIR_FS_FUNCTIONS . 'html_output.php');

// set the cookie domain
$cookie_domain = (($request_type == 'NONSSL') ? HTTP_COOKIE_DOMAIN : HTTPS_COOKIE_DOMAIN);
$cookie_path = (($request_type == 'NONSSL') ? HTTP_COOKIE_PATH : HTTPS_COOKIE_PATH);

// include cache functions if enabled
if (USE_CACHE == 'true') include(DIR_WS_FUNCTIONS . 'cache.php');

// include shopping cart class
require(DIR_FS_CLASSES . 'shopping_cart.php');

// define how the session functions will be used
require(DIR_FS_FUNCTIONS . 'sessions.php');

// set the session name and save path
tep_session_name('osCsid');
tep_session_save_path('./sessions');

// set the session cookie parameters
if (function_exists('session_set_cookie_params')) {
  session_set_cookie_params(0, $cookie_path, $cookie_domain);
} elseif (function_exists('ini_set')) {
  ini_set('session.cookie_lifetime', '0');
  ini_set('session.cookie_path', $cookie_path);
  ini_set('session.cookie_domain', $cookie_domain);
}

// set the session ID if it exists
if (isset($HTTP_POST_VARS[tep_session_name()])) {
  tep_session_id($HTTP_POST_VARS[tep_session_name()]);
} elseif ( ($request_type == 'SSL') && isset($HTTP_GET_VARS[tep_session_name()]) ) {
  tep_session_id($HTTP_GET_VARS[tep_session_name()]);
}

// start the session
$session_started = false;
if (SESSION_FORCE_COOKIE_USE == 'True') {
  tep_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, $cookie_path, $cookie_domain);

  if (isset($HTTP_COOKIE_VARS['cookie_test'])) {
    tep_session_start();
    $session_started = true;
  }
} elseif (SESSION_BLOCK_SPIDERS == 'True') {
  $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
  $spider_flag = false;

  if (tep_not_null($user_agent)) {
    $spiders = file(DIR_FS_INCLUDES . 'spiders.txt');

    for ($i=0, $n=sizeof($spiders); $i<$n; $i++) {
      if (tep_not_null($spiders[$i])) {
        if (is_integer(strpos($user_agent, trim($spiders[$i])))) {
          $spider_flag = true;
          break;
        }
      }
    }
  }

  if ($spider_flag == false) {
    tep_session_start();
    $session_started = true;
  }
} else {
  tep_session_start();
  $session_started = true;
}

if ( ($session_started == true) && (PHP_VERSION >= 4.3) && function_exists('ini_get') && (ini_get('register_globals') == false) ) {
  extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
}

// set SID once, even if empty
$SID = (defined('SID') ? SID : '');

// verify the ssl_session_id if the feature is enabled
if ( ($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true) ) {
  $ssl_session_id = getenv('SSL_SESSION_ID');
  if (!tep_session_is_registered('SSL_SESSION_ID')) {
    $SESSION_SSL_ID = $ssl_session_id;
    tep_session_register('SESSION_SSL_ID');
  }

  if ($SESSION_SSL_ID != $ssl_session_id) {
    tep_session_destroy();
    tep_redirect(tep_href_link(FILENAME_SSL_CHECK));
  }
}

// verify the browser user agent if the feature is enabled
if (SESSION_CHECK_USER_AGENT == 'True') {
  $http_user_agent = getenv('HTTP_USER_AGENT');
  if (!tep_session_is_registered('SESSION_USER_AGENT')) {
    $SESSION_USER_AGENT = $http_user_agent;
    tep_session_register('SESSION_USER_AGENT');
  }

  if ($SESSION_USER_AGENT != $http_user_agent) {
    tep_session_destroy();
    tep_redirect(tep_href_link(FILENAME_LOGIN));
  }
}

// verify the IP address if the feature is enabled
if (SESSION_CHECK_IP_ADDRESS == 'True') {
  $ip_address = tep_get_ip_address();
  if (!tep_session_is_registered('SESSION_IP_ADDRESS')) {
    $SESSION_IP_ADDRESS = $ip_address;
    tep_session_register('SESSION_IP_ADDRESS');
  }

  if ($SESSION_IP_ADDRESS != $ip_address) {
    tep_session_destroy();
    tep_redirect(tep_href_link(FILENAME_LOGIN));
  }
}

// create the shopping cart & fix if necesary
if (tep_session_is_registered('cart') && is_object($cart)) {
} else {
  tep_session_register('cart');
  $cart = new shoppingCart;
}

// include currencies class and create an instance
require(DIR_FS_CLASSES . 'currencies.php');
$currencies = new currencies();

// include the mail classes
require(DIR_FS_CLASSES . 'mime.php');
require(DIR_FS_CLASSES . 'email.php');

// set the language
if (!tep_session_is_registered('language') || isset($HTTP_GET_VARS['language'])) {
  if (!tep_session_is_registered('language')) {
    tep_session_register('language');
    tep_session_register('languages_id');
  }

  include(DIR_FS_CLASSES . 'language.php');
  $lng = new language();

  if (isset($HTTP_GET_VARS['language']) && tep_not_null($HTTP_GET_VARS['language'])) {
    $lng->set_language($HTTP_GET_VARS['language']);
  } else {
    $lng->get_browser_language();
  }

  $language = $lng->language['directory'];
  $languages_id = $lng->language['id'];
}

// include the language translations
require(DIR_FS_LANGUAGES . $language . '.php');

// currency
define('DEFAULT_CURRENCY','EUR');
$currency = DEFAULT_CURRENCY;

// include the password crypto functions
require(DIR_FS_FUNCTIONS . 'password_funcs.php');

// include validation functions (right now only email address)
require(DIR_FS_FUNCTIONS . 'validations.php');

// infobox
require(DIR_FS_CLASSES . 'boxes.php');
  // START STS 4.5
  require (DIR_FS_CLASSES.'sts.php');
  $sts= new sts();
  $sts->start_capture();
  // END STS
// initialize the message stack for output messages
require(DIR_FS_CLASSES . 'message_stack.php');
$messageStack = new messageStack;

?>
