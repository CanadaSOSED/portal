<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB Ordering Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Ordering {

	var $kb_config = array();
	/** @var  EPKB_KB_Config_Elements */
	var $form;
	var $feature_specs = array();
	/** @var EPKB_HTML_Elements */
	var $html;
	var $kb_id;

	function __construct() {
		add_action( 'epkb-wizard-ordering-page-feature-selection-container', array( $this, 'article_category_ordering' ) );
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
		
		<div class="eckb-wizard-ordering" id="epkb-config-wizard-content">
			<div class="epkb-config-wizard-inner">

				<!------- Wizard Header ------------>
				<div class="epkb-wizard-header">
					<div class="epkb-wizard-header__info">
						<h1 class="epkb-wizard-header__info__title">
							<?php _e( 'Ordering Wizard', 'echo-knowledge-base'); ?>
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
					</div>
				</div>

				<!------- Wizard Status Bar ------->
				<div class="epkb-wizard-status-bar">
					<ul>
						<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Articles and Categories Ordering', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Ordering', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-3" class="epkb-wsb-step"><?php _e( 'Finish', 'echo-knowledge-base'); ?></li>
					</ul>
				</div>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content">
					<?php EPKB_KB_Wizard::show_loader_html(); ?>
					<?php $this->page_article_category_ordering(); ?>
					<?php $this->page_ordering(); ?>
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
	private function page_article_category_ordering() {         ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active">
			<div class="epkb-wizard-ordering-selection-container eckb-wizard-accordion">
				<?php $this->wizard_section( 'epkb-wizard-ordering-page-feature-selection-container', array( 'id' => $this->kb_config['id'], 'config' => $this->kb_config ) ); ?>
			</div>
		</div>	<?php
	}
	
	// Wizard: Step 2 - Ordering Page
	private function page_ordering() {         ?>

		<div id="epkb-wsb-step-2-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-ordering-ordering-preview"><?php // will be filled with ajax ?></div>
		</div>	<?php
	}

	// Wizard: Step 5 - Finish
	private function wizard_step_finish() {     ?>

		<div id="epkb-wsb-step-3-panel" class="epkb-wc-step-panel eckb-wizard-step-3" >
			<h2><?php _e( 'Final Step: Update Your Knowledge Base', 'echo-knowledge-base'); ?></h2>
			<p><?php _e( 'Click Apply to update your Knowledge Base configuration based on selection from previous Wizard screens.', 'echo-knowledge-base'); ?></p>
		</div>	<?php

		// display link to KB Main Page if any
		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );     ?>

		<div id="epkb-wsb-step-4-panel" class="epkb-wc-step-panel eckb-wizard-step-5" style="display: none">
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
				<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply"  data-wizard-type="ordering"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

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
	 * Show Wizard page options for article and category ordering
	 *
	 * @param $args
	 */
	public function article_category_ordering( $args ) {
		$kb_id = $args['id'];
		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$form = new EPKB_KB_Config_Elements();

		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'I want organize Categories and Articles', 'echo-knowledge-base' ),
			'class'             => 'eckb-wizard-features eckb-mm-mp-links-organize--organize eckb-wizard-accordion__body',
			'inputs' => array(
				'0' => $form->radio_buttons_vertical( $feature_specs['categories_display_sequence'] + array(
						'id'        => 'front-end-columns',
						'value'     => $kb_config['categories_display_sequence'],
						'current'   => $kb_config['categories_display_sequence'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-12',
						'input_class'       => 'config-col-12',
						'radio_class'       => 'config-col-12'
					) ),
				'1' => $form->radio_buttons_vertical( $feature_specs['articles_display_sequence'] + array(
						'id'        => 'front-end-columns',
						'value'     => $kb_config['articles_display_sequence'],
						'current'   => $kb_config['articles_display_sequence'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical' . ( ($kb_config['kb_main_page_layout'] == 'Grid') ? ' epkb-grid-option-hide-show' : ''),
						'main_label_class'  => 'config-col-12',
						'input_class'       => 'config-col-12',
						'radio_class'       => 'config-col-12'
					) ),
				'2' => $form->radio_buttons_vertical( $feature_specs['show_articles_before_categories'] + array(
						'value'     => $kb_config['show_articles_before_categories'],
						'current'   => $kb_config['show_articles_before_categories'],
						'input_group_class' => 'eckb-wizard-radio-btn-vertical',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12'
					) ),	
				)));           
	}

	/**
	 * THis configuration defines fields that are part of this wizard configuration related to search.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	public static $ordering_fields = array(
		'categories_display_sequence',
		'articles_display_sequence',
		'show_articles_before_categories',
		'sidebar_show_articles_before_categories',
	);
}
