<?php

/**  Register JS and CSS files  */

/**
 * FRONT-END pages using our plugin features
 */
function epkb_load_public_resources() {

    global $eckb_kb_id;

    // if this is not KB Main Page then do not load public resources or is a Category Archive page
    if ( empty($eckb_kb_id) ) {
        return;
    }

	epkb_load_public_resources_now();
}
add_action( 'wp_enqueue_scripts', 'epkb_load_public_resources' );

function epkb_load_public_resources_now() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-public-styles', Echo_Knowledge_Base::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-public-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-public-scripts', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred (16)', 'echo-knowledge-base' ),
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved.', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'unknown error (17)', 'echo-knowledge-base' ),
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'reduce_name_size'      => esc_html__( 'Warning: Please reduce your name size. Tab will only show first 25 characters', 'echo-knowledge-base' ),
	));
}

/**
 * Only used for KB Configuration page
 */
function epkb_kb_config_load_public_css() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-public-styles', Echo_Knowledge_Base::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

	$kb_id = EPKB_KB_Handler::get_current_kb_id();
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
	if ( is_wp_error( $kb_config ) ) {
		return;
	}

	echo epkb_frontend_kb_theme_styles_now( $kb_config );
}

/**
 * ADMIN-PLUGIN MENU PAGES (Plugin settings, reports, lists etc.)
 */
function epkb_load_admin_plugin_pages_resources() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_enqueue_style( 'wp-color-picker' ); //Color picker

	wp_enqueue_script( 'epkb-admin-plugin-pages-scripts', Echo_Knowledge_Base::$plugin_url . 'js/admin-plugin-pages' . $suffix . '.js',
					array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core','jquery-effects-bounce', 'jquery-ui-sortable'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-plugin-pages-scripts', 'epkb_vars', array(
					'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
					'error_occurred'        => esc_html__( 'Error occurred (11)', 'echo-knowledge-base' ),
					'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (12).', 'echo-knowledge-base' ),
					'unknown_error'         => esc_html__( 'unknown error (13)', 'echo-knowledge-base' ),
					'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
					'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
					'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
					'sending_feedback'      => esc_html__('Sending feedback ...', 'echo-knowledge-base' ),
					'changing_debug'        => esc_html__('Changing debug ...', 'echo-knowledge-base' ),
					'help_text_coming'      => esc_html__('Help text is coming soon.', 'echo-knowledge-base' )
				));

	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );
}

function epkb_load_admin_kb_config_script() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'epkb-admin-kb-config-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-config-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-config-script', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred (14)', 'echo-knowledge-base' ),
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved.', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'unknown error (15)', 'echo-knowledge-base' ),
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'reduce_name_size'      => esc_html__( 'Warning: Please reduce your name size. Tab will only show first 25 characters', 'echo-knowledge-base' ),
		'archive_page'          => esc_html__( 'Archive Page configuration is available only for KB Template. Switch on KB Template to continue.', 'echo-knowledge-base' ),
		'updating_preview'      => esc_html__( 'Updating page preview ...', 'echo-knowledge-base' ),
		'changing_config'       => esc_html__('Changing to selected configuration...', 'echo-knowledge-base' ),
		'switching_article_seq' => esc_html__('Switching article sequence ...', 'echo-knowledge-base' ),
		'preview'               => esc_html__('Preview', 'echo-knowledge-base' )
	));
}

/**
 * Add style for current KB theme
 */
function epkb_frontend_kb_theme_styles() {

	global $eckb_kb_id;

	$kb_id = empty($eckb_kb_id) ? EPKB_KB_Handler::get_kb_id_from_kb_main_shortcode() : $eckb_kb_id;
	if ( empty( $kb_id ) ) {
		return;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
	if ( is_wp_error( $kb_config ) ) {
		$kb_config = EPKB_KB_Config_Specs::get_default_kb_config( $kb_id );
	}

	echo epkb_frontend_kb_theme_styles_now( $kb_config );
}
add_action( 'wp_head', 'epkb_frontend_kb_theme_styles' );

/**
 * Certain styles need to be inserted in the header.
 *
 * @param $kb_config
 * @return string
 */
function epkb_frontend_kb_theme_styles_now( $kb_config ) {

	global $eckb_is_kb_main_page;

	$is_kb_main_page = ! empty($eckb_is_kb_main_page);

	// get any style from add-ons
	$add_on_output = apply_filters( 'eckb_frontend_kb_theme_style', '', $kb_config['id'], $is_kb_main_page );
	if ( empty($add_on_output) || ! is_string($add_on_output) )  {
		$add_on_output = '';
	}

	$output = '<style type="text/css" id="epkb-advanced-style">
		/* KB Core 
		-----------------------------------------------------------------------*/
		#epkb-content-container .epkb-nav-tabs .active:after {
			border-top-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active {
			background-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active .epkb-category-level-1,
		#epkb-content-container .epkb-nav-tabs .active p {
			color: ' . $kb_config['tab_nav_active_font_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active:before {
			border-top-color: ' . $kb_config['tab_nav_border_color'] . '!important
		}		
	';

	$output .= $add_on_output;

	$output .= '</style>';

	return $output;
}
