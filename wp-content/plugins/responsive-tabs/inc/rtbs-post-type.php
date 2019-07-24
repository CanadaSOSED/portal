<?php 

/* Registers the tabs post type. */
add_action( 'init', 'register_rtbs_type' );
function register_rtbs_type() {
	
  /* Defines labels. */
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

  /* Defines permissions. */
	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
    'show_in_admin_bar'  => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title' ),
    'menu_icon'          => 'dashicons-plus'
	);

  /* Registers post type. */
	register_post_type( 'rtbs_tabs', $args );  

}


/* Customizes tab sets update messages. */
add_filter( 'post_updated_messages', 'rtbs_updated_messages' );
function rtbs_updated_messages( $messages ) {
	$post             = get_post();
	$post_type        = get_post_type( $post );
  $post_type_object = get_post_type_object( $post_type );
  
  /* Defines update messages. */
	$messages['rtbs_tabs'] = array(
		1  => __( 'Tab set updated.', RTBS_TXTDM ),
		4  => __( 'Tab set updated.', RTBS_TXTDM ),
		6  => __( 'Tab set published.', RTBS_TXTDM ),
		7  => __( 'Tab set saved.', RTBS_TXTDM ),
		10 => __( 'Tab set draft updated.', RTBS_TXTDM )
	);

	return $messages;

}

?>