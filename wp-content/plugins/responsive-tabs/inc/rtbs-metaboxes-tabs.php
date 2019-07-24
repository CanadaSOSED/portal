<?php

/* Hooks the metabox. */
add_action('admin_init', 'dmb_rtbs_add_tab_set', 1);
function dmb_rtbs_add_tab_set() {
	add_meta_box( 
		'rtbs', 
		'<span class="dashicons dashicons-edit"></span> '.__('Tab set editor', RTBS_TXTDM ), 
		'dmb_rtbs_tab_display', // Below
		'rtbs_tabs', 
		'normal', 
		'high'
	);
}


/* Displays the metabox. */
function dmb_rtbs_tab_display() {

	global $post;
	
	/* Gets team data. */
	$team = get_post_meta( $post->ID, '_rtbs_tabs_head', true );
	
	$fields_to_process = array(
    '_rtbs_title',
    '_rtbs_content'
	);

	wp_nonce_field( 'dmb_rtbs_meta_box_nonce', 'dmb_rtbs_meta_box_nonce' ); ?>

	<div id="dmb_preview_tabs">
		<!-- Closes preview button -->
		<a class="dmb_preview_button dmb_preview_tabs_close" href="#">
			<?php _e('Close preview', RTBS_TXTDM ) ?>
		</a>
	</div>

	<div id="dmb_unique_editor">
		<?php wp_editor( '', 'dmb_editor', array('editor_height' => '300px' ) );  ?>
		<br/>
		<a class="dmb_big_button_primary dmb_ue_update" href="#">
			<?php _e('Update', RTBS_TXTDM ) ?>
		</a>
		<a class="dmb_big_button_secondary dmb_ue_cancel" href="#">
			<?php _e('Cancel', RTBS_TXTDM ) ?>
		</a>
	</div>
	
	<!-- Toolbar for tab metabox -->
	<div class="dmb_toolbar">
		<div class="dmb_toolbar_inner">
			<a class="dmb_big_button_secondary dmb_expand_rows" href="#"><span class="dashicons dashicons-editor-expand"></span> <?php _e('Expand all', RTBS_TXTDM ) ?>&nbsp;</a>&nbsp;&nbsp;
			<a class="dmb_big_button_secondary dmb_collapse_rows" href="#"><span class="dashicons dashicons-editor-contract"></span> <?php _e('Collapse all', RTBS_TXTDM ) ?>&nbsp;</a>&nbsp;&nbsp;
			<a class="dmb_show_preview_tab_set dmb_preview_button"><span class="dashicons dashicons-admin-appearance"></span> <?php _e('Instant preview', RTBS_TXTDM ) ?>&nbsp;</a>
			<div class="dmb_clearfix"></div>
		</div>
	</div>

	<?php if ( $team ) {

		/* Loops through rows. */
		foreach ( $team as $team_member ) {

			/* Retrieves each field for current tab. */
			$member = array();
			foreach ( $fields_to_process as $field) {
				switch ($field) {
					default:
						$member[$field] = ( isset($team_member[$field]) ) ? esc_attr($team_member[$field]) : '';
						break;
				}
      } ?>
  

			<!-- START tab -->
			<div class="dmb_main">

				<!-- member handle bar -->
				<div class="dmb_handle">
					<a class="dmb_big_button_secondary dmb_move_row_up" href="#" title="Move up"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
					<a class="dmb_big_button_secondary dmb_move_row_down" href="#" title="Move down"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
					<div class="dmb_handle_title"></div>
					<a class="dmb_big_button_secondary dmb_remove_row_btn" href="#" title="Remove"><span class="dashicons dashicons-no-alt"></span></a>
					<a class="dmb_big_button_secondary dmb_clone_row" href="#" title="Clone"><span class="dashicons dashicons-admin-page"></span><?php _e('Clone', RTBS_TXTDM ); ?></a>
					<div class="dmb_clearfix"></div>
				</div>

				<!-- START inner -->
				<div class="dmb_inner">

					<div class="dmb_section_title">
						<?php _e('Tab details', RTBS_TXTDM ) ?>
					</div>

					<div class="dmb_grid dmb_grid_100 dmb_grid_first dmb_grid_last">
						<div class="dmb_field_title">
							<?php _e('Title', RTBS_TXTDM ) ?>
						</div>
						<input name="tab_titles[]" class="dmb_field dmb_highlight_field dmb_tab_title" type="text" value="<?php echo $member['_rtbs_title']; ?>" placeholder="<?php _e('e.g. Overview', RTBS_TXTDM ) ?>" />
					</div>

					<div class="dmb_grid dmb_grid_100 dmb_grid_first dmb_grid_last">

						<div class="dmb_field_title">
							<?php _e('Content', RTBS_TXTDM ) ?>
							<a class="dmb_inline_tip dmb_tooltip_large" data-tooltip="<?php _e('Edit your tab\'s content by clicking the button below. Once updated, it will show up here.', RTBS_TXTDM ) ?>">[?]</a>
						</div>

						<div class="dmb_field dmb_tab_content"><?php echo $member["_rtbs_content"]; ?></div>
						<textarea class="biofield" style="display:none;" name="tab_contents[]"><?php echo $member["_rtbs_content"]; ?></textarea>
						
						<div class="dmb_clearfix"></div>

						<div class="dmb_edit_tab_content dmb_small_button_primary">
							<span class="dashicons dashicons-edit"></span> <?php _e('Edit tab content', RTBS_TXTDM ) ?>&nbsp;
						</div>

					</div>

					<div class="dmb_clearfix"></div>

				<!-- END inner -->
				</div>
			
			<!-- END row -->
			</div>

			<?php
		}
	} ?>

	<!-- START empty tab -->
	<div class="dmb_main dmb_empty_row" style="display:none;">

		<!-- tab handle bar -->
		<div class="dmb_handle">
			<a class="dmb_big_button_secondary dmb_move_row_up" href="#" title="Move up"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
			<a class="dmb_big_button_secondary dmb_move_row_down" href="#" title="Move down"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
			<div class="dmb_handle_title"></div>
			<a class="dmb_big_button_secondary dmb_remove_row_btn" href="#" title="Remove"><span class="dashicons dashicons-no-alt"></span></a>
			<a class="dmb_big_button_secondary dmb_clone_row" href="#" title="Clone"><span class="dashicons dashicons-admin-page"></span><?php _e('Clone', RTBS_TXTDM ); ?></a>
			<div class="dmb_clearfix"></div>
		</div>

    <!-- START inner -->
    <div class="dmb_inner">

      <div class="dmb_section_title">
        <?php _e('Tab details', RTBS_TXTDM ) ?>
      </div>

      <div class="dmb_grid dmb_grid_100 dmb_grid_first dmb_grid_last">
        <div class="dmb_field_title">
          <?php _e('Title', RTBS_TXTDM ) ?>
        </div>
        <input name="tab_titles[]" class="dmb_field dmb_highlight_field dmb_tab_title" type="text" value="" placeholder="<?php _e('e.g. Overview', RTBS_TXTDM ) ?>" />
      </div>

      <div class="dmb_grid dmb_grid_100 dmb_grid_first dmb_grid_last">
			
				<div class="dmb_field_title">
					<?php _e('Content', RTBS_TXTDM ) ?>
					<a class="dmb_inline_tip dmb_tooltip_large" data-tooltip="<?php _e('Edit your tab\'s content by clicking the button below. Once updated, it will show up here.', RTBS_TXTDM ) ?>">[?]</a>
				</div>

				<div class="dmb_field dmb_tab_content"></div>
				<textarea class="biofield" style="display:none;" name="tab_contents[]"></textarea>
          
        <div class="dmb_clearfix"></div>
				
				<div class="dmb_edit_tab_content dmb_small_button_primary">
					<span class="dashicons dashicons-edit"></span> <?php _e('Edit tab content', RTBS_TXTDM ) ?>&nbsp;
				</div>

      </div>

      <div class="dmb_clearfix"></div>

    </div>

    <div class="dmb_clearfix"></div>

	<!-- END empty row -->
	</div>

	<div class="dmb_clearfix"></div>

	<div class="dmb_no_row_notice">
		<?php /* translators: Leave HTML tags */ _e('Create your tab set by <strong>adding tabs</strong> to it.<br/>Click the button below to get started.', RTBS_TXTDM ) ?>
	</div>

	<!-- Add row button -->
	<a class="dmb_big_button_primary dmb_add_row" href="#">
		<span class="dashicons dashicons-plus"></span> 
		<?php _e('Add a tab', RTBS_TXTDM ) ?>&nbsp;
	</a>

<?php }