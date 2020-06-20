<?php

/**  Register JS and CSS files  */

/**
 * FRONT-END pages using our plugin features
 */
function epkb_load_public_resources() {

    global $eckb_kb_id;

    // if this is not KB Main Page or Article Page then do not load public resources or is a Category Archive page
    if ( empty($eckb_kb_id) ) {
        return;
    }

	epkb_load_public_resources_now();
}
add_action( 'wp_enqueue_scripts', 'epkb_load_public_resources' );

/**
 * FRONT-END pages using our plugin features
 */
function epkb_load_public_resources_now() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-public-styles', Echo_Knowledge_Base::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-public-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-materialize', Echo_Knowledge_Base::$plugin_url . 'js/vendor/materialize' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-public-scripts', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred (16)', 'echo-knowledge-base' ),
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (6).', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'unknown error (17)', 'echo-knowledge-base' ),
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'reduce_name_size'      => esc_html__( 'Warning: Please reduce your name size. Tab will only show first 25 characters', 'echo-knowledge-base' ),
		'load_template'         => esc_html__('Loading Template...', 'echo-knowledge-base' )
	));
}

/**
 * BACK-END: KB Config page needs front-page CSS resources
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
	
	global $pagenow;
	
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
					'help_text_coming'      => esc_html__('Help text is coming soon.', 'echo-knowledge-base' ),
					'load_template'         => esc_html__('Loading Template...', 'echo-knowledge-base' )
				));
	
	// used by WordPress color picker  ( wpColorPicker() )
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n',
			array(
				'clear'            => __( 'Reset', 'echo-knowledge-base' ),
				'clearAriaLabel'   =>__( 'Reset color', 'echo-knowledge-base' ),
				'defaultString'    => __( 'Default', 'echo-knowledge-base' ),
				'defaultAriaLabel' => __( 'Select default color', 'echo-knowledge-base' ),
				'pick'             => '',
				'defaultLabel'     => __( 'Color value', 'echo-knowledge-base' ),
			));
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );

	// add for Category icon upload
	if ( $pagenow == 'term.php' || $pagenow == 'edit-tags.php' ) {
		wp_enqueue_media();
	}
}

function epkb_load_admin_kb_config_script() {
	
	global $pagenow;
	
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'epkb-admin-kb-config-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-config-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-config-script', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred (14)', 'echo-knowledge-base' ),
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (5).', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'unknown error (15)', 'echo-knowledge-base' ),
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'reduce_name_size'      => esc_html__( 'Warning: Please reduce your name size. Tab will only show first 25 characters', 'echo-knowledge-base' ),
		'archive_page'          => esc_html__( 'Archive Page configuration is available only for KB Template. Switch on KB Template to continue.', 'echo-knowledge-base' ),
		'updating_preview'      => esc_html__( 'Updating page preview ...', 'echo-knowledge-base' ),
		'changing_config'       => esc_html__('Changing to selected configuration...', 'echo-knowledge-base' ),
		'switching_article_seq' => esc_html__('Switching article sequence ...', 'echo-knowledge-base' ),
		'preview'               => esc_html__('Preview', 'echo-knowledge-base' ),
		'load_template'         => esc_html__('Loading Template...', 'echo-knowledge-base' )
	));

	wp_enqueue_script( 'epkb-admin-kb-wizard-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-wizard-script' . $suffix . '.js',
	
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce'), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-materialize', Echo_Knowledge_Base::$plugin_url . 'js/vendor/materialize' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-wizard-script', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred (14)', 'echo-knowledge-base' ),
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (5).', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'unknown error (15)', 'echo-knowledge-base' ),
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'load_template'         => esc_html__('Loading Preview...', 'echo-knowledge-base' ),
		'wizard_help_images_path' => Echo_Knowledge_Base::$plugin_url . 'img/',
		'asea_wizard_help_images_path' => class_exists( 'Echo_Advanced_Search' ) && ! empty(Echo_Advanced_Search::$plugin_url) ? Echo_Advanced_Search::$plugin_url . 'img/' : '',
		'elay_wizard_help_images_path' => class_exists( 'Echo_Elegant_Layouts' ) && ! empty(Echo_Elegant_Layouts::$plugin_url) ? Echo_Elegant_Layouts::$plugin_url . 'img/' : '',
		'eprf_wizard_help_images_path' => class_exists( 'Echo_Article_Rating_And_Feedback' ) && ! empty(Echo_Article_Rating_And_Feedback::$plugin_url) ? Echo_Article_Rating_And_Feedback::$plugin_url . 'img/' : ''
	));
	
	if ( $pagenow == 'edit.php' && isset( $_GET['wizard-text-on'] ) && class_exists( 'Echo_Elegant_Layouts' ) ) {
		wp_enqueue_editor();
	}
	
	add_filter('admin_body_class', 'epkb_admin_wizard_body_class' );
}

// remove wordpress strings on certain pages
function epkb_admin_wizard_body_class( $classes ) {
	// Note: Add a leading space and a trailing space.
	$classes .= ' epkb-configuration-page ';
	return $classes;
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

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

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

/**
 * Load TOC classes to counter theme issues
 * @param $classes
 * @return array
 */
function epkb_front_end_body_classes( $classes ) {
	global $eckb_kb_id;

	// load only on article pages
	if ( empty($eckb_kb_id) )  {
		return $classes;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );

	// load only if TOC is active
	if ( 'on' != $kb_config['article_toc_enable'] ) {
		return $classes;
	}

	// get current post
	$post = isset($GLOBALS['post']) ? $GLOBALS['post'] : '';
	if ( empty($post) || ! $post instanceof WP_Post ) {
		return $classes;
	}

	// is this KB Main Page ?
	$eckb_is_kb_main_page = false;
	$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs( true );
	foreach ( $all_kb_configs as $one_kb_config ) {
		if ( ! empty($one_kb_config['kb_main_pages']) && is_array($one_kb_config['kb_main_pages']) &&
		     in_array($post->ID, array_keys($one_kb_config['kb_main_pages']) ) ) {
			$eckb_is_kb_main_page = true;
			break;  // found matching KB Main Page
		}
	}

	if ( $eckb_is_kb_main_page ) {
		return $classes;
	}

	$classes[] = 'eckb-front-end-body';

	return $classes;

}
add_filter( 'body_class','epkb_front_end_body_classes' );

// load style for Admin Article Page
function epkb_load_admin_article_page_styles() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-article-page' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
}

/**
 * Register KB areas for widgets to be added to
 */
function epkb_register_kb_sidebar() {

	// add KB sidebar area
	register_sidebar( array(
		'name'          => __('Echo KB Articles Sidebar', 'echo-knowledge-base'),
		'id'            => 'eckb_articles_sidebar',
		'before_widget' => '<div id="eckb-%1$s" class="eckb-article-widget-sidebar-body__widget">',
		'after_widget'  => '</div> <!-- end Widget -->',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	) );
}

if ( isset(Echo_Knowledge_Base::$version) && version_compare(Echo_Knowledge_Base::$version, '6.4.0', '>=') ) {
	add_action( 'widgets_init', 'epkb_register_kb_sidebar' );
}

/**
 * Add KB filters for other plugins to use
 */
add_filter( 'kb_core/kb_config/get_kb_configs', function( $kb_config ) {
	return epkb_get_instance()->kb_config_obj->get_kb_configs();
} );
