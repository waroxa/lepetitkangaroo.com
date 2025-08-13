<?php
define( 'WP_CACHE', true ); // By Speed Optimizer by SiteGround

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db4nzyhgcmj0pz' );

/** Database username */
define( 'DB_USER', 'u4qmagvdh2t4o' );

/** Database password */
define( 'DB_PASSWORD', 'nzslwfx8q0og' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'J=-1+XS+vd,!%RfyF%BDbg~^rmuw[?/DU}%D~ba?h0FuF_%=k>l0}+UE3D}23VWL' );
define( 'SECURE_AUTH_KEY',   '#EvBoqlmC3Klf?83BRySENF!M-3i5|,G:(%Nedg~^IEv>L>$~HEoc&+y<gyoP!Ic' );
define( 'LOGGED_IN_KEY',     'R2*z/~FG]t|%E/Hbf5vVbW{X9~&A6c%GEP59D%e]hh2{(:7x48_cIV;W8orwz15S' );
define( 'NONCE_KEY',         '-E,e8#SOh`P10X+kS2WC^u_!@Td}l@cO(?-k+$Z}J=HXeS#`k|I%w2j/JLWy+|%$' );
define( 'AUTH_SALT',         'v,[r`BNJ>:TZ_h|c;.y<s3,i5gl? Gpf8bHU,*NdgrD^A4y@LCT;1kf% #Adk^ W' );
define( 'SECURE_AUTH_SALT',  'ild+bqsk3=N`7P/%<y}Q28a1.7FsGpVIB#]vd/!p%SpI:4Un=eW%V7pJWFzA!p[]' );
define( 'LOGGED_IN_SALT',    '|#e8q.Ifj ^[[uNV4%zE|xt1?zR&@{sO+E&PYB,r0-I(Yk|{MEH;Bkr!NCOW-iQk' );
define( 'NONCE_SALT',        'v_{+hQ`tr_ZekV9(vA]/S,#ZuuDiX#8b<Ty$=594  *=FG%Nz9x>OpQ%6c_G2G i' );
define( 'WP_CACHE_KEY_SALT', 'nUr}Fn5`CO8HYDP:FagqF>)6%{MZNd>z Y6#Voan7W4>_H 4XM`]}SQhh+`SRh*y' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_8dd453030b_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
@include_once('/var/lib/sec/wp-settings-pre.php'); // Added by SiteGround WordPress management system
require_once ABSPATH . 'wp-settings.php';
@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system
