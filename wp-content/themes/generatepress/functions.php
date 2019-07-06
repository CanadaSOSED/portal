<?php
/**
 * GeneratePress.
 *
 * Please do not make any edits to this file. All edits should be done in a child theme.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set our theme version.
define( 'GENERATE_VERSION', '2.0.2' );

if ( ! function_exists( 'generate_setup' ) ) {
	add_action( 'after_setup_theme', 'generate_setup' );
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since 0.1
	 */
	function generate_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'generatepress' );

		// Add theme support for various features.
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'status' ) );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
		add_theme_support( 'customize-selective-refresh-widgets' );

		add_theme_support( 'custom-logo', array(
			'height' => 70,
			'width' => 350,
			'flex-height' => true,
			'flex-width' => true
		) );

		// Register primary menu.
		register_nav_menus( array(
			'primary' => __( 'Primary Menu', 'generatepress' ),
		) );

		/**
		 * Set the content width to something large
		 * We set a more accurate width in generate_smart_content_width()
		 */
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 1200; /* pixels */
		}

		// This theme styles the visual editor to resemble the theme style.
		add_editor_style( 'css/admin/editor-style.css' );
	}
}

/**
 * Get all necessary theme files
 */
require get_template_directory() . '/inc/theme-functions.php';
require get_template_directory() . '/inc/defaults.php';
require get_template_directory() . '/inc/class-css.php';
require get_template_directory() . '/inc/css-output.php';
require get_template_directory() . '/inc/general.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/markup.php';
require get_template_directory() . '/inc/element-classes.php';
require get_template_directory() . '/inc/typography.php';
require get_template_directory() . '/inc/plugin-compat.php';
require get_template_directory() . '/inc/migrate.php';
require get_template_directory() . '/inc/deprecated.php';

if ( is_admin() ) {
	require get_template_directory() . '/inc/meta-box.php';
	require get_template_directory() . '/inc/dashboard.php';
}

/**
 * Load our theme structure
 */
require get_template_directory() . '/inc/structure/archives.php';
require get_template_directory() . '/inc/structure/comments.php';
require get_template_directory() . '/inc/structure/featured-images.php';
require get_template_directory() . '/inc/structure/footer.php';
require get_template_directory() . '/inc/structure/header.php';
require get_template_directory() . '/inc/structure/navigation.php';
require get_template_directory() . '/inc/structure/post-meta.php';
require get_template_directory() . '/inc/structure/sidebars.php';


// ismara - 2018/04/30 - removing footer (copyright)
add_action( 'after_setup_theme', 'tu_remove_footer_area' );

function tu_remove_footer_area() {
    remove_action( 'generate_footer','generate_construct_footer' );
}
// ismara - 2018/04/30 - end


// ismara - 2018/04/30 - Append Nav with more itens (my courses / Dashboard / logout / SOS campus links
function add_item_register_menu( $items, $args ) {
		if ( $args->theme_location != 'primary' ) {
				return $items;
		}
		if ( is_user_logged_in() ) {
				$active = get_active_blog_for_user( get_current_user_id() );
				$items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'My Account' ) . '</a></li>';
				$items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-courses">' . __( 'My Courses' ) . '</a></li>';
				if ($active->id != 32) //active blog is not SOS Training
						$items .= '<li><a class="nav-link link dropdown-item" href="'. get_admin_url( $active->blog_id, $path, $scheme ) .'">' . __( 'My Chapter Admin' ) . '</a></li>';
				$items .= '<li><a class="nav-link link dropdown-item" href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
		}
		else {
				$items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'Login' ) . '</a></li>';
				$items .= '<li><a class="nav-link link dropdown-item" href="'. network_site_url() .'">' . __( 'SOS Campus' ) . '</a></li>';
		}
		return $items;
}

add_filter( 'wp_nav_menu_items', 'add_item_register_menu', 199, 2 );
// ismara - 2018/04/30 - end

//ismara 2019/04/10 - My account for training
function woo_my_account_order() {
   if( current_user_can('edit_posts')  || current_user_can('vpid') ) {
//user has a role
    	   $myorder = array(
           'dashboard'          => __( 'Welcome', 'woocommerce' ),
           'admin'              => __( 'My Training Admin' ),
 					 'my-courses'          => __( 'My Courses', 'woocommerce' ),
           'edit-account'       => __( 'Account Details', 'woocommerce' ),
    		   'customer-logout'    => __( 'Logout', 'woocommerce' ),
    	   );
    } else {
//user has NO role
           $myorder = array(
             'dashboard'          => __( 'Welcome', 'woocommerce' ),
             'my-courses'          => __( 'My Courses', 'woocommerce' ),
             'edit-account'       => __( 'Account Details', 'woocommerce' ),
      	   	 'customer-logout'    => __( 'Logout', 'woocommerce' ),
    	   );
    }
	  return $myorder;
}
add_filter( 'woocommerce_account_menu_items', 'woo_my_account_order');

// My Account Tab Merged (Payment-Methods + Edit-Address into Edit-Account)
add_action( 'woocommerce_account_edit-account_endpoint', 'woocommerce_account_payment_methods');
add_action( 'woocommerce_account_edit-account_endpoint', 'woocommerce_account_edit_address');

//New Tabs
add_filter ( 'woocommerce_account_menu_items', 'extra_links' );
function extra_links( $menu_links ){
  if( current_user_can('edit_posts') || current_user_can('vpid') ) {
     $new = array( 'admin' => 'Admin', 'my-courses' => 'My Courses' );
  } else {
     $new = array( 'my-courses' => 'My Courses' );
  }
	$menu_links = array_slice( $menu_links, 0, 8, true )
	+ $new
	+ array_slice( $menu_links, 8, NULL, true );
	return $menu_links;
}

add_action( 'init', 'add_admin_endpoint' );
function add_admin_endpoint() {
    add_rewrite_endpoint( 'admin', EP_ROOT | EP_PAGES );
}

add_action( 'woocommerce_account_admin_endpoint', 'admin_content' );
function admin_content() {
  echo '<p>Click the link below to access your Chapter Admin:</p>';
  $url = admin_url();
  $link = "<strong><a href='{$url}'>Volunteer Dashboard</a></strong>";
  echo $link;
}
//ismara - 2019/04/10 - end


//ismara 2019/06/26 - Custom field for User - Chapter information - it will be dinamically created based on the blogs
add_filter('acf/load_field/name=user_chapter', 'acf_dinamically_user_chapter_value');
function acf_dinamically_user_chapter_value($field) {
	 $args = array(
			'site__not_in' => '5,29,30,31,32',
			'orderby' => 'blogname'
 	 );
	 // Here we grab the blogs using the arguments originated from the shortcode
	 $get_custom_blogs = get_sites($args);
	 // If we have blogs, proceed
	 if ($get_custom_blogs) {
		  // Foreach found custom blog, we build the option using the [key] => [value] fashion
		  foreach ($get_custom_blogs as $custom_blog) {
			  	$Chapter[str_replace('&#039;', '', $custom_blog->blogname)] = str_replace('&#039;', '', $custom_blog->blogname);
		  }
	 // If we don't have blogs, halt! Lets use a generic not found option
	 } else {
		  // Just a generic option to inform nothing was found
		  $Chapter['No blogs found'] = 'No blogs found';
 	 }
    $field['choices'] = $Chapter;
    return $field;
}
//ismara - 2019/06/26 - end
