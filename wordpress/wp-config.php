<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'care' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Anubis@68' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ')xNg)=4rL:K>aC.c}95%B_gP|izp_4g# 9DOP&GP?%%o[GhG3^~/Arw*8iFDOdmd' );
define( 'SECURE_AUTH_KEY',  'A0s89G &{3+P?4M#1a{s`/IMowDk[9KEO?82Rno <tV.iNN!)A8QIM?v[] Y,n:Y' );
define( 'LOGGED_IN_KEY',    '{XrfqKpo)1E98k~Ym@k@U&b ReJdXkeFW4do~s9)rQmiD6bzUQu7hKc3m$dx2RM#' );
define( 'NONCE_KEY',        'R{t5Kd3v|y TB2b~ mLQIUHv~A__+KW_l$}zYl)4tRGw4tmnEEp$-Al8^1Sr/&~P' );
define( 'AUTH_SALT',        'IV~.:P@?SyQ-:c=W]dzvw_2[)aE32~6jX7w.nIk[C.hE7aZd j%dCp=EG05Ae^m1' );
define( 'SECURE_AUTH_SALT', '%!Q.REhy4h<J*i9=r Qz]30^xy_/8qU56?dP.~FTvEF~5jaju#cbsZJ($[tML6>_' );
define( 'LOGGED_IN_SALT',   'P&v6OB&zT}yP*<z=1b(}8/7qr!Z->+q8w>stP.>p^y&h>x#:hiOI0+Y0s<;rr:LL' );
define( 'NONCE_SALT',       'D~JGx#~pKD#[ypX,j+gI*;a_WL~&MgIUr}1OiSS88R(rzC^J>E4=T#_hz=_^PL.>' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
