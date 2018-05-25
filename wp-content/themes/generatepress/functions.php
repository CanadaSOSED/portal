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
    if( current_user_can('edit_post') || current_user_can('vpid') ) {
			  $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-courses">' . __( 'My Courses' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="'. get_dashboard_url() .'">' . __( 'Dashboard' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
    } else {
 			  $items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-courses">' . __( 'My Courses' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="'. get_dashboard_url() .'">' . __( 'Dashboard' ) . '</a></li>';
        $items .= '<li><a class="nav-link link dropdown-item" href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
    }

 } else {
	     $items .= '<li><a class="nav-link link dropdown-item" href="'. network_site_url() .'">' . __( 'SOS Campus' ) . '</a></li>';

  //   $items .= '<li><a class="nav-link link dropdown-item" href="http://localhost/sosportal/toronto/my-account/">' . __( 'Login' ) . '</a></li>';
//		 $items .= '<li><a class="nav-link link dropdown-item" href="http://localhost/sosportal/toronto/my-account/">' . __( 'Sign Up' ) . '</a></li>';
     //$items .= '<li><a class="nav-link link dropdown-item" href="'. get_site_url() .'/my-account">' . __( 'Sign Up' ) . '</a></li>';
 }

 return $items;
}

add_filter( 'wp_nav_menu_items', 'add_item_register_menu', 199, 2 );
// ismara - 2018/04/30 - end
