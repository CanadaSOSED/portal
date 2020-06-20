<?php

/*** GENERIC NON-KB functions  ***?

/**
 * When page is added/updated, check if it contains KB main page shortcode. If it does,
 * add the page to KB config.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function epkb_save_any_page( $post_id, $post ) {

	// ignore autosave/revision which is not article submission; same with ajax and bulk edit
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return;
	}
	
	// return if this page does not have KB shortcode
	$kb_id = EPKB_KB_Handler::get_kb_id_from_kb_main_shortcode( $post );
	if ( empty( $kb_id ) ) {
		return;
	}

	// core handles only default KB
	if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {
		return;
	}

	// update KB kb_config if needed
	$kb_main_pages = epkb_get_instance()->kb_config_obj->get_value( 'kb_main_pages', $kb_id, null );
	if ( $kb_main_pages === null || ! is_array($kb_main_pages) ) {
		EPKB_Logging::add_log('Could not update KB Main Pages (2)', $kb_id);
		return;
	}

	// don't update if the page is not relevant and not stored
	if ( in_array($post->post_status, array('inherit', 'trash')) ) {
		if ( ! isset($kb_main_pages[$post_id]) ) {
			return;
		}

	// don't update if the page is stored already
	} else if ( in_array($post_id, array_keys($kb_main_pages)) && $kb_main_pages[$post_id] == $post->post_title ) {
		return;
	}

	// update list of KB Main Pages
	if ( in_array($post->post_status, array('inherit', 'trash')) ) {
		unset($kb_main_pages[$post_id]);
	} else {
		$kb_main_pages[$post_id] = $post->post_title;   // post_title not used
	}

	// sanitize and save configuration in the database. see EPKB_Settings_DB class
	$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'kb_main_pages', $kb_main_pages );
	if ( is_wp_error( $result ) ) {
		EPKB_Logging::add_log('Could not update KB Main Pages', $kb_id);
		return;
	}
}
add_action( 'save_post', 'epkb_save_any_page', 10, 2 );

/**
 * If user changed slug for page with KB shortcode and we do not have matching Article Common Path then let user know.
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function epkb_does_path_for_articles_need_update( $post_id, $post ) {
	
	// ignore autosave/revision which is not article submission; same with ajax and bulk edit
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return;
	}

	// check if we are changing any of the KB Main Pages or their parents
	$kb_config = array();
	$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
	foreach ( $all_kb_configs as $one_kb_config ) {

		$kb_main_pages = empty($one_kb_config['kb_main_pages']) ? array() : $one_kb_config['kb_main_pages'];

		// is saved page KB Main Page?
		if ( isset($kb_main_pages[$post_id]) ) {
			$kb_config = $one_kb_config;
			break;
		}

		foreach( $kb_main_pages as $kb_main_page_id => $title ) {
			$ancestors = get_post_ancestors( $post );
			if ( in_array($kb_main_page_id, $ancestors) ) {
				$kb_config = $one_kb_config;
				break 2;
			}
		}
	}

	// this page is not KB Main Page or its parent
	if ( empty($kb_config['kb_main_pages']) ) {
		return;
	}

	// get slugs for all KB Main Pages
	$kb_main_page_slugs = array();
	foreach( $kb_config['kb_main_pages'] as $kb_main_page_id => $title ) {

		$slug = EPKB_Utilities::get_main_page_slug( $kb_main_page_id );
		if ( empty($slug) ) {
			continue;
		}

		$kb_main_page_slugs[$kb_main_page_id] = $slug;
	}

	if ( empty($kb_main_page_slugs) ) {
		return;
	}

	// check if the Article Common Path does not match any more any of the KB Main Page paths
	foreach( $kb_main_page_slugs as $kb_main_page_slug ) {
		if ( $kb_config['kb_articles_common_path'] == $kb_main_page_slug ) {
			return;
		}
	}

	EPKB_Admin_Notices::add_ongoing_notice( __( 'We detected that your KB Main Page slug has changed. Please go to the Global Wizard to update slug for your articles.', 'echo-knowledge-base' ) .
	                                        ' <a href="' . esc_url( admin_url('edit.php?post_type=epkb_post_type_' . $kb_config['id'] . '&page=epkb-kb-configuration&wizard-global' ) ) . '">' .
	                                        __( 'Edit', 'echo-knowledge-base' ) . '</a> ','warning', 'epkb_changed_slug' );
	
}
add_action( 'save_post', 'epkb_does_path_for_articles_need_update', 15, 2 );  // needs to run AFTER epkb_save_any_page()

/**
 * If user deleted page then let them know if the page has active KB shortcode.
 * @param $post_id
 */
function epkb_add_delete_kb_page_warning( $post_id ) {

	$post = get_post( $post_id );
	if ( empty( $post ) ) {
		return;
	}
	
	$kb_id = EPKB_KB_Handler::get_kb_id_from_kb_main_shortcode( $post );
	if ( empty( $kb_id ) ) {
		return;
	}

	$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
	if ( empty ( $all_kb_configs ) ) {
		return;
	}
	
	$kb_articles_common_path = '';
	foreach ( $all_kb_configs as $kb_config ) {
		if ( $kb_config['id'] == $kb_id ) {
			$kb_articles_common_path = $kb_config['kb_articles_common_path'];
			break;
		}
	}
	
	$main_page_slug = EPKB_Utilities::get_main_page_slug( $post_id );
	
	if ( $kb_articles_common_path == $main_page_slug ) {
		EPKB_Admin_Notices::add_one_time_notice( sprintf( __( 'We detected that you deleted KB Main Page "%s". If you did this by accident you can restore here: ', 'echo-knowledge-base' ) , $post->post_title ) .
	                                        ' <a href="' . esc_url( admin_url('edit.php?post_status=trash&post_type=page' ) ) . '">' .
	                                        __( 'Restore', 'echo-knowledge-base' ) . '</a> ','warning' );
	}
}
add_action( 'wp_trash_post', 'epkb_add_delete_kb_page_warning', 15, 2 ); 

// Add "KB Page" to the page's list 
function epkb_add_post_state( $post_states, $post ) {

	if ( empty($post->post_type) || $post->post_type != 'page' ) {
		return $post_states;
	}

	$kb_id = EPKB_KB_Handler::get_kb_id_from_kb_main_shortcode( $post );
	if ( empty( $kb_id ) ) {
		return $post_states;
	}
	
	$post_states[] = __( 'Knowledge Base Page', 'echo-knowledge-base' ) . ' #' . $kb_id;
	
	return $post_states;
}
// TODO what the impact is ? add_filter( 'display_post_states', 'epkb_add_post_state', 10, 2 );