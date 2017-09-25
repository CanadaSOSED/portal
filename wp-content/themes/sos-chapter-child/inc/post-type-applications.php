<?php
/**
 * Custom Post Type For Volunteer Applications
 *
 * @package sos-chapter
 */

// Register Custom Post Type
function volunteer_opportunities_post_type() {

	$labels = array(
		'name'                  => _x( 'Volunteer Opportunities', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Opportunity', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Opportunities', 'text_domain' ),
		'name_admin_bar'        => __( 'Opportunity', 'text_domain' ),
		'archives'              => __( 'Opportunity Archives', 'text_domain' ),
		'attributes'            => __( 'Opportunity Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Opportunity:', 'text_domain' ),
		'all_items'             => __( 'All Opportunities', 'text_domain' ),
		'add_new_item'          => __( 'Add New Opportunity', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Opportunity', 'text_domain' ),
		'edit_item'             => __( 'Edit Opportunity', 'text_domain' ),
		'update_item'           => __( 'Update Opportunity', 'text_domain' ),
		'view_item'             => __( 'View Opportunity', 'text_domain' ),
		'view_items'            => __( 'View Opportunities', 'text_domain' ),
		'search_items'          => __( 'Search Opportunity', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Opportunities list', 'text_domain' ),
		'items_list_navigation' => __( 'Opportunities list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Volunteer Opportunities', 'text_domain' ),
		'description'           => __( 'Site articles.', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'revisions', ),
		'taxonomies'            => array( 'category'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 26,
		'menu_icon'				=> 'dashicons-universal-access',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'opportunities', $args );

}
add_action( 'init', 'volunteer_opportunities_post_type', 0 );

?>