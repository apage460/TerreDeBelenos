<?php

// BEGIN iThemes Security - Ne pas modifier ou supprimer cette ligne
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Désactiver l’éditeur de fichier - Sécurité > Réglages > Modifications de WordPress > Éditeur de fichier
// END iThemes Security - Ne pas modifier ou supprimer cette ligne

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
define('DB_NAME', 'nouveau_site');

/** MySQL database username */
define('DB_USER', 'bele_db');

/** MySQL database password */
define('DB_PASSWORD', 'try400Psy#51');

/** MySQL hostname */
define('DB_HOST', 'mysql.terres-de-belenos.com');

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
define('AUTH_KEY',         'mk[STO.{Rw,kv[?Fl0t59C(51@M.%setQy)j:lhaH:WoiQJpg{6{m 2_$aeFSHuL');
define('SECURE_AUTH_KEY',  '98UDrc )t#P0H:I+Dhpuv7e;|EE`-0@ZKOqp!oH ESyR<-!G[tFaK<2](Nf/8bGy');
define('LOGGED_IN_KEY',    '@]gTmiRMHsNw_ u?AI6~(.a44w]!<<u[UqhFoByZ.<Wc72h+fe;6,x*O;T]tY<s6');
define('NONCE_KEY',        '8{$>}h^LY@@qaay,HA_D-dez_`&!=^.yc7_L3qFh^T{[F-.*sHa6.[D}h>7P4wBZ');
define('AUTH_SALT',        'hp+4<3m%z9(z%jWH`CkEmjG15&%+2`y#@^3.bHn7(B@36i<@m[gtYW&6zVUCt,Qx');
define('SECURE_AUTH_SALT', 'Y(pO&u|L/L2T^:RZcC;gZJ*eMA%5O}p`Naya8G(3f)*&[{|jb?nb|l7Pm-^h!fsZ');
define('LOGGED_IN_SALT',   '!1F-f[}Ct9Tg8du|fY 8D)ib0ZNwRa*0#c5rqPJbnS6mq#4$_JBUAlbb-<R(/rtS');
define('NONCE_SALT',       'p& w7|G4DYaJzaajlc3kOCJx(P}a^?d1VClD*pS2;g&qaTYw>bNJ^- .}Rz[$1LQ');

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
