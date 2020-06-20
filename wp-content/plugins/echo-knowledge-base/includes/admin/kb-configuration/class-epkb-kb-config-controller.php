<?php

/**
 * Handle saving specific KB configuration.
 */
class EPKB_KB_Config_Controller {

	public function __construct() {
		add_action( 'wp_ajax_epkb_change_article_category_sequence', array( $this, 'change_article_category_sequence' ) );
		add_action( 'wp_ajax_nopriv_epkb_change_article_category_sequence', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_change_main_page_config_ajax', array( $this, 'change_main_page_config_ajax' ) );
		add_action( 'wp_ajax_nopriv_epkb_change_main_page_config_ajax', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_change_article_page_config_ajax', array( $this, 'change_article_page_config_ajax' ) );
		add_action( 'wp_ajax_nopriv_epkb_change_article_page_config_ajax', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_change_one_config_param_ajax', array( $this, 'change_one_configuration_param' ) );
		add_action( 'wp_ajax_nopriv_epkb_change_one_config_param_ajax', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_save_kb_config_changes', array( $this, 'save_kb_config_changes_in_db' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_kb_config_changes', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_save_kb_advanced_config_changes', array( $this, 'save_kb_advanced_config_changes_in_db' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_kb_advanced_config_changes', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_close_upgrade_message', array( $this, 'close_upgrade_header' ) );
	}

	/**
	 * Triggered when user changes article or category sequence drop down
	 */
	public function change_article_category_sequence() {

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce_epkb_save_kb_config'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epkb_save_kb_config'], '_wpnonce_epkb_save_kb_config' ) ) {
			$this->ajax_show_error_die( __( 'Sequence not changed. First refresh your page', 'echo-knowledge-base' ) );
		}

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			$this->ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		$page_type = isset($_POST['page_type']) ? EPKB_Utilities::sanitize_english_text( $_POST['page_type'] ) : '';
		if ( empty($_POST['page_type']) || ! in_array($_POST['page_type'], array('main_page', 'article_page') ) ) {
			$this->ajax_show_error_die( __( 'Parameter missing (02)', 'echo-knowledge-base' ) );
		}

		$chosen_main_page_layout = isset($_POST['epkb_chosen_main_page_layout']) ? sanitize_text_field( $_POST['epkb_chosen_main_page_layout'] ) : '';
		if ( $chosen_main_page_layout == 'Grid' && empty($_POST['articles_sequence_new_value']) ) {
			$_POST['articles_sequence_new_value'] = 'alphabetical-title';
		}

		// retrieve user input
		if ( empty( $_POST['categories_sequence_new_value'] ) || empty($_POST['articles_sequence_new_value']) ) {
			$this->ajax_show_error_die( __( 'Invalid parameters. Please refresh your page', 'echo-knowledge-base' ) );
		}

		// retrieve KB ID we are saving
		$kb_id = empty( $_POST['epkb_kb_id'] ) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['epkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "invalid kb id", $kb_id );
			$this->ajax_show_error_die( __( 'This page is outdated. Please refresh your browser', 'echo-knowledge-base' ) );
		}

		// retrieve current KB configuration
		$new_kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $new_kb_config ) ) {
			$this->ajax_show_error_die( __( 'Error occurred. Please refresh your browser and try again. (0)', 'echo-knowledge-base' ) );
		}

		// retrieve user input so we can refresh the KB Main or Article page
		$new_kb_config = $this->populate_kb_config_from_form( $kb_id, $new_kb_config );

		$articles_sequence_new_value = $new_kb_config['articles_display_sequence'];
		$categories_sequence_new_value = $new_kb_config['categories_display_sequence'];

		$config_seq = new EPKB_KB_Config_Sequence();
		$new_sequence = $config_seq->get_new_sequence();
		
		if ( $new_sequence === false ) {
			EPKB_Logging::add_log( "Could not retrieve new sequence" );
			$this->ajax_show_error_die( __( 'Error occurred. Could not retrieve new sequence.', 'echo-knowledge-base' ) );
		}

		// SEQUENCE ARTICLES and CATEGORIES
		$is_demo_data = isset($_POST['epkb_demo_kb']) && $_POST['epkb_demo_kb'] == "true";
		if ( $is_demo_data ) {
			$article_seq = array();
			$category_seq = array();
		} else {
			// get non-custom ordering regardless (default to by title if this IS custom order)
			$articles_order_method = $articles_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $articles_sequence_new_value;
			$articles_admin = new EPKB_Articles_Admin();
			$article_seq = $articles_admin->get_articles_sequence_non_custom( $kb_id, $articles_order_method );
			if ( $article_seq === false ) {
				$this->ajax_show_error_die( __( 'Error occurred. Please refresh your browser and try again. (1)', 'echo-knowledge-base' ) );
			}

			// ARTICLES: change to custom sequencde if necessary
			if ( $articles_sequence_new_value == 'user-sequenced' ) {
				$new_articles_ids_obj = $config_seq->update_articles_order( $kb_id, $new_sequence, new EPKB_Articles_Array( $article_seq ) );
				if ( $new_articles_ids_obj === false ) {
					$this->ajax_show_error_die( __( 'This page is outdated. Please refresh your browser (2)', 'echo-knowledge-base' ) );
				}
				$article_seq = $new_articles_ids_obj->ids_array;
				
			}

			// get non-custom ordering regardless (default to by title if this IS custom order)
			$categories_order_method = $categories_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $categories_sequence_new_value;
			$cat_admin = new EPKB_Categories_Admin();
			$category_seq = $cat_admin->get_categories_sequence_non_custom( $kb_id, $categories_order_method );
			if ( $category_seq === false ) {
				$this->ajax_show_error_die( __( 'Error occurred. Please refresh your browser and try again. (3)', 'echo-knowledge-base' ) );
			}

			// CATEGORIES: change to custom sequence if necessary
			if ( $categories_sequence_new_value == 'user-sequenced' ) {
				$new_cat_ids_obj = $config_seq->update_categories_order( $kb_id, $new_sequence, new EPKB_Categories_Array( $category_seq ) );
				if ( $new_cat_ids_obj === false ) {
					$this->ajax_show_error_die( __( 'This page is outdated. Please refresh your browser (BD06)', 'echo-knowledge-base' ) );
				}
				$category_seq = $new_cat_ids_obj->ids_array;
			}

			if ( ! $article_seq || ! $category_seq ) {
				$this->ajax_show_error_die( __( 'Error occurred. Please refresh your browser and try again. (4)', 'echo-knowledge-base' ) );
			}
		}

		$kb_config_page = new EPKB_KB_Config_Page( $new_kb_config );
		if ( $page_type == 'main_page' ) {
			$output = $kb_config_page->display_kb_main_page_layout_preview( false, $article_seq, $category_seq );
		} else {
			$output = $kb_config_page->display_article_page_layout_preview( false, $article_seq, $category_seq );
		}

		// add to output <script>
		$output = epkb_frontend_kb_theme_styles_now( $new_kb_config ) . $output;
		
		$msg = __( 'Sequence udpated but not saved.', 'echo-knowledge-base' );
		wp_die( json_encode( array( 'kb_page_output' => $output, 'message' => $this->get_kb_config_message_box( $msg, '', 'success') ) ) );
	}

	public function change_one_configuration_param() {

		// don't need nonce for preview

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			$this->ajax_show_error_die(__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ));
		}

		// retrieve KB ID we are saving
		$kb_id = empty($_POST['epkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['epkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "invalid kb id", $kb_id );
			$this->ajax_show_error_die(__( 'This page is outdated. Please refresh your browser', 'echo-knowledge-base' ));
		}

		// retrieve current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $orig_config ) ) {
			$this->ajax_show_error_die(__( 'Error occurred. Please refresh your browser and try again. (5)', 'echo-knowledge-base' ));
		}

		// retrieve user input
		$new_kb_config = $this->populate_kb_config_from_form( $kb_id, $orig_config );

		$kb_config_page = new EPKB_KB_Config_Page( $new_kb_config );

		// switch to Article Page layout if necessary
		if ( empty($_REQUEST['epkb_is_article_icon_active']) || $_REQUEST['epkb_is_article_icon_active'] != "true" ) {
			$output = $kb_config_page->display_kb_main_page_layout_preview( false );
		} else {
			$output = $kb_config_page->display_article_page_layout_preview();
		}

		wp_die( json_encode( array( 'kb_info_panel_output' => $output, 'message' => '' ) ) );
	}

	/**
	 * Triggered when user changes style, search box style or colors on MAIN PAGE
	 */
	public function change_main_page_config_ajax() {

		// don't need nonce for preview

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			$this->ajax_show_error_die(__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ));
		}

		// validate user input
		if ( empty($_POST['target_type']) || empty($_POST['target_name']) || empty($_POST['epkb_chosen_main_page_layout']) ) {
			$this->ajax_show_error_die(__( 'Invalid parameters. Please refresh your page', 'echo-knowledge-base' ));
		}

		// retrieve KB ID we are saving
		$kb_id = empty($_POST['epkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['epkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "invalid kb id", $kb_id );
			$this->ajax_show_error_die(__( 'This page is outdated. Please refresh your browser', 'echo-knowledge-base' ));
		}

		// retrieve current KB CORE configuration
		$current_kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $current_kb_config ) ) {
			$this->ajax_show_error_die(__( 'Error occurred. Please refresh your browser and try again. (6)', 'echo-knowledge-base' ));
		}

		$target_type = sanitize_text_field( $_POST['target_type'] );
		if ( ! in_array( $target_type, array('layout', 'style', 'search box style', 'colors', 'demo', 'advanced_search_box_style' ) ) ) {
			$this->ajax_show_error_die(__( 'Invalid type. Please refresh your page', 'echo-knowledge-base' ));
		}

		// retrieve user changes
		$kb_config = $this->populate_kb_config_from_form( $kb_id, $current_kb_config );

		// temporary update Main Page Layout to currently chosen layout
		$chosen_main_page_layout = sanitize_text_field( $_POST['epkb_chosen_main_page_layout'] );
		if ( ! in_array($chosen_main_page_layout, EPKB_KB_Config_Layouts::get_main_page_layout_names()) ) {
			$this->ajax_show_error_die(__( 'Invalid parameters. Please refresh your page. (6)', 'echo-knowledge-base' ));
		}
		$kb_config['kb_main_page_layout'] = $chosen_main_page_layout;

		$is_adv_search = EPKB_Utilities::is_advanced_search_enabled( $kb_config );
		$add_on_style_names = array();
		if ( $is_adv_search ) {
			$add_on_style_names = apply_filters( 'epkb_advanced_search_box_style_names', array() );
		}

		$target_name = sanitize_text_field( $_POST['target_name'] );
		if ( ( $target_type == 'layout' && ! in_array($target_name, EPKB_KB_Config_Layouts::get_main_page_layout_names()) ) ||
		     ( $target_type == 'style' && ! in_array($target_name, EPKB_KB_Config_Layouts::get_main_page_style_names( $kb_config )) ) ||
		     ( $target_type == 'search_box_style' && ! in_array($target_name, EPKB_KB_Config_Layouts::get_search_box_style_names( $kb_config )) ) ||
		     ( $target_type == 'colors' && ! in_array($target_name, EPKB_KB_Config_Layouts::get_colors_names()) ) ||
		     ( $target_type == 'advanced_search_box_style' && ! in_array($target_name, array_keys($add_on_style_names)))
		) {
			$this->ajax_show_error_die(__( 'Invalid parameters. Please refresh your page.', 'echo-knowledge-base' ));
		}

		// add filters for core/add-ons layouts and colors
		EPKB_KB_Config_Layouts::register_kb_config_hooks();

		// get given layout or color settings input
		$article_output = array('article_page_output' => '', 'article_widget' => '', 'article_style' => '', 'article_colors' => '',
                                'article_text' => '', 'article_general' => '', 'kb_mega_menu' => '', 'article_ordering_output' => '');
		$style_tab_output = 'NONE';
		$colors_tab_output = 'NONE';
        $kb_mega_menu = 'NONE';
		$message = '';
		$overview_page_output = '';
		$ordering_output = '';
		$text_output = '';
		$kb_config_page = new EPKB_KB_Config_Page( $kb_config );

		if ( $target_type == 'layout' ) {

			// get Ordering tab
			$ordering_output = $kb_config_page->get_main_page_order_form();

			// get Overview tab
			ob_start();
			EPKB_KB_Config_Overview::display_overview( $kb_config_page->kb_config, $kb_config_page->feature_specs, $kb_config_page->form );
			$overview_page_output = ob_get_clean();

			// update Text tab based on current layout (Sidebar has its own set)
			$text_output = $kb_config_page->get_main_page_text_form();

			// udpate Article Page to default layout, style and colors etc.
			$default_article_page_layout = in_array($chosen_main_page_layout, array( EPKB_KB_Config_Layouts::GRID_LAYOUT, EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT))
												? EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT : EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT;
			$kb_config['kb_article_page_layout'] = $default_article_page_layout;
            $kb_config_page = new EPKB_KB_Config_Page( $kb_config );

			// get Mega Menu (includes Article Page layout)
            ob_start();
            $kb_config_page->display_mega_menu();
            $kb_mega_menu = ob_get_clean();

            // prepare Article Page configuration
			$article_output = $this->generate_article_layout_and_sidebar( $target_type, $target_name, $kb_config, $default_article_page_layout );
		}

		// get new style
		if ( $target_type == 'layout' || $target_type == 'style' ) {
			$target_name = $target_type == 'layout' ? EPKB_KB_Config_Layouts::KB_DEFAULT_LAYOUT_STYLE : $target_name;
			$reset_style_config  = EPKB_KB_Config_Layouts::get_main_page_style_set( $chosen_main_page_layout, $target_name );
			$kb_config = array_merge($kb_config, $reset_style_config);
			$kb_config_page = new EPKB_KB_Config_Page( $kb_config );
			$style_tab_output = $kb_config_page->get_main_page_styles_form();
			$message .= __( 'Style was set to', 'echo-knowledge-base' ) . ' ' . ucfirst($target_name) . '. ' ;
		}

		// get new colors
		if ( $target_type == 'layout' ||  $target_type == 'colors' ) {
			$target_name = $target_type == 'layout' ? EPKB_KB_Config_Layouts::KB_DEFAULT_COLORS_STYLE : $target_name;
			$reset_color_config  = EPKB_KB_Config_Layouts::get_main_page_colors_set( $chosen_main_page_layout, $target_name );
			$kb_config = array_merge($kb_config, $reset_color_config);
			$kb_config_page = new EPKB_KB_Config_Page( $kb_config );
			$colors_tab_output = $kb_config_page->get_main_page_colors_form();
			$message = $this->get_color_change_msg( $target_name );
		}

		// get new advanced search style
		if ( $target_type == 'advanced_search_box_style' ) {  // TODO implement for core search too
			$reset_style_config  = EPKB_KB_Config_Layouts::get_advanced_search_style_set( 'mp', $target_name );
			$kb_config = array_merge($kb_config, $reset_style_config);
			$kb_config_page = new EPKB_KB_Config_Page( $kb_config );
			$colors_tab_output = $kb_config_page->get_main_page_colors_form();
			$text_output = $kb_config_page->get_main_page_text_form();
			$style_tab_output = $kb_config_page->get_main_page_styles_form();
			$advanced_search_style = empty($add_on_style_names[$target_name]) ? 'basic' : $add_on_style_names[$target_name];
			$message .= __( 'Advanced Search Style was set to', 'echo-knowledge-base' ) . ' ' . ucfirst($advanced_search_style) . '. ';
		}

		if ( empty($style_tab_output) || empty($colors_tab_output) ) {
			$this->ajax_show_error_die( $this->get_target_error_msg( $target_type ) );
		}

		// update Main Page layout
		$main_page_layout = $kb_config_page->display_kb_main_page_layout_preview( false );

		$message .= __( 'Configuration NOT saved (1). ', 'echo-knowledge-base' );

		// add to output <script>
		$main_page_layout = epkb_frontend_kb_theme_styles_now( $kb_config ) . $main_page_layout;

		// we are done here
		wp_die( json_encode( array( 'overview_page_output' => $overview_page_output, 'kb_mega_menu' => $kb_mega_menu, 'kb_main_page_output' => $main_page_layout,
		                            'ordering_output' => $ordering_output, 'style_tab_output' => $style_tab_output, 'colors_tab_output' => $colors_tab_output,
		                            'main_page_text_output' => $text_output, 'article_page_output' => $article_output['article_page_output'],
									'article_style_tab_output' => $article_output['article_style'], 'article_ordering_output' => $article_output['article_ordering_output'],
									'article_colors_tab_output' => $article_output['article_colors'], 'article_text_tab_output' => $article_output['article_text'],
		                            'article_page_layout' => $kb_config['kb_article_page_layout'], 'article_general_tab_output' => $article_output['article_general'],
		                            'message' => $this->get_kb_config_message_box( $message, '', 'attention' ) ) ) );
	}

	/**
	 * Triggered when user changes style, search box style or colors
	 */
	public function change_article_page_config_ajax() {

		// don't need nonce for preview

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			$this->ajax_show_error_die(__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ));
		}

		// validate user input
		if ( empty($_POST['target_type']) || empty($_POST['target_name']) || empty($_POST['epkb_chosen_article_page_layout']) ) {
			$this->ajax_show_error_die(__( 'Invalid parameters. Please refresh your page', 'echo-knowledge-base' ));
		}

		// retrieve KB ID we are saving
		$kb_id = empty($_POST['epkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['epkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "invalid kb id", $kb_id );
			$this->ajax_show_error_die(__( 'This page is outdated. Please refresh your browser', 'echo-knowledge-base' ));
		}

		// retrieve current KB configuration
		$current_kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $current_kb_config ) ) {
			$this->ajax_show_error_die(__( 'Error occurred. Please refresh your browser and try again. (7)', 'echo-knowledge-base' ));
		}

		// retrieve user changes
		$kb_config = $this->populate_kb_config_from_form( $kb_id, $current_kb_config );

		$target_type = sanitize_text_field( $_POST['target_type'] );
		if ( ! in_array( $target_type, array('layout', 'style', 'search box style', 'colors', 'demo', 'advanced_search_box_style' ) ) ) {
			$this->ajax_show_error_die(__( 'Invalid type. Please refresh your page', 'echo-knowledge-base' ));
		}

		$is_adv_search = EPKB_Utilities::is_advanced_search_enabled( $kb_config );
		$add_on_style_names = array();
		if ( $is_adv_search ) {
			$add_on_style_names = apply_filters( 'epkb_advanced_search_box_style_names', array() );
		}

		// validate change type
		$target_name = sanitize_text_field( $_POST['target_name'] );
		if ( ( $target_type == 'layout' && ! in_array($target_name, array_keys(EPKB_KB_Config_Layouts::get_article_page_layout_names()) ) ) ||
		     ( $target_type == 'style' && ! in_array($target_name, EPKB_KB_Config_Layouts::get_article_page_style_names( $kb_config )) ) ||
		     ( $target_type == 'search_box_style' && ! in_array($target_name, EPKB_KB_Config_Layouts::get_search_box_style_names( $kb_config )) ) ||
		     ( $target_type == 'colors' && ! in_array($target_name, EPKB_KB_Config_Layouts::get_colors_names()) ) ||
		     ( $target_type == 'advanced_search_box_style' && ! in_array($target_name, array_keys($add_on_style_names))) )
		{
			$this->ajax_show_error_die(__( 'Invalid parameters. Please refresh your page.', 'echo-knowledge-base' ));
		}

		// temporary update Main Page Layout to currently chosen layout
		$chosen_article_page_layout = sanitize_text_field( $_POST['epkb_chosen_article_page_layout'] );
		if ( ! in_array($chosen_article_page_layout, array_keys(EPKB_KB_Config_Layouts::get_article_page_layout_names()) ) ) {
			$this->ajax_show_error_die(__( 'Invalid parameters. Please refresh your page. (7)', 'echo-knowledge-base' ));
		}
		$kb_config['kb_article_page_layout'] = $chosen_article_page_layout;

		// add filters for core layouts and colors
		EPKB_KB_Config_Layouts::register_kb_config_hooks();

		$article_output = $this->generate_article_layout_and_sidebar( $target_type, $target_name, $kb_config, $chosen_article_page_layout );

		// we are done here
		wp_die( json_encode( array( 'article_page_output' => $article_output['article_page_output'], 'article_style_tab_output' => $article_output['article_style'],
		                            'article_colors_tab_output' => $article_output['article_colors'], 'article_text_tab_output' => $article_output['article_text'],
									'article_general_tab_output' => $article_output['article_general'], 'kb_mega_menu' => $article_output['kb_mega_menu'],
									'article_ordering_output' => $article_output['article_ordering_output'],
                                    'message' => $this->get_kb_config_message_box( $article_output['message'], '', 'attention' ) ) ) );
	}

	/**
	 * Prepare output for Article Page configuration
	 *
	 * @param $target_type
	 * @param $target_name
	 * @param $kb_config
	 * @param $chosen_article_page_layout
	 *
	 * @return array
	 */
	private function generate_article_layout_and_sidebar( $target_type, $target_name, $kb_config, $chosen_article_page_layout ) {

        // get given layout or color settings widgets
		$style_tab_output = 'NONE';
		$colors_tab_output = 'NONE';
		$text_tab_output = '';
		$general_tab_output = '';
		$message = '';
        $kb_mega_menu = 'NONE';
		$article_ordering_output = 'NONE';

		if ( $target_type == 'layout' ) {

            $kb_config['kb_article_page_layout'] = $chosen_article_page_layout;
            $kb_config_page = new EPKB_KB_Config_Page( $kb_config );
			$text_tab_output = $kb_config_page->get_article_page_text_form();

            // get Mega Menu (includes Article Page layout)
            ob_start();
            $kb_config_page->display_mega_menu();
            $kb_mega_menu = ob_get_clean();

			// get Ordering tab
			if ( $chosen_article_page_layout == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT ) {
				$article_ordering_output = $kb_config_page->get_article_page_order_form();
			}
		}

		// get new style
		if ( $target_type == 'layout' || $target_type == 'style' ) {
			$target_name = $target_type == 'layout' ? EPKB_KB_Config_Layouts::KB_DEFAULT_LAYOUT_STYLE : $target_name;
			$reset_style_config  = EPKB_KB_Config_Layouts::get_article_page_style_set( $chosen_article_page_layout, $target_name );
			$kb_config = array_merge($kb_config, $reset_style_config);
			$kb_config_page = new EPKB_KB_Config_Page( $kb_config );
			$style_tab_output = $kb_config_page->get_article_page_styles_form();
			$message .= __( 'Style was set to', 'echo-knowledge-base' ) . ' ' . ucfirst($target_name) . '. ' ;
		}

		// get new colors
		if ( $target_type == 'layout' ||  $target_type == 'colors' ) {
			$target_name = $target_type == 'layout' ? EPKB_KB_Config_Layouts::KB_DEFAULT_COLORS_STYLE : $target_name;
			$reset_color_config  = EPKB_KB_Config_Layouts::get_article_page_colors_set( $chosen_article_page_layout, $target_name );
			$kb_config = array_merge($kb_config, $reset_color_config);
			$kb_config_page = new EPKB_KB_Config_Page( $kb_config );
			$colors_tab_output = $kb_config_page->get_article_page_colors_form();
			$message = $this->get_color_change_msg( $target_name );
		}

		if ( empty($style_tab_output) || empty($colors_tab_output) ) {
			$this->ajax_show_error_die( $this->get_target_error_msg( $target_type ) . ' (22)' );
		}

		// get new advanced search style
		if ( $target_type == 'advanced_search_box_style' ) {  // TODO implement for core search too
			$reset_style_config  = EPKB_KB_Config_Layouts::get_advanced_search_style_set( 'ap', $target_name );
			$kb_config = array_merge($kb_config, $reset_style_config);
			$kb_config_page = new EPKB_KB_Config_Page( $kb_config );
			$colors_tab_output = $kb_config_page->get_article_page_colors_form( true );
			$text_tab_output = $kb_config_page->get_article_page_text_form( true );
			$style_tab_output = $kb_config_page->get_article_page_styles_form( true );
			$advanced_search_style = empty($add_on_style_names[$target_name]) ? 'basic' : $add_on_style_names[$target_name];
			$message .= __( 'Advanced Search Style was set to', 'echo-knowledge-base' ) . ' ' . ucfirst($advanced_search_style) . '. ';
		}

		// update Article Page layout
		$kb_config_page = new EPKB_KB_Config_Page( $kb_config );
		$article_page_output = $kb_config_page->display_article_page_layout_preview();

		$message .= __( 'Configuration NOT saved (2). ', 'echo-knowledge-base' );

		return array( 'article_page_output' => $article_page_output, 'article_text' => $text_tab_output, 'article_general' => $general_tab_output,
		              'article_ordering_output' => $article_ordering_output,
		              'article_style' => $style_tab_output, 'article_colors' => $colors_tab_output, 'message' => $message, 'kb_mega_menu' => $kb_mega_menu );
	}

	private function get_color_change_msg( $target_name ) {
		$option_name = substr($target_name, -1);
		$option_name = empty($option_name) ? '' : ', ' . __( 'Option', 'echo-knowledge-base' ) . $option_name;
		$theme_name = substr($target_name, 0, strlen($target_name) - 1);
		if ( $theme_name == 'demo_' ) {
			$theme_name = __( 'Installation Defaults', 'echo-knowledge-base' );
		}
		$color_theme_name = ( empty($theme_name) ? $target_name : $theme_name ) . $option_name;

		return __( 'Colors were set to ', 'echo-knowledge-base' ) . ucfirst($color_theme_name) . '. ';
	}

	/**
	 * Triggered when user submits changes to KB configuration
	 */
	public function save_kb_config_changes_in_db() {

		// verify that the request is authentic and check where it comes
		if ( empty( $_REQUEST['_wpnonce_epkb_save_kb_config'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epkb_save_kb_config'], '_wpnonce_epkb_save_kb_config' ) ) {
			$this->ajax_show_error_die(__( 'Settings not saved. First refresh your page', 'echo-knowledge-base' ));
		}

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			$this->ajax_show_error_die(__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ));
		}

		// retrieve KB ID we are saving
		$kb_id = empty($_POST['epkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['epkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "invalid kb id", $kb_id );
			$this->ajax_show_error_die(__( 'This page is outdated. Please refresh your browser', 'echo-knowledge-base' ));
		}

		// core handles only default KB
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {
			EPKB_Logging::add_log("received invalid kb_id when saving config. (x5)", $kb_id );
			$this->ajax_show_error_die(__( 'Ensure that Multiple KB add-on is active and refresh this page.', 'echo-knowledge-base' ));
			return;
		}

		// retrieve current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $orig_config ) ) {
			$this->ajax_show_error_die(__( 'Error occurred. Please refresh your browser and try again. (8)', 'echo-knowledge-base' ));
		}

		// retrieve user input
		$field_specs = self::retrieve_all_kb_specs( $kb_id );
		$form_fields = empty($_POST['form']) ? array() : EPKB_Utilities::retrieve_and_sanitize_form( $_POST['form'], $field_specs );
		if ( empty($form_fields) ) {
			EPKB_Logging::add_log("form fields missing");
			$this->ajax_show_error_die(__( 'Form fields missing. Please refresh your browser', 'echo-knowledge-base' ));
		} else if ( count($form_fields) < 100 ) {
			EPKB_Logging::add_log("Found KB configuration is incomplete", count($form_fields));
			$this->ajax_show_error_die(__( 'Some form fields are missing. Please refresh your browser and try again or contact support', 'echo-knowledge-base' ));
		}

		$input_handler = new EPKB_Input_Filter();
		$new_kb_config = $input_handler->retrieve_and_sanitize_form_fields( $form_fields, $field_specs, $orig_config );

		$result = apply_filters( 'epkb_kb_config_save_input_v2', '', $kb_id, $form_fields, $new_kb_config['kb_main_page_layout'] );
		if ( is_wp_error( $result ) ) {
			/* @var $result WP_Error */
			$message = $result->get_error_data();
			if ( empty($message) ) {
				$this->ajax_show_error_die( $result->get_error_message(), __( 'Could not save the new configuration (2)', 'echo-knowledge-base' ) );
			} else {
				$this->ajax_show_error_die( $this->generate_error_summary( $message ), __( 'Configuration NOT saved (3) due to following problems:', 'echo-knowledge-base' ) );
			}
		}

		// verify correct Article Page layout based on Main Page layout
		$article_page_layouts = EPKB_KB_Config_Layouts::get_article_page_layouts( $new_kb_config['kb_main_page_layout'] );
		if ( empty($article_page_layouts) ) {
			$new_kb_config['kb_article_page_layout'] = EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT;
		} else if ( ! in_array( $new_kb_config['kb_article_page_layout'], array_keys($article_page_layouts) ) ) {
			$article_pg_layouts = array_keys($article_page_layouts);
			$new_kb_config['kb_article_page_layout'] = $article_pg_layouts[0];
		}

        $new_kb_config['kb_articles_common_path'] = $this->process_common_path( $orig_config, $form_fields, $kb_id );

		// ensure kb id and WPML is preserved
		$new_kb_config['id'] = $kb_id;
		$new_kb_config['wpml_is_enabled'] = $orig_config['wpml_is_enabled'];

		// ensure no other KB has the same common article path
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $one_kb_config ) {
			if ( $new_kb_config['id'] != $one_kb_config['id'] && $new_kb_config['kb_articles_common_path'] == $one_kb_config['kb_articles_common_path'] ) {
				$this->ajax_show_error_die(__( 'Entered common path already exists in KB: ' . $one_kb_config['kb_name'], 'echo-knowledge-base' ));
			}
		}

		// if Sidebar layout chosen and TOC is on the left, set it to the right
		if ( $new_kb_config['kb_article_page_layout'] == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT && $new_kb_config['article_toc_position'] == 'left' ) {
			$new_kb_config['article_toc_position'] = 'right';
		}

		// sanitize and save configuration in the database. see EPKB_Settings_DB class
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_kb_config );
		if ( is_wp_error( $result ) ) {
			/* @var $result WP_Error */
			$message = $result->get_error_data();
			if ( empty($message) ) {
				$this->ajax_show_error_die( $result->get_error_message(), __( 'Could not save the new configuration (3)', 'echo-knowledge-base' ) );
			} else {
				$this->ajax_show_error_die( $this->generate_error_summary( $message ), __( 'Configuration NOT saved (4) due to following problems:', 'echo-knowledge-base' ) );
			}
		}
		
		// in case user changed article common path, flush the rules
		EPKB_Articles_CPT_Setup::register_custom_post_type( $new_kb_config, $kb_id );

		// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
		flush_rewrite_rules( false );
		update_option('epkb_flush_rewrite_rules', true);

		// update sequence of articles and categories
		$sync_sequence = new EPKB_KB_Config_Sequence();
		$sync_sequence->update_articles_sequence( $kb_id, $new_kb_config );
		$sync_sequence->update_categories_sequence( $kb_id, $new_kb_config );

		// some settings require page reload
		$reload = $this->is_page_reload( $orig_config, $new_kb_config, $field_specs);

		// we are done here
		$this->ajax_show_info_die( $reload ? __( 'Reload Settings saved. PAGE WILL RELOAD NOW.', 'echo-knowledge-base' ) : __( 'Settings saved', 'echo-knowledge-base' ) );
	}
	
	/**
	 * Triggered when user submits changes to Advanced KB configuration
	 */
	public function save_kb_advanced_config_changes_in_db() {

		// verify that the request is authentic and check where it comes
		if ( empty( $_REQUEST['_wpnonce_epkb_save_kb_config'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epkb_save_kb_config'], '_wpnonce_epkb_save_kb_config' ) ) {
			$this->ajax_show_error_die(__( 'Settings not saved. First refresh your page', 'echo-knowledge-base' ));
		}

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			$this->ajax_show_error_die(__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ));
		}

		// retrieve KB ID we are saving
		$kb_id = empty($_POST['epkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['epkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "invalid kb id", $kb_id );
			$this->ajax_show_error_die(__( 'This page is outdated. Please refresh your browser', 'echo-knowledge-base' ));
		}

		// core handles only default KB
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {
			EPKB_Logging::add_log("received invalid kb_id when saving config. (x5)", $kb_id );
			$this->ajax_show_error_die(__( 'Ensure that Multiple KB add-on is active and refresh this page.', 'echo-knowledge-base' ));
			return;
		}
		
		// retrieve user input
		$new_config_post = EPKB_Utilities::post('advanced_form', array());
		parse_str($new_config_post, $new_config_post);
		if ( empty($new_config_post) || count($new_config_post) < 10 ) {
			$this->ajax_show_error_die( __( 'Invalid post parameters (12). Please refresh your page', 'echo-knowledge-base' ) );
		}
		
		// retrieve current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $orig_config ) ) {
			$message = $orig_config->get_error_data();
			if ( empty($message) ) {
				$this->ajax_show_error_die( $orig_config->get_error_message(), __( 'Could not save the new advanced configuration (2)', 'echo-knowledge-base' ) );
			} else {
				$this->ajax_show_error_die( $this->generate_error_summary( $message ), __( 'Advanced configuration NOT saved (3) due to following problems:', 'echo-knowledge-base' ) );
			}
		}
		
		// get current KB configuration for addons (for blank KB, configuration will contain default values)
		$orig_config = apply_filters( 'epkb_all_wizards_get_current_config', $orig_config, $kb_id );
		$advanced_fields = EPKB_KB_Config_Advanced::$advanced_fields;
		
		// filter fields from Advanced Config to ensure we are saving only configuration that is applicable for this config
		$new_config = array();
		foreach($new_config_post as $field_name => $field_value) {
			if ( in_array($field_name, $advanced_fields) ) {
				$new_config[$field_name] = $field_value;
			} 
		}
		
		// merge with unused in this wizard config values from orig config
		foreach($orig_config as $field_name => $field_value) {
			if ( ! in_array($field_name, $advanced_fields) ) {
				$new_config[$field_name] = $orig_config[$field_name];
			} 
		}
		
		$is_blank_kb = EPKB_KB_Wizard::is_blank_KB( $kb_id );
		if ( is_wp_error($is_blank_kb) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not load KB config (3). Please contact us.', 'echo-knowledge-base' ) );
		}
		
		// KB should not be blank
		if ( $is_blank_kb ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not process KB config (3). Please contact us.', 'echo-knowledge-base' ) );
		}
		
		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];
		
		$field_specs = self::retrieve_all_kb_specs( $kb_id );
		$form_fields = EPKB_Utilities::retrieve_and_sanitize_form( $new_config, $field_specs );
		
		if ( empty($form_fields) ) {
			EPKB_Logging::add_log("form fields missing");
			$this->ajax_show_error_die(__( 'Form fields missing. Please refresh your browser', 'echo-knowledge-base' ));
		} else if ( count($form_fields) < 10 ) {
			EPKB_Logging::add_log("Found KB configuration is incomplete", count($form_fields));
			$this->ajax_show_error_die(__( 'Some form fields are missing. Please refresh your browser and try again or contact support', 'echo-knowledge-base' ));
		}

		$input_handler = new EPKB_Input_Filter();
		$new_kb_config = $input_handler->retrieve_and_sanitize_form_fields( $form_fields, $field_specs, $orig_config );
		
		// save add-ons configuration
		$form_fields['icons_not_saved'] = true;
		$result = apply_filters( 'epkb_kb_config_save_input_v2', '', $kb_id, $form_fields, $new_kb_config['kb_main_page_layout'] );
		if ( is_wp_error( $result ) ) {
			/* @var $result WP_Error */
			$message = $result->get_error_data();
			if ( empty($message) ) {
				$this->ajax_show_error_die( $result->get_error_message(), __( 'Could not save the new configuration (2)', 'echo-knowledge-base' ) );
			} else {
				$this->ajax_show_error_die( $this->generate_error_summary( $message ), __( 'Configuration NOT saved (3) due to following problems:', 'echo-knowledge-base' ) );
			}
		}

		// sanitize and save configuration in the database. see EPKB_Settings_DB class
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_kb_config );
		if ( is_wp_error( $result ) ) {
			/* @var $result WP_Error */
			$message = $result->get_error_data();
			if ( empty($message) ) {
				$this->ajax_show_error_die( $result->get_error_message(), __( 'Could not save the new configuration (3)', 'echo-knowledge-base' ) );
			} else {
				$this->ajax_show_error_die( $this->generate_error_summary( $message ), __( 'Configuration NOT saved (4) due to following problems:', 'echo-knowledge-base' ) );
			}
		}

		// we are done here
		$this->ajax_show_info_die( __( 'Settings saved', 'echo-knowledge-base' ) );
	}

    /**
     *
     * Setup common path for articles.
     *
     * @param $orig_config
     * @param $form_fields
     * @param $kb_id
     * @return string
     */
    private function process_common_path( $orig_config, $form_fields, $kb_id ) {

    	// 0. do not process if user did not change the path
	    if ( empty($form_fields['epkb_common_path_changed']) || $form_fields['epkb_common_path_changed'] !== 'yes' ) {
	    	return $orig_config['kb_articles_common_path'];
	    }

        // 1. set articles common path
        $kb_articles_common_path_rbtn =	empty($form_fields['kb_articles_common_path_rbtn']) ? '' : $form_fields['kb_articles_common_path_rbtn'];
        if ( $kb_articles_common_path_rbtn == 'path_custom_slug' ) {
            $custom_path = isset($form_fields['kb_articles_common_path']) ? $form_fields['kb_articles_common_path'] : '';
            $articles_common_path = empty($custom_path) ? $orig_config['kb_articles_common_path'] : $custom_path;
        } else {
            $articles_common_path = empty($kb_articles_common_path_rbtn) ? $orig_config['kb_articles_common_path'] : $kb_articles_common_path_rbtn;
        }

        // 2. ensure the common path is always set
        $articles_common_path = empty($articles_common_path) ? EPKB_KB_Handler::get_default_slug( $kb_id ) : $articles_common_path;

        // 3. respect page hierarchy if KB is a child page i.e. allow slash in article common path
        $pieces = explode('/', $articles_common_path);
        $articles_common_path_out = '';
        $first_piece = true;
        foreach( $pieces as $piece ) {
            $articles_common_path_out .= ( $first_piece ? '' : '/' ) . urldecode(sanitize_title_with_dashes( $piece, '', 'save' ));
            $first_piece = false;
        }

        return $articles_common_path_out;
    }

	private function populate_kb_config_from_form( $kb_id, $orig_kb_config ) {

		// get user input
		$feature_specs = self::retrieve_all_kb_specs( $kb_id );
		$form_fields = empty($_POST['form']) ? array() : EPKB_Utilities::retrieve_and_sanitize_form( $_POST['form'], $feature_specs );

		$input_handler = new EPKB_Input_Filter();
		$new_kb_config = $input_handler->retrieve_and_sanitize_form_fields( $form_fields, $feature_specs, $orig_kb_config );
		$new_kb_config = $this->retrieve_add_on_kb_config( $kb_id, $form_fields, $new_kb_config );

		$articles_display_sequence = empty($_POST['articles_sequence_new_value']) ? '' : EPKB_Utilities::sanitize_english_text( $_POST['articles_sequence_new_value'] );
		$new_kb_config['articles_display_sequence'] = empty($articles_display_sequence) ? $new_kb_config['articles_display_sequence'] : $articles_display_sequence;
		$categories_display_sequence = empty($_POST['categories_sequence_new_value']) ? '' : EPKB_Utilities::sanitize_english_text( $_POST['categories_sequence_new_value'] );
		$new_kb_config['categories_display_sequence'] = empty($categories_display_sequence) ? $new_kb_config['categories_display_sequence'] : $categories_display_sequence;

		return $new_kb_config;
	}

	/**
	 * Merge core KB config with add-ons KB specs
	 * @param $kb_id
	 * @return array
	 */
	public static function retrieve_all_kb_specs( $kb_id ) {

		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );

		// get add-on configuration from user changes if applicable
		$add_on_specs = apply_filters( 'epkb_add_on_config_specs', array() );
		if ( ! is_array($add_on_specs) || is_wp_error( $add_on_specs )) {
			
			$message = 
				"<div class='eckb-bottom-notice-message'>
					<div class='contents'>
						<span class='error'>
							<p> " . __( 'Could not change KB specs. (8)', 'echo-knowledge-base' ) . "</p>
						</span>
					</div>
					<div class='epkb-close-notice icon_close'></div>
				</div>";
				
			wp_die( json_encode( array( 'error' => true, 'message' => $message ) ) );
		}

		// merge core and add-on specs
		return array_merge( $add_on_specs, $feature_specs );
	}

	/**
	 * Merge core KB config with add-ons KB config
	 *
	 * @param $kb_id
	 * @param $form_fields
	 * @param $kb_config
	 * @return array
	 */
	private function retrieve_add_on_kb_config( $kb_id, $form_fields, $kb_config ) {
		// get add-on configuration from user changes if applicable
		$add_on_config = apply_filters( 'epkb_kb_config_get_add_on_input', array(), $kb_id, $form_fields );
		if ( ! is_array($add_on_config) || is_wp_error( $add_on_config )) {
			$this->ajax_show_error_die(__( 'Could not change KB configuration. (8)', 'echo-knowledge-base' ));
		}

		// merge core and add-on configuration
		return array_merge( $kb_config, $add_on_config );
	}

	private function is_page_reload( $orig_settings, $new_settings, $spec ) {

		$diff = EPKB_Utilities::diff_two_dimentional_arrays( $new_settings, $orig_settings );
		foreach( $diff as $key => $value ) {
			if ( ! empty($spec[$key]['reload']) ) {
				return true;
			}
		}

		return false;
	}

	private function generate_error_summary( $errors ) {

		$output = '';

		if ( empty( $errors ) || ! is_array( $errors )) {
			return $output . __( 'unknown error', 'echo-knowledge-base' ) . ' (344)';
		}

		$output .= '<ol>';
		foreach( $errors as $error ) {
			$output .= '<li>' . wp_kses( $error, array('strong' => array('style' => array()),'div' => array('style' => array()),'p' => array()) ) . '</li>';
		}
		$output .= '</ol>';

		return $output;
	}

	public function user_not_logged_in() {
		$this->ajax_show_error_die( '<p>' . __( 'You are not logged in. Refresh your page and log in', 'echo-knowledge-base' ) . '.</p>', __( 'Cannot save your changes', 'echo-knowledge-base' ) );
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param string $message
	 * @param string $title
	 * @param string $type
	 */
	private function ajax_show_info_die( $message, $title='', $type='success' ) {
		wp_die( json_encode( array( 'message' => $this->get_kb_config_message_box( $message, $title, $type) ) ) );
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param $message
	 * @param string $title
	 */
	private function ajax_show_error_die( $message, $title='' ) {
		wp_die( json_encode( array( 'error' => true, 'message' => $this->get_kb_config_message_box( $message, $title, 'error') ) ) );
	}

	/**
	 * Show info or error message to the user
	 *
	 * @param $message
	 * @param string $title
	 * @param string $type
	 *
	 * @return string
	 */
	private function get_kb_config_message_box( $message, $title='', $type='success' ) {
		$title = empty($title) ? '' : '<h4>' . $title . '</h4>';
		$message = empty($message) ? '' : $message;
		return
			"<div class='eckb-bottom-notice-message'>
				<div class='contents'>
					<span class='$type'>
						$title
						<p> " . wp_kses_post($message) . "</p>
					</span>
				</div>
				<div class='epkb-close-notice icon_close'></div>
			</div>";
	}

	/**
	 * Record that user closed the What's New message after plugin upgraded
	 */
	public function close_upgrade_header() {
		do_action('eckb_remove_upgrade_message');
	}

	private function get_target_error_msg( $target_type ) {
		if ( $target_type == 'layout' ) {
			$i18_message = __( 'Could not switch Layout.', 'echo-knowledge-base' );
		} else if ( $target_type == 'style' ) {
			$i18_message = __( 'Could not switch Style.', 'echo-knowledge-base' );
		} else if ( $target_type == 'colors' ) {
			$i18_message = __( 'Could not switch Colors.', 'echo-knowledge-base' );
		} else {
			$i18_message = __( 'Could not switch Search Box Style.', 'echo-knowledge-base' );
		}

		return $i18_message;
	}

}