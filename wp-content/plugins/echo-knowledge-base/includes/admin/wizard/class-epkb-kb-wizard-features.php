<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Features {

	var $kb_config = array();
	/** @var  EPKB_KB_Config_Elements */
	var $form;
	var $feature_specs = array();
	/** @var EPKB_HTML_Elements */
	var $html;
	var $kb_id;

	function __construct() {

		add_action( 'epkb-wizard-features-main-page-features-selection-container', array( $this, 'main_page_feature_inputs' ) );
		add_action( 'epkb-wizard-features-article-sidebar-features-selection-container', array( $this, 'article_sidebars_feature_inputs'	) );
		add_action( 'epkb-wizard-features-article-page-features-selection-container', array( $this,	'article_page_feature_inputs' ) );
		add_action( 'epkb-wizard-features-archive-page-features-selection-container', array( $this,	'archive_page_feature_inputs' ) );

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
		
		// core handles only default KB
		if ( $this->kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {
			echo '<div class="epkb-wizard-error-note">' . __('Ensure that Multiple KB add-on is active and refresh this page. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}       ?>
		
		<div class="eckb-wizard-features" id="epkb-config-wizard-content">
			<div class="epkb-config-wizard-inner">

				<!------- Wizard Header ------------>
				<div class="epkb-wizard-header">
					<div class="epkb-wizard-header__info">
						<h1 class="epkb-wizard-header__info__title">
							<?php _e( 'Features and Sidebar Wizard', 'echo-knowledge-base'); ?>
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
							<input type="checkbox" data-save_exit="<?php _e( 'Save and Exit', 'echo-knowledge-base' ); ?>" data-exit="<?php _e( 'Exit Wizard', 'echo-knowledge-base' ); ?>" >
							<span><?php _e( 'Save before exit', 'echo-knowledge-base' ); ?></span>
						</div>
					</div>
				</div>

				<!------- Wizard Status Bar ------->
				<div class="epkb-wizard-status-bar">
					<ul>
						<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Main Page Features', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Article Sidebars', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-3" class="epkb-wsb-step"><?php _e( 'Article Page Features', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-4" class="epkb-wsb-step"><?php _e( 'Archive Page Features', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-5" class="epkb-wsb-step"><?php _e( 'Finish', 'echo-knowledge-base'); ?></li>
					</ul>
				</div>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content">
					<?php EPKB_KB_Wizard::show_loader_html(); ?>
					<?php $this->main_page_features(); ?>
					<?php $this->article_sidebar_features(); ?>
					<?php $this->article_page_features(); ?>
					<?php $this->archive_page_features(); ?>
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
				<input type="hidden" id="eckb_current_slug" value="<?php echo _x( 'knowledge-base', 'initial SLUG for KB', 'echo-knowledge-base' ); ?>"><?php
				if ( !empty( $_GET['preselect'] ) ) { ?>
					<input type="hidden" id="eckb_preselect" value="<?php echo $_GET['preselect']; ?>"><?php
				} ?>
				<div class="eckb-bottom-notice-message"></div>
			</div>
		</div> <?php
	}

	// Wizard: Step 1 - Main Page
	private function main_page_features() {         ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active">
			<div class="epkb-wizard-features-main-page-preview eckb-wizard-help">
				<div class="eckb-wizard-help__image"></div><?php

				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_kb_main_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-features-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-features-main-page-features-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}
	
	// Wizard: Step 3 - Article Layout
	private function article_sidebar_features() {		?>

		<div id="epkb-wsb-step-2-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-features-article-page-preview eckb-wizard-help">
				<div class="eckb-wizard-help__image"></div>			<?php
				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_article_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-features-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-features-article-sidebar-features-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}
	
	// Wizard: Step 2 - Article Page
	private function article_page_features() {		?>

		<div id="epkb-wsb-step-3-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-features-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-features-article-page-features-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}

	// Wizard: Step 4 - Archive Page
	private function archive_page_features() {		?>

		<div id="epkb-wsb-step-4-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-features-archive-page-preview eckb-wizard-help">
				<div class="eckb-wizard-help__image"></div>		<?php
				$handler = new EPKB_KB_Config_Page( $this->kb_config );
				$handler->display_archive_page_layout_preview( true ); ?>
			</div>
			<div class="epkb-wizard-text-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-features-archive-page-features-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}

	// Wizard: Step 5 - Finish
	private function wizard_step_finish() {     ?>

		<div id="epkb-wsb-step-5-panel" class="epkb-wc-step-panel eckb-wizard-step-3" >
			<h2><?php _e( 'Final Step: Update Your Knowledge Base', 'echo-knowledge-base'); ?></h2>
			<p><?php _e( 'Click Apply to update your Knowledge Base configuration based on selection from previous Wizard screens.', 'echo-knowledge-base'); ?></p>
		</div>	<?php

		// display link to KB Main Page if any
		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );     ?>

		<div id="epkb-wsb-step-6-panel" class="epkb-wc-step-panel eckb-wizard-step-5" style="display: none">
			<div class="epkb-wizard-row-1">
				<p><?php _e( 'See your KB on the front-end:', 'echo-knowledge-base' ); ?></p>
				<a id="epkb-kb-main-page-link" href="<?php echo empty($link_output) ? '' : $link_output; ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-features"><?php _e( 'View My Knowledge base', 'echo-knowledge-base' ); ?></span>
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
				<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply"  data-wizard-type="features"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

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
	public function main_page_feature_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();
		
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Page Title', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'templates_for_kb' => 'kb_templates',
				)
			),
			'inputs'            => array (
				'0' => $form->checkbox( $feature_specs['templates_display_main_page_main_title'] + array(
						'value'             => $kb_config['templates_display_main_page_main_title'],
						'id'                => 'templates_display_main_page_main_title',
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'example_image'     =>      'features-wizard/wizard-screenshot-display-main-title.jpg'
						)
					) )
			)
		));

		// CONTENT - Style
		$main_page_articles_shown = defined( 'E'.'LAY_PLUGIN_NAME' ) ? array(	'show_when' => array('kb_main_page_layout' => 'Basic|Tabs|Categories' )  ) : array();
		
		$form->option_group_wizard( $feature_specs, array(
			'option-heading' => __( 'Layout', 'echo-knowledge-base' ),
			'class'          => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'          => $main_page_articles_shown,
			'inputs' => array(
				'0' => $form->dropdown( $feature_specs['width'] + array(
						'value' => $kb_config['width'],
						'current' => $kb_config['width'],
						'input_group_class' => 'eckb-wizard-single-dropdown eckb-wizard-single-dropdown-example',
						'main_label_class'  => 'config-col-3',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
						'data' => array(
							'example_image'     =>      'features-wizard/wizard-screenshot-page-width.jpg'
						)
					) ),
				'1' => $form->radio_buttons_horizontal( $feature_specs['nof_columns'] + array(
						'id'        => 'front-end-columns',
						'value'     => $kb_config['nof_columns'],
						'current'   => $kb_config['nof_columns'],
						'input_group_class' => 'eckb-wizard-radio-btn-horizontal',
						'main_label_class'  => 'config-col-5',
						'input_class'       => 'config-col-6',
						'radio_class'       => 'config-col-3',
						'data' => array(
							'preview' => '1'
						)
					) )
			)));

		// CATEGORIES - Style
		$form->option_group_wizard( $feature_specs, array(
			'option-heading' => __( 'Categories', 'echo-knowledge-base' ),
			'class'        => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'          => $main_page_articles_shown,
			'inputs' => array(
				'1' => $form->dropdown( $feature_specs['section_head_category_icon_location'] + array(
						'value' => $kb_config['section_head_category_icon_location'],
						'current' => $kb_config['section_head_category_icon_location'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-6',
						'data' => array(
							'preview' => '1'
						)
					)),
				'2' => $form->text( $feature_specs['section_head_category_icon_size'] + array(
						'value'             => $kb_config['section_head_category_icon_size'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'3' => $form->checkbox( $feature_specs['section_desc_text_on'] + array(
						'value'             => $kb_config['section_desc_text_on'],
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'4' => $form->dropdown( $feature_specs['section_head_alignment'] + array(
						'value' => $kb_config['section_head_alignment'],
						'current' => $kb_config['section_head_alignment'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-6',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'5' => $form->dropdown( $feature_specs['section_font_size'] + array(
						'value' => $kb_config['section_font_size'],
						'current' => $kb_config['section_font_size'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
						'data' => array(
							'preview' => '1'
						)
					) )
			)
		));

		// LIST OF ARTICLES - Style
		//Arg1 / Arg2  for text_and_select_fields_horizontal
		$arg1 = $feature_specs['section_body_height'] + array( 
			'value' => $kb_config['section_body_height'],
			'current' => $kb_config['section_body_height'], 
			'input_group_class' => 'config-col-6', 
			'input_class' => 'config-col-12',
			'data' => array(
				'preview' => '1'
			) );
			
		$arg2 = $feature_specs['section_box_height_mode'] + array( 
			'value'    => $kb_config['section_box_height_mode'], 
			'current'  => $kb_config['section_box_height_mode'], 
			'input_group_class' => 'eckb-wizard-single-dropdown', 
			'input_class' => 'config-col-12',
			'data' => array(
				'preview' => '1'
			) );

		$form->option_group_wizard( $feature_specs, array(
			'option-heading' => __( 'Article List', 'echo-knowledge-base' ),
			'class'        => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'          => $main_page_articles_shown,
			'inputs' => array(
				'0' => $form->text( $feature_specs['nof_articles_displayed'] + array(
						'value' => $kb_config['nof_articles_displayed'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-2',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'1' => $form->dropdown( $feature_specs['expand_articles_icon'] + array(
						'value' => $kb_config['expand_articles_icon'],
						'current' => $kb_config['expand_articles_icon'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'2' => $form->text_and_select_fields_horizontal( array(
					'id'                => 'list_height',
					'input_group_class' => 'eckb-wizard-miltiple-select-text',
					'main_label_class'  => 'config-col-5',
					'label'             => __( 'Articles List Height', 'echo-knowledge-base' ),
					'input_class'       => 'config-col-6',
				), $arg1, $arg2 )
			)
		));

		do_action( 'epkb_features_wizard_after_main_page_features', $kb_id );
	}

	/**
	 * Show Wizard page options for Article Page
	 *
	 * @param $args
	 *
	 * @noinspection PhpUnused
	 */
	public function article_page_feature_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();

		// CATEGORIES - List mode
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Categories List Mode', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'kb_main_page_layout' => 'Categories',
				)
			),
			'inputs'            => array(
				'0' => $form->dropdown( $feature_specs['categories_layout_list_mode'] + array(
						'value' =>$kb_config['categories_layout_list_mode'],
						'current' =>$kb_config['categories_layout_list_mode'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'main_label_class'  => 'config-col-3',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
						'data' => array(
							'example_image'     =>      'features-wizard/wizard-screenshot-categories-layout-list-mode.jpg'
						)
					) )
		)));

		// FEATURES - Breadcrumb
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Breadcrumb', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->checkbox( $feature_specs['breadcrumb_toggle'] + array(
						'value'             =>$kb_config['breadcrumb_toggle'],
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'1' => $form->dropdown( $feature_specs['breadcrumb_icon_separator'] + array(
						'value'             =>$kb_config['breadcrumb_icon_separator'],
						'current'           =>$kb_config['breadcrumb_icon_separator'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
						'data' => array(
							'preview' => '1'
						)
					) ),)
		));

		// FEATURES - TOC
		$toc_inputs = array(
				
				'3' => $form->text( $feature_specs['article_toc_font_size'] + array(
						'value'             =>$kb_config['article_toc_font_size'],
						'input_group_class' => 'eckb-wizard-single-text',
						'class'             => 'ekb-color-picker',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'4' => $form->radio_buttons_vertical( $feature_specs['article_toc_start_level'] + array(
						'value' =>$kb_config['article_toc_start_level'],
						'current' =>$kb_config['article_toc_start_level'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-5',
						'input_class'       => 'config-col-7',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'5' => $form->text( $feature_specs['article_toc_scroll_offset'] + array(
						'value' =>$kb_config['article_toc_scroll_offset'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
						'data' => array(
							'preview' => 1
						)
					) ),
				'6' => $form->text( $feature_specs['article_toc_exclude_class'] + array(
						'value' =>$kb_config['article_toc_exclude_class'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
						'data' => array(
							'preview' => 1
						)
					) ),
				'7' => $form->radio_buttons_vertical( $feature_specs['article_toc_border_mode'] + array(
						'value' =>$kb_config['article_toc_border_mode'],
						'current' =>$kb_config['article_toc_border_mode'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-5',
						'input_class'       => 'config-col-7',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					) ),
			);
		
		if ( ! EPKB_Articles_Setup::is_article_structure_v2( $kb_config ) ) {
			
			$toc_inputs['0'] = $form->checkbox( $feature_specs['article_toc_enable'] + array(
						'value'             =>$kb_config['article_toc_enable'],
						'current'           =>$kb_config['article_toc_enable'],
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
						'data' => array(
							'preview' => '1'
						)
					) );
				
			$toc_inputs['2'] = $form->radio_buttons_vertical( $feature_specs['article_toc_position'] + array(
						'value' =>$kb_config['article_toc_position'],
						'current' =>$kb_config['article_toc_position'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical ' .$kb_config['kb_main_page_layout'],
						'main_label_class'  => 'config-col-5',
						'input_class'       => 'config-col-7',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					) );
					
			$toc_inputs['9'] = $form->text( $feature_specs['article_toc_gutter'] + array(
						'value'             =>$kb_config['article_toc_gutter'],
						'input_group_class' => 'eckb-wizard-single-text',
						'class'             => 'ekb-color-picker',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
						'data' => array(
							'preview' => 1
						)
					) );
		}
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Table of Contents', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'inputs'            => $toc_inputs
		));

		// FEATURES - Back Navigation
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Back Navigation', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->checkbox( $feature_specs['back_navigation_toggle'] + array(
						'value'             =>$kb_config['back_navigation_toggle'],
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'preview' => '1'
						)
					) ),
				'1' => $form->radio_buttons_vertical( $feature_specs['back_navigation_mode'] + array(
						'value' =>$kb_config['back_navigation_mode'],
						'current'   =>$kb_config['back_navigation_mode'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'example_image'     =>      'features-wizard/wizard-screenshot-article-page-navigation.jpg'
						)
					) ),
				'2' => $form->text( $feature_specs['back_navigation_font_size'] + array(
						'value' =>$kb_config['back_navigation_font_size'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
						'data' => array(
							'target_selector' => '#epkb-wsb-step-2-panel .eckb-navigation-button a, #epkb-wsb-step-2-panel .eckb-navigation-button',
							'style_name' => 'font-size',
							'postfix' => 'px'
						)
					) ),
			)
		));

		// FEATURES - Comments
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Comments', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->checkbox( $feature_specs['articles_comments_global'] + array(
						'value'             =>$kb_config['articles_comments_global'],
						'current'           =>$kb_config['articles_comments_global'],
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9',
						'data' => array(
							'example_image'     =>      'features-wizard/wizard-screenshot-comments.jpg'
						)
				) ),
			)
		));

		// FETAURES - other
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Meta Data', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->radio_buttons_vertical( $feature_specs['last_udpated_on'] + array(
						'value'             =>$kb_config['last_udpated_on'],
						'current'           =>$kb_config['last_udpated_on'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					)),
				'2' => $form->radio_buttons_vertical( $feature_specs['created_on'] + array(
						'value'             =>$kb_config['created_on'],
						'current'           =>$kb_config['created_on'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					)),
				'4' => $form->radio_buttons_vertical( $feature_specs['author_mode'] + array(
						'value'             =>$kb_config['author_mode'],
						'current'           =>$kb_config['author_mode'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					)),
				'6' => $form->radio_buttons_vertical( $feature_specs['article_meta_icon_on'] + array(
						'value'             =>$kb_config['article_meta_icon_on'],
						'current'           =>$kb_config['article_meta_icon_on'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					) ),
				/*
				'7' => $form->dropdown( $feature_specs['date_format'] + array(
						'value'             =>$kb_config['date_format'],
						'current'           =>$kb_config['date_format'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-7'
					) )
				*/
			)
		));
		
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Categories List', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'article-structure-version' => 'version-2',
					'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
				)
			),
			'inputs'            => array(
				'0' => $form->text( $feature_specs['categories_box_font_size'] + array(
						'value'             => $kb_config['categories_box_font_size'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2',
						'data' => array(
							'preview' => '1'
						)
					) ),
		)));

		do_action( 'epkb_features_wizard_after_article_page_features', $kb_id, $kb_config );
	}
	
	/**
	 * Show Wizard page options for Article Page
	 *
	 * @param $args
	 *
	 * @noinspection PhpUnused
	 */
	public function article_sidebars_feature_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements(); ?>
		
		<div class="epkb-wizard-features-article-layout-info-link">
			<span class="epkbfa epkbfa-eye" data-example_image="features-wizard/wizard-screenshot-article-sidebars-info.jpg"></span>
			<a href="https://www.echoknowledgebase.com/documentation/display-structure-overview/" target="_blank"><?php _e( 'Read more about these settings', 'echo-knowledge-base' ); ?></a>
		</div><?php 
		
		$plugin_first_version = get_option( 'epkb_version_first' );
		
		if ( version_compare( $plugin_first_version, '6.4.0', '<' ) || ! EPKB_Articles_Setup::is_article_structure_v2( $kb_config ) ) {
			$form->option_group_wizard( $feature_specs, array(
				'option-heading'    => __( 'Structure', 'echo-knowledge-base' ),
				'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
				'depends'        => array(
					'hide_when' => array(
						'article-structure-version' => 'version-2',
						'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
					)
				),
				'inputs'            => array(
					'1' => $form->dropdown( $feature_specs['article-structure-version'] + array(
							'value'             =>$kb_config['article-structure-version'],
							'current'           =>$kb_config['article-structure-version'],
							'input_group_class' => 'eckb-wizard-single-dropdown',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-5',
							'data' => array(
								'preview' => '1'
							)
						) ),
				)
			));
		}
		
		$disabled_right_sidebar_message = '<div class="config-input-group eckb-wizard-single-text eckb-wizard-article-blocked-rsidebar"><span class="epkbfa epkbfa-info-circle"></span> ' .
		                                  __( 'Nothing is assigned to the Right Sidebar', 'echo-knowledge-base' ) . '</div>';
		$disabled_left_sidebar_message = '<div class="config-input-group eckb-wizard-single-text eckb-wizard-article-blocked-lsidebar"><span class="epkbfa epkbfa-info-circle"></span> ' .
		                                  __( 'Nothing is assigned to the Left Sidebar', 'echo-knowledge-base' ) . '</div>';
		
		$desktop_columns_alert = sprintf( '<div class="config-input-group eckb-wizard-single-text eckb-wizard-article-width-input-alert-desktop"><span class="epkbfa epkbfa-info-circle"></span> <span>' .
		                                  __( 'The sum of Desktop columns width is %s. It needs to be 100%%.', 'echo-knowledge-base' ) . '</span></div>', '<span  class="epkb-wizard-article-width-alert-num"></span>%' );
		$tablet_columns_alert = sprintf( '<div class="config-input-group eckb-wizard-single-text eckb-wizard-article-width-input-alert-tablet"><span class="epkbfa epkbfa-info-circle"></span> <span>' .
		                                 __( 'The sum of Tablet columns width is %s. It needs to be 100%%.', 'echo-knowledge-base' ) . '</span></div>', '<span  class="epkb-wizard-article-width-alert-num"></span>%' );
		
		$article_sidebar_component_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );
		$options = array(
			'0' => __( 'Not displayed', 'echo-knowledge-base' ),
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5'
		);
		$options2 = array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5'
		);

		$left_sidebar_inputs = array(
				'0' => $disabled_left_sidebar_message,
				'1' => $form->checkbox( $feature_specs['article-left-sidebar-match'] + array(
						'value'             =>$kb_config['article-left-sidebar-match'],
						'current'           =>$kb_config['article-left-sidebar-match'],
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9',
						'data' => array(
							'preview' => '1'
						)
				) ),
				'2' => $form->text( $feature_specs['article-left-sidebar-starting-position'] +
							array( $kb_config['article-left-sidebar-starting-position'],
								'value'             => $kb_config['article-left-sidebar-starting-position'],
								'input_group_class' => 'eckb-wizard-single-text',
			                    'label_class'       => 'config-col-5',
			                    'input_class'       => 'config-col-5',
			                    'data' => array(
				                   'target_selector' => '#epkb-wsb-step-2-panel #eckb-article-page-container-v2 #eckb-article-left-sidebar',
									'style_name' => 'margin-top',
									'postfix' => 'px'
			                    )
							) ),
				'3' => $form->dropdown( array(
						'name'          => 'toc_left',
						'label'            => __( 'TOC Location', 'echo-knowledge-base' ),
						'current'           => $article_sidebar_component_priority['toc_left'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4 epkb_toc_position',
						'options'    => $options,
						'data' => array(
							'preview' => '1'
						)
					) ),
				'4' => $form->dropdown( array(
						'name'          => 'kb_sidebar_left',
						'label'            => __( 'Widgets Location', 'echo-knowledge-base' ),
						'current'           => $article_sidebar_component_priority['kb_sidebar_left'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4 epkb_kb_sidebar_position',
						'options'    => $options,
						'data' => array(
							'preview' => '1'
						)
					) ),
				
		);

		// if Elegant Layouts is enabled then user can show Sidebar Layout even if Basic Layout is ON
		if ( EPKB_Utilities::is_elegant_layouts_enabled() ) {
			$left_sidebar_inputs['5'] = $form->dropdown( array(
					'name'          => 'elay_sidebar_left',
					'label'            => __( 'Navigation Menu', 'echo-knowledge-base' ),
					'current'           => $article_sidebar_component_priority['elay_sidebar_left'],
					'input_group_class' => 'eckb-wizard-single-dropdown',
					'label_class' => 'config-col-5',
					'input_class' => 'config-col-4 epkb_elay_sidebar_position',
					'options'    => ( $kb_config['kb_main_page_layout'] == 'Sidebar' ? $options2 : $options ),
					    'data' => array(
						'preview' => '1'
					)
				) );
		}

		if ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME ) {
			$left_sidebar_inputs['6'] = $form->dropdown( array(
					'name'          => 'categories_left',
					'label'            => __( 'Category Layout Location', 'echo-knowledge-base' ),
					'current'           => $article_sidebar_component_priority['categories_left'],
					'input_group_class' => 'eckb-wizard-single-dropdown',
					'label_class' => 'config-col-5',
					'input_class' => 'config-col-4 epkb_categories_position',
					'options'    => $options,
					'data' => array(
						'preview' => '1'
					)
				) );
		}

		$left_sidebar_inputs['7'] = $form->text( array($kb_config['article-left-sidebar-desktop-width-v2'],
			                    'value'             =>$kb_config['article-left-sidebar-desktop-width-v2'],
			                    'input_group_class' => 'eckb-wizard-single-text eckb-wizard-article-width-input-desktop',
			                    'label_class'       => 'config-col-5',
			                    'input_class'       => 'config-col-5',
			                    'data' => array(
				                    'preview' => '1'
			                    )
		                    ) + $feature_specs['article-left-sidebar-desktop-width-v2'] );
		
		$left_sidebar_inputs['8'] = $desktop_columns_alert;
		
		$left_sidebar_inputs['9'] = $form->text( array($kb_config['article-left-sidebar-tablet-width-v2'],
			                    'value'             =>$kb_config['article-left-sidebar-tablet-width-v2'],
			                    'input_group_class' => 'eckb-wizard-single-text eckb-wizard-article-width-input-tablet',
			                    'label_class'       => 'config-col-5',
			                    'input_class'       => 'config-col-5',
			                    'data' => array(
				                    'preview' => '1'
			                    )
		                    ) + $feature_specs['article-left-sidebar-tablet-width-v2'] );
							
		$left_sidebar_inputs['10'] = $tablet_columns_alert;
		
		$article_container_desktop_width = array($kb_config['article-container-desktop-width-v2'],
						'value'             =>$kb_config['article-container-desktop-width-v2'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
					) + $feature_specs['article-container-desktop-width-v2'];
		$article_container_desktop_width_units = $feature_specs['article-container-desktop-width-units-v2'] + array(
						'value' =>$kb_config['article-container-desktop-width-units-v2'],
						'current' =>$kb_config['article-container-desktop-width-units-v2'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'main_label_class'  => 'config-col-3',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
			);
		
		$article_container_tablet_width = array($kb_config['article-container-tablet-width-v2'],
						'value'             =>$kb_config['article-container-tablet-width-v2'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
					) + $feature_specs['article-container-tablet-width-v2'];
		$article_container_tablet_width_units = $feature_specs['article-container-tablet-width-units-v2'] + array(
						'value' =>$kb_config['article-container-tablet-width-units-v2'],
						'current' =>$kb_config['article-container-tablet-width-units-v2'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'main_label_class'  => 'config-col-3',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
					);
		// ARTICLE PAGE
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Article Page', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'article-structure-version' => 'version-2',
					'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
				)
			),
			'inputs'            => array(
				'0' => $form->text_and_select_fields_horizontal( array(
					'input_group_class' => 'eckb-wizard-units',
					'label' => __( 'Width', 'echo-knowledge-base' ),
				), $article_container_desktop_width, $article_container_desktop_width_units ),
				'1' => $form->text_and_select_fields_horizontal( array(
					'input_group_class' => 'eckb-wizard-units',
					'label' => __( 'Width (Tablets)', 'echo-knowledge-base' ),
				), $article_container_tablet_width, $article_container_tablet_width_units ),
			)
		));
		
		$article_body_desktop_width = array($kb_config['article-body-desktop-width-v2'],
						'value'             =>$kb_config['article-body-desktop-width-v2'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
					) + $feature_specs['article-body-desktop-width-v2'];
		$article_body_desktop_width_units = $feature_specs['article-body-desktop-width-units-v2'] + array(
						'value' =>$kb_config['article-body-desktop-width-units-v2'],
						'current' =>$kb_config['article-body-desktop-width-units-v2'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'main_label_class'  => 'config-col-3',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
					);
		$article_body_tablet_width = array($kb_config['article-body-tablet-width-v2'],
						'value'             =>$kb_config['article-body-tablet-width-v2'],
						'input_group_class' => 'eckb-wizard-single-text',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5',
					) + $feature_specs['article-body-tablet-width-v2'];
		$article_body_tablet_width_units = $feature_specs['article-body-tablet-width-units-v2'] + array(
						'value' =>$kb_config['article-body-tablet-width-units-v2'],
						'current' =>$kb_config['article-body-tablet-width-units-v2'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'main_label_class'  => 'config-col-3',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4',
					);
		// ARTICLE BODY
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Article Body', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'article-structure-version' => 'version-2',
					'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
				)
			),
			'inputs'            => array(
				'0' => $form->text_and_select_fields_horizontal( array(
					'input_group_class' => 'eckb-wizard-units',
					'label' => __( 'Width', 'echo-knowledge-base' ),
				), $article_body_desktop_width, $article_body_desktop_width_units ),
				'1' => $form->text_and_select_fields_horizontal( array(
					'input_group_class' => 'eckb-wizard-units',
					'label' => __( 'Width (Tablets)', 'echo-knowledge-base' ),
				), $article_body_tablet_width, $article_body_tablet_width_units ),
			)
		));

		// LEFT SIDEBAR
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Left Sidebar', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body eckb-wizard-features-left-sidebar',
			'depends'        => array(
				'show_when' => array(
					'article-structure-version' => 'version-2',
					'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
				)
			),
			'inputs'            => $left_sidebar_inputs,
		));

		// CENTER CONTENT
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Center Content', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'article-structure-version' => 'version-2'
				)
			),
			'inputs'            => array(
				'0' => $form->dropdown( array(
						'name'          => 'toc_content',
						'label'            => __( 'Display of TOC', 'echo-knowledge-base' ),
						'current'           => $article_sidebar_component_priority['toc_content'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4 epkb_toc_position',
						'options'    => array(
							'0' => __( 'Not displayed', 'echo-knowledge-base' ),
							'1' => __( 'Display', 'echo-knowledge-base' ),
						),
						'data' => array(
							'preview' => '1'
						)
					) ),
				'1' => $form->text( array($kb_config['article-content-desktop-width-v2'],
					                    'value'             =>$kb_config['article-content-desktop-width-v2'],
					                    'input_group_class' => 'eckb-wizard-single-text eckb-wizard-article-width-input-desktop',
					                    'label_class'       => 'config-col-5',
					                    'input_class'       => 'config-col-5',
					                    'data' => array(
						                    'preview' => '1'
					                    )
				                    ) + $feature_specs['article-content-desktop-width-v2'] ),
				'2' => $desktop_columns_alert,
				'3' => $form->text( array($kb_config['article-content-tablet-width-v2'],
					                    'value'             =>$kb_config['article-content-tablet-width-v2'],
					                    'input_group_class' => 'eckb-wizard-single-text eckb-wizard-article-width-input-tablet',
					                    'label_class'       => 'config-col-5',
					                    'input_class'       => 'config-col-5',
					                    'data' => array(
						                    'preview' => '1'
					                    )
				                    ) + $feature_specs['article-content-tablet-width-v2'] ),
				'4' => $tablet_columns_alert,
			)
		));

		// RIGHT SIDEBAR
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Right Sidebar', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body  eckb-wizard-features-right-sidebar',
			'depends'        => array(
				'show_when' => array(
					'article-structure-version' => 'version-2',
					'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
				)
			),
			'inputs'            => array(
				'0' => $disabled_right_sidebar_message,
				'1' => $form->checkbox( $feature_specs['article-right-sidebar-match'] + array(
						'value'             =>$kb_config['article-right-sidebar-match'],
						'current'           =>$kb_config['article-right-sidebar-match'],
						'input_group_class' => 'eckb-wizard-single-checkbox',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9',
						'data' => array(
							'preview' => '1'
						)
				) ),
				'2' => $form->text( $feature_specs['article-right-sidebar-starting-position'] +
							array( $kb_config['article-right-sidebar-starting-position'],
								'value'             => $kb_config['article-right-sidebar-starting-position'],
								'input_group_class' => 'eckb-wizard-single-text',
			                    'label_class'       => 'config-col-5',
			                    'input_class'       => 'config-col-5',
			                    'data' => array(
				                    'target_selector' => '#epkb-wsb-step-2-panel #eckb-article-page-container-v2 #eckb-article-right-sidebar',
									'style_name' => 'margin-top',
									'postfix' => 'px'
			                    )
							) ),
				'3' => $form->dropdown( array(
						'name'          => 'toc_right',
						'label'            => __( 'TOC Location', 'echo-knowledge-base' ),
						'current'           => $article_sidebar_component_priority['toc_right'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4 epkb_toc_position',
						'options'    => $options,
						'data' => array(
							'preview' => '1'
						)
					) ),
				'4' => $form->dropdown( array(
						'name'          => 'kb_sidebar_right',
						'label'            => __( 'Widgets Location', 'echo-knowledge-base' ),
						'current'           => $article_sidebar_component_priority['kb_sidebar_right'],
						'input_group_class' => 'eckb-wizard-single-dropdown',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4 epkb_kb_sidebar_position',
						'options'    => $options,
						'data' => array(
							'preview' => '1'
						)
					) ),
				'5' => $form->text( array($kb_config['article-right-sidebar-desktop-width-v2'],
					                    'value'             =>$kb_config['article-right-sidebar-desktop-width-v2'],
					                    'input_group_class' => 'eckb-wizard-single-text eckb-wizard-article-width-input-desktop',
					                    'label_class'       => 'config-col-5',
					                    'input_class'       => 'config-col-5',
					                    'data' => array(
						                    'preview' => '1'
					                    )
				                    ) + $feature_specs['article-right-sidebar-desktop-width-v2'] ),
				'6' => $desktop_columns_alert,
				'7' => $form->text( array($kb_config['article-right-sidebar-tablet-width-v2'],
					                    'value'             =>$kb_config['article-right-sidebar-tablet-width-v2'],
					                    'input_group_class' => 'eckb-wizard-single-text eckb-wizard-article-width-input-tablet',
					                    'label_class'       => 'config-col-5',
					                    'input_class'       => 'config-col-5',
					                    'data' => array(
						                    'preview' => '1'
					                    )
				                    ) + $feature_specs['article-right-sidebar-tablet-width-v2'] ),
				'8' => $tablet_columns_alert,
			)
		));
		
		// BREAKPOINTS
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Breakpoints', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'article-structure-version' => 'version-2',
					'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
				)
			),
			'inputs'            => array(
				'0' => $form->text( array($kb_config['article-tablet-break-point-v2'],
					                    'value'             =>$kb_config['article-tablet-break-point-v2'],
					                    'input_group_class' => 'eckb-wizard-single-text',
					                    'label_class'       => 'config-col-5',
					                    'input_class'       => 'config-col-5',
					                    'data' => array(
						                    'preview' => '1'
					                    )
				                    ) + $feature_specs['article-tablet-break-point-v2'] ),
				'1' => '<div class="config-input-group eckb-wizard-single-text "><span class="epkbfa epkbfa-info-circle"></span> ' . __( 'Recommended (1025)', 'echo-knowledge-base' ) . '</div>',
				'2' => $form->text( array($kb_config['article-mobile-break-point-v2'],
						'value'             =>$kb_config['article-mobile-break-point-v2'],
						'input_group_class' => 'eckb-wizard-single-text',
					    'label_class'       => 'config-col-5',
					    'input_class'       => 'config-col-5',
					    'data' => array(
							'preview' => '1'
					    )
				    ) + $feature_specs['article-mobile-break-point-v2'] ),
				'3' => '<div class="config-input-group eckb-wizard-single-text "><span class="epkbfa epkbfa-info-circle"></span> ' . __( 'Recommended (768)', 'echo-knowledge-base' ) . '</div>',

			)
		));
		
		do_action( 'epkb_features_wizard_after_article_layout_features', $kb_id );
	}
	
	/**
	 * Show Wizard page options for Article Page
	 *
	 * @param $args
	 *
	 * @noinspection PhpUnused
	 */
	public function archive_page_feature_inputs( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();

		// FETAURES - other
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Style', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->radio_buttons_vertical( $feature_specs['templates_for_kb_category_archive_page_style'] + array(
						'value'             =>$kb_config['templates_for_kb_category_archive_page_style'],
						'current'           =>$kb_config['templates_for_kb_category_archive_page_style'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12',
						'data' => array(
							'preview' => '1'
						)
					) ),
			)
		));

		do_action( 'epkb_features_wizard_after_archive_page_features', $kb_id );
	}

	/**
	 * THis configuration defines fields that are part of this wizard configuration related to features.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	public static $feature_fields = array(

		// CORE FEATURES
		// TODO

		// CORE MAIN PAGE
		'templates_display_main_page_main_title',
		'width',
		'nof_columns',
		'section_font_size',
		'section_desc_text_on',
		'section_head_category_icon_location',
		'section_head_category_icon_size',
		'nof_articles_displayed',
		'expand_articles_icon',
		'section_body_height',
		'section_box_height_mode',
		'section_head_alignment',
		
		// CORE ARTICLE SIDEBAR
		'article-structure-version',
		'article_sidebar_component_priority',
		'article-left-sidebar-desktop-width-v2',
		'article-left-sidebar-tablet-width-v2',
		'article-right-sidebar-tablet-width-v2',
		'article-content-tablet-width-v2',
		'article-content-desktop-width-v2',
		'article-right-sidebar-desktop-width-v2',
		'article-tablet-break-point-v2',
		'article-mobile-break-point-v2',
		'article-container-desktop-width-v2',
		'article-container-desktop-width-units-v2',
		'article-container-tablet-width-v2',
		'article-container-tablet-width-units-v2',
		'article-body-desktop-width-v2',
		'article-body-desktop-width-units-v2', 
		'article-body-tablet-width-v2',
		'article-body-tablet-width-units-v2',
		'article-left-sidebar-starting-position',
		'article-right-sidebar-starting-position',
		'article-right-sidebar-match',
		'article-left-sidebar-match',
		
		// CORE ARTICLE PAGE
		'categories_layout_list_mode',
		'breadcrumb_toggle',
		'breadcrumb_icon_separator',
		'article_toc_enable',
		'article_toc_position',
		'article_toc_font_size',
		'article_toc_start_level',
		'article_toc_scroll_offset',
		'article_toc_exclude_class',
		'article_toc_border_mode',
		'article_toc_gutter',
		'back_navigation_toggle',
		'back_navigation_mode',
		'back_navigation_font_size',
		'articles_comments_global',
		'last_udpated_on',
		'created_on',
		'author_mode',
		'article_meta_icon_on',
		'date_format',
		'categories_box_font_size',
		
		// Elegant Layouts MAIN PAGE
		'grid_width',
		'grid_nof_columns',
		'grid_section_font_size',
		'grid_section_desc_text_on',
		'grid_section_article_count',
		'grid_category_icon_location',
		'grid_section_icon_size',
		'grid_category_icon_thickness',
		'grid_section_body_height',
		'grid_section_box_height_mode',
		'grid_section_head_alignment',
		'grid_section_body_alignment',
		
		// Elegant Layouts ARTICLE PAGE
		'sidebar_side_bar_width',
		'sidebar_side_bar_height',
		'sidebar_side_bar_height_mode',
		'sidebar_scroll_bar',
		'sidebar_section_font_size',
		'sidebar_nof_articles_displayed',
		'sidebar_expand_articles_icon',
		'sidebar_section_body_height',
		'sidebar_section_box_height_mode',
		'sidebar_top_categories_collapsed',
		'sidebar_section_desc_text_on',
		'sidebar_section_head_alignment',
		'sidebar_section_divider',
		'sidebar_section_divider_thickness',
		
		// Ratings and Feedback
		'rating_mode',
		'rating_layout',
		'rating_stats_meta',
		'rating_element_location',
		'rating_feedback_name_prompt',
		'rating_feedback_email_prompt',
		'rating_feedback_trigger_stars',
		'rating_like_style',
		'rating_feedback_trigger_like',
		
		// Widgets
		'widgets_sidebar_location',
		
		// ARCHIVE PAGE
		'templates_for_kb_category_archive_page_style'
		
	);
}
