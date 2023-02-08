<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

define( 'GUJ_THEME_VERSION', '0.1' );
define( 'GUJ_THEME_NAME', 'Guj' );
define( 'GUJ_THEME_SLUG', 'guj' );
define( 'GUJ_THEME_PATH', get_stylesheet_directory() );
define( 'GUJ_THEME_URL', get_stylesheet_directory_uri() );

/** Theme Options in Appearance -> Customise */
require_once( __DIR__ . '/classes/theme-customizer.php' );

/** Widgets */
require_once( __DIR__ . '/classes/widgets-controller.php' );

/** General Functions */
require_once( __DIR__ . '/classes/general-hooks.php' );
require_once( __DIR__ . '/classes/user-hooks.php' );

/** Shortcodes */
require_once( __DIR__ . '/classes/shortcodes/general-shortcodes.php' );

// WP Ajax Callback Funtoins
require_once( __DIR__ . '/classes/wp-ajax-callback.php' );

/** Custom Post Type Register */
require_once( __DIR__ . '/classes/custom-post-types/event-cpt.php' );
