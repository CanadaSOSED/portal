<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Text {

	var $kb_config = array();
	/** @var  EPKB_KB_Config_Elements */
	var $form;
	var $feature_specs = array();
	/** @var EPKB_HTML_Elements */
	var $html;
	var $kb_id;

	function __construct() {

		add_action( 'epkb-wizard-text-main-page-text-selection-container', array( $this, 'main_page_text_inputs' ) );
		add_action( 'epkb-wizard-text-article-page-text-selection-container', array( $this, 'article_page_text_inputs' ) );
		add_action( 'epkb-wizard-text-archive-page-text-selection-container', array( $this, 'archive_page_text_inputs' ) );

		$_POST['epkb-wizard-demo-data'] = true;
	}

	/**
	 * Show Wizard page
	 * @param $kb_config
	 */
	public function display_kb_wizard( $kb_config ) {

		$this->kb_config              = array_merge($kb_config, EPKB_KB_Wizard_Color_Presets::get_template(13)); // 13: preset ID to see just shaded colors
		$this->kb_id                  = $this->kb_config['id'];
		$this->feature_specs          = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
		$this->form                   = new EPKB_KB_Config_Elements();
		$this->html                   = new EPKB_HTML_Elements();
		
		// core handles only default KB
		if ( $this->kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {
			echo '<div class="epkb-wizard-error-note">' . __('Ensure that Multiple KB add-on is active and refresh this page. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}       ?>
		
		<div class="eckb-wizard-text-page" id="epkb-config-wizard-content">
			
			<div class="epkb-config-wizard-inner">

				<!------- Wizard Header ------------>
				<div class="epkb-wizard-header">
					<div class="epkb-wizard-header__info">
						<h1 class="epkb-wizard-header__info__title">
							<?php _e( 'Text Wizard', 'echo-knowledge-base'); ?>
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
						<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Main Page Text', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Article Page Text', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-3" class="epkb-wsb-step"><?php _e( 'Archive Page Text', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-4" class="epkb-wsb-step"><?php _e( 'Finish', 'echo-knowledge-base'); ?></li>
					</ul>
				</div>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content">
					<?php EPKB_KB_Wizard::show_loader_html(); ?>
					<?php $this->main_page_text(); ?>
					<?php $this->article_page_text(); ?>
					<?php $this->archive_page_text(); ?>
					<?php $this->wizard_step_finish(); ?>
				</div>

				<!------- Wizard Footer ---------->
				<div class="epkb-wizard-footer">
					<?php $this->wizard_buttons(); ?>
				</div>
				
				<div id='epkb-ajax-in-progress' style="display:none;">
					<?php esc_html__( 'Saving configuration', 'echo-knowledge-base' ); ?> <img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
				</div>
				<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo $this->kb_id; ?>"/>
				<input type="hidden" id="eckb_current_theme_values" value="<?php echo EPKB_KB_Wizard_Themes::get_theme_data( $this->kb_config ); ?>">

				<div class="eckb-bottom-notice-message"></div>
			</div>
		</div> <?php
	}

	// Wizard: Step 1 - Main Page
	private function main_page_text() {         ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active">
			<div class="epkb-wizard-text-main-page-preview eckb-wizard-help">
				<div class="epkb-wizard-preview-description">
					<i class="epkbfa epkbfa-info-circle" aria-hidden="true"></i>
					<?php _e( 'This preview is in grey for easier highlight of text editing', 'echo-knowledge-base'); ?>
				</div>
				<div class="eckb-wizard-help__image"></div>		<?php
				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_kb_main_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-text-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-text-main-page-text-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}

	// Wizard: Step 2 - Article Page
	private function article_page_text() {		?>

		<div id="epkb-wsb-step-2-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-text-article-page-preview eckb-wizard-help">
				<div class="epkb-wizard-preview-description">
					<i class="epkbfa epkbfa-info-circle" aria-hidden="true"></i>
					<?php _e( 'This preview is in grey for easier highlight of text editing', 'echo-knowledge-base'); ?>
				</div>
				<div class="eckb-wizard-help__image"></div>		<?php
				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_article_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-text-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-text-article-page-text-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}
	
	// Wizard: Step 3 - Archive Page
	private function archive_page_text() {		?>

		<div id="epkb-wsb-step-3-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-text-archive-page-preview eckb-wizard-help">
				<div class="eckb-wizard-help__image"></div>		<?php
				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_archive_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-text-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-text-archive-page-text-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}

	// Wizard: Step 5 - Finish
	private function wizard_step_finish() {     ?>

		<div id="epkb-wsb-step-4-panel" class="epkb-wc-step-panel eckb-wizard-step-3" >
			<h2><?php _e( 'Final Step: Update Your Knowledge Base', 'echo-knowledge-base'); ?></h2>
			<p><?php _e( 'Click Apply to update your Knowledge Base configuration based on selection from previous Wizard screens.', 'echo-knowledge-base'); ?></p>
		</div>	<?php

		// display link to KB Main Page if any
		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );     ?>

		<div id="epkb-wsb-step-5-panel" class="epkb-wc-step-panel eckb-wizard-step-5" style="display: none">
			<div class="epkb-wizard-row-1">
				<p><?php _e( 'See your KB on the front-end:', 'echo-knowledge-base' ); ?></p>
				<a id="epkb-kb-main-page-link" href="<?php echo empty($link_output) ? '' : $link_output; ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'View My Knowledge base', 'echo-knowledge-base' ); ?></span>
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
				<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply"  data-wizard-type="text"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

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
	 * Show Text Wizard page options for Main Page
	 *
	 * @param $args
	 */
	public function main_page_text_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();

		do_action( 'epkb_text_wizard_before_main_page_texts', $kb_id );

		// SEARCH BOX
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Search Box', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'depends'        => array(
				'hide_when' => array(
					'advanced_search_mp_show_top_category' => 'on|off',  // true if Advanced Search is enabled
					'kb_main_page_layout' => 'Grid|Sidebar'
				)
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['search_title'] + array(
						'value'             => $kb_config['search_title'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.epkb-doc-search-container__title',
							'text' => '1', // use this input like .text() 
						//TODO	'example_image' => 'search_box_title.png'
						)
					) ),
				'1' => $form->text( $feature_specs['search_box_hint'] + array(
						'value'             => $kb_config['search_box_hint'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '#epkb_search_terms',
							'target_attr' => 'placeholder|aria-label' // use this input value like one of the attributes, divided by | 
						)
					) ),
				'2' => $form->text( $feature_specs['search_button_name'] + array(
						'value'             => $kb_config['search_button_name'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '#epkb-search-kb',
							'target_attr' => 'value',
							'text' => '1',
						)
					) ),
				'3' => $form->text( $feature_specs['search_results_msg'] + array(
						'value'             => $kb_config['search_results_msg'],
						'input_group_class' => 'eckb-wizard-single-text',
					) ),
				'4' => $form->text( $feature_specs['no_results_found'] + array(
						'value'             => $kb_config['no_results_found'],
						'input_group_class' => 'eckb-wizard-single-text',
					) ),
				'5' => $form->text( $feature_specs['min_search_word_size_msg'] + array(
						'value'             => $kb_config['min_search_word_size_msg'],
						'input_group_class' => 'eckb-wizard-single-text',
					) ),
			)
		));

		// CATEGORIES
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Categories', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'depends'          => array(
				'hide_when' => array(
					'kb_main_page_layout' => 'Grid|Sidebar'
				)
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['category_empty_msg'] + array(
						'value'             => $kb_config['category_empty_msg'],
						'input_group_class' => 'eckb-wizard-single-text',
					) ),
			)
		));

		// ARTICLES
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Articles', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'depends'          => array(
				'show_when' => array(
					'kb_main_page_layout' => 'Basic|Tabs|Categories' )
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['collapse_articles_msg'] + array(
						'value'             => $kb_config['collapse_articles_msg'],
						'input_group_class' => 'eckb-wizard-single-text',
					) ),
				'1' => $form->text( $feature_specs['show_all_articles_msg'] + array(
						'value'             => $kb_config['show_all_articles_msg'],
						'input_group_class' => 'eckb-wizard-single-text',
					) ),
			)
		));
		
		do_action( 'epkb_text_wizard_after_main_page_texts', $kb_id );
	}

	/**
	 * Show Text Wizard page options for Article Page
	 *
	 * @param $args
	 *
	 * @noinspection PhpUnused
	 */
	public function article_page_text_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();

		do_action( 'epkb_text_wizard_before_article_page_texts', $kb_id );

		// TOC
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('TOC', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'article_toc_enable' => 'on',
				)
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['article_toc_title'] + array(
						'value'             => $kb_config['article_toc_title'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-article-toc__title',
							'text' => '1' // use this input like .text() 
						)
					) ),
			)
		));

		// BACK NAVIGATION
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Back Navigation', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'inputs'            => array (
				'0' => $form->text( $feature_specs['back_navigation_text'] + array(
						'value'             => $kb_config['back_navigation_text'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-navigation-button',
							'text' => '1' // use this input like .text() 
						)
					) ),
			)
		));

		// BREADCRUMBS
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Breadcrumbs', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'breadcrumb_toggle' => 'on',
				)
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['breadcrumb_description_text'] + array(
						'value'             => $kb_config['breadcrumb_description_text'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-breadcrumb-label',
							'text' => '1' // use this input like .text() 
						)
					) ),
				'1' => $form->text( $feature_specs['breadcrumb_home_text'] + array(
						'value'             => $kb_config['breadcrumb_home_text'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-breadcrumb-nav li:first-child a span, .eckb-breadcrumb-nav li:first-child .eckb-breadcrumb-link span:first-child',
							'text' => '1' // use this input like .text() 
						)
					) ),
			)
		));
		
		// Rename fields to understand what they are
		$feature_specs['last_udpated_on_text']['label'] = __( 'Last Updated Text', 'echo-knowledge-base');
		$feature_specs['created_on_text']['label'] = __( 'Created On Text', 'echo-knowledge-base');
		$feature_specs['author_text']['label'] = __( 'Author Text', 'echo-knowledge-base');

		// ARTICLE META
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Article Meta', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'last_udpated_on' => 'article_top|article_bottom', // OR
					'created_on' => 'article_top|article_bottom', // OR
					'author_mode' => 'article_top|article_bottom',
				)
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['last_udpated_on_text'] + array(
						'value'             => $kb_config['last_udpated_on_text'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-ach__article-meta__date-updated__text',
							'text' => '1' // use this input like .text() 
						)
					) ),
				'1' => $form->text( $feature_specs['created_on_text'] + array(
						'value'             => $kb_config['created_on_text'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-ach__article-meta__date-created__text',
							'text' => '1' // use this input like .text() 
						)
					) ),
				'2' => $form->text( $feature_specs['author_text'] + array(
						'value'             => $kb_config['author_text'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-ach__article-meta__author__text',
							'text' => '1' // use this input like .text() 
						)
					) ),
			)
		));
		
		do_action( 'epkb_text_wizard_after_article_page_texts', $kb_id );
	}
	
	/**
	 * Show Text Wizard page options for Article Page
	 *
	 * @param $args
	 *
	 * @noinspection PhpUnused
	 */
	public function archive_page_text_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();

		do_action( 'epkb_text_wizard_before_archive_page_texts', $kb_id );


		// ALL TEXTS
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Description', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'inputs'            => array (
				'0' => $form->text( $feature_specs['templates_for_kb_category_archive_page_heading_description'] + array(
						'value'             => $kb_config['templates_for_kb_category_archive_page_heading_description'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'example_image'     =>      'text-wizard/wizard-screenshot-archive-page-heading-description.jpg'
						)
					) ),
				'1' => $form->text( $feature_specs['templates_for_kb_category_archive_read_more'] + array(
						'value'             => $kb_config['templates_for_kb_category_archive_read_more'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'example_image'     =>      'text-wizard/wizard-screenshot-archive-page-read-more.jpg'
						)
					) ),
			)
		));
		
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Categories Menu', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-texts eckb-wizard-accordion__body',
			'inputs'            => array (
				'0' => $form->text( $feature_specs['category_focused_menu_heading_text'] + array(
						'value'             => $kb_config['category_focused_menu_heading_text'],
						'input_group_class' => 'eckb-wizard-single-text',
						'data' => array(
							'example_image'     =>      'text-wizard/wizard-screenshot-category-focused-heading.jpg'
						)
					) ),
			)
		));

		
		do_action( 'epkb_text_wizard_after_archive_page_texts', $kb_id );
	}

	/**
	 * THis configuration defines fields that are part of this wizard configuration related to text.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	// TODO remove advanced search and elegant layout fields
	public static $text_fields = array(

		// CORE TEXT
		'last_udpated_on_text',
		'created_on_text',
		'author_text',
		'templates_display_main_page_main_title',
		'collapse_articles_msg',
		'show_all_articles_msg',

		// SEARCH TEXT
		'search_title',
		'search_box_hint',
		'search_button_name',
		'search_results_msg',
		'no_results_found',
		'min_search_word_size_msg',

		// TOC TEXT
		'article_toc_title',
		'back_navigation_text',
		'breadcrumb_description_text',
		'breadcrumb_home_text',

		// GRID SEARCH
		'grid_search_title',
		'grid_search_box_hint',
		'grid_search_button_name',
		'grid_search_results_msg',
		'grid_no_results_found',
		'grid_min_search_word_size_msg',

		// GRID TEXTS
		'grid_category_empty_msg',
		'grid_article_count_text',
		'grid_article_count_plural_text',
		'grid_category_link_text',

		// SIDEBAR SEARCH
		'sidebar_search_title',
		'sidebar_search_box_hint',
		'sidebar_search_button_name',
		'sidebar_search_results_msg',
		'sidebar_no_results_found',
		'sidebar_min_search_word_size_msg',

		// SIDEBAR TEXTS
		'sidebar_category_empty_msg',
		'sidebar_collapse_articles_msg',
		'sidebar_show_all_articles_msg',
		'sidebar_main_page_intro_text',
		
		
		// ARTICLE RATING Texts
		'rating_text_value',
		'rating_confirmation_positive',
		'rating_confirmation_negative',
		'rating_stars_text_1',
		'rating_stars_text_2',
		'rating_stars_text_3',
		'rating_stars_text_4',
		'rating_stars_text_5',
		'rating_like_style_yes_button',
		'rating_like_style_no_button',
		'rating_feedback_title',
		'rating_feedback_description',
		'rating_feedback_support_link_text',
		'rating_feedback_support_link_url',
		'rating_feedback_button_text',

		// CATEGORIES
		'category_empty_msg',
		
		// ARCHIVE PAGE
		'templates_for_kb_category_archive_page_heading_description',
		'category_focused_menu_heading_text',
		'templates_for_kb_category_archive_read_more',
		
		// ADVANCED SEARCH TEXTS MAIN PAGE
		'advanced_search_mp_title',
		'advanced_search_mp_description_below_title',
		'advanced_search_mp_description_below_input',
		'advanced_search_mp_box_hint',
		'advanced_search_mp_results_msg',
		'advanced_search_mp_no_results_found',
		'advanced_search_mp_more_results_found',
		'advanced_search_mp_filter_indicator_text',

		// ADVANCED SEARCH TEXTS ARTICLE PAGE
		'advanced_search_ap_title',
		'advanced_search_ap_description_below_title',
		'advanced_search_ap_description_below_input',
		'advanced_search_ap_box_hint',
		'advanced_search_ap_results_msg',
		'advanced_search_ap_no_results_found',
		'advanced_search_ap_more_results_found',
		'advanced_search_ap_filter_indicator_text',
	);
}
