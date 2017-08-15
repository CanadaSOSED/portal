<?php
/**
 * Template Name: Login Page
 *
 * Template for displaying a page with login form
 *
 * @package sos-chapter
 */

get_header(); ?>


<?php 
// check to make sure this is the login page and then load some custom styles to hide header and footer
    if ( is_page_template( 'page-templates/login.php' ) ) { 
        echo '<style type="text/css">.fixed-top, #wrapper-footer{ display:none !important; }</style>';
    }
?>
<script type="text/javascript">
// Add placeholders "username" and "password" using javascript
    jQuery(document).ready(function($) {

        $('#loginform input[type="text"]').attr('placeholder', 'Username');
        $('#loginform input[type="password"]').attr('placeholder', 'Password');
    });
</script>

<div class="page-header" filter-color="blue">
    <div class="page-header-image-login" style="background-image:url(<?php bloginfo('stylesheet_directory') ?>/assets/img/login-page-bg.jpg)"></div>
    <div class="container">
        <div class="col-md-4 content-center">
            <div class="card card-login card-plain">
                <div class="header header-primary text-center">
                        <div class="logo-container">
                            <img src="<?php bloginfo('template_directory') ?>/assets/img/sos-logo-207x128.png" width="120px" alt=""><br/>
                            <h4><?php bloginfo( 'name' ); ?></h4>
                        </div>
                    </div>
                    <div class="content">
                    <?php 
                        if ( ! is_user_logged_in() ) { // Display WordPress login form:
                            $args = array(
                                'echo'           => true,
                                'remember'       => true,
                                'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/wp-admin',
                                'form_id'        => 'loginform',
                                'id_username'    => 'user_login',
                                'id_password'    => 'user_pass',
                                'id_remember'    => 'rememberme',
                                'id_submit'      => 'wp-submit',
                                'label_username' => __( 'Username' ),
                                'label_password' => __( 'Password' ),
                                'label_remember' => __( 'Remember Me' ),
                                'label_log_in'   => __( 'Log In' ),
                                'value_username' => '',
                                'value_remember' => false
                                );
                            
                            wp_login_form();

                            } else { // If logged in:
                                sos_wp_loginout( home_url() ); // Display "Log Out" link.
                                echo "&nbsp; &nbsp; &nbsp; &nbsp;";
                                sos_wp_register('', ''); // Display "Site Admin" link.
                            }
                    ?>   
                    </div>
            </div>
        </div>
    </div>                                
</div>                              
<?php
get_footer();
?>