<?php
/*
Template Name: Password Reset
*/
global $wpdb, $user_ID;

function tg_validate_url() {
    global $post;
    $page_url = esc_url( get_permalink( $post->ID ));
    $urlget    = strpos( $page_url, "?" );
    if ($urlget === false) {
        $concate = "?";
    } else {
        $concate = "&";
    }
    return $page_url.$concate;
}

if ( !$user_ID ) { //block logged in users
    if(isset( $_POST['action']) && $_POST['action']=='save_pw_reset' ){
    if ( !wp_verify_nonce( $_POST['save_pwd_nonce'], "save_reset_password" ) ) {
      exit("No trick please");
    }
        $user_login = base64_decode( $_POST['user_token'] );
        $user_data  = $wpdb->get_row( "SELECT ID, user_login, user_email FROM $wpdb->users WHERE ID = '". $user_login ."'" );
        if( $user_data )
        {
            wp_set_password( $_POST['new_password'], $user_login );
            $wpdb->update( $wpdb->users,array('user_activation_key'=>'' ), array( 'ID'=>$user_login ) );
            echo json_encode( array( 'status' => 'success', 'msg' => 'Password Reset successfully' ) );
        }else{
            echo json_encode( array( 'status' => 'error', 'msg' => 'please try again' ) );
        }
        exit();
    }

    if(isset( $_GET['key'] ) && $_GET['action'] == "reset_pwd" ) {
        $reset_key  = $_GET['key'];
        $user_login = base64_decode($_GET['login']);
        $user_data  = $wpdb->get_row( $wpdb->prepare( "SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND (user_login = %s OR user_email = %s )" , $reset_key, $user_login,$user_login ));
       
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
       
        if( !empty( $reset_key ) && !empty( $user_data ) ) {           
      get_header();
?>
      <div data-height="200" class="title_outer title_without_animation">
        <div style="height:100px;" class="title title_size_small  position_left ">
          <div class="image not_responsive"></div>
          <div style="height:100px;" class="title_holder">
            <div class="container">
              <div class="container_inner clearfix">
                <div class="title_subtitle_holder">
                  <h1><span><?php echo the_title();  ?></span></h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <?php if( isset( $qode_options_proya['overlapping_content'] ) && $qode_options_proya['overlapping_content'] == 'yes' ) { ?>
          <div class="overlapping_content"><div class="overlapping_content_inner">
        <?php } ?>
        <div class="container_inner forgot_password default_template_holder clearfix">
          <div class="forgot_box">               
            <?php if ( have_posts() ) : ?>   
              <?php while ( have_posts() ) : the_post(); ?>
                <div class="pwd_res_container">
                  <p>Please enter your new password.</p>
                  <form method="post" action=""  id="saveresetpwdfrm">
                    <label>New password</label>
                    <br />           
                    <input type="password" class="text" name="new_password" id="new_password"  value="" style="width:200px" /><br /><br />
                    <label>Repeat new password</label>
                    <br />       
                    <input type="hidden" name="user_email" value="<?php echo $user_email; ?>" />
                    <input type="password" class="text" name="repeat_new_password" id="repeat_new_password" value="" style="width:200px" /><br />
                    <input type="hidden" name="action" value="save_pw_reset" />
                    <input type="hidden" name="save_pwd_nonce" value="<?php echo wp_create_nonce("save_reset_password"); ?>" />
                    <input type="hidden" name="user_token" value="<?php echo base64_encode($user_data->ID); ?>" />
                    <input type="submit" id="savesubmitbtn" class="reset_password" name="submit" value="Save" />       
                  </form>
                  <div id="result"></div> <!-- To hold validation results -->
                </div>
                <script type="text/javascript">                         
                jQuery( "#saveresetpwdfrm" ).submit( function() {
                  if( jQuery( '#new_password' ).val() != '' ){
                    if( jQuery( '#repeat_new_password' ).val() != '' ){
                      if( jQuery( '#new_password' ).val() == jQuery( '#repeat_new_password' ).val() ){
                          jQuery( '#result' ).html( '<span class="loading">Validating...</span>' ).fadeIn();
                          var input_data = jQuery( '#saveresetpwdfrm' ).serialize();
                          jQuery.ajax({
                            type: "POST",
                            url:  "<?php echo get_permalink( $post->ID ); ?>",
                            data: input_data,
                            success: function( msg ){
                              change_data = JSON.parse(msg);
                              if(change_data.status=='success'){
                                jQuery( '.pwd_res_container' ).html( '<div><p>Your password reset successfully.</p><p><a href="<?php echo home_url('/login'); ?>">Click here </a> to logged in.</p></div>' ).fadeIn();
                              }else{
                                jQuery( '#result' ).html( change_data.msg ).fadeIn();
                              }
                            }
                          });
                      }else{
                        jQuery( '#result' ).html( '<span class="loading">Password and repeat password not matched</span>' ).fadeIn();
                      }
                    }else{
                      jQuery( '#result' ).html( '<span class="loading">Please enter repeat password</span>' ).fadeIn();
                    }
                  }else{
                    jQuery( '#result' ).html( '<span class="loading">Please enter password</span>' ).fadeIn();
                  }
                  return false;
                });
                </script>           
              <?php endwhile; ?>
            <?php else : ?>       
            <h2><?php _e('Not Found'); ?></h1>           
          <?php endif; ?>           
          </div>
        </div>
        <?php if(isset($qode_options_proya['overlapping_content']) && $qode_options_proya['overlapping_content'] == 'yes') {?>
          </div></div>
        <?php } ?>
        
      </div>
<?php
      get_footer();          
        }else{
            $redirect_to = get_permalink( $post->ID );
            wp_safe_redirect($redirect_to);
            exit();
        }       
    }
    //exit();

    if( $_POST['action'] == "tg_pwd_reset" ){
        if ( !wp_verify_nonce( $_POST['tg_pwd_nonce'], "tg_pwd_nonce" )) {
          exit( "No trick please" );
       } 
        if( empty( $_POST['user_input'] ) ) {
            echo "<div class='error'>Please enter your Username or E-mail address</div>";
            exit();
        }
        //We shall SQL escape the input
        $user_input = $wpdb->escape( trim( $_POST['user_input'] ) );
       
        if ( strpos( $user_input, '@' ) ) {
            $user_data = get_user_by_email( $user_input );
            if( empty( $user_data )  ) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
                echo "<div class='error'>Invalid E-mail address!</div>";
                exit();
            }
        }else {
            $user_data = get_userdatabylogin($user_input);
            if(empty( $user_data ) ) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
                echo "<div class='error'>Invalid Username!</div>";
                exit();
            }
        }
       
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
       
        $key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s OR user_email = %s ", $user_login ) );       
        //generate reset key
        $key = wp_generate_password( 20, false );
          
        $key = sha1( $key . $user_email . uniqid( time(), true ) );
        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $key), array( 'user_login' => $user_login ) );   
       
        //mailing reset details to the user
        $message = __( 'Someone requested that the password be reset for the following account:' ) . "\r\n\r\n";
        $message .= get_option('siteurl') . "\r\n\r\n";
        $message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
        $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ) . "\r\n\r\n";
        $message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";
        $message .= tg_validate_url() . "action=reset_pwd&key=$key&login=" . base64_encode($user_login) . "\r\n";
        $headers[] = 'From: Techventure Kids < contact@techventurekids.org >';
        if ( $message && !wp_mail( $user_email, 'Password Reset Request From Techventure Kids', $message, $headers ) ) {
            echo "<div class='error'>Email failed to send for some unknown reason.</div>";
            exit();
        }else {
            echo "<div class='success'>We have just sent you an email with Password reset instructions.</div>";
            exit();
        }       
    } else if( $_REQUEST['action']!='reset_pwd' ) {
    get_header(); 
?>
    <div data-height="200" class="title_outer title_without_animation">
      <div style="height:100px;" class="title title_size_small  position_left ">
        <div class="image not_responsive"></div>
        <div style="height:100px;" class="title_holder">
          <div class="container">
            <div class="container_inner clearfix">
              <div class="title_subtitle_holder">
                <h1><span><?php echo the_title();  ?></span></h1>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
            <?php if( isset($qode_options_proya['overlapping_content'] ) && $qode_options_proya['overlapping_content'] == 'yes' ){ ?>
                <div class="overlapping_content"><div class="overlapping_content_inner">
            <?php } ?>
            <div class="container_inner forgot_password default_template_holder clearfix">
        <div class="forgot_box">               
          <?php if ( have_posts() ) : ?>   
            <?php while ( have_posts() ) : the_post(); ?>
              <p>Please enter your username or email address. <br/>You will receive a link to create a new password via email.</p>
              <form class="user_form" id="wp_pass_reset" action="" method="post">   
              <label>Username or Email:</label>
              <br />       
              <input type="text" class="text" name="user_input" value="" style="width:200px" /><br />
              <input type="hidden" name="action" value="tg_pwd_reset" />
              <input type="hidden" name="tg_pwd_nonce" value="<?php echo wp_create_nonce("tg_pwd_nonce"); ?>" />
              <input type="submit" id="submitbtn" class="reset_password" name="submit" value="Reset Password" />                   
              </form>
              <div id="result"></div> <!-- To hold validation results -->
              <script type="text/javascript">                         
                jQuery( '#wp_pass_reset' ).submit(function() {           
                  jQuery( '#result' ).html( '<span class="loading">Validating...</span>' ).fadeIn();
                  var input_data = jQuery( '#wp_pass_reset' ).serialize();
                  jQuery.ajax({
                    type: "POST",
                    url:  "<?php echo get_permalink( $post->ID ); ?>",
                    data: input_data,
                    success: function( msg ){
                    jQuery( '.loading' ).remove();
                    jQuery( '<div>' ).html( msg ).appendTo( 'div#result' ).hide().fadeIn( 'slow' );
                  }
                });
                return false;                 
              });
              </script>               
            <?php endwhile; ?>       
          <?php else : ?>       
          <h2><?php _e('Not Found'); ?></h1>           
        <?php endif; ?>
        </div>
      </div>
            <?php if( isset($qode_options_proya['overlapping_content'] ) && $qode_options_proya['overlapping_content'] == 'yes' ){ ?>
                </div></div>
            <?php } ?>
        </div>
        
<?php
    get_footer();
    }
}
else {
    wp_redirect( home_url() ); exit;
    //redirect logged in user to home page
}
?>