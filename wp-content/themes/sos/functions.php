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

// function sos_change_woo_post_object() {
//     global $wp_post_types;
//     $labels = &$wp_post_types['product']->labels;
//     $labels->name = 'Sessions';
//     $labels->singular_name = 'Session';
//     $labels->add_new = 'Add Session';
//     $labels->add_new_item = 'Add Session';
//     $labels->edit_item = 'Edit Session';
//     $labels->new_item = 'Session';
//     $labels->view_item = 'View Session';
//     $labels->search_items = 'Search Sessions';
//     $labels->not_found = 'No Sessions found';
//     $labels->not_found_in_trash = 'No Sessions found in Trash';
//     $labels->all_items = 'All Sessions';
//     $labels->menu_name = 'Sessions';
//     $labels->name_admin_bar = 'Sessions';
// }
//
// add_action( 'init', 'sos_change_woo_post_object' );


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

  // ismara - 2018-04-30 - changing href for the new training page (LMS) site_url('training')
  echo "<p><a class='button button-primary button-large' href='http://training.soscampus.com'>Training Resources</a></p>";
  //echo "<p><a class='button button-primary button-large' href='http://www.studentsofferingsupport.ca/TrainingResources/'>Training Resources</a></p>";
  // ismara - 2018-04-30 - end
}

// HR Dashboard Box
//////////////////////////////////////////////////////////////////////
function sos_dashboard_hr_support_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find role descriptions & expectation agreements, hiring & training guides, in addition to other HR resources.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHaHV3RVJkSmdDSDA'>Access Resources</a></p>";
}

// ED Dashboard box
//////////////////////////////////////////////////////////////////////
function sos_dashboard_ed_support_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find Exam Aid powerpoint templates, sample cover letters for take home packages, and other ED resources.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHNkNKLVQzWkllLVU'>Access Resources</a></p>";
}
// add_meta_box('sos_dashboard_edsupport', 'ED Resources', 'sos_dashboard_ed_support_widget_function','dashboard', 'normal');

// Archived EA Materials
//////////////////////////////////////////////////////////////////////
function sos_dashboard_archived_materials_widget_function( $post, $callback_args ) {
  echo "<p>Looking for old Exam Aid/Take-Home/DEA packages? We've archived them for you to view.</p>";
  echo "<p>Login using <strong>username: amaterials</strong> and <strong>password: amaterials</strong>.";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='http://www.studentsofferingsupport.ca/portal/Files/CourseDownloadPage.php'>Archived Materials</a></p>";
}
// add_meta_box('sos_dashboard_archivedmaterials', 'Archived Materials', 'sos_dashboard_archived_materials_widget_function', 'dashboard', 'normal');

// Refund Forms
//////////////////////////////////////////////////////////////////////
function sos_dashboard_refund_widget_function( $post, $callback_args ) {
  echo "<p>Once a donation is made for participation in an Exam Aid session, refunds will be provided only in the extenuating circumstances (unsatisfied customer or cancelled session). If a student would like to submit a refund request, please ask them to fill out this form.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://docs.google.com/forms/d/e/1FAIpQLSfg9HYrST-ZxPfoi4oGMIZFE48tLKHjbSx4pjgMgkwVrEEXJQ/viewform'>Refund Request</a></p>";
}
// add_meta_box('sos_dashboard_refund', 'Refund Request Form', 'sos_dashboard_refund_widget_function', 'dashboard', 'normal');

// EA FAQs
//////////////////////////////////////////////////////////////////////
function sos_dashboard_faq_widget_function( $post, $callback_args ) {
  echo "<p>This link will direct you to our Frequently Asked Exam Aid Questions. Don't see your question listed? You can also submit any questions through this database.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://faq.soscampus.com/'>Access FAQ</a></p>";
}
// add_meta_box('sos_dashboard_faq', 'Frequently Asked Questions', 'sos_dashboard_faq_widget_function', 'dashboard', 'normal');

// EA Presentation templates
//////////////////////////////////////////////////////////////////////
function sos_dashboard_ea_template_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find Exam Aid powerpoint templates and sample cover letters for take home packages.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHNmNtUk1PX2doem8'>Access Template</a></p>";
}
// add_meta_box('sos_dashboard_eatemplate', 'Exam Aid Template', 'sos_dashboard_ea_template_widget_function', 'dashboard', 'normal');

// Finance Mastersheets
//////////////////////////////////////////////////////////////////////
function sos_dashboard_fin_mastersheets_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will see all SOS Chapter's mastersheets. You just need to find your Chapter's specific document (it is in alphabetical order).</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0BxsgrL3RdWEccXBxSV9NU0lRMGs'>Access Mastersheets</a></p>";
}
// add_meta_box('sos_dashboard_financemastersheet', 'Finance Mastersheet', 'sos_dashboard_fin_mastersheets_widget_function', 'dashboard', 'normal');

// Quickbook Links
//////////////////////////////////////////////////////////////////////
function sos_dashboard_quickbook_links_widget_function( $post, $callback_args ) {
  echo "<p>Complete your weekly finance tasks and reconciliations by clicking on this link to get to your Quickbooks account.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://quickbooks.intuit.ca/'>Quickbooks Links</a></p>";
}
// add_meta_box('sos_dashboard_quickbook', 'Quickbook Links', 'sos_dashboard_quickbook_links_widget_function', 'dashboard', 'normal');

// Budget and Income Statements
//////////////////////////////////////////////////////////////////////
function sos_dashboard_fin_statements_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the general Chapter Resources folder. In it, you will see your Chapter has an individual folder, with your Year Plan and Budget. Your Budget document ensures that the Chapter can stay on track to reach your revenue goal. The Income Statement tab should be updated regularly throughout the year, as finances come in.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHM0xvendZY096eG8'>Finance Statements</a></p>";
}
// add_meta_box('sos_dashboard_financestatement', 'Budget and Income Statements', 'sos_dashboard_fin_statements_widget_function', 'dashboard', 'normal');

// Marketing Support
//////////////////////////////////////////////////////////////////////
function sos_dashboard_marketing_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find templates & resources for HR and general SOS marketing, EA session marketing and Outreach Trip recrutiment, in addition to SOS photos and other resources to ensure your marketing plan is stellar!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHREtkSHpvZ3QwVjQ'>Marketing Support</a></p>";
}
// add_meta_box('sos_dashboard_marketing', 'Marketing Support', 'sos_dashboard_marketing_widget_function', 'dashboard', 'normal');

// Facebook
//////////////////////////////////////////////////////////////////////
function sos_dashboard_fb_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the SOS Facebook page! Give the page a 'like' and feel free to share any posts to your Chapter's account!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://www.facebook.com/StudentsOfferingSupport/'>SOS Facebook</a></p>";
}
// add_meta_box('sos_dashboard_fb', 'Facebook', 'sos_dashboard_fb_widget_function', 'dashboard', 'normal');

// Instagram
//////////////////////////////////////////////////////////////////////
function sos_dashboard_ig_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the SOS Instagram page! Please follow us, and feel free to share any posts to your Chapter's account!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://www.instagram.com/studentsofferingsupport/'>SOS Instagram</a></p>";
}
// add_meta_box('sos_dashboard_ig', 'Instagram', 'sos_dashboard_ig_widget_function', 'dashboard', 'normal');

// Twitter
//////////////////////////////////////////////////////////////////////
function sos_dashboard_twitter_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the SOS Twitter! Follow us, and feel free to retweet any posts to your Chapter's account!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://twitter.com/sosheadoffice'>SOS Twitter</a></p>";
}
// add_meta_box('sos_dashboard_twitter', 'Twitter', 'sos_dashboard_twitter_widget_function', 'dashboard', 'normal');

// BD Support
//////////////////////////////////////////////////////////////////////
function sos_dashboard_bd_support_widget_function( $post, $callback_args ) {
  echo "<p>In this folder, you will find national sponsorship materials & sponsor logos, Chapter level sponsorship templates, in addition to other great Business Development resources.</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHSUN6SWhWVXBseTQ'>Access Resources</a></p>";
}
// add_meta_box('sos_dashboard_bdresources', 'Business Development Resources', 'sos_dashboard_bd_support_widget_function', 'dashboard', 'normal');

// Chapter Resources
//////////////////////////////////////////////////////////////////////
function sos_dashboard_chapter_resources_widget_function( $post, $callback_args ) {
  echo "<p>Here is a link to the general Chapter Resources folder. In it, you will see HQ'd folder (filled with resources per department and general SOS policies) and Chapter folders. Please save all SOS related materials in your Chapter's folder - and feel free to peruse the other Chapter folders to see what your SOS family members are up to!</p>";
  echo '<p><hr/></p>';
  echo "<p><a class='button button-primary button-large' href='https://drive.google.com/open?id=0B-cl0XfKOoxHM0xvendZY096eG8'>Access Resources</a></p>";
}
// add_meta_box('sos_dashboard_chapresources', 'Chapter Resources', 'sos_dashboard_chapter_resources_widget_function', 'dashboard', 'normal');

// Function used in the action hook
function sos_add_dashboard_widgets() {
  global $current_user;

  wp_get_current_user();

  // President
  if ( current_user_can( 'publish_posts' ) ) {
    add_meta_box('sos_dashboard_chapresources', 'Chapter Resources', 'sos_dashboard_chapter_resources_widget_function', 'dashboard', 'side');
  }

  // HR Dashboard
  elseif ( current_user_can( 'remove_users' ) ) {
    add_meta_box('sos_dashboard_hr', 'HR Resources', 'sos_dashboard_hr_support_widget_function','dashboard', 'side');
  }

  // VP Finance
  elseif ( current_user_can( 'view_woocommerce_reports' ) ) {
    add_meta_box('sos_dashboard_refund', 'Refund Request Form', 'sos_dashboard_refund_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_financemastersheet', 'Finance Mastersheet', 'sos_dashboard_fin_mastersheets_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_quickbook', 'Quickbook Links', 'sos_dashboard_quickbook_links_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_financestatement', 'Budget and Income Statements', 'sos_dashboard_fin_statements_widget_function', 'dashboard', 'side');
  }

  // VP BD
  elseif ( current_user_can( 'list_users' ) ) {
    add_meta_box('sos_dashboard_bdresources', 'Business Development Resources', 'sos_dashboard_bd_support_widget_function', 'dashboard', 'side');
  }

  // VP Marketing
  elseif ( current_user_can( 'moderate_comments' ) ) {
    add_meta_box('sos_dashboard_marketing', 'Marketing Support', 'sos_dashboard_marketing_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_fb', 'Facebook', 'sos_dashboard_fb_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_ig', 'Instagram', 'sos_dashboard_ig_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_twitter', 'Twitter', 'sos_dashboard_twitter_widget_function', 'dashboard', 'side');
  }

  // ED Dashboard
  elseif ( current_user_can( 'publish_products' ) ) {
    add_meta_box('sos_dashboard_edsupport', 'ED Resources', 'sos_dashboard_ed_support_widget_function','dashboard', 'side');
    add_meta_box('sos_dashboard_archivedmaterials', 'Archived Materials', 'sos_dashboard_archived_materials_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_refund', 'Refund Request Form', 'sos_dashboard_refund_widget_function', 'dashboard', 'side');
  }

  // Coordinator/EAI Dashboard
  elseif ( current_user_can( 'edit_published_products' ) ) {
    add_meta_box('sos_dashboard_archivedmaterials', 'Archived Materials', 'sos_dashboard_archived_materials_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_refund', 'Refund Request Form', 'sos_dashboard_refund_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_faq', 'Frequently Asked Questions', 'sos_dashboard_faq_widget_function', 'dashboard', 'side');
    add_meta_box('sos_dashboard_eatemplate', 'Exam Aid Template', 'sos_dashboard_ea_template_widget_function', 'dashboard', 'side');
  }

  add_meta_box('sos_dashboard_training', 'Training Resources','sos_dashboard_training_widget_function', 'dashboard', 'normal');
  add_meta_box('sos_dashboard_help', 'Portal Knowledge Base ', 'sos_dashboard_knowledgebase_widget_function','dashboard', 'normal');
  add_meta_box('sos_dashboard_finance', 'Chapter Finance Forms ', 'sos_dashboard_finance_widget_function','dashboard', 'normal');
  add_meta_box('sos_dashboard_princeton', 'Princeton Review Discount ', 'sos_dashboard_princeton_widget_function','dashboard', 'normal');
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'sos_add_dashboard_widgets' );


// Stop the text editor from auto adding markup to html
//////////////////////////////////////////////////////////////////////
// remove_filter( 'the_content', 'wpautop' );


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

// create a second taxonomy for woocommerce "Types"
//////////////////////////////////////////////////////////////////////
function create_type_taxonomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Types', 'taxonomy general name', 'textdomain' ),
		'singular_name'     => _x( 'Type', 'taxonomy singular name', 'textdomain' ),
		'search_items'      => __( 'Search Types', 'textdomain' ),
		'all_items'         => __( 'All Types', 'textdomain' ),
		'parent_item'       => __( 'Parent Type', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Type:', 'textdomain' ),
		'edit_item'         => __( 'Edit Type', 'textdomain' ),
		'update_item'       => __( 'Update Type', 'textdomain' ),
		'add_new_item'      => __( 'Add New Type', 'textdomain' ),
		'new_item_name'     => __( 'New Type Name', 'textdomain' ),
		'menu_name'         => __( 'Type', 'textdomain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'type' ),
	);

	register_taxonomy( 'type', array( 'product' ), $args );

}

// hook into the init action and call create_course_type_taxonomy when it fires
//////////////////////////////////////////////////////////////////////////////////
add_action( 'init', 'create_type_taxonomy', 0 );



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

	echo '<style>._sold_individually_field, #product-type {display:none !important;} </style>';

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
    if( current_user_can('edit_post') || current_user_can('vpid') ) {
        $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/wp-admin">' . __( 'Admin' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'My Account' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
    } else {
        $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'My Account' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
    }

 } else {
     $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'Login' ) . '</a></li>';
     $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'Register' ) . '</a></li>';
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
//2018-07-04 - ismara - excluded chapters from the list -> (1))soscampus.com - (5)kb.soscampus.com - (29)hq.soscampus.com - (30)faq.soscampus.com - (31)national.soscampus.com - (32)training.soscampus.com
        'site__not_in' => '1,5,29,30,31,32',
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

/*begin - ismara - 2018-08-21 dynamically populated Gform select fields - chapters' list*/
add_filter( 'gform_pre_render', 'populate_chapters' );
add_filter( 'gform_pre_validation', 'populate_chapters' );
add_filter( 'gform_pre_submission_filter', 'populate_chapters' );
add_filter( 'gform_admin_pre_render', 'populate_chapters' );
function populate_chapters( $form ) {
    $args = array(
        'site__not_in' => '1,5,29,30,31,32',
        'orderby' => 'domain'
    );

    foreach ( $form['fields'] as &$field ) {

        if ( $field->type != 'select' || strpos( $field->cssClass, 'populate_chapters' ) === false ) {
            continue;
        }


        if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
          $sites = get_sites($args);

          $choices = array();

          foreach ( $sites as $site ) {
              switch_to_blog( $site->blog_id );
              $choices[] = array( 'text' => get_option('blogname'), 'value' => get_option('blogname') );
              restore_current_blog();
          }

          $field->placeholder = '---';
          $field->choices = $choices;
        }
    }

    return $form;
}
/*end - ismara - 2018-08-21 dynamically populated Gform select fields - chapters' list*/




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

  wp_get_current_user();

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

///////////////////// Create Custom Post Types /////////////////////

function create_posttype_sos() {

    register_post_type( 'trip_applications',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Trip Applications' ),
                'singular_name' => __( 'Trip Application' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'trip_applications'),
        )
    );
}
add_action( 'init', 'create_posttype_sos' );


function create_posttype_trip() {

    register_post_type( 'trips',
        array(
            'labels' => array(
                'name' => __( 'Trips' ),
                'singular_name' => __( 'Trip' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'trips'),
        )
    );
}

add_action( 'init', 'create_posttype_trip' );




///////////////////// Create Archive Status for Trip Post Type /////////////////////

function trips_archive_post_status(){
	register_post_status( 'archive', array(
		'label'                     => _x( 'Archive', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>' ),
	) );
}
add_action( 'init', 'trips_archive_post_status' );

function add_to_post_status_dropdown(){
    ?>
    <script>
    jQuery(document).ready(function($){
        $("select#post_status").append("<option value=\"archive\" <?php selected('archive', $post->post_status); ?>>Archive</option>");
    });
    </script>
    <?php
}
add_action( 'post_submitbox_misc_actions', 'add_to_post_status_dropdown');

///////////////////// Cleaning up Custom Post Type Admin /////////////////////

add_filter( 'page_row_actions', 'wpse_125800_row_actions', 10, 2 );
add_filter( 'post_row_actions', 'wpse_125800_row_actions', 10, 2 );
function wpse_125800_row_actions( $actions, $post ) {
    unset( $actions['inline hide-if-no-js'] );
    unset( $actions['view'] );

    return $actions;
}

if(!isset($_GET['trip_applications']) || !isset($_GET['trip_applications'])){
    add_action('admin_head', 'remove_preview_button');
    function remove_preview_button() {
      echo '<style>
            #post-preview{
                display:none !important;
            }
            }
          </style>';
    }
}

///////////////////// Loading all Schools Dynamically into ACF /////////////////////

function acf_load_trip_field_choices( $field ) {

    $field['choices'] = array();

    $all_blog = wp_get_sites();

    $choices = [];
    $values = [];
    foreach ($all_blog as $blog) {
        array_push($choices, $blog['domain']);
        array_push($values, $blog['blog_id']);
    }

    if( is_array($all_blog) ) {
        foreach( $all_blog as $blog ) {
            $field['choices'][ $blog['blog_id'] ] = $blog['domain'];
        }
    }

    // return the field
    return $field;

}

add_filter('acf/load_field/name=trip_schools', 'acf_load_trip_field_choices');

///////////////////// Add all Trips Dynamically to ACF /////////////////////

function acf_load_trip_select_field_choices( $field ) {

    $field['choices'] = array();

    $all_trips = get_posts(array(
        'posts_per_page'    =>  -1,
        'post_type'         =>  'trips',
        'post_status'       =>  'publish'
    ));

    if( is_array($all_trips) ) {
        foreach( $all_trips as $trip ) {
            $field['choices'][ $trip->ID ] = $trip->post_title;
        }
    }

    // return the field
    return $field;

}
add_filter('acf/load_field/name=ta_trip_select', 'acf_load_trip_select_field_choices');

///////////////////// Adding Custom Table Headers for Trip Application Post Type /////////////////////

add_filter('manage_trip_applications_posts_columns', 'trip_applications_table_head');
function trip_applications_table_head( $defaults ) {
    $defaults['trip_name']  = 'Trip';
    $defaults['trip_state']  = 'Application State';
    $defaults['campus']  = 'Campus';
    // $defaults['interview_complete']    = 'Interview Complete';
    // $defaults['deposit_received']   = 'Deposit Received';
    // $defaults['flight_cost_received']   = 'Flight Cost Received';
    // $defaults['participation_fee_received']   = 'Participation Fee Received';
    return $defaults;
}

add_action( 'manage_trip_applications_posts_custom_column', 'trip_applications_table_content', 10, 2 );
function trip_applications_table_content( $column_name, $post_id ) {
    if ($column_name == 'trip_name') {
        $trip_id = get_post_meta( $post_id, 'ta_trip_select', true );
        $trip_name = get_post($trip_id)->post_title;
        echo $trip_name;
    }
    if ($column_name == 'trip_state') {
        $trip_state = get_post_meta( $post_id, 'ta_application_state', true );

        $application_states = get_field_object("field_59ef820057f5c");

        foreach($application_states['choices'] as $value => $state){

            if($trip_state == $value){
                echo $state;
            }
        }
    }
    if ($column_name == 'campus') {
        $campus_name = get_field('ta_university', $post_id);
        echo $campus_name;
    }
}

///////////////////// Make Custom Columns Sortable /////////////////////

add_filter( 'manage_edit-trip_applications_sortable_columns', 'trip_applications_table_sorting' );
function trip_applications_table_sorting( $columns ) {
  $columns['trip_name'] = 'trip_name';
  $columns['trip_state'] = 'trip_state';
  $columns['campus'] = 'campus';
  return $columns;
}

add_filter( 'request', 'trip_applications_trip_name_column_orderby' );
function trip_applications_trip_name_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'trip_name' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'ta_trip_select',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
}

add_filter( 'request', 'trip_applications_trip_state_column_orderby' );
function trip_applications_trip_state_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'trip_state' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'ta_application_state',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
}

add_filter( 'request', 'trip_applications_campus_column_orderby' );
function trip_applications_campus_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'campus' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'ta_university',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
}

///////////////////// Column Filtering /////////////////////

add_action( 'restrict_manage_posts', 'trip_applications_table_filtering' );
function trip_applications_table_filtering() {
  global $wpdb, $current_screen;
  if ( $current_screen->post_type == 'trip_applications' ) {

    $all_trips = get_posts(array(
            'posts_per_page'    =>  -1,
            'post_type'         =>  'trips',
            'post_status'       =>  'publish'
    ));

    $application_states = get_field_object("field_59ef820057f5c");

    /// Show All Trips Selection ///

    echo '<select name="trip_selection">';
    echo '<option value="">' . __( 'Show all Trips', 'textdomain' ) . '</option>';

    foreach( $all_trips as $trip ) {
        $value = $trip->ID;
        $name = $trip->post_title;

        $selected = ( !empty( $_GET['trip_selection'] ) AND $_GET['trip_selection'] == $value ) ? 'selected="select"' : '';
        echo '<option ' .$selected . ' value="'.$value.'">' . $name . '</option>';
    }
    echo '</select>';


    /// Show All Status Selection ///

    echo '<select name="application_state_selection">';
    echo '<option value="">' . __( 'Show all Status', 'textdomain' ) . '</option>';

    foreach($application_states['choices'] as $value => $state){

        $selected = ( !empty( $_GET['application_state_selection'] ) AND $_GET['application_state_selection'] == $value ) ? 'selected="select"' : '';
        echo '<option ' .$selected . ' value="'.$value.'">' . $state . '</option>';
    }
    echo '</select>';

  }
}

///////////////////// Filtering Logic Here /////////////////////

add_filter( 'parse_query','trip_applications_table_filter' );
function trip_applications_table_filter( $query ) {
    if( is_admin() AND $query->query['post_type'] == 'trip_applications' ) {
        $qv = &$query->query_vars;
        $qv['meta_query'] = array();

        if( !empty( $_GET['trip_selection'] ) ) {
             $qv['meta_query'][] = array(
               'key' => 'ta_trip_select',
               'value' => $_GET['trip_selection'],
               'compare' => '=',
               'type' => 'CHAR'
             );
        }

        if( !empty( $_GET['application_state_selection'] ) ) {
             $qv['meta_query'][] = array(
               'key' => 'ta_application_state',
               'value' => $_GET['application_state_selection'],
               'compare' => '=',
               'type' => 'CHAR'
             );
        }
    }
}

///////////////////// Gravity Form Creating Trip Application post type title from 2 fields in form /////////////////////

add_action( 'gform_pre_submission_1', 'pre_submission_handler' );
function pre_submission_handler( $form ) {
    $_POST['input_17'] = rgpost( 'input_23' );
    $_POST['input_1'] = rgpost( 'input_17' ) . " - " . rgpost( 'input_2' );

}

///////////////////// Gravity Form adding fields from Volunteer Outreach Form /////////////////////

add_action( 'gform_after_submission_2', 'insert_volunteer_outreach_form_fields', 10, 2 );
function insert_volunteer_outreach_form_fields( $entry, $form ) {
    global $post;

    $post_id = $_GET['App'];

    //Personal Info
    update_field('ta_personal_address', $entry['3'], $post_id );
    update_field('ta_personal_phone', $entry['5'], $post_id );
    update_field('ta_personal_shirt_size', $entry['6'], $post_id );
    update_field('ta_personal_fluency_in_spanish', $entry['7'], $post_id );
    update_field('ta_personal_school', $entry['45'], $post_id );

    //Passport Info
    update_field('ta_passport_first_name', $entry['9'], $post_id );
    update_field('ta_passport_middle_name', $entry['10'], $post_id );
    update_field('ta_passport_last_name', $entry['11'], $post_id );
    update_field('ta_passport_expiration', $entry['19'], $post_id );

    update_field('ta_passport_canadianpassport', $entry['12'], $post_id );

    if($entry['12'] == 'no'){
        $to = get_field('ta_email', $post_id);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $email_subject = get_field('non_canadian_passport_email_subject', 'options');
        $email_body = get_field('non_canadian_passport_email_body', 'options');

        wp_mail( $to, $email_subject, $email_body, $headers );
    }

    update_field('ta_passport_wherefrom', $entry['13'], $post_id );
    update_field('ta_passport_status_in_canada', $entry['14'], $post_id );

    update_field('ta_passport_number', $entry['15'], $post_id );
    update_field('ta_passport_nationality', $entry['17'], $post_id );
    update_field('ta_passport_other_citizenships', $entry['18'], $post_id );

    update_field('ta_passport_place_of_issue', $entry['20'], $post_id );
    if($entry['21.1'] == 'yes'){
        update_field('ta_passport_covered_by_provincial_health', 1, $post_id );
    }else{
        update_field('ta_passport_covered_by_provincial_health', 0, $post_id );
    }

    update_field('ta_passport_profession', $entry['23'], $post_id );

    //Emergency Contacts
    update_field('ta_emergency_contact_1_full_name', $entry['26'], $post_id );
    update_field('ta_emergency_contact_1_relationship', $entry['27'], $post_id );
    update_field('ta_emergency_contact_1_cell', $entry['28'], $post_id );
    update_field('ta_emergency_contact_1_email', $entry['29'], $post_id );

    update_field('ta_emergency_contact_2_full_name', $entry['31'], $post_id );
    update_field('ta_emergency_contact_2_relationship', $entry['32'], $post_id );
    update_field('ta_emergency_contact_2_cell', $entry['33'], $post_id );
    update_field('ta_emergency_contact_2_email', $entry['34'], $post_id );

    //Medical Information
    update_field('ta_medical_cigarettes', $entry['36'], $post_id );
    update_field('ta_medical_allergies', $entry['37'], $post_id );
    update_field('ta_medical_dietary_restrictions', $entry['38'], $post_id );
    update_field('ta_medical_physical', $entry['39'], $post_id );
    update_field('ta_medical_prescription_medication', $entry['40'], $post_id );
    update_field('ta_medical_other_concerns', $entry['41'], $post_id );
    update_field('ta_medial_first_aid', $entry['42'], $post_id );
    if($entry['44.1'] == 'yes'){
        update_field('ta_medical_acknowledge_medical_conditions', 1, $post_id );
        update_field('ta_volunteer_outreach_form_complete', 1, $post_id );
    }else{
        update_field('ta_medical_acknowledge_medical_conditions', 0, $post_id );
    }


    //update_field('ta_application_state', 'info_collected', $post_id);

    $post = get_post($post_id);

    $headers = array('Content-Type: text/html; charset=UTF-8');
    $email_subject = get_field('emergency_contact_email_subject', 'options');
    $email_body = get_field('emergency_contact_email_body', 'options');

    wp_mail( $entry['29'], $email_subject, $email_body, $headers );
    wp_mail( $entry['34'], $email_subject, $email_body, $headers );

}

///////////////////// Gravity Form adding fields from Policies and Procedures Form /////////////////////

add_action( 'gform_after_submission_4', 'insert_policies_form_fields', 10, 2 );
function insert_policies_form_fields( $entry, $form ) {

    $post_id = $_GET['App'];

    if($entry['2.1'] == 'yes'){
        update_field('ta_agree_to_policies_and_procedures', 1, $post_id );
    }else{
        update_field('ta_agree_to_policies_and_procedures', 0, $post_id );
    }


    //update_field('ta_application_state', 'policies_agreed', $post_id);


}

///////////////////// Gravity Form adding fields from Waiver Sent in Form /////////////////////

add_action( 'gform_after_submission_5', 'waiver_upload', 10, 2 );
function waiver_upload( $entry, $form ) {

    $post_id = $_GET['App'];


    update_field('ta_waiver_uploaded', 1, $post_id );



    //update_field('ta_application_state', 'waiver_signed', $post_id);



}

///////////////////// Gravity Form adding fields from PDF Upload Sent in Form /////////////////////

add_action( 'gform_after_submission_6', 'pdf_upload', 10, 2 );
function pdf_upload( $entry, $form ) {

    $post_id = $_GET['App'];


    update_field('ta_pdf_uploaded', 1, $post_id );



    //update_field('ta_application_state', 'waiver_signed', $post_id);



}

///////////////////// Gravity Form adding fields from Medical Fitness Form /////////////////////

add_action( 'gform_after_submission_3', 'insert_medical_fitness_form_fields', 10, 2 );
function insert_medical_fitness_form_fields( $entry, $form ) {

    $post_id = $_GET['App'];

    update_field('ta_fitness_physician_name', $entry['5'], $post_id );
    update_field('ta_fitness_physician_contact_number', $entry['6'], $post_id );

    update_field('ta_fitness_personal_fitness_level', $entry['8'], $post_id );

    update_field('ta_fitness_describe_injury', $entry['10'], $post_id );
    update_field('ta_fitness_taking_current_measures', $entry['11'], $post_id );
    update_field('ta_fitness_list_complications', $entry['12'], $post_id );
    update_field('ta_fitness_outline_treatment_plan', $entry['13'], $post_id );


    if($entry['15.1'] == 'yes'){
        update_field('ta_fitness_agree_to_terms_medical_fitness_form', 1, $post_id );
        update_field('ta_medical_fitness_form_complete', 1, $post_id );
    }else{
        update_field('ta_fitness_agree_to_terms_medical_fitness_form', 0, $post_id );
    }

    //update_field('ta_application_state', 'medical_fitness_collected', $post_id);



}

///////////////////// Delete Applications for Trip when Trip is Archived /////////////////////

function delete_trip_applications_on_trip_change( $new_status, $old_status, $post ) {
    if (get_post_type($post) !== 'trips'){
        return;
    }

    if( $old_status === 'publish' && $new_status === 'trash') {

        $args = array('post_type'=> 'trip_applications',
             'post_status'      => 'publish',
             'posts_per_page'   => -1,
             'meta_key'         => 'ta_trip_select',
             'meta_value'       => $post->ID

        );
        $related_trip_applications = get_posts($args);

        foreach($related_trip_applications as $trip_application){
           $query = array(
            'ID' => $trip_application->ID,
            'post_status' => 'trash',
           );
           wp_update_post( $query, true );
        }
    }
}
add_action('transition_post_status', 'delete_trip_applications_on_trip_change', 10, 3);

///////////////////// Send Email Updates on Trip Application Update /////////////////////

add_action( 'edit_post', 'setup_automated_email' );
function setup_automated_email(){
    global $post;
    if(get_post_type($post) == "trip_applications"){

        global $application_email_subject, $application_email_body;

        $old_value = get_field('ta_application_state');
        $new_value = $_POST['acf']['field_59ef820057f5c'];

        if($old_value != $new_value){
            if($new_value == 'interview_setup'){
                $application_email_subject = 'interview_setup_email_subject';
                $application_email_body = 'interview_setup_email_body';

            }elseif($new_value == 'application_confirmed'){
                $application_email_subject = 'application_confirmation_subject';
                $application_email_body = 'application_confirmed_email_body';

            }elseif($new_value == 'suspended'){
                $application_email_subject = 'suspended_email_subject';
                $application_email_body = 'suspended_email_body';

            }elseif($new_value == 'refunded'){
                $application_email_subject = 'refunded_email_subject';
                $application_email_body = 'refunded_email_body';

            }elseif($new_value == 'insurance_info_approved'){
                $application_email_subject = 'insurance_email_subject';
                $application_email_body = 'insurance_email_body';

            }
        }
    }
}

///////////////////// Send The Actual Email /////////////////////

add_action( 'save_post', 'send_automated_email' );
function send_automated_email(){

    global $application_email_subject, $application_email_body;

    if($application_email_subject == 'insurance_email_subject'){
        $to = get_field('insurance_email_address', 'options');
    }else{
        $to = get_field('ta_email');
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');

    if($application_email_subject != null && $application_email_body != null){

        $email_subject = get_field($application_email_subject, 'options');
        $email_body = get_field($application_email_body, 'options');

        wp_mail( $to, $email_subject, $email_body, $headers );
    }


}

///////////////////// Set up 60 days before Email cron job /////////////////////

add_action('init','auto_email_recurring_schedule');
add_action('auto_email_recurring_cron_job','auto_email_recurring_cron_function');

function auto_email_recurring_cron_function(){

    $current_day = time();

    $all_trips = get_posts(array(
        'posts_per_page'    =>  -1,
        'post_type'         =>  'trips',
        'post_status'       =>  'publish'
    ));

    foreach($all_trips as $trip){

        $trip_departure_date = get_field('trip_departure_date', $trip->ID);
        $trip_return_date = get_field('trip_return_date', $trip->ID);

        $trip_flight_cost_due_date = get_field('trip_flight_cost_due_date', $trip->ID);
        $trip_participation_fee_due_date = get_field('trip_participation_fee_due_date', $trip->ID);

        if(get_field('trip_60_days_before', $trip->ID) != 1 && $current_day >= strtotime($trip_departure_date . '- 60 days')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                $to = get_field('ta_email');
                $email_subject = get_field('60_days_before_email_subject', 'options');
                $email_body = get_field('60_days_before_email_body', 'options');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $email_subject, $email_body, $headers );

            }

            update_field('trip_60_days_before', 1, $trip->ID);

        }elseif(get_field('trip_30_days_before', $trip->ID) != 1 && $current_day >= strtotime($trip_departure_date . '- 30 days')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                $to = get_field('ta_email');
                $email_subject = get_field('30_days_before_email_subject', 'options');
                $email_body = get_field('30_days_before_email_body', 'options');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $email_subject, $email_body, $headers );

            }

            update_field('trip_30_days_before', 1, $trip->ID);

        }elseif(get_field('trip_14_days_before', $trip->ID) != 1 && $current_day >= strtotime($trip_departure_date . '- 14 days')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                $to = get_field('ta_email');
                $email_subject = get_field('14_days_before_email_subject', 'options');
                $email_body = get_field('14_days_before_email_body', 'options');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $email_subject, $email_body, $headers );

            }

            update_field('trip_14_days_before', 1, $trip->ID);

        }elseif(get_field('trip_day_of_arrival', $trip->ID) != 1 && $current_day >= strtotime($trip_return_date)){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                $to = get_field('ta_email');
                $email_subject = get_field('welcome_home_email_subject', 'options');
                $email_body = get_field('welcome_home_email_body', 'options');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $email_subject, $email_body, $headers );

            }

            update_field('trip_day_of_arrival', 1, $trip->ID);

        }elseif(get_field('trip_1_day_after', $trip->ID) != 1 && $current_day >= strtotime($trip_return_date . '+ 1 day')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                $to = get_field('ta_email');
                $email_subject = get_field('1_day_after_email_subject', 'options');
                $email_body = get_field('1_day_after_email_body', 'options');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $email_subject, $email_body, $headers );

            }

            update_field('trip_1_day_after', 1, $trip->ID);

        }elseif(get_field('trip_7_days_after', $trip->ID) != 1 && $current_day >= strtotime($trip_return_date . '+ 7 days')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                $to = get_field('ta_email');
                $email_subject = get_field('7_days_after_email_subject', 'options');
                $email_body = get_field('7_days_after_email_body', 'options');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $email_subject, $email_body, $headers );

            }

            update_field('trip_7_days_after', 1, $trip->ID);

        }elseif(get_field('trip_6_months_after', $trip->ID) != 1 && $current_day >= strtotime($trip_return_date . '+ 6 months')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                $to = get_field('ta_email');
                $email_subject = get_field('6_months_after_trip_email_subject', 'options');
                $email_body = get_field('6_months_after_trip_email_body', 'options');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $email_subject, $email_body, $headers );

            }

            update_field('trip_6_months_after', 1, $trip->ID);

        }elseif(get_field('flight_cost_deadline_approaching', $trip->ID) != 1 && $current_day >= strtotime($trip_flight_cost_due_date . '- 7 days')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                if(get_field('ta_flight_cost_received', $application->ID) != 1){

                    $to = get_field('ta_email');
                    $email_subject = get_field('deadline_approaching_flight_cost_email_subject', 'options');
                    $email_body = get_field('deadline_approaching_flight_cost_email_body', 'options');
                    $headers = array('Content-Type: text/html; charset=UTF-8');

                    wp_mail( $to, $email_subject, $email_body, $headers );

                }

            }

            update_field('flight_cost_deadline_approaching', 1, $trip->ID);

        }elseif(get_field('participation_fee_deadline_approaching', $trip->ID) != 1 && $current_day >= strtotime($trip_participation_fee_due_date . '- 7 days')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                if(get_field('ta_participation_fee_received', $application->ID) != 1){

                    $to = get_field('ta_email');
                    $email_subject = get_field('deadline_approaching_participation_fee_email_subject', 'options');
                    $email_body = get_field('deadline_approaching_participation_fee_email_body', 'options');
                    $headers = array('Content-Type: text/html; charset=UTF-8');

                    wp_mail( $to, $email_subject, $email_body, $headers );

                }

            }

            update_field('participation_fee_deadline_approaching', 1, $trip->ID);

        // Didn't change the variable names for this but flight_cost_deadline_missed has been changed to Flight Cost Deadline Approaching 2 in the backend //
      }elseif(get_field('flight_cost_deadline_missed', $trip->ID) != 1 && $current_day >= strtotime($trip_flight_cost_due_date . '- 2 days')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                if(get_field('ta_flight_cost_received', $application->ID) != 1){

                    $to = get_field('ta_email');
                    $email_subject = get_field('deadline_missed_flight_cost_email_subject', 'options');
                    $email_body = get_field('deadline_missed_flight_cost_email_body', 'options');
                    $headers = array('Content-Type: text/html; charset=UTF-8');

                    wp_mail( $to, $email_subject, $email_body, $headers );

                }

            }

            update_field('flight_cost_deadline_missed', 1, $trip->ID);

        }elseif(get_field('participation_fee_deadline_missed', $trip->ID) != 1 && $current_day >= strtotime($trip_participation_fee_due_date . '+ 1 day')){

            $trip_applications = get_posts(array(
                'posts_per_page'    =>  -1,
                'post_type'         =>  'trip_applications',
                'post_status'       =>  'publish',
        		'meta_key'			=>  'ta_trip_select',
        		'meta_value'		=>  $trip->ID
            ));

            foreach($trip_applications as $application){

                if(get_field('ta_participation_fee_received', $application->ID) != 1){

                    $to = get_field('ta_email');
                    $email_subject = get_field('deadline_missed_participation_fee_email_subject', 'options');
                    $email_body = get_field('deadline_missed_participation_fee_email_body', 'options');
                    $headers = array('Content-Type: text/html; charset=UTF-8');

                    wp_mail( $to, $email_subject, $email_body, $headers );

                }

            }

            update_field('participation_fee_deadline_missed', 1, $trip->ID);

        }
    }
}

function auto_email_recurring_schedule(){

    if(!wp_next_scheduled('auto_email_recurring_cron_job')){
        wp_schedule_event (time(), 'daily', 'auto_email_recurring_cron_job');
    }


}


///////////////////// Set up Email specific shortcodes /////////////////////

// Applicant Name
function applicant_name_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_fullname');
    }

}
add_shortcode( 'applicant_name', 'applicant_name_shortcode' );

// Applicant Email
function applicant_email_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_email');
    }

}
add_shortcode( 'applicant_email', 'applicant_email_shortcode' );

// Applicant Birth Date
function applicant_birth_date_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_birthdate');
    }

}
add_shortcode( 'applicant_birth_date', 'applicant_birth_date_shortcode' );

// Applicant Interview Date
function applicant_interview_date_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_interview_date');
    }

}
add_shortcode( 'applicant_interview_date', 'applicant_interview_date_shortcode' );

// Applicant Interview Location
function applicant_interview_location_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_interview_location');
    }

}
add_shortcode( 'applicant_interview_location', 'applicant_interview_location_shortcode' );

// Applicant Selected Trip
function applicant_trip_selected_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        $all_trips = get_posts(array(
            'posts_per_page'    =>  -1,
            'post_type'         =>  'trips',
            'post_status'       =>  'publish'
        ));
        foreach($all_trips as $trip){
            if($trip->ID == $post->ta_trip_select){
                return $trip->post_title;
            }
        }
    }

}
add_shortcode( 'applicant_trip_selected', 'applicant_trip_selected_shortcode' );

///// Passport Shortcodes /////

// Applicant Passport First Name
function applicant_passport_first_name_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_passport_first_name');
    }

}
add_shortcode( 'applicant_passport_first_name', 'applicant_passport_first_name_shortcode' );

// Applicant Passport Middle Name
function applicant_passport_middle_name_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_passport_middle_name');
    }

}
add_shortcode( 'applicant_passport_middle_name', 'applicant_passport_middle_name_shortcode' );

// Applicant Passport Last Name
function applicant_passport_last_name_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_passport_last_name');
    }

}
add_shortcode( 'applicant_passport_last_name', 'applicant_passport_last_name_shortcode' );

// Applicant Passport Expiration Date
function applicant_passport_expiration_date_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_passport_expiration');
    }

}
add_shortcode( 'applicant_passport_expiration_date', 'applicant_passport_expiration_date_shortcode' );

///// Personal Information Shortcodes /////

// Applicant Address
function applicant_address_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_personal_address');
    }

}
add_shortcode( 'applicant_address', 'applicant_address_shortcode' );

// Applicant Phone
function applicant_phone_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_personal_phone');
    }

}
add_shortcode( 'applicant_phone', 'applicant_phone_shortcode' );

// Applicant School
function applicant_school_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        return get_field('ta_personal_school');
    }

}
add_shortcode( 'applicant_school', 'applicant_school_shortcode' );

///// Trip Information Shortcodes /////

// Trip Departure City
function trip_departure_city_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        $all_trips = get_posts(array(
            'posts_per_page'    =>  -1,
            'post_type'         =>  'trips',
            'post_status'       =>  'publish'
        ));
        foreach($all_trips as $trip){
            if($trip->ID == $post->ta_trip_select){
                return get_field('trip_departure_city', $trip->ID);
            }
        }
    }

}
add_shortcode( 'trip_departure_city', 'trip_departure_city_shortcode' );

// Trip Departure Date
function trip_departure_date_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        $all_trips = get_posts(array(
            'posts_per_page'    =>  -1,
            'post_type'         =>  'trips',
            'post_status'       =>  'publish'
        ));
        foreach($all_trips as $trip){
            if($trip->ID == $post->ta_trip_select){
                return get_field('trip_departure_date', $trip->ID);
            }
        }
    }

}
add_shortcode( 'trip_departure_date', 'trip_departure_date_shortcode' );

// Trip Return Date
function trip_return_date_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        $all_trips = get_posts(array(
            'posts_per_page'    =>  -1,
            'post_type'         =>  'trips',
            'post_status'       =>  'publish'
        ));
        foreach($all_trips as $trip){
            if($trip->ID == $post->ta_trip_select){
                return get_field('trip_return_date', $trip->ID);
            }
        }
    }

}
add_shortcode( 'trip_return_date', 'trip_return_date_shortcode' );

// Trip Country
function trip_country_shortcode() {
    global $post;
    if(get_post_type($post) == 'trip_applications'){
        $all_trips = get_posts(array(
            'posts_per_page'    =>  -1,
            'post_type'         =>  'trips',
            'post_status'       =>  'publish'
        ));
        foreach($all_trips as $trip){
            if($trip->ID == $post->ta_trip_select){
                return get_field('trip_country', $trip->ID);
            }
        }
    }

}
add_shortcode( 'trip_country', 'trip_country_shortcode' );

///////////////////// Add ACF Options Page /////////////////////

if(function_exists('acf_add_options_page')) {
    acf_add_options_page();
}

///////////////////// Remove WooCommerce Product Type Filtering /////////////////////

add_filter('woocommerce_product_filters', 'woocommerce_product_filter_remove');
function woocommerce_product_filter_remove($content){
    return "";

}

///////////////////// Add WooCommerce Custom Type Filtering /////////////////////

add_action('restrict_manage_posts', 'product_type_filter');
function product_type_filter() {
    global $typenow;

    if ($typenow == 'product') {
        $selected = isset($_GET['type']) ? $_GET['type'] : '';
        $info_taxonomy = get_taxonomy('type');

        wp_dropdown_categories(array(
            'show_option_all' => __("Show All Products"),
            'taxonomy' => 'type',
            'name' => 'type',
            'orderby' => 'name',
            'selected' => $selected,
            'value_field' => 'slug',
            'show_count' => true,
            'hide_empty' => true,
        ));
    };
}

// Clean up the WP Admin Backend for user vpid
add_action('admin_menu', 'cleanup_admin_menu', 99999999);
function cleanup_admin_menu(){
    if(!current_user_can('administrator') && current_user_can('vpid')){
        global $menu;
        foreach($menu as $k=>$v){
            if($v[0] == 'Appearance'){
                $menu[$k][0] = 'Menus';
                $menu[$k][2] = 'nav-menus.php';
            }
        }

        remove_menu_page( 'tools.php' );
        remove_menu_page( 'edit.php' );
        remove_menu_page( 'edit-comments.php' );
        remove_menu_page( 'wpcf7' );
        remove_menu_page( 'acf-options' );
        remove_menu_page( 'edit.php?post_type=trips' );
        remove_menu_page( 'woocommerce' );
        remove_menu_page( 'edit_products' );
        remove_menu_page( 'edit-tags.php?taxonomy=session_type&post_type=product' );

        // echo '<pre>';
        // print_r($menu);
        // die();
        return $menu;
    }elseif(!current_user_can('administrator')){
        global $menu;
        foreach($menu as $k=>$v){
            if($v[0] == 'Appearance'){
                $menu[$k][0] = 'Menus';
                $menu[$k][2] = 'nav-menus.php';
            }
        }

        // remove_menu_page( 'tools.php' );
        // remove_menu_page( 'edit.php' );
        // remove_menu_page( 'edit-comments.php' );
        // remove_menu_page( 'wpcf7' );
        // remove_menu_page( 'acf-options' );
        if(!current_user_can('president')){
            remove_menu_page( 'edit.php?post_type=trip_applications' );
        }
        remove_menu_page( 'edit.php?post_type=trips' );
        // remove_menu_page( 'woocommerce' );
        // remove_menu_page( 'edit_products' );
        // remove_menu_page( 'edit-tags.php?taxonomy=session_type&post_type=product' );

    }
}

add_action('admin_head', 'hide_products_vpid');
function hide_products_vpid() {
    if(!current_user_can('administrator') && current_user_can('vpid')){
      echo '<style>
        #menu-posts-product {
          display: none;
        }
      </style>';
  }
}

/////////////////// WooCommerce Hook Run when Payment is Complete /////////////////////

add_action( 'woocommerce_order_status_completed', 'wc_payment_complete');
function wc_payment_complete( $order_id ){
    $order = new WC_Order( $order_id );

    $user_id = (int)$order->user_id;
    $products = $order->get_items();

    $trip_applications = get_posts(array(
        'posts_per_page'    =>  -1,
        'post_type'         =>  'trip_applications',
        'post_status'       =>  'publish',
		'meta_key'			=>  'ta_user_id',
		'meta_value'		=>  $user_id
    ));

    if( sizeof($trip_applications) != 1 ){

    }else{

        foreach($trip_applications as $application){

            $trip_id = get_field('ta_trip_select', $application->ID);

            $trip = get_post($trip_id);
            $trip_deposit_id = get_field('trip_deposit_installment', $trip_id)->ID;
            $trip_flight_cost_id = get_field('trip_flight_cost_installment', $trip_id)->ID;
            $trip_participation_id = get_field('trip_participation_fee_installment', $trip_id)->ID;

            foreach($products as $product){
                if($product['product_id'] == $trip_deposit_id){

					update_field('ta_trip_deposit_received', 1, $application->ID);
                    update_field('ta_application_state', 'deposit_received', $application->ID);

				}elseif($product['product_id'] == $trip_flight_cost_id){

					update_field('ta_flight_cost_received', 1, $application->ID);
                    update_field('ta_application_state', 'flight_cost_received', $application->ID);

				}elseif($product['product_id'] == $trip_participation_id){

					update_field('ta_participation_fee_received', 1, $application->ID);
                    update_field('ta_application_state', 'participation_fee_received', $application->ID);

				}
            }
        }
    }

}



//Joanna
//Menu order
//////////////
function woo_my_account_order() {

   $disable = get_option( 'gens_raf_disable' );
   if( current_user_can('edit_posts')  || current_user_can('vpid') ) {
      if($disable === TRUE || $disable === "yes") {
//user has a role - refer a friend is disable
         $myorder = array(
           'dashboard'          => __( 'Welcome', 'woocommerce' ),
           'admin'              => __( 'My Chapter Admin' ),
           'orders'             => __( 'Order History', 'woocommerce' ),
           'downloads'          => __( 'Exam Aid Materials', 'woocommerce' ),
    		   'my-trips'           => __( 'My Trips' ),
           'edit-account'       => __( 'Account Details', 'woocommerce' ),
           'my-cart'            => __( 'My Cart', 'woocommerce' ),
    		   'customer-logout'    => __( 'Logout', 'woocommerce' ),
         );
       } else {
//user has a role - refer a friend is enable
    	   $myorder = array(
           'dashboard'          => __( 'Welcome', 'woocommerce' ),
           'admin'              => __( 'My Chapter Admin' ),
           'orders'             => __( 'Order History', 'woocommerce' ),
           'downloads'          => __( 'Exam Aid Materials', 'woocommerce' ),
    		   'my-trips'           => __( 'My Trips' ),
           'myreferrals'        => __( 'Refer A Friend' ),
           'edit-account'       => __( 'Account Details', 'woocommerce' ),
           'my-cart'            => __( 'My Cart', 'woocommerce' ),
    		   'customer-logout'    => __( 'Logout', 'woocommerce' ),
    	   );
       }
    } else {
      if($disable === TRUE || $disable === "yes") {
//user has NO role - refer a friend is disable
         $myorder = array(
           'dashboard'          => __( 'Welcome', 'woocommerce' ),
           'orders'             => __( 'Order History', 'woocommerce' ),
           'downloads'          => __( 'Exam Aid Materials', 'woocommerce' ),
    		   'my-trips'           => __( 'My Trips' ),
           'edit-account'       => __( 'Account Details', 'woocommerce' ),
           'my-cart'            => __( 'My Cart', 'woocommerce' ),
     	   	 'customer-logout'    => __( 'Logout', 'woocommerce' ),
    	   );
         } else{
//user has NO role - refer a friend is enable
           $myorder = array(
             'dashboard'          => __( 'Welcome', 'woocommerce' ),
             'orders'             => __( 'Order History', 'woocommerce' ),
             'downloads'          => __( 'Exam Aid Materials', 'woocommerce' ),
      		   'my-trips'           => __( 'My Trips' ),
             'myreferrals'        => __( 'Refer A Friend' ),
             'edit-account'       => __( 'Account Details', 'woocommerce' ),
             'my-cart'            => __( 'My Cart', 'woocommerce' ),
      	   	 'customer-logout'    => __( 'Logout', 'woocommerce' ),
    	   );
       }
    }

	  return $myorder;
}
add_filter( 'woocommerce_account_menu_items', 'woo_my_account_order');



// My Account Tab Merged (Payment-Methods + Edit-Address into Edit-Account)
//////////////////////////////////////////////////////////////////////
add_action( 'woocommerce_account_edit-account_endpoint', 'woocommerce_account_payment_methods');
add_action( 'woocommerce_account_edit-account_endpoint', 'woocommerce_account_edit_address');

//New Tabs
///////////////////////////////////////////////////////////////////////
add_filter ( 'woocommerce_account_menu_items', 'extra_links' );
function extra_links( $menu_links ){
  if( current_user_can('edit_posts') || current_user_can('vpid') ) {
     $new = array( 'my-trips' => 'My Trips', 'admin' => 'Admin', 'my-cart' => 'My Cart' );
  } else {
     $new = array( 'my-trips' => 'My Trips', 'my-cart' => 'My Cart' );
  }
	$menu_links = array_slice( $menu_links, 0, 8, true )
	+ $new
	+ array_slice( $menu_links, 8, NULL, true );
	return $menu_links;
}

add_action( 'init', 'add_my_trips_endpoint' );
function add_my_trips_endpoint() {
    add_rewrite_endpoint( 'my-trips', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'add_my_cart_endpoint' );
function add_my_cart_endpoint() {
    add_rewrite_endpoint( 'my-cart', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'add_admin_endpoint' );
function add_admin_endpoint() {
    add_rewrite_endpoint( 'admin', EP_ROOT | EP_PAGES );
}

//My Cart tab
//////////////////////
add_action( 'woocommerce_account_my-cart_endpoint', 'my_cart_content' );
function my_cart_content() {
  echo do_shortcode( '[woocommerce_cart]' );
}


//Admin; I have to figure out how to make other roles show this
//////////////////////
add_action( 'woocommerce_account_admin_endpoint', 'admin_content' );
function admin_content() {
  echo '<p>Click the link below to access your Chapter Admin:</p>';
  $url = admin_url();
  $link = "<strong><a href='{$url}'>Volunteer Dashboard</a></strong>";
  echo $link;
}


// My Trips
////////////////////////
add_action( 'woocommerce_account_my-trips_endpoint', 'my_trips_content' );
function my_trips_content() {
//2018-07-05 - ismara - we are will use the same my-trip page, not the one created at woocommerce
//  $file_path = include 'woocommerce/myaccount/my-trip.php';
  $file_path = include 'page-templates/my-trip.php';
  $content = @file_get_contents($file_path);
  echo $content;
}
