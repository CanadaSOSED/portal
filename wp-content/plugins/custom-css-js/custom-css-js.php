<?php
/**
 * Plugin Name: Simple Custom CSS and JS 
 * Plugin URI: https://wordpress.org/plugins/custom-css-js/
 * Description: Easily add Custom CSS or JS to your website with an awesome editor.
 * Version: 3.3 
 * Author: Diana Burduja
 * Author URI: https://www.silkypress.com/
 * License: GPL2
 *
 * Text Domain: custom-css-js 
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'CustomCSSandJS' ) ) :
    define( 'CCJ_VERSION', '3.3' );
/**
 * Main CustomCSSandJS Class
 *
 * @class CustomCSSandJS 
 */
final class CustomCSSandJS {
    public $plugins_url = '';
    public $plugin_dir_path = '';
    public $plugin_file = __FILE__;
    public $search_tree = false;
    public $upload_dir = '';
    public $upload_url = '';
    protected static $_instance = null; 


    /**
     * Main CustomCSSandJS Instance
     *
     * Ensures only one instance of CustomCSSandJS is loaded or can be loaded
     *
     * @static
     * @return CustomCSSandJS - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
      * Cloning is forbidden.
      */
    public function __clone() {
         _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'custom-css-js' ), '1.0' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'custom-css-js' ), '1.0' );
    }

    /**
     * CustomCSSandJS Constructor
     * @access public
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        $this->plugins_url = plugins_url( '/', __FILE__ );
        $this->plugin_dir_path = plugin_dir_path( __FILE__ );
        $wp_upload_dir = wp_upload_dir();
        $this->upload_dir = $wp_upload_dir['basedir'] . '/custom-css-js';
        $this->upload_url = $wp_upload_dir['baseurl'] . '/custom-css-js';
         if ( is_admin() ) {
            $this->load_plugin_textdomain();
            add_action('admin_init', array($this, 'create_roles'));
            include_once( 'includes/admin-screens.php' );
            include_once( 'includes/admin-addons.php' );
            include_once( 'includes/admin-warnings.php' );
            include_once( 'includes/admin-notices.php' );
         }

        $this->search_tree = get_option( 'custom-css-js-tree' );

        if ( ! $this->search_tree || count( $this->search_tree ) == 0 ) {
            return false;
        }

        if ( is_null( self::$_instance ) ) {
            $this->print_code_actions();
        } 
    }

    /**
     * Add the appropriate wp actions
     */
    function print_code_actions() {
        foreach( $this->search_tree as $_key => $_value ) {
            $action = 'wp_';
            if ( strpos( $_key, 'admin' ) !== false ) {
                $action = 'admin_';
            }
            if ( strpos( $_key, 'login' ) !== false ) {
                $action = 'login_';
            }
            if ( strpos( $_key, 'header' ) !== false ) {
                $action .= 'head';
            } else {
                $action .= 'footer';
            }
            add_action( $action, array( $this, 'print_' . $_key ) );
        }
    }

    /**
     * Print the custom code.
     */
    public function __call( $function, $args ) {

        if ( strpos( $function, 'print_' ) === false ) {
            return false;
        }

        $function = str_replace( 'print_', '', $function );

        if ( ! isset( $this->search_tree[ $function ] ) ) {
            return false;
        } 

        $args = $this->search_tree[ $function ];

        if ( ! is_array( $args ) || count( $args ) == 0 ) {
            return false;
        }

        // print the `internal` code
        if ( strpos( $function, 'internal' ) !== false ) {

            $before = '<!-- start Simple Custom CSS and JS -->' . PHP_EOL; 
            $after = '<!-- end Simple Custom CSS and JS -->' . PHP_EOL;
            if ( strpos( $function, 'css' ) !== false ) {
                $before .= '<style type="text/css">' . PHP_EOL;
                $after = '</style>' . PHP_EOL . $after;
            }
            if ( strpos( $function, 'js' ) !== false ) {
                $before .= '<script type="text/javascript">' . PHP_EOL;
                $after = '</script>' . PHP_EOL . $after;
            }


            foreach( $args as $_post_id ) {
                if ( strstr( $_post_id, 'css' ) || strstr( $_post_id, 'js' ) ) {
                    @include_once( $this->upload_dir . '/' . $_post_id );
                } else {
                    $post = get_post( $_post_id );
                    echo $before . $post->post_content . $after;
                }
            }            
        }

        // link the `external` code
        if ( strpos( $function, 'external' ) !== false) {
            $in_footer = false;
            if ( strpos( $function, 'footer' ) !== false ) {
                $in_footer = true;
            }
            
            if ( strpos( $function, 'js' ) !== false ) {
                foreach( $args as $_filename ) {
                    echo PHP_EOL . "<script type='text/javascript' src='".$this->upload_url . '/' . $_filename."'></script>" . PHP_EOL;
                }
            }

            if ( strpos( $function, 'css' ) !== false ) {
                foreach( $args as $_filename ) {
                    $shortfilename = preg_replace( '@\.css\?v=.*$@', '', $_filename );
                    echo PHP_EOL . "<link rel='stylesheet' id='".$shortfilename ."-css'  href='".$this->upload_url . '/' . $_filename ."' type='text/css' media='all' />" . PHP_EOL;
                }
            }
        }

        // link the HTML code
        if ( strpos( $function, 'html' ) !== false ) {
            foreach( $args as $_post_id ) {
                $_post_id = str_replace('.html', '', $_post_id);
                $post = get_post( $_post_id );
                echo $post->post_content . PHP_EOL;
            }            

        }
    }

    /**
     * Create the custom-css-js post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                   => _x( 'Custom Code', 'post type general name', 'custom-css-js'),
            'singular_name'          => _x( 'Custom Code', 'post type singular name', 'custom-css-js'),
            'menu_name'              => _x( 'Custom CSS & JS', 'admin menu', 'custom-css-js'),
            'name_admin_bar'         => _x( 'Custom Code', 'add new on admin bar', 'custom-css-js'),
            'add_new'                => _x( 'Add Custom Code', 'add new', 'custom-css-js'),
            'add_new_item'           => __( 'Add Custom Code', 'custom-css-js'),
            'new_item'               => __( 'New Custom Code', 'custom-css-js'),
            'edit_item'              => __( 'Edit Custom Code', 'custom-css-js'),
            'view_item'              => __( 'View Custom Code', 'custom-css-js'),
            'all_items'              => __( 'All Custom Code', 'custom-css-js'),
            'search_items'           => __( 'Search Custom Code', 'custom-css-js'),
            'parent_item_colon'      => __( 'Parent Custom Code:', 'custom-css-js'),
            'not_found'              => __( 'No Custom Code found.', 'custom-css-js'),
            'not_found_in_trash'     => __( 'No Custom Code found in Trash.', 'custom-css-js')
        );

        $capability_type = 'custom_css';
        $capabilities = array(
            'edit_post'              => "edit_{$capability_type}",
            'read_post'              => "read_{$capability_type}",
            'delete_post'            => "delete_{$capability_type}",
            'edit_posts'             => "edit_{$capability_type}s",
            'edit_others_posts'      => "edit_others_{$capability_type}s",
            'publish_posts'          => "publish_{$capability_type}s",
            'read'                   => "read",
            'delete_posts'           => "delete_{$capability_type}s",
            'delete_published_posts' => "delete_published_{$capability_type}s",
            'delete_others_posts'    => "delete_others_{$capability_type}s",
            'edit_published_posts'   => "edit_published_{$capability_type}s",
            'create_posts'           => "edit_{$capability_type}s",
        );

        $args = array(
            'labels'                 => $labels,
            'description'            => __( 'Custom CSS and JS code', 'custom-css-js' ),
            'public'                 => false,
            'publicly_queryable'     => false,
            'show_ui'                => true,
            'show_in_menu'           => true,
            'menu_position'          => 100,
            'menu_icon'              => 'dashicons-plus-alt',
            'query_var'              => false,
            'rewrite'                => array( 'slug' => 'custom-css-js' ),
            'capability_type'        => $capability_type,
            'capabilities'           => $capabilities, 
            'has_archive'            => true,
            'hierarchical'           => false,
            'exclude_from_search'    => true,
            'menu_position'          => null,
            'can_export'             => false,
            'supports'               => array( 'title' )
        );

        register_post_type( 'custom-css-js', $args );
    }

        
    /**
     * Create roles and capabilities.
     */
    function create_roles() {
        global $wp_roles;


        if ( !current_user_can('update_plugins') )
            return;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        if ( isset($wp_roles->roles['css_js_designer'])) 
            return;

        // Add Web Designer role
        add_role( 'css_js_designer', __( 'Web Designer', 'custom-css-js'), array(
            'level_9'                => true,
            'level_8'                => true,
            'level_7'                => true,
            'level_6'                => true,
            'level_5'                => true,
            'level_4'                => true,
            'level_3'                => true,
            'level_2'                => true,
            'level_1'                => true,
            'level_0'                => true,
            'read'                   => true,
            'read_private_pages'     => true,
            'read_private_posts'     => true,
            'edit_users'             => true,
            'edit_posts'             => true,
            'edit_pages'             => true,
            'edit_published_posts'   => true,
            'edit_published_pages'   => true,
            'edit_private_pages'     => true,
            'edit_private_posts'     => true,
            'edit_others_posts'      => true,
            'edit_others_pages'      => true,
            'publish_posts'          => true,
            'publish_pages'          => true,
            'delete_posts'           => true,
            'delete_pages'           => true,
            'delete_private_pages'   => true,
            'delete_private_posts'   => true,
            'delete_published_pages' => true,
            'delete_published_posts' => true,
            'delete_others_posts'    => true,
            'delete_others_pages'    => true,
            'manage_categories'      => true,
            'moderate_comments'      => true,
            'unfiltered_html'        => true,
            'upload_files'           => true,
        ) );

        $capabilities = array();

        $capability_types = array( 'custom_css' );

        foreach ( $capability_types as $capability_type ) {

            $capabilities[ $capability_type ] = array(
                // Post type
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_published_{$capability_type}s",
            );
        }

        foreach ( $capabilities as $cap_group ) {
            foreach ( $cap_group as $cap ) {
                $wp_roles->add_cap( 'css_js_designer', $cap );
                $wp_roles->add_cap( 'administrator', $cap );
            }
        }
    }


	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'custom-css-js', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

}

endif; 

/**
 * Returns the main instance of CustomCSSandJS 
 *
 * @return CustomCSSandJS 
 */
function CustomCSSandJS() {
    return CustomCSSandJS::instance();
}

CustomCSSandJS();


/**
 * Plugin action link to Settings page
*/
function custom_css_js_plugin_action_links( $links ) {

    $settings_link = '<a href="edit.php?post_type=custom-css-js">' .
        esc_html( __('Settings', 'custom-css-js' ) ) . '</a>';

    return array_merge( array( $settings_link), $links );
    
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'custom_css_js_plugin_action_links' );


