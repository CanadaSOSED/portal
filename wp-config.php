<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'portal_test_2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'B1`(C4[-?|pYATt-]|s}iri;dT_A|z7d;,>]/{zH<5MZDt9Mu2+9T6g;`BRiakK;');
define('SECURE_AUTH_KEY',  'KKaQLIQTpTAe7}`p+fCxN)2eB_a7ORlYbu.K-*%APCGnEdy^1!Xzg!+9{yKY3umF');
define('LOGGED_IN_KEY',    'F[y(53pm2P(KBq^7+B+bYJ)<PGQX/M+8a|O3<D@>>dsJ0^;yXI<2q6~?/CW%4d,D');
define('NONCE_KEY',        'f0O]R]=na@.M.Px8k@hi!8VU:mlnWh@V.?.+vI`rI?va:&`IV~G&$ksJ8fY677[F');
define('AUTH_SALT',        'y5mgi]/[SVZDbxUh`[Loj+El$I,w[=W8YyBo>,f2f(#&_p=)8(qO<QB8>Vxw2F&g');
define('SECURE_AUTH_SALT', 'p?T9,a-A%-^ WL4(Pd3I%*q1z-](0-+Da62lqy`g E-^4TG=*z)EmE#T6.7J%f!r');
define('LOGGED_IN_SALT',   'guZ+gXE+PC(_fvzyC-2e?TX:?*S+Y~l1v7R).trj:LIm(eZK5lGuDYR$UIYaZi41');
define('NONCE_SALT',       'PA-9%]v6Erh0jTz8?7O?^!cLJk*Qt84b/,=Fk,oH-nz{v?+_hOi#|y~#y+wUDw+}');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
 // Enable WP_DEBUG mode
define( 'WP_DEBUG', true );

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );

// Disable display of errors and warnings 
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

define('FS_METHOD', 'direct');
/* That's all, stop editing! Happy blogging. */

/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost');
define('PATH_CURRENT_SITE', '/sos3/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
