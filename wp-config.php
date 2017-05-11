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
define('DB_NAME','equippin_wp931_st2');

/** MySQL database username */
define('DB_USER','equippin_st2');

/** MySQL database password */
define('DB_PASSWORD','uMKxcU7UMKlY-A-DyJ3I');

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
define('AUTH_KEY',         '7plqmqoji1nwwlgc0gohgqwwmg5a7remxaipiwpiu3sldipdcbx6oq7l6jmkz0fy');
define('SECURE_AUTH_KEY',  'ifvsgmqzubrdjortwfruwcgtvkcohvsshw8vnrf67rxfrknjf6d3ghgyxihc6gdj');
define('LOGGED_IN_KEY',    'qtjouskwpcd7rqxvquaaz8musxbjfakx6cedyg6w30eykwova7xqbix07vntoljl');
define('NONCE_KEY',        '7yloncsbtdp5phx0tqjw00ae4b19kgowx9cwdhmtolliaylciocgsbinoqq6xbna');
define('AUTH_SALT',        'pvay7zha3jdhtan1p0jxznzvvqpuas1ulkngfl7fp1ekmfkaikyfthtw7salpuyx');
define('SECURE_AUTH_SALT', 'cinvub2ufd1ewg0i2xp8w6de3x58fucmk4safejrra1xvxxezskto8q7s8bmlwcj');
define('LOGGED_IN_SALT',   'ke6qwpbwytnbtyj2gpf7g7zvfwnei6mbpbstvt13hqbmp2hfj0o5vucj0j5ebcqh');
define('NONCE_SALT',       '51chquvgqpir5tpjcfhs10g9rmbm7wd0jzwlyhmvt8tletbykbsdodx1bqazbviu');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpmj_';

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
define( 'WP_MEMORY_LIMIT', '128M' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

# Disables all core updates. Added by SiteGround Autoupdate:
define( 'WP_AUTO_UPDATE_CORE', false );
