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

    wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/assets/css/child-theme-min.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/assets/js/child-theme-min.js', array(), $the_theme->get( 'Version' ), true );
}


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

// Rename Default "Post" type to "Articles"
//////////////////////////////////////////////////////////////////////
function change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Articles';
    $submenu['edit.php'][5][0] = 'Articles';
    $submenu['edit.php'][10][0] = 'Add Article';
    $submenu['edit.php'][16][0] = 'Article Tags';
}
function change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Articles';
    $labels->singular_name = 'Article';
    $labels->add_new = 'Add Article';
    $labels->add_new_item = 'Add Article';
    $labels->edit_item = 'Edit Article';
    $labels->new_item = 'Article';
    $labels->view_item = 'View Article';
    $labels->search_items = 'Search Articles';
    $labels->not_found = 'No Articles found';
    $labels->not_found_in_trash = 'No Articles found in Trash';
    $labels->all_items = 'All Articles';
    $labels->menu_name = 'Articles';
    $labels->name_admin_bar = 'Articles';
}
 
add_action( 'admin_menu', 'change_post_label' );
add_action( 'init', 'change_post_object' );


// Rename Default "Category" Taxonomy to "Topics"
//////////////////////////////////////////////////////////////////////
function change_cat_label() {
    global $submenu;
    $submenu['edit.php'][15][0] = 'Topics'; // Rename categories to Topics
}
add_action( 'admin_menu', 'change_cat_label' );

function change_cat_object() {
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['category']->labels;
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
add_action( 'init', 'change_cat_object' );

// Display Post Tags - display using  display_post_tags();
///////////////////////////////////////////////////////////////////////
function display_post_tags() {

    $tags = get_tags();
    $html = '<div class="post_tags"> Tagged: ';
    foreach ( $tags as $tag ) {
        $tag_link = get_tag_link( $tag->term_id );
                
        $html .= "<a href='{$tag_link}' title='{$tag->name} Tag' class='{$tag->slug} btn btn-sm btn-outline-secondary'>";
        $html .= "{$tag->name}</a> ";
    }
    $html .= '</div>';
    echo $html;
}

@include 'inc/widgets.php';
@include 'inc/breadcrumbs.php';
@include 'inc/recent-posts-by-category-widget.php';
//@include 'inc/related-posts-by-category-widget.php';

