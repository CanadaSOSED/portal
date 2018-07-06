<?php
/**
 * Plugin Name: Responsive Tabs
 * Plugin URI: http://wpdarko.com/responsive-tabs/
 * Description: A responsive, simple and clean way to display your content. Create new tabs in no-time (custom type) and copy-paste the shortcode into any post/page. Find help and information on our <a href="http://wpdarko.com/support/">support site</a>. This free version is NOT limited and does not contain any ad. Check out the <a href='http://wpdarko.com/responsive-tabs/'>PRO version</a> for more great features.
 * Version: 3.3.1
 * Author: WP Darko
 * Author URI: http://wpdarko.com
 * Text Domain: responsive-tabs
 * Domain Path: /lang/
 *License: GPL2
 */

// Loading text domain
add_action( 'plugins_loaded', 'rtbs_load_plugin_textdomain' );
function rtbs_load_plugin_textdomain() {
  load_plugin_textdomain( 'responsive-tabs', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}


/* Check for the PRO version */
add_action( 'admin_init', 'rtbs_free_pro_check' );
function rtbs_free_pro_check() {
    if (is_plugin_active('responsive-tabs-pro/rtbs_pro.php')) {

        function my_admin_notice(){
        echo '<div class="updated">
                <p><strong>PRO</strong> version is activated.</p>
              </div>';
        }
        add_action('admin_notices', 'my_admin_notice');

        deactivate_plugins(__FILE__);
    }
}


/* Recover old data if there is */
add_action( 'init', 'rtbs_old_data' );
function rtbs_old_data() {

    if(!get_option('rtbs_is_updated_yn8')){

        global $post;
        $args = array(
            'post_type' => 'rtbs_tabs',
            'posts_per_page'   => 9999,
        );

        $get_old = get_posts( $args );
        foreach ( $get_old as $post ) : setup_postdata( $post );

            $current_id = get_the_id();
            $old_data_tabs = get_post_meta( $current_id, 'rtbs_tabs_head', false );

            $i = 0;
            foreach ($old_data_tabs as $key => $odata) {
                $num = count($key);
                $num = $num +1;

                $test_man[$key]['_rtbs_title'] = $odata['rtbs_title'];
                $test_man[$key]['_rtbs_content'] = $odata['rtbs_content'];

                update_post_meta($current_id, '_rtbs_tabs_head', $test_man);

            }

            $old_data_settings = get_post_meta( $current_id, 'rtbs_settings_head', false );

            $i = 0;
            foreach ($old_data_settings as $key => $odata) {
                $num = count($key);
                $num = $num +1;

                $var1 = $odata['rtbs_tabs_bg_color'];
                $var2 = $odata['rtbs_breakpoint'];

                update_post_meta($current_id, '_rtbs_tabs_bg_color', $var1);
                update_post_meta($current_id, '_rtbs_breakpoint', $var2);

            }

        endforeach;

        update_option('rtbs_is_updated_yn8', 'old_data_recovered');

    }

}


/* Enqueue scripts & styles */
add_action( 'wp_enqueue_scripts', 'add_rtbs_scripts', 99 );
function add_rtbs_scripts() {
	wp_enqueue_style( 'rtbs', plugins_url('css/rtbs_style.min.css', __FILE__));
  wp_enqueue_script( 'rtbs', plugins_url('js/rtbs.min.js', __FILE__), array( 'jquery' ));
}


/* Enqueue admin scripts & styles */
add_action( 'admin_enqueue_scripts', 'add_admin_rtbs_style' );
function add_admin_rtbs_style($hook) {
    global $post_type;
    if( 'rtbs_tabs' == $post_type ) {
        wp_enqueue_style( 'rtbs', plugins_url('css/admin_de_style.min.css', __FILE__));
    }
}


// Register Tabs post type
add_action( 'init', 'register_rtbs_type' );
function register_rtbs_type() {
	$labels = array(
		'name'               => __( 'Tab sets', 'responsive-tabs' ),
		'singular_name'      => __( 'Tab set', 'responsive-tabs' ),
		'menu_name'          => __( 'Tab sets', 'responsive-tabs' ),
		'name_admin_bar'     => __( 'Tab set', 'responsive-tabs' ),
		'add_new'            => __( 'Add New', 'responsive-tabs' ),
		'add_new_item'       => __( 'Add New Tab set', 'responsive-tabs' ),
		'new_item'           => __( 'New Tab set', 'responsive-tabs' ),
		'edit_item'          => __( 'Edit Tab set', 'responsive-tabs' ),
		'view_item'          => __( 'View Tab set', 'responsive-tabs' ),
		'all_items'          => __( 'All Tab sets', 'responsive-tabs' ),
		'search_items'       => __( 'Search Tab sets', 'responsive-tabs' ),
		'not_found'          => __( 'No Tab sets found.', 'responsive-tabs' ),
		'not_found_in_trash' => __( 'No Tab sets found in Trash.', 'responsive-tabs' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
        'show_in_admin_bar'  => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor' ),
        'menu_icon'          => 'dashicons-plus'
	);
	register_post_type( 'rtbs_tabs', $args );
}


// Customize update messages
add_filter( 'post_updated_messages', 'rtbs_updated_messages' );
function rtbs_updated_messages( $messages ) {
	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );
	$messages['rtbs_tabs'] = array(
		1  => __( 'Tab set updated.', 'responsive-tabs' ),
		4  => __( 'Tab set updated.', 'responsive-tabs' ),
		6  => __( 'Tab set published.', 'responsive-tabs' ),
		7  => __( 'Tab set saved.', 'responsive-tabs' ),
		10 => __( 'Tab set draft updated.', 'responsive-tabs' )
	);

	if ( $post_type_object->publicly_queryable ) {
		$permalink = get_permalink( $post->ID );

		$view_link = sprintf( '', '', '' );
		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link = sprintf( '', '', '' );
		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}
	return $messages;
}


// Add the metabox class (CMB2)
if ( file_exists( dirname( __FILE__ ) . '/inc/cmb2/init.php' ) ) {
    require_once dirname( __FILE__ ) . '/inc/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/inc/CMB2/init.php' ) ) {
    require_once dirname( __FILE__ ) . '/inc/CMB2/init.php';
}


// Create the metabox class (CMB2)
add_action( 'cmb2_init', 'rtbs_register_group_metabox' );
require_once('inc/rtbs-metaboxes.php');


// Add shortcode column
add_action( 'manage_rtbs_tabs_posts_custom_column' , 'dkrtbs_custom_columns', 10, 2 );
add_filter('manage_rtbs_tabs_posts_columns' , 'add_rtbs_tabs_columns');
function dkrtbs_custom_columns( $column, $post_id ) {
    switch ( $column ) {
	case 'shortcode' :
		global $post;
		$slug = '' ;
		$slug = $post->post_name;
        $shortcode = '<span style="display:inline-block;border:solid 2px lightgray; background:white; padding:0 8px; font-size:13px; line-height:25px; vertical-align:middle;">[rtbs name="'.$slug.'"]</span>';
	    echo $shortcode;
	    break;
    }
}
function add_rtbs_tabs_columns($columns) { return array_merge($columns, array('shortcode' => 'Shortcode')); }


// Create the Responsive Tabs shortcode
function rtbs_sc($atts) {
	extract(shortcode_atts(array(
		"name" => ''
	), $atts));

    global $post;
    $args = array('post_type' => 'rtbs_tabs', 'name' => $name);
    $custom_posts = get_posts($args);
    foreach($custom_posts as $post) : setup_postdata($post);

	$entries = get_post_meta( $post->ID, '_rtbs_tabs_head', true );
    $options = get_post_meta( $post->ID, '_rtbs_settings_head', true );

    // Forcing original fonts?
    $original_font = get_post_meta( $post->ID, '_rtbs_original_font', true );
    if ($original_font == true){
        $ori_f = 'rtbs_tab_ori';
    } else {
        $ori_f = '';
    }

    $rtbs_breakpoint = get_post_meta( $post->ID, '_rtbs_breakpoint', true );
    $rtbs_color = get_post_meta( $post->ID, '_rtbs_tabs_bg_color', true );

    $output = '';

    /* Outputing the options in invisible divs */
    $output = '<div class="rtbs '.$ori_f.' rtbs_'.$name.'">';
    $output .= '<div class="rtbs_slug" style="display:none">'.$name.'</div>';
    $output .= '<div class="rtbs_breakpoint" style="display:none">'.$rtbs_breakpoint.'</div>';
    $output .= '<div class="rtbs_color" style="display:none">'.$rtbs_color.'</div>';

    $output .= '
        <div class="rtbs_menu">
            <ul>
                <li class="mobile_toggle">&zwnj;</li>';
                foreach ($entries as $key => $tabs) {
                    if ($key == 0){
                    $output .= '<li class="current">';
                    $output .= '<a style="background:'.$rtbs_color.'" class="active '.$name.'-tab-link-'.$key.'" href="#" data-tab="#'.$name.'-tab-'.$key.'">';

                    (!empty($tabs['_rtbs_title'])) ?
                            $output .= $tabs['_rtbs_title'] :
                                $output .= '&nbsp;';

                    $output .= '</a>';
                    $output .= '</li>';
                    } else {
                    $output .= '<li>';
                    $output .= '<a href="#" data-tab="#'.$name.'-tab-'.$key.'" class="'.$name.'-tab-link-'.$key.'">';
                    (!empty($tabs['_rtbs_title'])) ?
                            $output .= $tabs['_rtbs_title'] :
                                $output .= '&nbsp;';

                    $output .= '</a>';
                    $output .= '</li>';
                    }
                }
    $output .= '
            </ul>
        </div>';

    foreach ($entries as $key => $tabs) {
        if ($key == 0){
            $output .= '<div style="border-top:7px solid '.$rtbs_color.';" id="'.$name.'-tab-'.$key.'" class="rtbs_content active">';
                $output .= do_shortcode(wpautop($tabs['_rtbs_content']));
            $output .= '<div style="margin-top:30px; clear:both;"></div></div>';
        } else {
            $output .= '<div style="border-top:7px solid '.$rtbs_color.';" id="'.$name.'-tab-'.$key.'" class="rtbs_content">';
                $output .= do_shortcode(wpautop($tabs['_rtbs_content']));
            $output .= '<div style="margin-top:30px; clear:both;"></div></div>';
        }
    }
    $output .= '
    </div>
    ';

  endforeach; wp_reset_postdata();
  return $output;

}

add_shortcode("rtbs", "rtbs_sc");
?>
