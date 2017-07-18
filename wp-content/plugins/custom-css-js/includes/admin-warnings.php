<?php
/**
 * Custom CSS and JS
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * CustomCSSandJS_Warnings 
 */
class CustomCSSandJS_Warnings {

    /**
     * Constructor
     */
    public function __construct() {

        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        } 

        $this->check_qtranslatex();
        add_action( 'wp_ajax_ccj_dismiss', array( $this, 'notice_dismiss' ) );
    }

    /**
     * Check if qTranslate plugin is active and doesn't have the custom-css-js removed from the settings 
     */
    function check_qtranslatex() {

        if ( ! is_plugin_active( 'qtranslate-x/qtranslate.php' ) ) return false;

        if ( get_option('ccj_dismiss_qtranslate') !== false ) {
            return;
        }

        $qtranslate_post_type_excluded = get_option('qtranslate_post_type_excluded');

        if ( ! is_array( $qtranslate_post_type_excluded ) || array_search( 'custom-css-js', $qtranslate_post_type_excluded ) === false ) { 
            var_dump( $qtranslate_post_type_excluded );
            add_action( 'admin_notices', array( $this, 'check_qtranslate_notice' ) );
            return;
        }
    }

    /**
     * Show a warning about qTranslate 
     */
    function check_qtranslate_notice() {
        $id = 'ccj_dismiss_qtranslate';
        $class = 'notice notice-warning is-dismissible';
        $message = sprintf(__( 'Please remove the <b>custom-css-js</b> post type from the <b>qTranslate settings</b> in order to avoid some malfunctions in the Simple Custom CSS & JS plugin. Check out <a href="%s" target="_blank">this screenshot</a> for more details on how to do that.', 'custom-css-js'), 'https://www.silkypress.com/wp-content/uploads/2016/08/ccj_qtranslate_compatibility.png' );

        printf( '<div class="%1$s" id="%2$s"><p>%3$s</p></div>', $class, $id, $message );

        $this->dismiss_js( $id );

    }

    /**
     * Allow the dismiss button to remove the notice
     */
    function dismiss_js( $slug ) {
    ?>
        <script type='text/javascript'>
        jQuery(function($){
            $(document).on( 'click', '#<?php echo $slug; ?> .notice-dismiss', function() {
            var data = {
                action: 'ccj_dismiss',
                option: '<?php echo $slug; ?>',
            };
            $.post(ajaxurl, data, function(response ) {
                $('#<?php echo $slug; ?>').fadeOut('slow');
            });
            });
        });
        </script>
    <?php
    }


    /**
     * Ajax response for `notice_dismiss` action
     */
    function notice_dismiss() {

        $option = $_POST['option'];

        update_option( $option, 1 );

        wp_die();
    }

}


return new CustomCSSandJS_Warnings();
