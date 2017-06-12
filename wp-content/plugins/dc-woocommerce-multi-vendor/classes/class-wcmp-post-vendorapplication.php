<?php

/**
 * WCMp vendor application Class
 *
 * @version		2.4.3
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Vendor_Application {

    private $post_type;
    public $dir;
    public $file;

    public function __construct() {
        $this->post_type = 'wcmp_vendorrequest';
        $this->register_post_type();
    }

    /**
     * Register vendor-application post type
     *
     * @access public
     * @return void
     */
    function register_post_type() {
        global $WCMp;
        if (post_type_exists($this->post_type))
            return;
        $post_type_visibility = false;
        if(is_super_admin(get_current_user_id())){
            $post_type_visibility = true;
        }
        $labels = array(
            'name' => _x('Vendor Application', $WCMp->text_domain),
            'singular_name' => _x('Vendor Application', $WCMp->text_domain),
            'add_new' => _x('Add New', $this->post_type, $WCMp->text_domain),
            'add_new_item' => sprintf(__('Add New %s', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'edit_item' => sprintf(__('View %s', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'new_item' => sprintf(__('New %s', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'all_items' => sprintf(__('All %s', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'view_item' => sprintf(__('View %s', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'search_items' => sprintf(__('Search %a', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'not_found' => sprintf(__('No %s found', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'not_found_in_trash' => sprintf(__('No %s found in trash', $WCMp->text_domain), __('Application', $WCMp->text_domain)),
            'parent_item_colon' => '',
            'all_items' => __('Vendor Application', $WCMp->text_domain),
            'menu_name' => __('Vendor Application', $WCMp->text_domain)
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => $post_type_visibility,
            'show_in_menu' => 'users.php',
            'show_in_nav_menus' => false,
            'query_var' => false,
            'rewrite' => true,
            'capability_type' => 'post',
            'capabilities' => array('create_posts' => false, 'delete_posts' => false,),
            'map_meta_cap' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'supports' => array('')
        );
        register_post_type($this->post_type, $args);
    }

}
