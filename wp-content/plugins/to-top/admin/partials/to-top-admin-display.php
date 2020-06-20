<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       catchplugins.com
 * @since      1.0.0
 *
 * @package    Catch_Instagram_Feed_Gallery_Widget
 * @subpackage Catch_Instagram_Feed_Gallery_Widget/admin/partials
 */

?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'To Top', 'to-top' ); ?></h1>

    <div class="catchp-content-wrapper">
        <div class="catchp_widget_settings">
            <form id="catch-instagram-feed-gallery-widget-main" method="post" action="options.php">

                <h2 class="nav-tab-wrapper">
                    <a class="nav-tab nav-tab-active" id="dashboard-tab" href="#dashboard"><?php esc_html_e( 'Dashboard', 'to-top' ); ?></a>
                    <a class="nav-tab" id="features-tab" href="#features"><?php esc_html_e( 'Features', 'to-top' ); ?></a>
                </h2>

                <div id="dashboard" class="wpcatchtab  nosave active">
                    <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . '/partials/to-top-dashboard.php'; ?>

                    <div id="ctp-switch" class="content-wrapper col-3 to-top-main">
                        <div class="header">
                            <h2><?php esc_html_e( 'Catch Themes & Catch Plugins Tabs', 'to-top' ); ?></h2>
                        </div> <!-- .Header -->

                        <div class="content">

                            <p><?php echo esc_html__( 'If you want to turn off Catch Themes & Catch Plugins tabs option in Add Themes and Add Plugins page, please uncheck the following option.', 'to-top' ); ?>
                            </p>
                            <table>
                                <tr>
                                    <td>
                                        <?php echo esc_html__( 'Turn On Catch Themes & Catch Plugin tabs', 'to-top' );  ?>
                                    </td>
                                    <td><?php //echo '<pre>'; print_r($settings); echo '</pre>';  
                                        $ctp_options = ctp_get_options()
                                    ?>
                                        <div class="module-header <?php echo $ctp_options['theme_plugin_tabs'] ? 'active' : 'inactive'; ?>">
                                            <div class="switch">
                                                <input type="checkbox" id="ctp_options[theme_plugin_tabs]" class="ctp-switch" rel="theme_plugin_tabs" <?php checked( true, $ctp_options['theme_plugin_tabs'] ); ?> >
                                                <label for="ctp_options[theme_plugin_tabs]"></label>
                                            </div>
                                            <div class="loader"></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                        </div>
                    </div><!-- .content-wrapper -->
                </div><!-- .dashboard -->

                <div id="features" class="wpcatchtab save">
                    <div class="content-wrapper col-3">
                        <div class="header">
                            <h3><?php esc_html_e( 'Features', 'to-top' ); ?></h3>
                        </div><!-- .header -->
                        <div class="content">
                            <ul class="catchp-lists">
                                <li>
                                    <strong><?php esc_html_e( 'Supports all themes on WordPress', 'to-top' ); ?></strong>
                                    <p><?php esc_html_e( 'You don’t have to worry if you have a slightly different or complicated theme installed on your website. It supports all the themes on WordPress and makes your website more striking and playful.', 'to-top' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Lightweight', 'to-top' ); ?></strong>
                                    <p><?php esc_html_e( 'It is extremely lightweight. You do not need to worry about it affecting the space and speed of your website.', 'to-top' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Incredible Support', 'to-top' ); ?></strong>
                                    <p><?php esc_html_e( 'We have a great line of support team and support documentation. You do not need to worry about how to use the plugins we provide, just refer to our Tech Support Forum. Further, if you need to do advanced customization to your website, you can always hire our theme customizer!', 'to-top' ); ?></p>
                                </li>
                            </ul>
                        </div><!-- .content -->
                    </div><!-- content-wrapper -->
                </div> <!-- Featured -->

            </form><!-- #catch-instagram-feed-gallery-widget-main -->

        </div><!-- .catchp_widget_settings -->


        <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . '/partials/sidebar.php'; ?>
    </div> <!-- .catchp-content-wrapper -->

    <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . '/partials/footer.php'; ?>
</div><!-- .wrap -->