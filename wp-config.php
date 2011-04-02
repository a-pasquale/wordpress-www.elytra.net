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
define('DB_USER', 'drew');

/** MySQL database password */
define('DB_PASSWORD', 'river1');

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
define('AUTH_KEY',         'o3]u<7+9&&i(6WvxgS/u2N/{UMn+jdvPv?ZHroZw|G^(>lU0//6|hE(UD`R)(f-p');
define('SECURE_AUTH_KEY',  'k2//B@w@c.OW%h~=M]P6XQg-jqq4IV?xmD7,j>:9^$yN-]Q,l~mPz<mxEMp7Cs{ ');
define('LOGGED_IN_KEY',    '&9m#@X#xxIjKj7x1,;vt.`2{oZ3V^E9jLGOCH)G^J*Y)<u*_QpIM.~yiQIaXYnct');
define('NONCE_KEY',        'qB2gUWxMUkQ]&Y-wKi8VQ7FO~T[!w{ UscM)?qzuc)#notr[KY/oNe4V]7s8dsES');
define('AUTH_SALT',        'ClNcQ+>EIw XJn[u-k@z,DfBw7L6m?.GkMI/U4ky39yS/3B1G<t;Mu.`[Js|RPeq');
define('SECURE_AUTH_SALT', 'Hc>.O<`%9.E:S;b[(3f`dwJTI|+(?XSS7Y!_k0J!HiSF^GyaM:`(M.47M:iY,.D^');
define('LOGGED_IN_SALT',   'CF/TUaR-qEU}f^.TSPcx];5jYW=9fS) P++nryC()!H.Gw|E3RYSjGKFV|O+5_:<');
define('NONCE_SALT',       'c?:XBEXPog9CF wd|8V%I*/YNNkjQYNLx*tEDRFH_2tkYe5W|U#gYdlnQ@?:0=Ue');

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
