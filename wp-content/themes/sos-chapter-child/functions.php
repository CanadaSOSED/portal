<?php
function understrap_remove_scripts() {
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );

    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

	// Get the theme data
	$the_theme = wp_get_theme();

    wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme-min.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_style( 'dev-css', get_stylesheet_directory_uri() . '/css/dev.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme-min.js', array(), $the_theme->get( 'Version' ), true );
}


// Custom SOS Login Button -  Displayed on /page-templates/login-page.php
////////////////////////////////////////////////////////////////////////////////////
function sos_wp_loginout($class ='', $redirect = '', $echo = true) {
    if ( ! is_user_logged_in() )
        $link = '<a href="' . esc_url( wp_login_url($redirect) ) . '" class="' . $class . '">' . __('Log In') . '</a>';
    else
        $link = '<a href="' . esc_url( wp_logout_url($redirect) ) . '" class="' . $class . '">' . __('Log Out') . '</a>';

    if ( $echo ) {
        /**
         * Filters the HTML output for the Log In/Log Out link.
         *
         * @since 1.5.0
         *
         * @param string $link The HTML link content.
         */
        echo apply_filters( 'loginout', $link );
    } else {
        /** This filter is documented in wp-includes/general-template.php */
        return apply_filters( 'loginout', $link );
    }
}

// Custom SOS Register Button - Displayed on /page-templates/login-page.php
//////////////////////////////////////////////////////////////////////////////////////////
function sos_wp_register( $before = '<li>', $after = '</li>', $echo = true ) {
    if ( ! is_user_logged_in() ) {
        if ( get_option('users_can_register') )
            $link = $before . '<a href="' . esc_url( wp_registration_url() ) . '" class="btn btn-info">' . __('Create Account') . '</a>' . $after;
        else
            $link = '';
    } elseif ( current_user_can( 'read' ) ) {
        $link = $before . '<a href="' . admin_url() . '" class="btn btn-info">' . __('View Dashboard') . '</a>' . $after;
    } else {
        $link = '';
    }

    /**
     * Filters the HTML link to the Registration or Admin page.
     *
     * Users are sent to the admin page if logged-in, or the registration page
     * if enabled and logged-out.
     *
     * @since 1.5.0
     *
     * @param string $link The HTML code for the link to the Registration or Admin page.
     */
    $link = apply_filters( 'register', $link );

    if ( $echo ) {
        echo $link;
    } else {
        return $link;
    }
}



// Woocommerce products (aka sessions) - display adjustments
//////////////////////////////////////////////////////////////////////////////////////////

// Hide Image
remove_action ( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images',  20 );

// Hide Reviews
remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);



//begin --ismara - 2018-07-19 - redesign products page
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

if ( ! function_exists( 'woocommerce_template_single_instructor' ) ) {

	function woocommerce_template_single_instructor() {
		wc_get_template( 'single-product/instructor.php' );
	}
}

add_action ('woocommerce_after_single_product_summary', 'woocommerce_template_single_instructor',5);
//End --ismara - 2018-07-19 - redesign products page



// // Allow SVG Upload
// //////////////////////////////////////////////////////////////////////
// function cc_mime_types_kb($mimes) {
//   $mimes["svg"] = "image/svg+xml";
//   return $mimes;
// }
// add_filter("upload_mimes", "cc_mime_types_kb");


// // Remove Auto-Complete from login page password field
// //////////////////////////////////////////////////////////////////////
// add_action('login_init', 'acme_autocomplete_login_init_kb');
// function acme_autocomplete_login_init_kb()
// {
//     ob_start();
// }

// add_action('login_form', 'acme_autocomplete_login_form_kb');
// function acme_autocomplete_login_form_kb()
// {
//     $content = ob_get_contents();
//     ob_end_clean();
//     $content = str_replace('id="user_pass"', 'id="user_pass" autocomplete="off"', $content);
//     echo $content;
// }


// // Remove CSS version Parameter (messes with cacheing in chrome)
// //////////////////////////////////////////////////////////////////////
// function remove_cssjs_ver_kb( $src ) {
//     if( strpos( $src, '?ver=' ) )
//         $src = remove_query_arg( 'ver', $src );
//     return $src;
// }
// add_filter( 'style_loader_src', 'remove_cssjs_ver_kb', 10, 2 );
// add_filter( 'script_loader_src', 'remove_cssjs_ver_kb', 10, 2 );

// // Rename Default "Post" type to "Articles"
// //////////////////////////////////////////////////////////////////////
// function change_post_label() {
//     global $menu;
//     global $submenu;
//     $menu[5][0] = 'Articles';
//     $submenu['edit.php'][5][0] = 'Articles';
//     $submenu['edit.php'][10][0] = 'Add Article';
//     $submenu['edit.php'][16][0] = 'Article Tags';
// }
// function change_post_object() {
//     global $wp_post_types;
//     $labels = &$wp_post_types['post']->labels;
//     $labels->name = 'Articles';
//     $labels->singular_name = 'Article';
//     $labels->add_new = 'Add Article';
//     $labels->add_new_item = 'Add Article';
//     $labels->edit_item = 'Edit Article';
//     $labels->new_item = 'Article';
//     $labels->view_item = 'View Article';
//     $labels->search_items = 'Search Articles';
//     $labels->not_found = 'No Articles found';
//     $labels->not_found_in_trash = 'No Articles found in Trash';
//     $labels->all_items = 'All Articles';
//     $labels->menu_name = 'Articles';
//     $labels->name_admin_bar = 'Articles';
// }

// add_action( 'admin_menu', 'change_post_label' );
// add_action( 'init', 'change_post_object' );


// Rename Default "Post" type to "Sessions"
//////////////////////////////////////////////////////////////////////
function sos_chapter_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['product']->labels;
    $labels->name = 'Sessions';
    $labels->singular_name = 'Session';
    $labels->add_new = 'Add Session';
    $labels->add_new_item = 'Add Session';
    $labels->edit_item = 'Edit Session';
    $labels->new_item = 'Session';
    $labels->view_item = 'View Session';
    $labels->search_items = 'Search Sessions';
    $labels->not_found = 'No Sessions found';
    $labels->not_found_in_trash = 'No Sessions found in Trash';
    $labels->all_items = 'All Sessions';
    $labels->menu_name = 'Sessions';
    $labels->name_admin_bar = 'Sessions';
}

add_action( 'init', 'sos_chapter_change_post_object' );


// Rename Default "Category" Taxonomy to "Topics"
//////////////////////////////////////////////////////////////////////
function sos_chapter_change_cat_object() {
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['product_cat']->labels;
    $labels->name = 'Topic';
    $labels->singular_name = 'Topic';
    $labels->add_new = 'Add Topic';
    $labels->add_new_item = 'Add Topic';
    $labels->edit_item = 'Edit Topic';
    $labels->new_item = 'Topic';
    $labels->view_item = 'View Topic';
    $labels->search_items = 'Search Topics';
    $labels->not_found = 'No Topics found';
    $labels->not_found_in_trash = 'No Topics found in Trash';
    $labels->all_items = 'All Topics';
    $labels->menu_name = 'Topic';
    $labels->name_admin_bar = 'Topic';
}
add_action( 'init', 'sos_chapter_change_cat_object' );



// Hide tabs on single product (aka session page)
//////////////////////////////////////////////////////////////////////

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );          // Remove the description tab
    //unset( $tabs['reviews'] );          // Remove the reviews tab
    unset( $tabs['additional_information'] );   // Remove the additional information tab

    return $tabs;

}


add_filter( 'woocommerce_subcategory_count_html', 'sos_subcat_count', 10, 2);
function sos_subcat_count( $markup, $category){

    return '<p class="count pt-2 text-secondary">' . $category->count . ' Sessions Available</p>';
}

// Change Default "Shop" page title to "Sessions"

add_filter( 'woocommerce_page_title', 'woo_shop_page_title');
function woo_shop_page_title( $page_title ) {
  if( 'Shop' == $page_title) {
    return "Sessions";
 } else {
    return $page_title;
 }
}


// Hide Product Thumbnail Placeholder
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

// Start Card Wrapper
add_action( 'woocommerce_before_subcategory', 'sos_cat_card_start', 9 );
function sos_cat_card_start() {
    echo '<div class="card align-self-stretch">';
    echo '<div class="card-body">';
}

// End Card Wrapper
add_action( 'woocommerce_after_subcategory', 'sos_cat_card_end', 10 );
function sos_cat_card_end() {
    echo '</div>';
    echo '</div>';
}


// Start Card Wrapper
add_action( 'woocommerce_before_shop_loop_item', 'sos_card_start', 10 );
function sos_card_start() {
    echo '<div class="card align-self-stretch">';
    echo '<div class="card-body">';
}


// Start Card Wrapper
add_action( 'woocommerce_shop_loop_item_title', 'sos_card_title_start', 9 );
function sos_card_title_start() {
    echo '<div class="card-title" style="font-weight:500;line-height:1.1;font-size:1.5em;">';

}


// Start Card Wrapper
add_action( 'woocommerce_after_shop_loop_item_title', 'sos_card_title_end', 6 );
function sos_card_title_end() {
    global $post;
    echo '</div>';
    echo '<a href="' . get_permalink( $post->ID ) . '">View Details</a>';

}

// End Card Wrapper
add_action( 'woocommerce_after_shop_loop_item', 'sos_card_end', 10 );
function sos_card_end() {

    echo '</div>';

    echo '<div class="card-footer">';
    $product_cats = wp_get_post_terms( get_the_ID(), 'session_type' );

    if ( $product_cats && ! is_wp_error ( $product_cats ) ){

        $single_cat = array_shift( $product_cats );

        echo '<span class="badge badge-primary">' . $single_cat->name . '</span>';
    }

    echo '</div>';
    echo '</div>';


}

// Change number or products per row to 3
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
    function loop_columns() {
        return 3; // 3 products per row
    }
}


// Remove Refunds for Anyone who isn't from SOS HQ

add_action('admin_head', 'sos_hide_wc_refund_button');

function sos_hide_wc_refund_button() {

    global $post;

    if (current_user_can('create_sites')) {
        return;
    }
    if (strpos($_SERVER['REQUEST_URI'], 'post.php?post=') === false) {
        return;
    }

    if (empty($post) || $post->post_type != 'shop_order') {
        return;
    }
?>
    <script>
      jQuery(function () {
            jQuery('.refund-items').hide();
            jQuery('.order_actions option[value=send_email_customer_refunded_order]').remove();
            if (jQuery('#original_post_status').val()=='wc-refunded') {
                jQuery('#s2id_order_status').html('Refunded');
            } else {
                jQuery('#order_status option[value=wc-refunded]').remove();
            }
        });
    </script>
    <?php

}


// Changed the 'Add to Cart' text
//////////////////////////////////////////////////////////////////////
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_product_add_to_cart_text' );

function woo_custom_product_add_to_cart_text() {

    return __( 'Enroll Now', 'woocommerce' );

}

// Add Enroll Now Button Text
//////////////////////////////////////////////////////////////////////
add_filter( 'woocommerce_product_single_add_to_cart_text', 'themeprefix_cart_button_text' );

function themeprefix_cart_button_text() {
  return __( 'Enroll Now', 'woocommerce' );
}


// Redirect to Pay Now instead of View Cart
//////////////////////////////////////////////////////////////////////
add_filter('woocommerce_add_to_cart_redirect', 'themeprefix_add_to_cart_redirect');

function themeprefix_add_to_cart_redirect() {
    global $woocommerce;
    $checkout_url = $woocommerce->cart->get_checkout_url();
    return $checkout_url;
}



// RESTRICT AMOUNT IN CART TO 1
//////////////////////////////////////////////////////////////////////
add_filter( 'woocommerce_add_to_cart_validation', 'so_27030769_maybe_empty_cart', 10, 3 );

function so_27030769_maybe_empty_cart( $valid, $product_id, $quantity ) {

    if( ! empty ( WC()->cart->get_cart() ) && $valid ){
        WC()->cart->empty_cart();
        // wc_add_notice( 'Whoa hold up. You can only have 1 item in your cart', 'error' );
    }

    return $valid;

}



// Allow authors on single products
//////////////////////////////////////////////////////////////////////

if ( post_type_exists( 'product' ) ) {
    add_post_type_support( 'product', 'author' );
}



// Remove additional signup fields
//////////////////////////////////////////////////////////////////////
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {

    // REMOVING BILLING PHONE NUMBER
    unset($fields['billing']['billing_phone']);

    // REMOVING BILLING COMPANY
    unset($fields['billing']['billing_company']);

    // REMOVING ADDITIONAL INFORMATION FIELD
    unset($fields['order']['order_comments']);

    return $fields;
}



// Description for shipping class page to aid users.
//////////////////////////////////////////////////////////////////////
function product_attribute_description() {
    echo wpautop( __( 'Attribute terms can be assigned to products and variations.<br/><br/><b>Note</b>: Deleting a term will remove it from all products and variations to which it has been assigned. Recreating a term will not automatically assign it back to products.', 'woocommerce' ) );
}


//ismara - 2/13/2018 - Adding new fields options for Contact us
// Dynamic Select for Contact Form 7
function dynamic_select_for_custom_blogs($choices, $args=array()) {

	// Here we grab the blogs using the arguments originated from the shortcode
	$get_custom_blogs = get_sites($args);

	// If we have blogs, proceed
	if ($get_custom_blogs) {
    //insert a blank option - this is a required field
    $choices['---'] = '';
		// Foreach found custom blog, we build the option using the [key] => [value] fashion
		foreach ($get_custom_blogs as $custom_blog) {
				$choices[$custom_blog->blogname] = $custom_blog->blogname;
			}

	// If we don't have blogs, halt! Lets use a generic not found option
	} else {

		// Just a generic option to inform nothing was found
		$choices['No blogs found'] = 'No blogs found';

	}
	return $choices;
}
// Lets add a suggestive name to our filter (we will use it on the shortcode)
add_filter('conjure-blogs-dynamically', 'dynamic_select_for_custom_blogs', 10, 2);

// Hide referral link for customers without any order.
//////////////////////////////////////////////////////////////////////
add_filter('wpgens_raf_link','gens_raf_link',10,3);
function gens_raf_link($raf_link, $referral_id, $type) {
	$user_id = get_current_user_id();
	if($user_id == 0) {
		return 0;
	}
	$customer_orders = get_posts( array(
	    'numberposts' => -1,
	    'meta_query' => array(
			array(
				'key'    => '_customer_user',
	    		'value'  => $user_id,
			),
		),
	    'post_type'   => wc_get_order_types(),
	    'post_status' => 'wc-completed',
	) );

	if(count($customer_orders) < 1) {
		return "You need at least one order for referral link";
	} else {
		return $raf_link;
	}
}



@include 'inc/post-type-opportunities.php';
@include 'inc/widgets.php';
@include 'inc/breadcrumbs.php';
@include 'inc/recent-posts-by-category-widget.php';
@include 'inc/customizer.php';
//@include 'inc/related-posts-by-category-widget.php';
