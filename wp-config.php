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
define('DB_NAME', 'portal_test');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '|>X+KAyQQ;AH*||1# N}BY;4!iv3s..m,+*<::hkc=n2b&ATeF9k40#f.4?iQX D');
define('SECURE_AUTH_KEY',  '|;$0JnX2k,7nBRM<EFqvb3t:G$gd/sno@iAk]`b.,*bI>f>C-^r+ae?d_P[CQ@)i');
define('LOGGED_IN_KEY',    'p`O?@N$>S^WM.=_sqv@>dgA4_*Pkt%HHs?&D.wzKGsDaV^M#=[#81vS[P+_gy_^`');
define('NONCE_KEY',        '0%9:Mc(PWeKp`hjF7O8N@Ruk%,>=8N`N ={N|TFyH.)EDKK6UO>q/w .m@hFal,d');
define('AUTH_SALT',        's7Qst8DSDtTo.vsc_n`_BJQv0]YK/YQguI cjzF} qDghwbY*lYG &r61:uc`v@R');
define('SECURE_AUTH_SALT', 'O(gBq{aL-wY1=d;4DA5f<[Uo6lm+Yfe=t%o2=w0bKTD<`|Du:W:_6`oT{o<:O41K');
define('LOGGED_IN_SALT',   'uQkEbxg?xN#LK9+w/?5.(n,yL^,boMn^n[l6K7m!|rfI$~nS@VjdC_rOL[aKyFc8');
define('NONCE_SALT',       'vC-8,%75@WNp.WwE?-&nvx=/D$(ViV6~:mPj4.Q&5J&jccvu0=$.>Ba<[PqqM[r!');

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
