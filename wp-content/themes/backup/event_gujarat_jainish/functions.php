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

require('gujarat/controller.php');

add_action('admin_post_nopriv_your_action_name', 'your_function_to_process_form');
function your_function_to_process_form()
{
  print_r($_POST);
}

//redirect users to homepage after logging out

add_action('wp_logout', 'redirect_to_homepage_after_logout');

function redirect_to_homepage_after_logout()
{
  wp_safe_redirect(home_url());
  exit;
}

add_filter('wp_nav_menu_items', 'event_add_login_logout_menu', 10, 2);
function event_add_login_logout_menu($items, $args)
{
  // ob_start();
  // wp_loginout('index.php');
  // echo $loginoutlink = ob_get_contents();
  // ob_end_clean();  
  if(is_user_logged_in()){
    // echo wp_loginout($_SERVER['REQUEST_URI']);
    $items .= '<li style=" float: right;">'.wp_loginout('/').'</li>';
  }
  else{
    // var_dump( $_COOKIE );
    // echo wp_loginout($_SERVER['REQUEST_URI']);
    // $login = site_url().'/login';  
    $items .= '<li style=" float: right;">'.wp_loginout().'</li>';  
  }
  
  return $items;
}
