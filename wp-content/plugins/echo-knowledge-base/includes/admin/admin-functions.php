<?php

/*** GENERIC NON-KB functions  ***?

/**
 * When page is added/updated, check if it contains KB main page shortcode. If it does,
 * add the page to KB config.
 *
 * @param int $post_id The ID of the post being saved.
 * @param object $post The post object.
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
