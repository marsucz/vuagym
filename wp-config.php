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
define('DB_NAME', 'db_vuagymnew');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '12345678');

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
define('AUTH_KEY',         '6NQ?N:%a>_Ir0k43(yUz)20H|aNFGVF&d?(kOZ1B+|MD[Bd&nYHs-I|7$kKAT(W}');
define('SECURE_AUTH_KEY',  '^j^q=.qAXAvRp{^c#1C@X_2a^xj$jA_~[3Gx<Z-bHI33*/OM[w)JU}0[J^D{#c6{');
define('LOGGED_IN_KEY',    'OYs8DUS>Rr3nVS_KgFiEAmj^*megtYO&]$oDqjdWO7 |oW/3)xIYEcj5Q=9k:5Z!');
define('NONCE_KEY',        'z;xC|;5;uMh[^iQnhJHJ^2U<8m6B|NySCJ>%l/*8Dcn4H^JM#Dj>Gb4^(/KdoD`<');
define('AUTH_SALT',        'm8^Q.QL^&>gfm+d@6H)U3J$Zg!r{aRPS{*Y#l}zt55&wlvdax8Xj_xxbi3&&2h:-');
define('SECURE_AUTH_SALT', 'da&e:%d00pw~Z&|Y~% P9nh1P(/`*-4E06%`(_r[3y%RUqxRdk~-1|d[dXluOd/W');
define('LOGGED_IN_SALT',   ',5?E.Ae/l,@!h4`k^Pic#$-O9W.agvh5U4O]E,t,6e6*v&5Qs(PmKt:hPHM -Hv(');
define('NONCE_SALT',       'Wy ARH:K_&DL^:;&H5ZJ 1}c7_wXhjmlz->cH)t6MBRt ^1I]E}E& .UJbJ%)rwX');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'vg_';

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
define('FS_METHOD', 'direct');
