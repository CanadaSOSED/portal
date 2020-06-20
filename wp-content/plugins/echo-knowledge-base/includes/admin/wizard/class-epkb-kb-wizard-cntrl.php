<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Cntrl {

	function __construct() {
		add_action( 'wp_ajax_epkb_apply_wizard_changes', array( $this, 'apply_wizard_changes' ) );
		add_action( 'wp_ajax_epkb_wizard_update_color_article_view', array( $this, 'wizard_update_color_article_view' ) );
		add_action( 'wp_ajax_epkb_wizard_update_order_view', array( $this, 'wizard_update_order_view' ) );
		add_action( 'wp_ajax_epkb_update_wizard_preview', array( $this, 'update_wizard_preview' ) );
		add_action( 'wp_ajax_epkb_hide_demo_content_alert', array( $this, 'hide_demo_content_alert' ) );
	}

	public function apply_wizard_changes() {

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce_apply_wizard_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_wizard_changes'], '_wpnonce_apply_wizard_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		// ensure that user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		// get current KB ID
		$wizard_kb_id = EPKB_Utilities::post('epkb_wizard_kb_id');
		if ( empty($wizard_kb_id) || ! EPKB_Utilities::is_positive_int( $wizard_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid wizard id parameter (2). Please refresh your page', 'echo-knowledge-base' ) );
		}

		// get Wizard type
		$wizard_type = EPKB_Utilities::post('wizard_type');
		if ( empty($wizard_type) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid wizard type parameter (22). Please refresh your page', 'echo-knowledge-base' ) );
		}

		// get new KB template related configuration
		$new_config_post = EPKB_Utilities::post('kb_config', array());
		if ( empty($new_config_post) || count($new_config_post) < 100 ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid post parameters (1). Please refresh your page', 'echo-knowledge-base' ) );
		}

		// get Wizard type specific filter
		switch( $wizard_type ) {
			case 'theme':
				$wizard_fields = apply_filters( 'epkb_kb_theme_fields_list', EPKB_KB_Wizard_Themes::$theme_fields );
				break;
			case 'text':
				$wizard_fields = apply_filters( 'epkb_kb_text_fields_list', EPKB_KB_Wizard_Text::$text_fields );
				break;
			case 'features':
				$wizard_fields = EPKB_KB_Wizard_Features::$feature_fields;
				break;
			case 'search':
				$wizard_fields = apply_filters( 'epkb_kb_search_fields_list', EPKB_KB_Wizard_Search::$search_fields );
				break;
			case 'ordering':
				$wizard_fields = EPKB_KB_Wizard_Ordering::$ordering_fields;
				break;
			case 'global':
				$wizard_fields = EPKB_KB_Wizard_Global::$global_fields;
				break;
			default:
				EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters - Wizard type. Please refresh your page', 'echo-knowledge-base' ) );
				return;
		}

		// filter fields from Wizard to ensure we are saving only configuration that is applicable for this Wizard
		$new_config = array();
		foreach($new_config_post as $field_name => $field_value) {
			if ( in_array($field_name, $wizard_fields) ) {
				$new_config[$field_name] = $field_value;
			}
		}

		// get current KB configuration (for blank KB, configuration will contain default values)
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $wizard_kb_id );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please contact us.', 'echo-knowledge-base' ) . $orig_config->get_error_message() . '(8)' );
		}

		// get current KB configuration (for blank KB, configuration will contain default values)
		$orig_config = apply_filters( 'epkb_all_wizards_get_current_config', $orig_config, $wizard_kb_id );
		if ( empty($orig_config) || count($orig_config) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters (111). Please refresh your page', 'echo-knowledge-base' ) );
		}

		// overwrite current KB configuration with new configuration from this Wizard
		$new_config = array_merge($orig_config, $new_config);

		$is_blank_kb = EPKB_KB_Wizard::is_blank_KB( $wizard_kb_id );
		if ( is_wp_error($is_blank_kb) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not load KB config. Please contact us.', 'echo-knowledge-base' ) . $is_blank_kb->get_error_message() . '(3)' );
		}

		// call Wizard type specific saving function
		switch( $wizard_type ) {
			case 'theme':
				$this->apply_theme_wizard_changes( $wizard_kb_id, $orig_config, $new_config, $is_blank_kb );
				break;
			case 'text':
			case 'search':
				$this->apply_non_default_wizard_changes( $orig_config, $new_config, $is_blank_kb );
				break;
			case 'features':
				$this->apply_features_wizard_changes( $orig_config, $new_config, $is_blank_kb );
				break;
			case 'ordering':
				$this->apply_ordering_wizard_changes( $orig_config, $new_config, $is_blank_kb );
				break;
			case 'global':
				$this->apply_global_wizard_changes( $orig_config, $new_config, $is_blank_kb );
				break;
			default:
				EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters - Wizard type. Please refresh your page', 'echo-knowledge-base' ) );
				return;
		}
	}

	/**
	 * Apply THEME WIZARD changes
	 *
	 * @param $wizard_kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @param $is_blank_kb
	 */
	private function apply_theme_wizard_changes( $wizard_kb_id, $orig_config, $new_config, $is_blank_kb ) {

		// get and sanitize KB name
		$kb_name = EPKB_Utilities::post('kb_name');
		$kb_name = empty($kb_name) ? '' : substr( $kb_name, 0, 50 );
		$kb_name = sanitize_text_field($kb_name);
		if ( empty($kb_name) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters (3). Please refresh your page', 'echo-knowledge-base' ) );
		}

		// if user selectes Image theme then change font icons to image icons
		if ( EPKB_Icons::is_theme_with_image_icons( $new_config ) ) {

			$categories_icons = EPKB_Utilities::get_kb_option( $wizard_kb_id, EPKB_Icons::CATEGORIES_ICONS, array(), true );
			$categories_icons_ids = array();
			foreach( $categories_icons as $term_id => $categories_icon ) {
				$categories_icons_ids[] = $term_id;
			}

			$kb_categories = EPKB_Categories_DB::get_top_level_categories( $wizard_kb_id );
			foreach ( $kb_categories as $kb_category ) {
				$term_id = $kb_category->term_id;
				if ( in_array( $term_id, $categories_icons_ids) ) {
					$categories_icons[$term_id]['type'] = 'image';
					$categories_icons[$term_id]['image_thumbnail_url'] = empty($categories_icons[$term_id]['image_thumbnail_url']) ? Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG: $categories_icons[$term_id]['image_thumbnail_url'];
				} else {
					$image_icon = array(
						'type' => 'image',
						'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
						'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
						'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG,
						'color' => '#000000'
					);
					$categories_icons[$term_id] = $image_icon;
				}
			}

			EPKB_Utilities::save_kb_option( $wizard_kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );

		// set each icon as font icon
		} else {
			$categories_icons = EPKB_Utilities::get_kb_option( $wizard_kb_id, EPKB_Icons::CATEGORIES_ICONS, array(), true );
			foreach( $categories_icons as $term_id => $categories_icon ) {
				$categories_icons[$term_id]['type'] = EPKB_Icons::DEFAULT_CATEGORY_TYPE;
			}
			EPKB_Utilities::save_kb_option( $wizard_kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );
		}

		// set sidebar priority
		$article_sidebar_component_priority = EPKB_Utilities::post('article_sidebar_component_priority');
		if ( empty($article_sidebar_component_priority) || ! array( $article_sidebar_component_priority ) ) {
			if ( $is_blank_kb ) {
				$article_sidebar_component_priority = array();
			} else {
				EPKB_Utilities::ajax_show_error_die( __( 'Invalid priority parameter (2). Please refresh your page', 'echo-knowledge-base' ) );
			}
		}

		// sanitize
		foreach( $article_sidebar_component_priority as $key => $value ) {
			if ( ! in_array($key, array_keys(EPKB_KB_Config_Specs::$sidebar_component_priority_defaults)) ) {
				unset($article_sidebar_component_priority[$key]);
			}
			$article_sidebar_component_priority[$key] = sanitize_text_field($value);
		}

		$article_sidebar_component_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $article_sidebar_component_priority );
		$new_config['article_sidebar_component_priority'] = $article_sidebar_component_priority;

		// set TOC position from v2 article settings
		if ( $new_config['article-structure-version'] == 'version-2'
		     && $new_config['article_sidebar_component_priority'] !== $orig_config['article_sidebar_component_priority'] ) {
			if ( $new_config['article_sidebar_component_priority']['toc_left'] != '0' ) {
				$new_config['article_toc_position'] = 'left';
			} else if ( $new_config['article_sidebar_component_priority']['toc_right'] != '0' ) {
				$new_config['article_toc_position'] = 'right';
			} else if ( $new_config['article_sidebar_component_priority']['toc_content'] != '0' ) {
				$new_config['article_toc_position'] = 'middle';
			}
		}

		// auto-determine whether we need sidebar or let user override it to be displayed
		$is_left_sidebar_on = EPKB_Articles_Setup::is_left_sidebar_on( $new_config, $new_config['article_sidebar_component_priority'] );
		$is_right_sidebar_on = EPKB_Articles_Setup::is_right_sidebar_on( $new_config, $new_config['article_sidebar_component_priority'] );
		$new_config['article-left-sidebar-desktop-width-v2'] = $is_left_sidebar_on ? '20' : '0';
		$new_config['article-left-sidebar-tablet-width-v2']  = $is_left_sidebar_on ? '20' : '0';
		$new_config['article-content-desktop-width-v2'] = $is_left_sidebar_on && $is_right_sidebar_on ? '60' : '80';
		$new_config['article-content-tablet-width-v2'] = $is_left_sidebar_on && $is_right_sidebar_on ? '60' : '80';
		$new_config['article-right-sidebar-desktop-width-v2'] = $is_right_sidebar_on ? '20' : '0';
		$new_config['article-right-sidebar-tablet-width-v2'] = $is_right_sidebar_on ? '20' : '0';

		// do we need to create a new KB content?
		if ( $is_blank_kb ) {

			// 1. save KB configuration based on Wizard changes
			$new_config['status'] = EPKB_KB_Status::BLANK;
			$orig_config = EPKB_KB_Config_Specs::get_default_kb_config( $wizard_kb_id );
			$orig_config['status'] = EPKB_KB_Status::BLANK;
			$update_kb_msg = $this->update_kb_configuration( $wizard_kb_id, $orig_config, $new_config );
			if ( ! empty($update_kb_msg) ) {
				EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Could not create KB. ', 'echo-knowledge-base' ) . $update_kb_msg . ' (34) ' . EPKB_Utilities::contact_us_for_support() );
			}

			// get KB slug
			$kb_slug = EPKB_Utilities::post('kb_slug');
			$kb_slug = empty($kb_slug) ? '' : substr( $kb_slug, 0, 100 );
			$kb_slug = sanitize_title($kb_slug);
			if ( $is_blank_kb && empty($kb_slug) ) {
				EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters (4). Please refresh your page', 'echo-knowledge-base' ) );
			}

			// 2. add sample content and Main Page
			$new_config = EPKB_KB_Handler::add_new_knowledge_base( $wizard_kb_id, $kb_name, $kb_slug );
			if ( is_wp_error($new_config) ) {
				EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Could not create KB. ', 'echo-knowledge-base' ) . $new_config->get_error_message() . ' (35) ' . EPKB_Utilities::contact_us_for_support() );
			}
			
		} else {

			// prevent new config to overwrite essential fields
			$new_config['id'] = $orig_config['id'];
			$new_config['status'] = $orig_config['status'];
			$new_config['kb_name'] = $kb_name;
			$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
			$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];

			// update KB and add-ons configuration
			$update_kb_msg = $this->update_kb_configuration( $wizard_kb_id, $orig_config, $new_config );
			if ( ! empty($update_kb_msg) ) {
				EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Could not create KB (36). ' . $update_kb_msg, 'echo-knowledge-base' ) . EPKB_Utilities::contact_us_for_support() );
			}
			
			// save priority
			epkb_get_instance()->kb_config_obj->set_value( $orig_config['id'], 'article_sidebar_component_priority', $article_sidebar_component_priority );
		}

		// add items to menus if needs
		$menu_ids = EPKB_Utilities::post( 'menu_ids', array(), false );
		if ( $menu_ids && ! empty($new_config['kb_main_pages']) ) {
			$kb_main_pages = $new_config['kb_main_pages'];
			foreach ( $menu_ids as $id ) {
				$itemData =  array(
					'menu-item-object-id'   => key($kb_main_pages),
					'menu-item-parent-id'   => 0,
					'menu-item-position'    => 99,
					'menu-item-object'      => 'page',
					'menu-item-type'        => 'post_type',
					'menu-item-status'      => 'publish'
				  );

				wp_update_nav_menu_item( $id, 0, $itemData );
			}
		}

		// in case user changed article common path, flush the rules
		EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

		// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
		flush_rewrite_rules( false );
		update_option('epkb_flush_rewrite_rules', true);

		$message = __('Configuration Saved', 'echo-knowledge-base');
		wp_die( json_encode( array( 'message' => $message, 'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Apply NON-DEFAULT WIZARD changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 * @param $is_blank_kb
	 */
	private function apply_non_default_wizard_changes( $orig_config, $new_config, $is_blank_kb ) {

		// KB should not be blank
		if ( $is_blank_kb ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not process KB config (3). Please contact us.', 'echo-knowledge-base' ) );
		}

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}

		$message = __('Configuration Saved', 'echo-knowledge-base');
		wp_die( json_encode( array( 'message' => $message, 'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}
	
	/**
	 * Apply FEATURES WIZARD changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 * @param $is_blank_kb
	 */
	private function apply_features_wizard_changes( $orig_config, $new_config, $is_blank_kb ) {

		// KB should not be blank
		if ( $is_blank_kb ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not process KB config (3). Please contact us.', 'echo-knowledge-base' ) );
		}

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];

		// set sidebar priority
		$article_sidebar_component_priority = EPKB_Utilities::post('article_sidebar_component_priority');
		if ( empty($article_sidebar_component_priority) || ! array( $article_sidebar_component_priority ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid priority parameter (2). Please refresh your page', 'echo-knowledge-base' ) );
		}

		// sanitize
		foreach( $article_sidebar_component_priority as $key => $value ) {
			if ( ! in_array($key, array_keys(EPKB_KB_Config_Specs::$sidebar_component_priority_defaults)) ) {
				unset($article_sidebar_component_priority[$key]);
			}
			$article_sidebar_component_priority[$key] = sanitize_text_field($value);
		}

		$article_sidebar_component_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $article_sidebar_component_priority );
		$new_config['article_sidebar_component_priority'] = $article_sidebar_component_priority;

		// set TOC position from v2 article settings 
		if ( $new_config['article-structure-version'] == 'version-2' 
			 && $new_config['article_sidebar_component_priority'] !== $orig_config['article_sidebar_component_priority'] ) {
			if ( $new_config['article_sidebar_component_priority']['toc_left'] != '0' ) {
				$new_config['article_toc_position'] = 'left';
			} else if ( $new_config['article_sidebar_component_priority']['toc_right'] != '0' ) {
				$new_config['article_toc_position'] = 'right';
			} else if ( $new_config['article_sidebar_component_priority']['toc_content'] != '0' ) {
				$new_config['article_toc_position'] = 'middle';
			}
		}

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}

		// save priority
		epkb_get_instance()->kb_config_obj->set_value( $orig_config['id'], 'article_sidebar_component_priority', $article_sidebar_component_priority );

		$message = __('Configuration Saved', 'echo-knowledge-base');
		wp_die( json_encode( array( 'message' => $message, 'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}
	
	/**
	 * Apply GLOBAL WIZARD changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 * @param $is_blank_kb
	 */
	private function apply_global_wizard_changes( $orig_config, $new_config, $is_blank_kb ) {

		// KB should not be blank
		if ( $is_blank_kb ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not process KB config (3). Please contact us.', 'echo-knowledge-base' ) );
		}

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];

		// ensure the common path is always set
		$articles_common_path = empty($new_config['kb_articles_common_path']) ? EPKB_KB_Handler::get_default_slug( $orig_config['id'] ) : $new_config['kb_articles_common_path'];

		// sanitize article path 
		$pieces = explode('/', $articles_common_path);
        $articles_common_path_out = '';
        $first_piece = true;
        foreach( $pieces as $piece ) {
            $articles_common_path_out .= ( $first_piece ? '' : '/' ) . urldecode(sanitize_title_with_dashes( $piece, '', 'save' ));
            $first_piece = false;
        }
		
		$new_config['kb_articles_common_path'] = $articles_common_path_out;
		
		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}

		// in case user changed article common path, flush the rules
		if ( $new_config['kb_articles_common_path'] != $orig_config['kb_articles_common_path'] || $new_config['categories_in_url_enabled'] != $orig_config['categories_in_url_enabled'] ) {
			EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

			// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
			flush_rewrite_rules( false );
			update_option('epkb_flush_rewrite_rules', true);
			
			EPKB_Admin_Notices::dismiss_long_notice( 'epkb_changed_slug' );
		}
		
		$message = __('Configuration Saved', 'echo-knowledge-base');
		wp_die( json_encode( array( 'message' => $message, 'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Apply ORDERING changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 * @param $is_blank_kb
	 */
	private function apply_ordering_wizard_changes( $orig_config, $new_config, $is_blank_kb ) {

		// KB should not be blank
		if ( $is_blank_kb ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not process KB config (3). Please contact us.', 'echo-knowledge-base' ) );
		}
		
		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];
		
		global $eckb_kb_id;
		$eckb_kb_id = $new_config['id'];
		
		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}
		
		// update sequence of articles and categories
		$sync_sequence = new EPKB_KB_Config_Sequence();
		
		$sync_sequence->update_articles_sequence( $orig_config['id'], $new_config );
		$sync_sequence->update_categories_sequence( $orig_config['id'], $new_config );

		$message = __('Configuration Saved', 'echo-knowledge-base');
		wp_die( json_encode( array( 'message' => $message, 'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Triggered when user submits changes to KB configuration
	 *
	 * @param $kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @return string
	 */
	 // TODO if future: refractor this function and the same in kb-config-controller
	public function update_kb_configuration( $kb_id, $orig_config, $new_config ) {

		// core handles only default KB
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {
			return __('Ensure that Multiple KB add-on is active and refresh this page', 'echo-knowledge-base');
		}

		// verify correct Article Page layout based on Main Page layout
		$article_page_layouts = EPKB_KB_Config_Layouts::get_article_page_layouts( $new_config['kb_main_page_layout'] );
		if ( empty($article_page_layouts) ) {
			$new_config['kb_article_page_layout'] = EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT;
		} else if ( ! in_array( $new_config['kb_article_page_layout'], array_keys($article_page_layouts) ) ) {
			$article_pg_layouts = array_keys($article_page_layouts);
			$new_config['kb_article_page_layout'] = $article_pg_layouts[0];
		}

		// sanitize all fields in POST message
		$field_specs = EPKB_KB_Config_Controller::retrieve_all_kb_specs( $kb_id );
		$form_fields = EPKB_Utilities::retrieve_and_sanitize_form( $new_config, $field_specs );
		if ( empty($form_fields) ) {
			EPKB_Logging::add_log("form fields missing");
			return __( 'Form fields missing. Please refresh your browser', 'echo-knowledge-base' );
		} else if ( count($form_fields) < 100 ) {
			return __( 'Some form fields are missing. Please refresh your browser and try again or contact support', 'echo-knowledge-base' );
		}

		// sanitize fields based on each field type
		$input_handler = new EPKB_Input_Filter();
		$new_kb_config = $input_handler->retrieve_and_sanitize_form_fields( $form_fields, $field_specs, $orig_config );

		// save add-ons configuration
		$form_fields['icons_not_saved'] = true;
		$result = apply_filters( 'epkb_kb_config_save_input_v2', '', $kb_id, $form_fields, $new_kb_config['kb_main_page_layout'] );
		if ( is_wp_error( $result ) ) {
			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(4)';
			} else {
				return __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' ) . '(4)';
			}
		}

		// ensure kb id is preserved
		$new_kb_config['id'] = $kb_id;

		// TODO for now save previous configuration
		EPKB_Utilities::save_kb_option( $kb_id, 'epkb_orignal_config', $orig_config, true );

		// save KB core configuration
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_kb_config );
		if ( is_wp_error( $result ) ) {

			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(3)';
			} else {
				return __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' );
			}
		}

		// we are done here
		return '';
	}

	/**
	 * Article theme layout has changed so update the article preview.
	 */
	public function wizard_update_color_article_view() {
		if ( empty( $_REQUEST['_wpnonce_apply_wizard_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_wizard_changes'], '_wpnonce_apply_wizard_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}
		
		// get new KB config
		$new_config_post = EPKB_Utilities::post('kb_config', array());
		if ( empty($new_config_post) || count($new_config_post) < 100 ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters. Please refresh your page', 'echo-knowledge-base' ) . ' (10)' );
		}

		$_POST['epkb-wizard-demo-data'] = true;
		$_GET['wizard-on'] = true;
		
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$new_config = array_merge($orig_config, $new_config_post);
		$handler = new EPKB_KB_Config_Page( $new_config );
		wp_die( json_encode( array( 'message' => $new_config, 'html' => $handler->display_article_page_layout_preview( false ) ) ) );
	}

	/**
	 * Based on user selection of article/category ordering, setup the nex step
	 */
	public function wizard_update_order_view() {
		
		if ( empty( $_REQUEST['_wpnonce_apply_wizard_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_wizard_changes'], '_wpnonce_apply_wizard_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}
		
		$sequence_settings = EPKB_Utilities::post('sequence_settings', array());
		$kb_id = EPKB_Utilities::post('kb_id', 0);
		if ( empty($sequence_settings) || empty($kb_id) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters (14). Please refresh your page', 'echo-knowledge-base' ) );
		}
		
		$_GET['wizard-on'] = true;
		
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		$new_kb_config = array_merge($orig_config, $sequence_settings);
		
		$articles_sequence_new_value = $new_kb_config['articles_display_sequence'];
		$categories_sequence_new_value = $new_kb_config['categories_display_sequence'];
		
		$articles_order_method = $articles_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $articles_sequence_new_value;
		
		$articles_admin = new EPKB_Articles_Admin();
		$article_seq = $articles_admin->get_articles_sequence_non_custom( $kb_id, $articles_order_method );
		if ( $article_seq === false ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please refresh your browser and try again. (1)', 'echo-knowledge-base' ) );
		}

		// ARTICLES: change to custom sequencde if necessary
		if ( $articles_sequence_new_value == 'user-sequenced' ) {
			$article_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
			if ( ! empty($article_seq_data) ) {
				$article_seq = $article_seq_data;
			}
		}

		// get non-custom ordering regardless (default to by title if this IS custom order)
		$categories_order_method = $categories_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $categories_sequence_new_value;
		$cat_admin = new EPKB_Categories_Admin();
		$category_seq = $cat_admin->get_categories_sequence_non_custom( $kb_id, $categories_order_method );
		if ( $category_seq === false ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please refresh your browser and try again. (3)', 'echo-knowledge-base' ) );
		}

		// CATEGORIES: change to custom sequence if necessary
		if ( $categories_sequence_new_value == 'user-sequenced' ) {
			$custom_categories_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
			if ( ! empty($custom_categories_data) ) {
				$category_seq = $custom_categories_data;
			}
		}

		if ( ! $article_seq || ! $category_seq ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please refresh your browser and try again. (4)', 'echo-knowledge-base' ) );
		}

		// ensure user can order articles and categories easily
		$new_kb_config['nof_articles_displayed'] = '200';
		$new_kb_config['sidebar_top_categories_collapsed'] = 'off';
		$new_kb_config['article_toc_title'] = '';
		unset($_POST['epkb-wizard-demo-data']);

		$handler = new EPKB_KB_Config_Page( $new_kb_config );
		if ( $new_kb_config['kb_main_page_layout'] == 'Grid' ) {
			// article page
			$html = $handler->display_article_page_layout_preview( false, $article_seq, $category_seq );
		} else {
			// main page
			$html = $handler->display_kb_main_page_layout_preview( false, $article_seq, $category_seq );
		}
		
		$message = '';
		if ( $sequence_settings['articles_display_sequence'] == 'user-sequenced' || $sequence_settings['categories_display_sequence'] == 'user-sequenced' ) {
			$message =  __( 'Drag & Drop Elements for Sorting', 'echo-knowledge-base' );
		}
		
		wp_die( json_encode( array( 'message' =>$message, 'html' => $html ) ) );
	}

	/**
	 * Search and Features Wizard show live preview for certain fields
	 */
	public function update_wizard_preview() {

		if ( empty( $_REQUEST['_wpnonce_apply_wizard_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_wizard_changes'], '_wpnonce_apply_wizard_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}
		
		// get new KB config
		$new_config_post = EPKB_Utilities::post('kb_config', array());
		if ( empty($new_config_post) || count($new_config_post) < 100 ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters (10). Please refresh your page', 'echo-knowledge-base' ) );
		}
		
		$_POST['epkb-wizard-demo-data'] = true;

		$_GET['wizard-on'] = true;
		
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$new_config = array_merge($orig_config, $new_config_post);
		
		$handler = new EPKB_KB_Config_Page( $new_config );
		
		switch ( EPKB_Utilities::post('wizard_screen', 'main_page') ) {
			case 'main_page': 
				$html = $handler->display_kb_main_page_layout_preview( false );
				break;
			case 'article_page':
				$html = $handler->display_article_page_layout_preview( false );
				break;
			case 'archive_page':
				$html = $handler->display_archive_page_layout_preview( false ); 
				break;
		}
		
		wp_die( json_encode( array( 
			'message' => __('Preview Updated', 'echo-knowledge-base'),
			'html' => $html
		) ) );
	}
	
	public function hide_demo_content_alert() {
		update_option( 'epkb_hide_demo_content_alert', 1 );
		
		wp_die( json_encode( array( 
			'message' => __('Alert was hidden', 'echo-knowledge-base'),
			) ) );
	}
	/**
	 * This function will change category icon to image or icon for default categories.
	 * @param $icon_type: icon|image|none
	 * @param $kb_id
	 */
	
	/* private function toggle_default_categories_icon_type( $icon_type = 'icon', $kb_config ) {
		// Get all categories
		$categories = EPKB_Categories_DB::get_all_categories( $taxonomy_name, $kb_config['id'] );
		
		if ( !$categories ) {
			return;
		}
		
		
	} */
}
