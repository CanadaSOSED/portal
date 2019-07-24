<?php 

/* Defines force font select options. */
function dmb_rtbs_force_fonts_options() {
	$options = array ( 
		__('Theme\'s font', RTBS_TXTDM ) => 'no',
		__('Default font', RTBS_TXTDM ) => 'yes'
	);
	return $options;
}

/* Defines tab background select options. */
function dmb_rtbs_tab_background_options() {
	$options = array ( 
		__('Transparent', RTBS_TXTDM ) => 'transparent',
		__('Light grey', RTBS_TXTDM ) => 'whitesmoke'
	);
	return $options;
}

/* Hooks the metabox. */
add_action('admin_init', 'dmb_rtbs_add_settings', 1);
function dmb_rtbs_add_settings() {
	add_meta_box( 
		'rtbs_settings', 
		'<span class="dashicons dashicons-admin-generic"></span> '.__('Settings', RTBS_TXTDM), 
		'dmb_rtbs_settings_display', 
		'rtbs_tabs', 
		'side', 
		'high'
	);
}


/* Displays the metabox. */
function dmb_rtbs_settings_display() { 
	
	global $post;

	/* Retrieves select options. */
	$tabs_force_font = dmb_rtbs_force_fonts_options();
	$tabs_tbg = dmb_rtbs_tab_background_options();

	/* Processes retrieved fields. */
	$settings = array();

	$settings['_rtbs_tabs_bg_color'] = get_post_meta( $post->ID, '_rtbs_tabs_bg_color', true );

	$settings['_rtbs_breakpoint'] = get_post_meta( $post->ID, '_rtbs_breakpoint', true );

	$settings['_rtbs_tbg'] = get_post_meta( $post->ID, '_rtbs_tbg', true );

	/* Checks if forcing original fonts. */
	$settings['_rtbs_original_font'] = get_post_meta( $post->ID, '_rtbs_original_font', true );
	(($settings['_rtbs_original_font'] == 'no' || $settings['_rtbs_original_font'] != true) ? $settings['_rtbs_original_font'] = 'no' : $settings['_rtbs_original_font'] = 'yes');

	?>

	<div class="dmb_settings_box">

		<div class="dmb_section_title">
			<?php /* translators: Styling settings */ _e('Styling', RTBS_TXTDM) ?>
		</div>

		<div class="dmb_grid dmb_grid_50 dmb_grid_first">
			<div class="dmb_field_title">
				<?php _e('Max. mobile width', RTBS_TXTDM ) ?>
				<a class="dmb_inline_tip dmb_tooltip_medium" data-tooltip="<?php _e('When the window\'s width goes below this number, the tab set will switch to accordion mode.', RTBS_TXTDM ) ?>">[?]</a>
			</div>
			<input name="tabs_breakpoint" class="dmb_field dmb_breakpoint" type="text" value="<?php echo (!empty($settings['_rtbs_breakpoint'])) ? $settings['_rtbs_breakpoint'] : '600'; ?>" placeholder="<?php _e('e.g. 600', RTBS_TXTDM ) ?>" />
		</div>

		<!-- Font option -->
		<div class="dmb_grid dmb_grid_50 dmb_grid_last">
			<div class="dmb_field_title">
				<?php _e('Font to use', RTBS_TXTDM ) ?>
			</div>
			<select class="dmb_side_select" name="tabs_force_font">
				<?php foreach ( $tabs_force_font as $label => $value ) { ?>
					<option value="<?php echo $value; ?>"<?php selected( (isset($settings['_rtbs_original_font'])) ? $settings['_rtbs_original_font'] : 'no', $value ); ?>><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</div>

		<!-- Tab background option -->
		<div class="dmb_grid dmb_grid_100 dmb_grid_first dmb_grid_last">
			<div class="dmb_field_title">
				<?php _e('Inactive tabs background', RTBS_TXTDM ) ?>
			</div>
			<select class="dmb_side_select" name="tabs_tbgs">
				<?php foreach ( $tabs_tbg as $label => $value ) { ?>
					<option value="<?php echo $value; ?>"<?php selected( (isset($settings['_rtbs_tbg'])) ? $settings['_rtbs_tbg'] : 'transparent', $value ); ?>><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</div>

		<!-- Main color -->
		<div class="dmb_color_of_team dmb_grid dmb_grid_100 dmb_grid_first dmb_grid_last">
			<div class="dmb_field_title">
				<?php _e('Main color', RTBS_TXTDM) ?>
			</div>
			<input class="dmb_color_picker dmb_field dmb_color_of_tabs" name="tabs_color" type="text" value="<?php echo (!empty($settings['_rtbs_tabs_bg_color'])) ? $settings['_rtbs_tabs_bg_color'] : '#12bece'; ?>" />
		</div>

		<div class="dmb_clearfix"></div>

	</div>

<?php } ?>