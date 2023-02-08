<?php
/**
 * Twenty Twenty functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

/**
 * Table of Contents:
 * Theme Support
 * Required Files
 * Register Styles
 * Register Scripts
 * Register Menus
 * Custom Logo
 * WP Body Open
 * Register Sidebars
 * Enqueue Block Editor Assets
 * Enqueue Classic Editor Styles
 * Block Editor Settings
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Twenty Twenty 1.0
 */

function wpdocs_theme_name_scripts() {

    wp_enqueue_style( 'jquery-ui', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', [], time() );

    $ajax_params = [
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
        'niuNonce' => wp_create_nonce( 'niuNonce' )
    ];

    wp_enqueue_script( 'jquery-js', 'https://code.jquery.com/jquery-3.6.0.js', [], time() );
    wp_enqueue_script( 'jq-js', 'https://code.jquery.com/ui/1.13.2/jquery-ui.js', [], time() );

    wp_enqueue_script( 'clusterer-js', 'https://cdnjs.cloudflare.com/ajax/libs/js-marker-clusterer/1.0.0/markerclusterer.js', [], time() );
    wp_enqueue_script( 'typeahead-js', 'https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js', [], time() );

    wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/js/child-script.js');

    wp_localize_script( 'child-script', 'ajaxPar', $ajax_params );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );

require_once( __DIR__ . '/event-cpt.php' );
// WP Ajax Callback Funtoins
require_once( __DIR__ . '/wp-ajax-callback.php' );