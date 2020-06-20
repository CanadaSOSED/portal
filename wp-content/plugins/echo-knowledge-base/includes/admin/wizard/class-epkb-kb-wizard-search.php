<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Search {

	var $kb_config = array();
	/** @var  EPKB_KB_Config_Elements */
	var $form;
	var $feature_specs = array();
	/** @var EPKB_HTML_Elements */
	var $html;
	var $kb_id;
	var $show_article_step = true;

	function __construct() {

		add_action( 'epkb-wizard-search-main-page-feature-selection-container', array( $this, 'main_page_search_inputs' ) );
		add_action( 'epkb-wizard-search-article-page-feature-selection-container', array( $this, 'article_page_search_inputs' ) );

		$_POST['epkb-wizard-demo-data'] = true;
	}

	/**
	 * Show Wizard page
	 * @param $kb_config
	 */
	public function display_kb_wizard( $kb_config ) {

		$this->kb_config              = $kb_config;
		$this->kb_id                  = $this->kb_config['id'];
		$this->feature_specs          = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
		$this->form                   = new EPKB_KB_Config_Elements();
		$this->html                   = new EPKB_HTML_Elements();

		// assume no search configuration on the Article Page STEP
		$this->show_article_step = false;

		// Advanced Search overwrites everything else
		if ( EPKB_Utilities::is_advanced_search_enabled( $kb_config )  ) {
			if ( in_array( $this->kb_config['kb_main_page_layout'], array( 'Grid', 'Sidebar' ) ) ) {
				$this->show_article_step = true;
			}
		// Elegant Layout overwrites basic search
		} else if (  EPKB_Utilities::is_elegant_layouts_enabled() ) {
			if ( in_array( $this->kb_config['kb_main_page_layout'], array( 'Grid' ) ) ) {
				$this->show_article_step = true;
			} else if ( in_array( $this->kb_config['kb_main_page_layout'], array( 'Basic', 'Tabs' ) ) && EPKB_Articles_Setup::is_article_structure_v2( $kb_config ) && $kb_config['kb_main_page_layout'] != 'Categories' ) {
				$this->show_article_step = true;
			}
		}

		$this->show_article_step = apply_filters( 'epkb_wizard_search_show_article_step_filter', $this->show_article_step, $this->kb_config );

		// core handles only default KB
		if ( $this->kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			echo '<div class="epkb-wizard-error-note">' . __('Ensure that Multiple KB add-on is active and refresh this page. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}       ?>
		
		<div class="eckb-wizard-search" id="epkb-config-wizard-content">
			<div class="epkb-config-wizard-inner">

				<!------- Wizard Header ------------>
				<div class="epkb-wizard-header">
					<div class="epkb-wizard-header__info">
						<h1 class="epkb-wizard-header__info__title">
							<?php _e( 'Search Wizard', 'echo-knowledge-base'); ?>
						</h1>
						<span class="epkb-wizard-header__info__current-kb">							<?php
							$kb_name = $this->kb_config['kb_name'];
							echo __( 'for', 'echo-knowledge-base' ) . ' ' . '<span id="epkb_current_kb_name" class="epkb-wizard-header__info__current-kb__name">' . esc_html( $kb_name ) . '</span>';  ?>
						</span>
					</div>
					<div class="epkb-wizard-button-link epkb-wizard-header__exit-wizard">
						<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . '&epkb-wizard-tab' ) ); ?>&page=epkb-kb-configuration">
							<?php _e( 'Exit Wizard', 'echo-knowledge-base' ); ?>
						</a>
						<div class="epkb-wizard-header__exit-wizard__label">
							<input type="checkbox" data-save_exit="<?php _e( 'Save and Exit', 'echo-knowledge-base' ); ?>" data-exit="<?php _e( 'Exit Wizard', 'echo-knowledge-base' ); ?>">
							<span><?php _e( 'Save before exit', 'echo-knowledge-base' ); ?></span>
						</div>
					</div>
				</div>

				<!------- Wizard Status Bar ------->
				<div class="epkb-wizard-status-bar">
					<ul>
						<?php if ( $this->show_article_step ) { ?>
							<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Main Page Search', 'echo-knowledge-base'); ?></li>
							<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Article Page Search', 'echo-knowledge-base'); ?></li>
							<li id="epkb-wsb-step-3" class="epkb-wsb-step"><?php _e( 'Finish', 'echo-knowledge-base'); ?></li>
						<?php } else { ?>
							<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Main Page Search', 'echo-knowledge-base'); ?></li>
							<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Finish', 'echo-knowledge-base'); ?></li>
						<?php } ?>
					</ul>
				</div>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content"><?php 
					EPKB_KB_Wizard::show_loader_html();
					$this->main_page_search(); 
					if ( $this->show_article_step ) { 
						$this->article_page_search();
					} 
					$this->wizard_step_finish(); ?>
				</div>

				<!------- Wizard Footer ---------->
				<div class="epkb-wizard-footer">
					<?php $this->wizard_buttons(); ?>
				</div>
				
				<div id='epkb-ajax-in-progress' style="display:none;">
					<?php esc_html__( 'Saving configuration', 'echo-knowledge-base' ); ?> <img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
				</div>
				<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo $this->kb_id; ?>"/>
				<input type="hidden" id="epkb_wizard_show_article_step" name="epkb_wizard_show_article_step" value="<?php echo $this->show_article_step ? 1 : 0; ?>"/>
				<input type="hidden" id="eckb_current_theme_values" value="<?php echo EPKB_KB_Wizard_Themes::get_theme_data( $this->kb_config ); ?>">

				<div class="eckb-bottom-notice-message"></div>
			</div>
		</div> <?php
	}

	// Wizard: Step 1 - Main Page
	private function main_page_search() {    
		$panel_class = apply_filters('search_wizard_main_page_classes', '');	?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active <?php echo $panel_class; ?>">
			<?php do_action('search_wizard_main_page_before_preview_action'); ?>
			<div class="epkb-wizard-search-main-page-preview eckb-wizard-help">
				<div class="eckb-wizard-help__image"></div>	<?php
				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_kb_main_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-search-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-search-main-page-feature-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}

	// Wizard: Step 2 - Article Page
	private function article_page_search() {		?>

		<div id="epkb-wsb-step-2-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-search-article-page-preview eckb-wizard-help">
				<div class="eckb-wizard-help__image"></div>	<?php
				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_article_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-search-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-search-article-page-feature-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}

	// Wizard: Step 5 - Finish
	private function wizard_step_finish() {    
		if ( $this->show_article_step ) {
			$i = 3;
		} else {
			$i = 2;
		}	?>

		<div id="epkb-wsb-step-<?php echo $i; ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-3" >
			<h2><?php _e( 'Final Step: Update Your Knowledge Base', 'echo-knowledge-base'); ?></h2>
			<p><?php _e( 'Click Apply to update your Knowledge Base configuration based on selection from previous Wizard screens.', 'echo-knowledge-base'); ?></p>
		</div>	<?php

		// display link to KB Main Page if any
		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );     ?>

		<div id="epkb-wsb-step-<?php echo $i+1; ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-5" style="display: none">
			<div class="epkb-wizard-row-1">
				<p><?php _e( 'See your KB on the front-end:', 'echo-knowledge-base' ); ?></p>
				<a id="epkb-kb-main-page-link" href="<?php echo empty($link_output) ? '' : $link_output; ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-search"><?php _e( 'View My Knowledge base', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon dashicons-before dashicons-welcome-learn-more"></span>
				</a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Create Categories from the Categories menu.', 'echo-knowledge-base' ); ?></p>
				<a href="<?php echo admin_url('edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_id ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id )); ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'Create Categories', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-book"></span></a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Create Articles from the Add New Article menu.', 'echo-knowledge-base' ); ?></p>
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id )) ); ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'Create Articles', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-file-text-o "></span>
				</a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Documentation for Knowledge Base and add-ons.', 'echo-knowledge-base' ); ?></p>
				<a href="https://www.echoknowledgebase.com/documentation/getting-started" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'KB Documentation', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-book"></span></a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Submit a technical support question.', 'echo-knowledge-base' ); ?></p>
				<a href="https://www.echoknowledgebase.com/contact-us/" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'Support', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-book"></span></a>
			</div>
		</div>			<?php
	}

	//Wizard: Previous / Next Buttons / Apply Buttons
	public function wizard_buttons() {      ?>

		<div class="epkb-wizard-button-container epkb-wizard-button-container--first-step">
			<div class="epkb-wizard-button-container__inner">
				<button value="0" id="epkb-wizard-button-prev" class="epkb-wizard-button epkb-wizard-button-prev">
					<span class="epkb-wizard-button-prev__icon epkbfa epkbfa-caret-left"></span>
					<span class="epkb-wizard-button-prev__text"><?php _e( 'Previous', 'echo-knowledge-base' ); ?></span>
				</button>
				<button value="2" id="epkb-wizard-button-next" class="epkb-wizard-button epkb-wizard-button-next">
					<span class="epkb-wizard-button-next__text"><?php _e( 'Next', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-button-next__icon epkbfa epkbfa-caret-right"></span>
				</button>
				<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply"  data-wizard-type="search"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

				<input type="hidden" id="_wpnonce_apply_wizard_changes" name="_wpnonce_apply_wizard_changes" value="<?php echo wp_create_nonce( "_wpnonce_apply_wizard_changes" ); ?>">
			</div>
			<div class="epkb-wizard-link epkb-wizard-button-container__support-wizard">
				<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank">
					<?php _e( 'Support', 'echo-knowledge-base' ); ?>
					<span class="epkbfa epkbfa-external-link"></span>
				</a>
			</div>
		</div>	<?php
	}

	/**
	 * Call all hooks for given Wizard section.
	 *
	 * @param $hook - both hook name and div id
	 * @param $args
	 */
	public function wizard_section( $hook, $args ) {
		do_action( $hook, $args );
	}

	/**
	 * Show Wizard page options for Main Page
	 *
	 * @param $args
	 */
	public function main_page_search_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();

		// SEARCH BOX
		$form->option_group_wizard( $feature_specs, array(
			'option-heading' => __( 'Search', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'         => array(
				'show_when' => array(
					'kb_main_page_layout' => 'Basic|Tabs|Categories'
				),
				'hide_when' => array(
					'advanced_search_mp_show_top_category' => 'on|off',  // true if Advanced Search is enabled
				)
			),
			'inputs' => array(
				'0' => $form->dropdown( $feature_specs['search_layout'] + array(
						'value' => $kb_config['search_layout'],
						'current' => $kb_config['search_layout'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-3',
						'input_class' => 'config-col-7',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'1' => $form->text( $feature_specs['search_box_input_width'] + array(
						'value' => $kb_config['search_box_input_width'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-2',
					) ),

				'2' => $form->text( $feature_specs['search_title_html_tag'] + array(
						'value'             => $kb_config['search_title_html_tag'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'preview' => 1
						)
					) ),
				'3' => $form->text( $feature_specs['search_title_font_size'] + array(
						'value'             => $kb_config['search_title_font_size'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'target_selector' => '.epkb-doc-search-container__title, #grid-elay-search-kb, .epkb-wizard-search-main-page-preview .elay-doc-search-title',
							'style_name' => 'font-size',
							'postfix' => 'px'
						)
					) ),
			)
		));

		// Elegant Layouts and Widgets SEARCH
		do_action( 'epkb_search_wizard_after_main_page', $kb_id );
	}

	/**
	 * Show Wizard page options for Article Page
	 *
	 * @param $args
	 *
	 * @noinspection PhpUnused
	 */
	public function article_page_search_inputs( $args ) {
		$kb_id = $args['id'];
		do_action( 'epkb_search_wizard_after_article_page', $kb_id );
	}

	/**
	 * THis configuration defines fields that are part of this wizard configuration related to search.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	// TODO remove advanced search and elegant layout fields
	public static $search_fields = array(

		// CORE MAIN PAGE
		'search_box_input_width',
		'search_title_html_tag',
		'search_title_font_size',
		'search_layout',

		// SIDEBAR SEARCH
		'sidebar_search_layout',
		'sidebar_search_box_collapse_mode',
		'sidebar_search_box_input_width',
		'sidebar_search_input_border_width',

		// GRID SEARCH
		'grid_search_layout',
		'grid_search_box_input_width',
		'grid_search_input_border_width',

		// Widgets
		'widg_search_preset_styles',
		
		// ADVANCED SEARCH - MAIN PAGE
		'advanced_search_mp_box_visibility',
		'advanced_search_mp_auto_complete_wait',
		'advanced_search_mp_results_list_size',
		'advanced_search_mp_results_page_size',
		'advanced_search_mp_show_top_category',
		'advanced_search_mp_title_font_size',
		'advanced_search_mp_title_font_weight',
		'advanced_search_mp_title_padding_bottom',
		'advanced_search_mp_box_font_width',
		
		'advanced_search_mp_box_input_width',
		'advanced_search_mp_input_border_width',
		'advanced_search_mp_input_box_radius',
		'advanced_search_mp_input_box_font_size',
		'advanced_search_mp_input_box_padding_top',
		'advanced_search_mp_input_box_padding_bottom',
		'advanced_search_mp_input_box_padding_left',
		'advanced_search_mp_input_box_padding_right',
		'advanced_search_mp_input_box_shadow_x_offset',
		'advanced_search_mp_input_box_shadow_y_offset',
		'advanced_search_mp_input_box_shadow_blur',
		'advanced_search_mp_input_box_shadow_spread',
		'advanced_search_mp_input_box_shadow_rgba',
		'advanced_search_mp_input_box_shadow_position_group',
		'advanced_search_mp_input_box_shadow_position_group',
		'advanced_search_mp_input_box_search_icon_placement',
		'advanced_search_mp_input_box_loading_icon_placement',
		'advanced_search_mp_background_image_url',
		'advanced_search_mp_background_image_position_x',
		'advanced_search_mp_background_image_position_y',
		'advanced_search_mp_background_pattern_image_url',
		'advanced_search_mp_background_pattern_image_position_x',
		'advanced_search_mp_background_pattern_image_position_y',
		'advanced_search_mp_background_pattern_image_opacity',
		'advanced_search_mp_background_gradient_degree',
		'advanced_search_mp_background_gradient_opacity',
		'advanced_search_mp_background_gradient_toggle',
		'advanced_search_mp_box_padding_top',
		'advanced_search_mp_box_padding_bottom',
		'advanced_search_mp_box_padding_left',
		'advanced_search_mp_box_padding_right',
		'advanced_search_mp_box_margin_top',
		'advanced_search_mp_box_margin_bottom',
		'advanced_search_mp_box_input_width',
		'advanced_search_mp_title_font_size',
		'advanced_search_mp_title_font_weight',
		'advanced_search_mp_title_padding_bottom',
		'advanced_search_mp_text_title_shadow_position_group',
		'advanced_search_mp_title_text_shadow_x_offset',
		'advanced_search_mp_title_text_shadow_y_offset',
		'advanced_search_mp_title_text_shadow_blur',
		'advanced_search_mp_title_text_shadow_toggle',
		'advanced_search_mp_title_tag',
		'advanced_search_mp_filter_category_level',
		'advanced_search_mp_filter_toggle',
		'advanced_search_mp_filter_dropdown_width',

		'advanced_search_mp_description_below_title_font_size',
		'advanced_search_mp_description_below_title_padding_top',
		'advanced_search_mp_description_below_title_padding_bottom',
		'advanced_search_mp_description_below_title_text_shadow_x_offset',
		'advanced_search_mp_description_below_title_text_shadow_y_offset',
		'advanced_search_mp_description_below_title_text_shadow_blur',
		'advanced_search_mp_description_below_title_text_shadow_toggle',
		'advanced_search_mp_description_below_input',
		'advanced_search_mp_description_below_input_font_size',
		'advanced_search_mp_description_below_input_padding_top',
		'advanced_search_mp_description_below_input_padding_bottom',
		'advanced_search_mp_search_results_article_font_size',
		'advanced_search_mp_box_results_style',
		'advanced_search_mp_title_font_color',
		'advanced_search_mp_link_font_color',
		'advanced_search_mp_background_color',
		'advanced_search_mp_text_input_background_color',
		'advanced_search_mp_text_input_border_color',
		'advanced_search_mp_btn_background_color',
		'advanced_search_mp_btn_border_color',
		'advanced_search_mp_background_gradient_from_color',
		'advanced_search_mp_background_gradient_to_color',
		'advanced_search_mp_title',
		'advanced_search_mp_description_below_title',
		'advanced_search_mp_description_below_title',
		'advanced_search_mp_box_hint',
		'advanced_search_mp_button_name',
		'advanced_search_mp_title_font_shadow_color',
		'advanced_search_mp_filter_box_font_color',
		'advanced_search_mp_filter_box_background_color',
		'advanced_search_mp_filter_indicator_text',	

		// ADVANCED SEARCH - ARTICLE PAGE
		'advanced_search_ap_box_visibility',
		'advanced_search_ap_auto_complete_wait',
		'advanced_search_ap_results_list_size',
		'advanced_search_ap_results_page_size',
		'advanced_search_ap_show_top_category',
		'advanced_search_ap_title_font_size',
		'advanced_search_ap_title_font_weight',
		'advanced_search_ap_title_padding_bottom',
		'advanced_search_ap_text_title_shadow_position_group',
		'advanced_search_ap_title_text_shadow_x_offset',
		'advanced_search_ap_title_text_shadow_y_offset',
		'advanced_search_ap_title_text_shadow_blur',
		'advanced_search_ap_title_text_shadow_toggle',
		'advanced_search_ap_title_tag',
		'advanced_search_ap_filter_category_level',
		'advanced_search_ap_filter_toggle',
		'advanced_search_ap_filter_dropdown_width',
		'advanced_search_ap_box_font_width',
		
		'advanced_search_ap_box_input_width',
		'advanced_search_ap_input_border_width',
		'advanced_search_ap_input_box_radius',
		'advanced_search_ap_input_box_font_size',
		'advanced_search_ap_input_box_padding_top',
		'advanced_search_ap_input_box_padding_bottom',
		'advanced_search_ap_input_box_padding_left',
		'advanced_search_ap_input_box_padding_right',
		'advanced_search_ap_input_box_shadow_x_offset',
		'advanced_search_ap_input_box_shadow_y_offset',
		'advanced_search_ap_input_box_shadow_blur',
		'advanced_search_ap_input_box_shadow_spread',
		'advanced_search_ap_input_box_shadow_rgba',
		'advanced_search_ap_input_box_shadow_position_group',
		'advanced_search_ap_input_box_shadow_position_group',
		'advanced_search_ap_input_box_search_icon_placement',
		'advanced_search_ap_input_box_loading_icon_placement',
		'advanced_search_ap_background_image_url',
		'advanced_search_ap_background_image_position_x',
		'advanced_search_ap_background_image_position_y',
		'advanced_search_ap_background_pattern_image_url',
		'advanced_search_ap_background_pattern_image_position_x',
		'advanced_search_ap_background_pattern_image_position_y',
		'advanced_search_ap_background_pattern_image_opacity',
		'advanced_search_ap_background_gradient_degree',
		'advanced_search_ap_background_gradient_opacity',
		'advanced_search_ap_background_gradient_toggle',
		'advanced_search_ap_box_padding_top',
		'advanced_search_ap_box_padding_bottom',
		'advanced_search_ap_box_padding_left',
		'advanced_search_ap_box_padding_right',
		'advanced_search_ap_box_margin_top',
		'advanced_search_ap_box_margin_bottom',
		'advanced_search_ap_box_input_width',
		'advanced_search_ap_title_font_size',
		'advanced_search_ap_title_font_weight',
		'advanced_search_ap_title_padding_bottom',
		'advanced_search_ap_description_below_title_font_size',
		'advanced_search_ap_description_below_title_padding_top',
		'advanced_search_ap_description_below_title_padding_bottom',
		'advanced_search_ap_description_below_title_text_shadow_x_offset',
		'advanced_search_ap_description_below_title_text_shadow_y_offset',
		'advanced_search_ap_description_below_title_text_shadow_blur',
		'advanced_search_ap_description_below_title_text_shadow_toggle',
		'advanced_search_ap_description_below_input_font_size',
		'advanced_search_ap_description_below_input_padding_top',
		'advanced_search_ap_description_below_input_padding_bottom',
		'advanced_search_ap_search_results_article_font_size',
		'advanced_search_ap_box_results_style',
		'advanced_search_ap_title_font_color',
		'advanced_search_ap_link_font_color',
		'advanced_search_ap_background_color',
		'advanced_search_ap_text_input_background_color',
		'advanced_search_ap_text_input_border_color',
		'advanced_search_ap_btn_background_color',
		'advanced_search_ap_btn_border_color',
		'advanced_search_ap_background_gradient_from_color',
		'advanced_search_ap_background_gradient_to_color',
		'advanced_search_ap_title',
		'advanced_search_ap_description_below_title',
		'advanced_search_ap_description_below_title',
		'advanced_search_ap_box_hint',
		'advanced_search_ap_button_name',
		'advanced_search_ap_title_font_shadow_color',
		'advanced_search_ap_filter_box_font_color',
		'advanced_search_ap_filter_box_background_color',
		'advanced_search_ap_filter_indicator_text',
	);
}
