<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'wordpress');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ut:zq-^URGh8L7/ tf+L,$z.:H4qc.HL!&61|GYA)-;5e.`~usREUs>X eLPWH:y');
define('SECURE_AUTH_KEY',  ';1A{HTFWJMZ$,<}On/l!wJ%U)<omJ+j!#|ss7E(YjOoj,&EtdSI4vMw1c8w&Vq/g');
define('LOGGED_IN_KEY',    'j_IwprLK>`5>(sw0)3p2;evE<+:WJ+=%SHDh0m+_=rquE9(+<_?nTPs-5+q:Lwj!');
define('NONCE_KEY',        ':-1oxD,`4NAK`(frqQezxN*%8]{Yof(_QMmN4MW^~wJ*vp(!sH+1/fog}n.d5fvY');
define('AUTH_SALT',        'FX)J&V@tvr+BP6%Cft@ugw4D>_%<]wh:S.++W!T%<:=$zgztOK<5^M!o|-q]q|Ch');
define('SECURE_AUTH_SALT', 'q6]i? J!S|+H(0)3y~XOL[V0oKpw<!5`TRrt@-~{G[^o;i`rzsC#UY->--Q~hFr%');
define('LOGGED_IN_SALT',   'purd!v36esh^D-+94J-*e 2&GJ7L+6]s;iDcAcZ1&Tp?T~hQUjT{W+C]g+C+sk@u');
define('NONCE_SALT',       '||1+~^sy:o|[D(Z9$k{S6X/:b|l}|kfNzzPc K~uFoK1Ly+_Tj%v+FbDjlmFyrSI');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
