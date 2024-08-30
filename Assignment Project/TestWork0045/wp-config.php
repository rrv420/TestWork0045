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
define( 'DB_NAME', 'u742736691_testwork0045' );

/** Database username */
define( 'DB_USER', 'u742736691_testwork0045' );

/** Database password */
define( 'DB_PASSWORD', '5453641491Re+' );

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
define( 'AUTH_KEY',         'hc_#mPYovT!9(<efa@Zb)9x&vln- m0*_8q03j70j!=<bSj1<5s=& i7vm.Ja^aL' );
define( 'SECURE_AUTH_KEY',  'G$)8y*M}VW#lA2R.] 2dk#$v-fY_DP`k1t+Ape5cMTD/si Z>o,Z_3rr<ML.MNk.' );
define( 'LOGGED_IN_KEY',    'URu@^KrbQd PSJfoIR& eZ-moO<VZcQ9$3v]*EZ*2f;oK~iY95Y]<QBtTGY|gh$J' );
define( 'NONCE_KEY',        ')-@/7:j!v>NO>Sg/YacK8$G/1Yj^.plsw:mt4Ga7p0J`&&kCQyNVx}Y=)$P#OJza' );
define( 'AUTH_SALT',        '2o,S_l*((1][{%gVjfuZuE^&LWW-JzJqk41u>GyqfAa;N#nr*3$UfgZ&.9LO9xpe' );
define( 'SECURE_AUTH_SALT', ']g16{N(?,rrw8Vjv:U+zux3F,()$u.Qcf1[WW}-U)v$[`*dcYiU9d AZYBCHIYtP' );
define( 'LOGGED_IN_SALT',   'NKoHcrE]@h.nYHjj&Vx&T01[JcAP&u{dK[Yv?;=HOqae>9{68zy xjeb,+A$+hN[' );
define( 'NONCE_SALT',       'AQ/z)We7yBx*n9f=UVB6RhbXTD](s:pR?-vlR_AJN%x(t=K}0*.>e,o$pC5VZl*X' );
define( 'OPENWEATHER_API_KEY', '37876874c186113917051cfa7c678f4c');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
