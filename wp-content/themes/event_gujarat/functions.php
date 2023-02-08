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

// add_action('admin_post_nopriv_your_action_name', 'your_function_to_process_form');
function your_function_to_process_form()
{
    print_r($_POST);
}

//redirect users to homepage after logging out

// add_action('wp_logout', 'redirect_to_homepage_after_logout');

function redirect_to_homepage_after_logout()
{
    wp_safe_redirect(home_url());
    exit;
}

add_filter('wp_nav_menu_items', 'event_add_login_logout_menu', 10, 2);
function event_add_login_logout_menu($items, $args)
{
    ob_start();
    wp_loginout('index.php');
    $loginoutlink = ob_get_contents();
    ob_end_clean();
    if (is_user_logged_in()) {
        $items .= '<li style=" float: right;">' . $loginoutlink . '</li>';
    } else {
        $login = site_url() . '/login';
        $items .= '<li style=" float: right;"><a href="' . $login . '">Login</a></li>';
    }

    return $items;
}

function login_success_redirect($redirect_to, $request, $user)
{
    $redirect_to =  home_url('index.php');
    return $redirect_to;
}

add_filter('login_redirect', 'login_success_redirect', 10, 3);

function redirect_login_page()
{
    $login_page  = home_url('/sign-up/');
    $page_viewed = basename($_SERVER['REQUEST_URI']);

    if ($page_viewed == "sign-up.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page);
        exit;
    }
}
add_action('init', 'redirect_login_page');

function login_failed()
{

    $login_page  = home_url('/login/');
    wp_redirect($login_page . '?login=failed');
    exit;
}
add_action('wp_login_failed', 'login_failed');


function verify_username_password($user, $username, $password)
{
    $login_page  = home_url('/login/');
    if ($username == "" || $password == "") {
        wp_redirect($login_page . "?login=empty");
        exit;
    }
}
add_filter('authenticate', 'verify_username_password', 1, 3);

function logout_page()
{
    $login_page  = home_url('/login/');
    wp_redirect($login_page . "?login=false");
    exit;
}
add_action('wp_logout', 'logout_page');

add_action('login_form_middle', 'add_lost_password_link');
// forgot link
function add_lost_password_link()
{
    return '<a href="http://192.168.24.159/wordpress_projects/event/forgot/">forgot</a>';
}

add_action('login_redirect', 'redirect_login', 10, 3);

// function redirect_login($redirect_to, $url, $user)
// {

//     if ($user->errors['invalid_username']) {
//         wp_redirect(get_bloginfo('url') . '/our-products/');
//         $error = "Wrong username";
//         return $error;
//     }

//     exit;
// }

function start_session() {
    if(!session_id()) {
        session_start();
        
    }
}

add_action('init', 'start_session', 1);

function end_session() {
    session_destroy ();
}

add_action('wp_logout', 'end_session');
add_action('wp_login', 'end_session');
