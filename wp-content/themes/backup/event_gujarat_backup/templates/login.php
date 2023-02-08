<?php
/* 
Template Name: login
*/
get_header();
?>

<?php

// if ( is_user_logged_in() ) { 
//  wp_redirect(site_url());
//   exit();
// }
?>

<?php

if ($_POST) {

  global $wpdb;

  //We shall SQL escape all inputs  
  $username = $wpdb->escape($_REQUEST['username']);
  $password = $wpdb->escape($_REQUEST['password']);
  $remember = $wpdb->escape($_REQUEST['rememberme']);

  if ($remember) $remember = "true";
  else $remember = "false";

  $user_id = array();
  $user_id['user_login'] = $username;
  $user_id['user_password'] = $password;
  $user_id['remember'] = $remember;

  $user_verify = wp_signon($user_id, false);

  if (is_wp_error($user_verify)) {
    echo "Invalid login details";
    // Note, I have created a page called "Error" that is a child of the login page to handle errors. This can be anything, but it seemed a good way to me to handle errors.  
  } else {
    echo "<script type='text/javascript'>window.location.href='" . home_url() . "'</script>";

    exit();
  }
} else {

  // No login details entered - you should probably add some more user feedback here, but this does the bare minimum  

  //echo "Invalid login details";  

}
?>
<div class="page-main">
  <div class="container">
    <div class="event_inner">
      <form id="login1" name="form" action="<?php echo home_url(); ?>/login/" method="post">

        <input id="username" type="text" placeholder="Username" name="username"><br>
        <input id="password" type="password" placeholder="Password" name="password"><br>
        <input id="submit" type="submit" name="submit" value="Submit"> 
        <div class="col">
      <!-- <a href="<?php echo site_url('forgot'); ?>">Forgot password?</a> -->
    </div>
    <div class="text-center">
    <a href="<?php echo esc_url( wp_lostpassword_url( get_permalink() ) ); ?>" alt="<?php esc_attr_e( 'Lost Password', 'textdomain' ); ?>">
    <?php esc_html_e( 'Lost Password', 'textdomain' ); ?></a>

    <!-- <p>Not a member? <a href="<?php echo site_url('account'); ?>">Register</a></p> -->
    <?php 
        if ( is_user_logged_in() ) { 
          
         } else { ?>
        <p>Not a member? <a href="<?php echo site_url('account'); ?>">Register</a></p>
<?php } ?>  
        
      </form>
    </div>
  </div>
</div>
<?php get_footer(); ?>

