<?php
/**
 * Understrap functions and definitions
 *
 * @package sos-primary
 */


// Allow SVG Upload
//////////////////////////////////////////////////////////////////////
function cc_mime_types($mimes) {
  $mimes["svg"] = "image/svg+xml";
  return $mimes;
}
add_filter("upload_mimes", "cc_mime_types");


// Remove Auto-Complete from login page password field
//////////////////////////////////////////////////////////////////////
add_action('login_init', 'acme_autocomplete_login_init');
function acme_autocomplete_login_init()
{
    ob_start();
}

add_action('login_form', 'acme_autocomplete_login_form');
function acme_autocomplete_login_form()
{
    $content = ob_get_contents();
    ob_end_clean();
    $content = str_replace('id="user_pass"', 'id="user_pass" autocomplete="off"', $content);
    echo $content;
}


// Remove CSS version Parameter (messes with cacheing in chrome)
//////////////////////////////////////////////////////////////////////
function remove_cssjs_ver( $src ) {
    if( strpos( $src, '?ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );



// Hide All WordPress and plugin update notifications
//////////////////////////////////////////////////////////////////////
function remove_update_notifications(){

    global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);

}

// Make sure we're on the development server
$ip = getenv('REMOTE_ADDR');

if ($ip = '138.197.168.139') {

    add_filter('pre_site_transient_update_core','remove_update_notifications');
    add_filter('pre_site_transient_update_plugins','remove_update_notifications');
    add_filter('pre_site_transient_update_themes','remove_update_notifications');

}


// Disable User Roles
//////////////////////////////////////////////////////////////////////
add_action('admin_menu', 'remove_built_in_roles');

function remove_built_in_roles() {
    global $wp_roles;

    $roles_to_remove = array('contributor', 'author', 'editor', 'subscriber', 'shop_manager');

    foreach ($roles_to_remove as $role) {
        if (isset($wp_roles->roles[$role])) {
            $wp_roles->remove_role($role);
        }
    }
}

// Rename Flamingo Default "Page" type to "Form Submissions"
//////////////////////////////////////////////////////////////////////

function sos_rename_flamingo_menu( $translated, $original, $domain ) {

$strings = array(
    'Flamingo' => 'Applications',
    'Address Book' => 'Contact Info',
    'Flamingo Address Book' => 'Applicant Contact Information',
    'Inbound Messages' => 'All Applications'
);

if ( isset( $strings[$original] ) && is_admin() ) {
    $translations = get_translations_for_domain( $domain );
    $translated = $translations->translate( $strings[$original] );
}

  return $translated;
}

add_filter( 'gettext', 'sos_rename_flamingo_menu', 10, 3 );

// Rename WooCommerce Default "Post" type to "Sessions"
//////////////////////////////////////////////////////////////////////

function sos_change_woo_post_object() {
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

add_action( 'init', 'sos_change_woo_post_object' );


// Remove WooCommerce Default supports for "products aka: sessions"
//////////////////////////////////////////////////////////////////////

function sos_supports_for_woo_post_object() {

	remove_post_type_support(
		'product',
		'editor',
		'author',
		'trackbacks',
		'comments',
		'revisions',
		'post-formats',
		'thumbnail'
	);
}

add_action( 'init', 'sos_supports_for_woo_post_object' );


// Remove dashboard metaboxes
//////////////////////////////////////////////////////////////////////
function sos_disable_dashboard_widgets() {

	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
  remove_action('welcome_panel', 'wp_welcome_panel'); // Welcome panel on update & first signin

}
add_action('wp_dashboard_setup', 'sos_disable_dashboard_widgets');


function sos_remove_plugin_metaboxes(){
	$post_types = get_post_types();
		// change name of reviews meta box by removing it and adding it back with a new name
	  remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal' );
	  remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' ); // woocommerce activity metabox
}
add_action( 'do_meta_boxes', 'sos_remove_plugin_metaboxes' );


// Add Custom dashboard metaboxes
//////////////////////////////////////////////////////////////////////

// Function that outputs the contents of the dashboard widget

// General Dashboard Content
function sos_dashboard_knowledgebase_widget_function( $post, $callback_args ) {
	echo "<p>This is SOS if you've got any questions about the new system, or are unclear about any processes. If you're question isn't there, someone from HQ will answer your question, and then it will get added to the system for everyone else to see! </p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='http://kb.soscampus.com'>Visit Knowledge Base</a></p>";
}

function sos_dashboard_finance_widget_function( $post, $callback_args ) {
  echo "<p>Expenses, revenues, and everything in between - All finance forms for your Chapter are accessible here. </p> ";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='http://soscampus.com/finance-forms'>Go To Finance Forms</a></p>";
}

function sos_dashboard_princeton_widget_function( $post, $callback_args ) {
  echo "<p>SOS has an awesome partnership with The Princeton Review - offering everyone who gets involved with SOS discount on prep courses. Visit the link below to apply for your discount!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://docs.google.com/forms/d/e/1FAIpQLScva-U8KWwPpcm5wf5xIQwzzkfKX9ziV_JDsY3OlFFnKO6URQ/viewform?usp=sf_link'>Claim Discount</a></p>";
}

function sos_dashboard_training_widget_function( $post, $callback_args ) {
  echo "<p>Need some review on training? Wanting to grow within your department, or try out a new department? Visit our Training Resources site to check them out. </p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='http://www.studentsofferingsupport.ca/TrainingResources/'>Training Resources</a></p>";
}

// HR Dashboard Box
//////////////////////////////////////////////////////////////////////
function sos_dashboard_hr_support_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find role descriptions & expectation agreements, hiring & training guides, in addition to other HR resources.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHaHV3RVJkSmdDSDA'>HR Resources</a></p>";
}

// ED Dashboard box
//////////////////////////////////////////////////////////////////////
function sos_dashboard_ed_support_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find Exam Aid powerpoint templates, sample cover letters for take home packages, and other ED resources.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHNkNKLVQzWkllLVU'>ED Resources</a></p>";
}

// Archived EA Materials
//////////////////////////////////////////////////////////////////////
function sos_dashboard_archived_materials_widget_function( $post, $callback_args ) {
  echo "<p>Looking for old Exam Aid/Take-Home/DEA packages? We've archived them for you to view.</p>";
  echo "<p>Login using <strong>username: amaterials</strong> and <strong>password: amaterials</strong>.";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='http://www.studentsofferingsupport.ca/portal/Files/CourseDownloadPage.php'>Archived Materials</a></p>";
}

// Refund Forms
//////////////////////////////////////////////////////////////////////
function sos_dashboard_refund_widget_function( $post, $callback_args ) {
  echo "<p>Once a donation is made for participation in an Exam Aid session, refunds will be provided only in the extenuating circumstances (unsatisfied customer or cancelled session). If a student would like to submit a refund request, please ask them to fill out this form.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://docs.google.com/forms/d/e/1FAIpQLSfg9HYrST-ZxPfoi4oGMIZFE48tLKHjbSx4pjgMgkwVrEEXJQ/viewform'>Refund Request</a></p>";
}

// EA FAQs
//////////////////////////////////////////////////////////////////////
function sos_dashboard_faq_widget_function( $post, $callback_args ) {
  echo "<p>This link will direct you to our Frequently Asked Exam Aid Questions. Don't see your question listed? You can also submit any questions through this database.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://faq.soscampus.com/'>Access FAQ</a></p>";
}

// EA Presentation templates
//////////////////////////////////////////////////////////////////////
function sos_dashboard_ea_template_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find Exam Aid powerpoint templates and sample cover letters for take home packages.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHNmNtUk1PX2doem8'>FAQ</a></p>";
}

// Finance Mastersheets
//////////////////////////////////////////////////////////////////////
function sos_dashboard_fin_mastersheets_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will see all SOS Chapter's mastersheets. You just need to find your Chapter's specific document (it is in alphabetical order).</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0BxsgrL3RdWEccXBxSV9NU0lRMGs'>Finance Mastersheets</a></p>";
}

// Quickbook Links
//////////////////////////////////////////////////////////////////////
function sos_dashboard_quickbook_links_widget_function( $post, $callback_args ) {
  echo "<p>Complete your weekly finance tasks and reconciliations by clicking on this link to get to your Quickbooks account.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://quickbooks.intuit.ca/'>Finance Mastersheets</a></p>";
}

// Budget and Income Statements
//////////////////////////////////////////////////////////////////////
function sos_dashboard_fin_statements_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the general Chapter Resources folder. In it, you will see your Chapter has an individual folder, with your Year Plan and Budget. Your Budget document ensures that the Chapter can stay on track to reach your revenue goal. The Income Statement tab should be updated regularly throughout the year, as finances come in.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHM0xvendZY096eG8'>Finance Statements</a></p>";
}

// Marketing Support
//////////////////////////////////////////////////////////////////////
function sos_dashboard_marketing_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find templates & resources for HR and general SOS marketing, EA session marketing and Outreach Trip recrutiment, in addition to SOS photos and other resources to ensure your marketing plan is stellar!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHREtkSHpvZ3QwVjQ'>Marketing Support</a></p>";
}

// Facebook
//////////////////////////////////////////////////////////////////////
function sos_dashboard_fb_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the SOS Facebook page! Give the page a "like" and feel free to share any posts to your Chapter's account!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://www.facebook.com/StudentsOfferingSupport/'>SOS Facebook</a></p>";
}

// Instagram
//////////////////////////////////////////////////////////////////////
function sos_dashboard_ig_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the SOS Instagram page! Please follow us, and feel free to share any posts to your Chapter's account!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://www.instagram.com/studentsofferingsupport/'>SOS Instagram</a></p>";
}

// Twitter
//////////////////////////////////////////////////////////////////////
function sos_dashboard_twitter_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the SOS Twitter! Follow us, and feel free to retweet any posts to your Chapter's account!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://twitter.com/sosheadoffice'>SOS Twitter</a></p>";
}

// BD Support
//////////////////////////////////////////////////////////////////////
function sos_dashboard_bd_support_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find national sponsorship materials & sponsor logos, Chapter level sponsorship templates, in addition to other great Business Development resources.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHSUN6SWhWVXBseTQ'>BD Resources</a></p>";
}

// Chapter Resources
//////////////////////////////////////////////////////////////////////
function sos_dashboard_chapter_resources_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the general Chapter Resources folder. In it, you will see HQ'd folder (filled with resources per department and general SOS policies) and Chapter folders. Please save all SOS related materials in your Chapter's folder - and feel free to peruse the other Chapter folders to see what your SOS family members are up to!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHM0xvendZY096eG8'>Chapter Resources</a></p>";
}

// Function used in the action hook
function sos_add_dashboard_widgets() {
  add_meta_box('sos_dashboard_help', 'Portal Knowledge Base ', 'sos_dashboard_knowledgebase_widget_function','dashboard', 'normal');
  add_meta_box('sos_dashboard_finance', 'Chapter Finance Forms ', 'sos_dashboard_finance_widget_function','dashboard', 'side');
  add_meta_box('sos_dashboard_princeton', 'Princeton Review Discount ', 'sos_dashboard_princeton_widget_function','dashboard', 'normal');
  add_meta_box('sos_dashboard_training', 'Training Resources','sos_dashboard_training_widget_function', 'dashboard', 'side');

}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'sos_add_dashboard_widgets' );


// Stop the text editor from auto adding markup to html
//////////////////////////////////////////////////////////////////////
remove_filter( 'the_content', 'wpautop' );


// Rename WooCommerce Default "Category" Taxonomy to "Session Topics" & Register a new one "course topics"
//////////////////////////////////////////////////////////////////////

function sos_change_woo_cat_object() {
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['product_cat']->labels;
    $labels->name = 'Session Topic';
    $labels->singular_name = 'Session Topic';
    $labels->add_new = 'Add Session Topic';
    $labels->add_new_item = 'Add Session Topic';
    $labels->edit_item = 'Edit Session Topic';
    $labels->new_item = 'Session Topic';
    $labels->view_item = 'View Session Topic';
    $labels->search_items = 'Search Session Topics';
    $labels->not_found = 'No Session Topics found';
    $labels->not_found_in_trash = 'No Session Topics found in Trash';
    $labels->all_items = 'All Session Topics';
    $labels->menu_name = 'Session Topic';
    $labels->name_admin_bar = 'Session Topic';
}
add_action( 'init', 'sos_change_woo_cat_object' );


// create a second taxonomy for woocommerce "Session Types"
//////////////////////////////////////////////////////////////////////
function create_course_type_taxonomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Session Types', 'taxonomy general name', 'textdomain' ),
		'singular_name'     => _x( 'Session Type', 'taxonomy singular name', 'textdomain' ),
		'search_items'      => __( 'Search Session Types', 'textdomain' ),
		'all_items'         => __( 'All Session Session Types', 'textdomain' ),
		'parent_item'       => __( 'Parent Session Type', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Session Type:', 'textdomain' ),
		'edit_item'         => __( 'Edit Session Type', 'textdomain' ),
		'update_item'       => __( 'Update Session Type', 'textdomain' ),
		'add_new_item'      => __( 'Add New Session Type', 'textdomain' ),
		'new_item_name'     => __( 'New Session Type Name', 'textdomain' ),
		'menu_name'         => __( 'Session Type', 'textdomain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'session_type' ),
	);

	register_taxonomy( 'session_type', array( 'product' ), $args );

}


// hook into the init action and call create_course_type_taxonomy when it fires
//////////////////////////////////////////////////////////////////////////////////
add_action( 'init', 'create_course_type_taxonomy', 0 );


// remove the tags taxonomy from the product (aka: session) post type. We don't need it.
////////////////////////////////////////////////////////////////////////////////////
function unregister_product_tags() {
    unregister_taxonomy_for_object_type( 'product_tag', 'product' );
    unregister_taxonomy_for_object_type( 'product_variation', 'product' );
}

add_action( 'init', 'unregister_product_tags' );



// Remove "attributes" page from the woocommerce post type "products" aka: sessions
////////////////////////////////////////////////////////////////////////////////////
function remove_attributes_subpage() {

    $ptype = 'product';
    remove_submenu_page( "edit.php?post_type=product", "product_attributes" );

}

add_action( 'admin_menu', 'remove_attributes_subpage', 99, 0 );


// Remove Default product types on single product page. aka "session page" We only need simple products.
////////////////////////////////////////////////////////////////////////////////////////////////////////////
function remove_product_types( $types ){
    unset( $types['grouped'] );
    unset( $types['external'] );
    unset( $types['variable'] );

    return $types;
}
add_filter( 'product_type_selector', 'remove_product_types' );


// Removes uneeded items from the logistics panel on the single product/sessions pages
//////////////////////////////////////////////////////////////////////////////////////////////
function hide_panel_items_woocommerce(){

	echo '<style>._manage_stock_field, ._sold_individually_field, #product-type {display:none !important;} </style>';

}

add_action('admin_head', 'hide_panel_items_woocommerce', 99, 0 );
add_filter( 'wc_product_sku_enabled', '__return_false' );



// FUNCTION TO REMOVE ALL UNUSED TABS
///////////////////////////////////////////////////////////////////////////////////
function remove_linked_products($tabs){
  unset($tabs['shipping']);
  unset($tabs['linked_product']);
  unset($tabs['attribute']);
  unset($tabs['advanced']);

  return($tabs);
}

add_filter('woocommerce_product_data_tabs', 'remove_linked_products', 10, 1);

// FUNCTION TO CHANGE TAB NAMES
//////////////////////////////////////////////////////////////////////////////////
function rename_tabs($tabs){
  $tabs['general']['label'] = __('Pricing');
  $tabs['inventory']['label'] = __('Logistics');

  return $tabs;
}

add_filter('woocommerce_product_data_tabs', 'rename_tabs');

// REMOVING SKU OPTION
////////////////////////////////////////////////////////////////////////////
add_filter( 'wc_product_sku_enabled', '__return_false' );
add_filter( 'woocommerce_product_options_stock_fields', '__return_false' );


// Replace wordpress admin branding with SOS admin branding
////////////////////////////////////////////////////////////////////////////
function no_wp_logo_admin_bar_remove() {
    ?>
        <style type="text/css">
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
                content: url(<?php echo get_template_directory_uri(); ?>/img/sos-logo-white.svg) !important;
                top: 2px;
                opacity: 0.6;
            }

            #wpadminbar #wp-admin-bar-wp-logo>.ab-item .ab-icon{
                width: 35px;
                height: auto;
                padding: 3px 0;
            }

            #wpadminbar #wp-admin-bar-wp-logo > a.ab-item {
                pointer-events: none;
                cursor: default;
            }
        </style>
    <?php
}
add_action('wp_before_admin_bar_render', 'no_wp_logo_admin_bar_remove', 0);


// Add custom login page styles
//////////////////////////////////////////////////////////////////////
function my_custom_login() {
echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/assets/css/login.css" />';
}
add_action('login_head', 'my_custom_login');

// Append Nav with login / logout link
//////////////////////////////////////////////////////////////////////

function add_login_logout_register_menu( $items, $args ) {
 if ( $args->theme_location != 'primary' ) {
 return $items;
 }

if ( is_user_logged_in() ) {
    if( current_user_can('edit_post') ) {
        $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/wp-admin">' . __( 'Admin' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
    } else {
        $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'My Account' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
    }

 } else {
     $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'Login' ) . '</a></li>';
     $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'Sign Up' ) . '</a></li>';
 }

 return $items;
}

add_filter( 'wp_nav_menu_items', 'add_login_logout_register_menu', 199, 2 );


/**
* Redirect user after successful login to Woocomerce My Account Page if User can edit_posts
*
*
* @param string $url URL to redirect to.
* @param string $request URL the user is coming from.
* @param object $user Logged user's data.
* @return string
*/

function sos_login_redirect( $url, $request, $user ){
    if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        if( $user->has_cap( 'edit_posts' ) ) {
            $url = admin_url();
        } else {
            $url = home_url('/my-account/');
        }
    }
    return $url;
}
add_filter('login_redirect', 'sos_login_redirect', 10, 3 );

// Service Fee Added to Stripe Transactions
//////////////////////////////////////////////////////////////////////
function woocommerce_custom_fee( ) {
	if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || ! is_checkout() )
		return;

	$chosen_gateway = WC()->session->chosen_payment_method;

	$fee = .5;

	if ( $chosen_gateway == 'stripe' ) { //test with paypal method
		WC()->cart->add_fee( 'Service Fee', $fee, false, '' );
	}
}

add_action( 'woocommerce_cart_calculate_fees','woocommerce_custom_fee' );

function cart_update_script() {
  if (is_checkout()) :
  ?>
  <script>
	 jQuery( function( $ ) {

		// woocommerce_params is required to continue, ensure the object exists
		if ( typeof woocommerce_params === 'undefined' ) {
			return false;
		}

		$checkout_form = $( 'form.checkout' );

		$checkout_form.on( 'change', 'input[name="payment_method"]', function() {
				$checkout_form.trigger( 'update' );
		});

	});
  </script>
  <?php
  endif;
}
add_action( 'wp_footer', 'cart_update_script', 999 );


// // Append Nav with login / logout link
// //////////////////////////////////////////////////////////////////////

// function sos_chapters_leaderboard(){
//     $args = array(
//         'site__not_in' => '1,5',
//         'orderby' => 'domain'
//     );

//     if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
//         $sites = get_sites($args);
//         foreach ( $sites as $site ) {
//             switch_to_blog( $site->blog_id );

//                     $details->blogname   = get_option( 'blogname' );
//                     $details->siteurl    = get_option( 'siteurl' );
//                     $details->home       = get_option( 'home' );

//                     $order = wc_get_order( $order_id );
//                     foreach ($order->get_items() as $item_key => $item_values){
//                         $item_data = $item_values->get_data();
//                         $product_name = $item_data['name'];
//                     }

//                     echo '<div class="col-12">' . $details->blogname . '</div>';

//                     // echo '<div class="button-text col-12 col-sm-4 my-2">';
//                     // echo '<a href="' . $details->siteurl . '" class="btn btn-lrg btn-outline-primary w-100" >' . $details->blogname . '</a>';
//                     // echo '</div>';


//             restore_current_blog();
//         }
//         return;
//     }
// }


function sos_chapters_list_option_box(){
    $args = array(
        'site__not_in' => '1,5,29,30',
        'orderby' => 'domain'
    );

    if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
        $sites = get_sites($args);
        foreach ( $sites as $site ) {
            switch_to_blog( $site->blog_id );
                    $details->blogname   = get_option( 'blogname' );
                    $details->siteurl    = get_option( 'siteurl' );
                    $details->post_count = get_option( 'post_count' );
                    $details->home       = get_option( 'home' );
                    echo '<option value="' . $details->siteurl . '">' . $details->blogname . '</option>';
            restore_current_blog();
         }
        return;
    }

}


function sos_chapters_list_apply_option_box(){
    $args = array(
        'site__not_in' => '1,5,29,30',
        'orderby' => 'domain'
    );

    if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
        $sites = get_sites($args);
        foreach ( $sites as $site ) {
            switch_to_blog( $site->blog_id );
                    $details->blogname   = get_option( 'blogname' );
                    $details->siteurl    = get_option( 'siteurl' );
                    $details->post_count = get_option( 'post_count' );
                    $details->home       = get_option( 'home' );
                    echo '<option value="' . $details->siteurl . '/apply">' . $details->blogname . '</option>';
            restore_current_blog();
         }
        return;
    }

}

// User-specific Dashboard
///////////////////////////////////////////////////
add_action('admin_init','customize_meta_boxes');

function customize_meta_boxes() {
  global $current_user;

  get_currentuserinfo();

  if ( !current_user_can( 'create_sites' ) ) {

  }
}


/**
 * Theme setup and custom theme supports.
 */
require get_template_directory() . '/inc/setup.php';

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Load functions to secure your WP install.
 */
require get_template_directory() . '/inc/security.php';

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/enqueue.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/pagination.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/custom-comments.php';


/**
 * Load custom WordPress nav walker.
 */
require get_template_directory() . '/inc/bootstrap-wp-navwalker.php';

/**
 * Load WooCommerce functions.
 */
require get_template_directory() . '/inc/woocommerce.php';

/**
 * Load Editor functions.
 */
require get_template_directory() . '/inc/editor.php';
