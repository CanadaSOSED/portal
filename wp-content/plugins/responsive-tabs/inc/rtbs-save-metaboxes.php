<?php 

/* Saves metaboxes */
add_action('save_post', 'dmb_rtbs_tabs_meta_box_save');
function dmb_rtbs_tabs_meta_box_save($post_id) {

	if ( ! isset( $_POST['dmb_rtbs_meta_box_nonce'] ) ||
	! wp_verify_nonce( $_POST['dmb_rtbs_meta_box_nonce'], 'dmb_rtbs_meta_box_nonce' ) )
		return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	if (!current_user_can('edit_post', $post_id))
		return;

	/* Gets plans. */
	$old_plans = get_post_meta($post_id, '_rtbs_tabs_head', true);
	
	$new_plans = array();

	/* Settings. */
	$old_tabs_settings = array();

	$old_tabs_settings['_rtbs_breakpoint'] = get_post_meta( $post_id, '_rtbs_breakpoint', true );
	$old_tabs_settings['_rtbs_tabs_bg_color'] = get_post_meta( $post_id, '_rtbs_tabs_bg_color', true );
	$old_tabs_settings['_rtbs_original_font'] = get_post_meta( $post_id, '_rtbs_original_font', true );
	$old_tabs_settings['_rtbs_tbg'] = get_post_meta( $post_id, '_rtbs_tbg', true );

	$count = count( $_POST['tab_titles'] );

	for ( $i = 0; $i < $count; $i++ ) {

    if( isset($_POST['tab_titles'][$i]) && $_POST['tab_titles'][$i] != ''){

      /* Head. */
      (isset($_POST['tab_titles'][$i]) && $_POST['tab_titles'][$i]) ? $new_plans[$i]['_rtbs_title'] = stripslashes( wp_kses_post( $_POST['tab_titles'][$i] ) ) : $new_plans[$i]['_rtbs_title'] = 'Untitled';
      (isset($_POST['tab_contents'][$i]) && $_POST['tab_contents'][$i]) ? $new_plans[$i]['_rtbs_content'] = balanceTags( $_POST['tab_contents'][$i] ) : $new_plans[$i]['_rtbs_content'] = '';

      
    }

	}


	/* Settings. */
	(isset($_POST['tabs_color']) && $_POST['tabs_color']) ? $new_tabs_settings['_rtbs_tabs_bg_color'] = stripslashes( strip_tags( sanitize_text_field( $_POST['tabs_color'] ) ) ) : $new_tabs_settings['_rtbs_tabs_bg_color'] = '';
	(isset($_POST['tabs_breakpoint']) && $_POST['tabs_breakpoint']) ? $new_tabs_settings['_rtbs_breakpoint'] = stripslashes( strip_tags( sanitize_text_field( absint( $_POST['tabs_breakpoint'] ) ) ) ) : $new_tabs_settings['_rtbs_breakpoint'] = '';
	(isset($_POST['tabs_force_font']) && $_POST['tabs_force_font']) ? $new_tabs_settings['_rtbs_original_font'] = stripslashes( strip_tags( sanitize_text_field( $_POST['tabs_force_font'] ) ) ) : $new_tabs_settings['_rtbs_original_font'] = '';
	(isset($_POST['tabs_tbgs']) && $_POST['tabs_tbgs']) ? $new_tabs_settings['_rtbs_tbg'] = stripslashes( strip_tags( sanitize_text_field( $_POST['tabs_tbgs'] ) ) ) : $new_tabs_settings['_rtbs_tbg'] = '';


  /* Updates tab set. */
	if ( !empty( $new_plans ) && $new_plans != $old_plans )
		update_post_meta( $post_id, '_rtbs_tabs_head', $new_plans );
	elseif ( empty($new_plans) && $old_plans )
		delete_post_meta( $post_id, '_rtbs_tabs_head', $old_plans );
		
	
	if ( !empty( $new_tabs_settings['_rtbs_tabs_bg_color'] ) && $new_tabs_settings['_rtbs_tabs_bg_color'] != $old_tabs_settings['_rtbs_tabs_bg_color'] )
		update_post_meta( $post_id, '_rtbs_tabs_bg_color', $new_tabs_settings['_rtbs_tabs_bg_color'] );

	if ( !empty( $new_tabs_settings['_rtbs_breakpoint'] ) && $new_tabs_settings['_rtbs_breakpoint'] != $old_tabs_settings['_rtbs_breakpoint'] )
		update_post_meta( $post_id, '_rtbs_breakpoint', $new_tabs_settings['_rtbs_breakpoint'] );

	if ( !empty( $new_tabs_settings['_rtbs_original_font'] ) && $new_tabs_settings['_rtbs_original_font'] != $old_tabs_settings['_rtbs_original_font'] )
		update_post_meta( $post_id, '_rtbs_original_font', $new_tabs_settings['_rtbs_original_font'] );

	if ( !empty( $new_tabs_settings['_rtbs_tbg'] ) && $new_tabs_settings['_rtbs_tbg'] != $old_tabs_settings['_rtbs_tbg'] )
		update_post_meta( $post_id, '_rtbs_tbg', $new_tabs_settings['_rtbs_tbg'] );

}