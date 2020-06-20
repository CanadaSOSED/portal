<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration of THEME Wizard (default Wizard)
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard {

	var $kb_config = array();
	/** @var  EPKB_KB_Config_Elements */
	var $form;
	var $feature_specs = array();
	/** @var EPKB_HTML_Elements */
	var $html;
	var $templates;
	var $kb_id;
	var $is_existing_kb;
	var $is_blank_kb;

	function __construct() {
		$_POST['epkb-wizard-demo-data'] = true;
		new EPKB_KB_Wizard_Colors();
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
		}

		// ensure KB config is there
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $this->kb_id );
		if ( is_wp_error( $kb_config ) || empty($kb_config) || ! is_array($kb_config) || count($kb_config) < 100 ) {
			EPKB_Logging::add_log('Could not retrieve KB configuration (715)', $kb_config);
			echo '<div class="epkb-wizard-error-note">' . __('Error occurred', 'echo-knowledge-base') . ' (x1) ' . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}

		$this->is_blank_kb = self::is_blank_KB( $this->kb_id );
		if ( is_wp_error($this->is_blank_kb) ) {
			EPKB_Logging::add_log('Could not retrieve KB configuration (725)', $this->is_blank_kb);
			echo '<div class="epkb-wizard-error-note">' . __('Error occurred', 'echo-knowledge-base') . ' (x2). ' . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}

		$this->is_existing_kb = self::is_existing_KB( $this->kb_id );
		if ( is_wp_error($this->is_existing_kb) ) {
			EPKB_Logging::add_log('Could not retrieve KB configuration (735)', $this->is_existing_kb);
			echo '<div class="epkb-wizard-error-note">' . __('Error occurred', 'echo-knowledge-base') . ' (x3). ' . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}

		$this->templates = EPKB_KB_Wizard_Themes::get_all_themes();
		if ( empty($this->templates) ) {
			echo '<div class="epkb-wizard-error-note">' . __('Error occurred', 'echo-knowledge-base') . ' (x4). ' . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}		?>

		<div class="" id="epkb-config-wizard-content">
			<div class="epkb-config-wizard-inner">

				<!------- Wizard Header ------------>
				<div class="epkb-wizard-header">
					<div class="epkb-wizard-header__info">
						<h1 class="epkb-wizard-header__info__title">
							<?php _e( 'Theme Wizard', 'echo-knowledge-base' ); ?>
						</h1>
						<span class="epkb-wizard-header__info__current-kb">							<?php
							$kb_name = $this->kb_config['kb_name'];
							echo __( 'for', 'echo-knowledge-base' ) . ' ' . '<span id="epkb_current_kb_name" class="epkb-wizard-header__info__current-kb__name">' . esc_html( $kb_name ) . '</span>';  ?>
						</span>
					</div>
					<div class="epkb-wizard-button-link epkb-wizard-header__exit-wizard">
						<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . '&epkb-wizard-tab' ) ); ?>&page=epkb-kb-configuration">
							<?php _e( 'Exit Wizard', 'echo-knowledge-base' ); ?>
						</a><?php 
						
						if ( ! $this->is_blank_kb ) { ?>
							<div class="epkb-wizard-header__exit-wizard__label">
								<input type="checkbox" data-save_exit="<?php _e( 'Save and Exit', 'echo-knowledge-base' ); ?>" data-exit="<?php _e( 'Exit Wizard', 'echo-knowledge-base' ); ?>">
								<span><?php _e( 'Save before exit', 'echo-knowledge-base' ); ?></span>
							</div><?php 
						} ?>
					</div>
				</div>

				<!------- Wizard Status Bar ------->
				<div class="epkb-wizard-status-bar">
					<ul>
						<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Title & URL', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Theme', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wsb-step-3" class="epkb-wsb-step"><?php _e( 'Main Page Colors', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wsb-step-4" class="epkb-wsb-step"><?php _e( 'Article Page Colors', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wsb-step-5" class="epkb-wsb-step"><?php _e( 'Finish', 'echo-knowledge-base' ); ?></li>
					</ul>
				</div>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content">
					<?php self::show_loader_html(); ?>
					<?php $this->wizard_step_title_url(); ?>
					<?php $this->wizard_step_theme(); ?>
					<?php $this->wizard_step_main_page_colors(); ?>
					<?php $this->wizard_step_article_page_colors(); ?>
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

				<div class="eckb-bottom-notice-message"></div>
			</div>
		</div> <?php
	}

	// Wizard: Step 1 - Title & URL
	private function wizard_step_title_url() {

		if ( self::is_wizard_disabled() ) {   ?>
			<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1  epkb-wc-step-panel--active">
				<div><?php _e( 'Elegant Layouts, Advanced Search and Article Rating plugins need to be up to date. ', 'echo-knowledge-base' ); ?></div>
			</div>      <?php
			return;
		}   ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1  epkb-wc-step-panel--active epkb-wizard-theme-step-1 ">  <?php

			if ( $this->is_blank_kb ) {      ?>
				<h3 class="epkb-wsb-welcome-msg"><?php _e( 'Hi and welcome to the Knowledge Base Wizard. <br/>Let\'s start with naming your KB.', 'echo-knowledge-base' ); ?></h3>		<?php
			}

			$this->html->text(
				array(
					'label'             => __('Knowledge Base Title', 'echo-knowledge-base'),
					'placeholder'       => __('Knowledge Base', 'echo-knowledge-base'),
					'main_tag'          => 'div',
					'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-name',
					'value'             => $this->kb_config['kb_name']
				)
			);      ?>
			<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p class="epkb-wizard-input-desc"><?php 
						if ( $this->is_blank_kb ) {
							_e( 'Name and page title of your knowledge base<br/>Examples: Knowledge Base, Help, Support', 'echo-knowledge-base' ); 
						} else {
							_e( 'Name of your knowledge base<br/>Examples: Knowledge Base, Help, Support', 'echo-knowledge-base' ); 
						}	?>
					</p>
				</div>
			</div>			<?php

			// only initial KB slug can be set here
			if ( $this->is_blank_kb ) {

				$this->html->text(
					array(
						'label'             => __('Knowledge Base Slug', 'echo-knowledge-base'),
						'placeholder'       => 'knowledge-base',
						'main_tag'          => 'div',
						'readonly'          => $this->is_existing_kb,
						'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-slug',
						'value'             => _x( 'knowledge-base', 'initial SLUG for KB', 'echo-knowledge-base' ),
					)
				);      ?>
				<div class="epkb-wizard-row-form-input">
					<div class="epkb-wizard-col2">
						<p id="epkb-wizard-slug-error">
							<?php _e('The slug should not contain full KB URL.', 'echo-knowledge-base'); ?>
						</p>
						<p class="epkb-wizard-input-desc"><?php _e( 'Enter a KB slug that will be part of your full knowledge base URL.<br/>Example of KB URL: &nbsp;&nbsp;www.your-domain.com/your-KB-slug', 'echo-knowledge-base' ); ?>
						</p>
					</div>

				</div>
				<?php
			}

			// if we have menus and menus without link
			$menus = $this->kb_menus_without_item();

			if ( is_array($menus) && ! empty($menus) ) {      ?>

				<div class="input_group epkb-wizard-row-form-input epkb-wizard-menus" >
					<label><?php _e( 'Add KB to Website Menu', 'echo-knowledge-base' ); ?></label>
					<ul>	<?php
						foreach ($menus as $menu_id => $menu_title) {
							$this->html->checkbox( array(
								'name'              => 'epkb_menu_' . $menu_id,
								'label'             => $menu_title,
								'input_group_class' => 'epkb-menu-checkbox',
								'value'             => 'off'
							) );
						}           ?>
					</ul>
				</div>
				<div class="epkb-wizard-row-form-input">
					<div class="epkb-wizard-col2">
						<p class="epkb-wizard-input-desc"><?php _e( 'Choose the website menu(s) where users will access the Knowledge Base. You can change it at any time in WordPress -> Appearance -> Menus.', 'echo-knowledge-base' ); ?></p>
					</div>
				</div><?php

			}       ?>
		</div>	<?php
	}

	// Wizard: Step 2 - Theme
	private function wizard_step_theme() {		?>

		<div id="epkb-wsb-step-2-panel" class="epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-wizard-theme-preview">

				<!-- THEME BUTTONS -->
				<div class="epkb-wizard-theme-tab-container">
					<input type="hidden" id="_wpnonce_wizard_templates" name="_wpnonce_wizard_templates" value="<?php echo wp_create_nonce( "_wpnonce_wizard_templates" ); ?>"/>
					<ul>						<?php

						$first_theme_id = $this->is_blank_kb ? 'theme_standard' : 'current';
						
						// add default/current theme ?>
						<li id="epkb-wt-theme-<?php echo $first_theme_id; ?>" class="epkb-wt-tab epkb-wt--active epkb-wt--current epkb-wt-theme-first" data-template_id="<?php echo $first_theme_id; ?>" >
							<div class="epkb-wt-theme-first__icon epkbfa epkbfa-cog"></div>

							<div class="epkb-wt-theme-first__name">
								<span class="epkb-wt-tab__name"><?php
								echo $this->is_blank_kb ? __( 'Default', 'echo-knowledge-base' ) : __('Saved Configuration', 'echo-knowledge-base'); ?></span>
								<div class="epkb-wt-theme-first__desc">								<?php
									echo $this->is_blank_kb ? __('Default KB configuration', 'echo-knowledge-base') : __('Your last saved KB configuration as seen on the front end.', 'echo-knowledge-base'); ?>
								</div>
							</div>

						</li> <?php 
							
						// add categories get_divided_templates
						$divided_templates = $this->get_divided_templates();
						
						if ( $divided_templates ) { 	?>
							<li class="eckb-wizard-accordion"> <?php
								foreach ( $divided_templates as $title => $group ) { ?>
									<div class="epkb-wt-tc__themes-group eckb-wizard-accordion__body-content">
										<div class="epkb-wt-tc__themes-group__header eckb-wizard-option-heading">											<?php

											// Setup Theme Group Icons.
											switch ( $title ) {
												case 'Basic Layout':
												case __( 'Basic Layout', 'echo-knowledge-base' ):
													$theme_group_icon = 'epkbfa-sitemap';
													break;
												case 'Tabs Layout':
												case __( 'Tabs Layout', 'echo-knowledge-base' ):
													$theme_group_icon = 'epkbfa-folder-o';
													break;
												case 'Category Focused Layout':
												case __( 'Category Focused Layout', 'echo-knowledge-base' ):
													$theme_group_icon = 'epkbfa-list-ul';
													break;
												case 'Grid Layout':
												case __( 'Grid Layout', 'echo-knowledge-base' ):
													$theme_group_icon = 'epkbfa-th';
													break;
												case 'Sidebar Layout':
												case __( 'Sidebar Layout', 'echo-knowledge-base' ):
													$theme_group_icon = 'epkbfa-sort-amount-asc';
													break;
												default:
													$theme_group_icon = 'epkbfa-align-justify';
													break;
											}											?>

											<h4>
												<span class="epkb-wt-tc__themes-group__header__icon epkbfa <?php echo $theme_group_icon; ?>"></span>
												<span class="epkb-wt-tc__themes-group__header__title"><?php _e( $title, 'echo-knowledge-base' ); ?></span>
												<span class="epkbfa epkbfa-caret-right"></span>
												<span class="epkbfa epkbfa-caret-down"></span>
											</h4>
										</div>	
										<ul class="epkb-wt-tc__themes-group__list config-input-group"><?php
											foreach ( $group as $template_id => $template ) { ?>
												<li id="epkb-wt-theme-<?php echo $template_id; ?>" class="epkb-wt-tab" data-template_id="<?php echo $template_id; ?>" >
													<div class="epkb-wt-tab__name"><?php echo $template['kb_name']; ?></div>
													<div class="epkb-wt-tab__desc"><?php echo $template['theme_desc']; ?></div>
												</li>											<?php 
											} ?>
										</ul>
									</div>
									<?php
								} ?>
							</li> <?php
						}					?>
					</ul>
				</div>

				<!-- THEME PREVIEW -->
				<div class="epkb-wizard-theme-panel-container">					<?php
					self::show_demo_articles_categories_alert();
					
					$first_theme_id = $this->is_blank_kb ? 'theme_standard' : 'current';
					$first_theme_config = $this->is_blank_kb ? EPKB_KB_Wizard_Themes::get_theme( 'theme_standard', $this->kb_config["article-structure-version"] ) : $this->kb_config; ?>
					<div id="epkb-wt-theme-<?php echo $first_theme_id; ?>-panel" class="epkb-wt-panel epkb-wt-panel--active">	<?php

							$handler = new EPKB_KB_Config_Page( $first_theme_config );
							$handler->display_kb_main_page_layout_preview();
							$theme_data = EPKB_KB_Wizard_Themes::get_theme_data( $first_theme_config );
							echo '<input type="hidden" class="theme-values" value="' . $theme_data . '">'; ?>

					</div> <?php 
					foreach ( $this->templates as $template_id => $template ) {   ?>
						<div id="epkb-wt-theme-<?php echo $template_id; ?>-panel" class="epkb-wt-panel">	<?php

							$theme_kb_config = EPKB_KB_Wizard_Themes::get_theme( $template_id, $this->kb_config["article-structure-version"] );
							if ( is_wp_error($theme_kb_config) || empty($theme_kb_config) ) {
								echo '<div class="epkb-wizard-error-note">' . __('Error occurred', 'echo-knowledge-base') . ' (x42). ' . EPKB_Utilities::contact_us_for_support() . '</div>';
								return;
							}

							$handler = new EPKB_KB_Config_Page( $theme_kb_config );
							$handler->display_kb_main_page_layout_preview();

							$theme_data = EPKB_KB_Wizard_Themes::get_theme_data( $theme_kb_config );
							if ( is_wp_error($theme_kb_config) || empty($theme_kb_config) ) {
								echo '<div class="epkb-wizard-error-note">' . __('Error occurred', 'echo-knowledge-base') . ' (x43). ' . EPKB_Utilities::contact_us_for_support() . '</div>';
								return;
							}

							echo '<input type="hidden" class="theme-values" value="' . $theme_data . '">'; ?>
						</div>	<?php
					}					?>
				</div>

			</div>
		</div>	<?php
	}

	// Wizard: Step 3 - Main Page Colors
	private function wizard_step_main_page_colors() {

		// auto-determine whether we need sidebar or let user override it to be displayed
		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $this->kb_config['article_sidebar_component_priority'] );
		$is_left_sidebar_on = EPKB_Articles_Setup::is_left_sidebar_on( $this->kb_config, $sidebar_priority );
		$is_right_sidebar_on = EPKB_Articles_Setup::is_right_sidebar_on( $this->kb_config, $sidebar_priority );

		foreach ( $this->templates as &$theme ) {
			$theme['article-left-sidebar-desktop-width-v2'] = $is_left_sidebar_on ? '20' : '0';
			$theme['article-left-sidebar-tablet-width-v2']  = $is_left_sidebar_on ? '20' : '0';
			$theme['article-content-desktop-width-v2'] = $is_left_sidebar_on && $is_right_sidebar_on ? '60' : '80';
			$theme['article-content-tablet-width-v2'] = $is_left_sidebar_on && $is_right_sidebar_on ? '60' : '80';
			$theme['article-right-sidebar-desktop-width-v2'] = $is_right_sidebar_on ? '20' : '0';
			$theme['article-right-sidebar-tablet-width-v2'] = $is_right_sidebar_on ? '20' : '0';
			$theme['article_sidebar_component_priority'] = $sidebar_priority;
		}		?>

		<div id="epkb-wsb-step-3-panel" class="epkb-wc-step-panel eckb-wizard-step-3">
			<div class="epkb-wizard-color-preview">
				<div class="epkb-wizard-color-preset-container">
					<ul>
						<li id="epkb-wc-preset-0" class="epkb-wcp-tab epkb-preset-button epkb-wcp-current-settings" data-colors="">
							<div class="epkb-wcp-current-settings__icon epkbfa epkbfa-cog"></div>
							<div class="epkb-wcp-current-settings__name"><?php _e( 'Current Theme', 'echo-knowledge-base' ); ?></div>
						</li>
						<li id="epkb-wc-preset-1" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 1 ); ?>"><?php _e( 'Yellow', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-3" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 3 ); ?>"><?php _e( 'Light Blue', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 4 ); ?>"><?php _e( 'Medium Blue', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 5 ); ?>"><?php _e( 'Dark Blue', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-3" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 6 ); ?>"><?php _e( 'Light Green', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 7 ); ?>"><?php _e( 'Medium Green', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 8 ); ?>"><?php _e( 'Dark Green', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-3" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 9 ); ?>"><?php _e( 'Light Red', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 10 ); ?>"><?php _e( 'Medium Red', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 11 ); ?>"><?php _e( 'Dark Red', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-3" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 12 ); ?>"><?php _e( 'Light Gray', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 13 ); ?>"><?php _e( 'Medium Gray', 'echo-knowledge-base' ); ?></li>
						<li id="epkb-wc-preset-4" class="epkb-wcp-tab epkb-preset-button" data-colors="<?php echo EPKB_KB_Wizard_Color_Presets::get_template_data( 14 ); ?>"><?php _e( 'Dark Gray', 'echo-knowledge-base' ); ?></li>
					</ul>
				</div>
				<div id="eckb-wizard-main-page-preview" class="epkb-wizard-theme-preview-container eckb-wizard-help">
					
				</div>
				<div class="epkb-wizard-color-selection-container eckb-wizard-accordion">
					<?php $this->wizard_section( 'epkb-wizard-main-page-color-selection-container', array( 'id' => $this->kb_config, 'config' => $this->kb_config ) ); ?>
				</div>
			</div>
		</div>	<?php
	}

	// Wizard: Step 4 - Article Page Colors
	private function wizard_step_article_page_colors() {        ?>

		<div id="epkb-wsb-step-4-panel" class="epkb-wc-step-panel eckb-wizard-step-4">
			<div class="epkb-wizard-color-preview">
				<div id="eckb-wizard-article-page-preview" class="epkb-wizard-theme-preview-container eckb-wizard-help">	</div><?php      // filled with Ajax ?>
				<div class="epkb-wizard-color-selection-container eckb-wizard-accordion">
					<?php $this->wizard_section( 'epkb-wizard-article-page-color-selection-container', array( 'id' => $this->kb_config, 'config' => $this->kb_config ) ); ?>
				</div>
			</div>
		</div>	<?php
	}

	// Wizard: Step 5 - Finish
	private function wizard_step_finish() {
		
		if ( $this->is_blank_kb ) {
			$page_title = __( 'Final Step: Generate New Knowledge Base', 'echo-knowledge-base');
			$page_description = __( 'Click Apply to generate your custom styled Knowledge Base.', 'echo-knowledge-base');
		} else {
			$page_title = __( 'Final Step: Update Your Knowledge Base', 'echo-knowledge-base');
			$page_description = __( 'Click Apply to update your Knowledge Base configuration based on selection from previous Wizard screens.', 'echo-knowledge-base');
		}	?>

		<div id="epkb-wsb-step-5-panel" class="epkb-wc-step-panel eckb-wizard-step-5" >

				<h2><?php echo $page_title; ?></h2>
				<p><?php echo $page_description; ?></p>

		</div>	<?php

		// display link to KB Main Page if any
		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );     ?>

		<div id="epkb-wsb-step-6-panel" class="epkb-wc-step-panel eckb-wizard-step-5" style="display: none">
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
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-book"></span>
				</a>
			</div>
		</div>
			<?php /*
			$this->html->text(
				array(
					'label'             => 'Sign up for the Newsletter',
					'placeholder'       => 'Email Address',
					'main_tag'          => 'div',
					'input_group_class' => 'epkb-wizard-row-form-input',
				)
			); ?>
			<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p class="epkb-wizard-input-desc">We're here for you - get tips, product updates, and news!</p>
				</div>
			</div>


		</div>	<?php */
	}

	//Wizard: Previous / Next Buttons / Apply Buttons
	public function wizard_buttons() {
		// TODO remove
		if (  self::is_wizard_disabled() ) {
			return;
		}   ?>

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
				<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply" data-wizard-type="theme"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

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

    public static function is_wizard_disabled() {

		if ( defined( 'WIZARD_DEBUG' ) && WIZARD_DEBUG ) return false;

		if ( defined( 'E'.'LAY_PLUGIN_NAME' ) && version_compare(Echo_Elegant_Layouts::$version, '2.2.0', '<' ) ) {
			return true;
		}

	    if ( defined( 'A'.'SEA_PLUGIN_NAME' ) && version_compare(Echo_Advanced_Search::$version, '2.7.0', '<' ) ) {
		    return true;
	    }

	    if ( defined( 'E'.'PRF_PLUGIN_NAME' ) && version_compare(Echo_Article_Rating_And_Feedback::$version, '1.1.0', '<' ) ) {
		    return true;
	    }

		return false;
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
	 * Find menu items with a link to KB
	 *
	 * @return array|bool - true on ERROR, 
	 *                      false if found a menu with KB link
	 *                      empty array if no menu exists
	 *                      non-empty array for existing menus.
	 */
	private function kb_menus_without_item() {

		$menus = wp_get_nav_menus();
		if ( empty($menus) || ! is_array($menus) ) {
			return array();
		}

		$kb_main_pages_info = EPKB_KB_Handler::get_kb_main_pages( $this->kb_config );

		// check if we have any menu item with KB page
		$menu_without_kb_links = array();
		foreach ( $menus as $menu ) {

			// does menu have any menu items?
			$menu_items = wp_get_nav_menu_items($menu);
			if ( empty($menu_items) && ! is_array($menu_items) )  {
				continue;
			}

			foreach ( $menu_items as $item ) {

				// true if we already have KB link in menu
				if ( $item->object == 'page' && isset( $kb_main_pages_info[$item->object_id]) ) {
					return false; // use this string to show menus without KB link only if ALL menus have no KB links
					//continue 2; // use this string to show menus without KB link always
				}
			}

			$menu_without_kb_links[$menu->term_id] = $menu->name;
		}
		
		/* don't need this if ( ! count( $menu_without_kb_links ) ) {
			// we have menus but in all menus we have a link 
			return false;
		} */
		
		return $menu_without_kb_links;
	}
	
	// return templates divided by category
	private function get_divided_templates() {
		
		$divided_templates = array();
		$other_category = __( 'Other', 'echo-knowledge-base' );
		
		if ( is_array( $this->templates) ) {
			foreach ( $this->templates as $template_id => $template ) {
				if ( isset( $template['theme_category'] ) ) {
					$divided_templates[$template['theme_category']][$template_id] = $template;
				} else {
					$divided_templates[$other_category][$template_id] = $template;
				}
			}
		}
		
		return $divided_templates;
	}
	
	public static function show_demo_articles_categories_alert() {

		$hide_demo_alert = EPKB_Utilities::get_wp_option('epkb_hide_demo_content_alert', false);

		if ( ! empty($hide_demo_alert) ) {
			return;
		} ?>
		
		<div class="epkb-daca">

			<div class="epkb-daca__icon-container">
				<div class="epkb-daca__icon epkbfa epkbfa-book"></div>
			</div>

			<div class="epkb-daca__text-contaienr">
				<div class="epkb-daca__text"><?php _e( "We have created demo Categories and Articles so that you can see how it will look while making changes to the configuration.", 'echo-knowledge-base' ); ?></div>
				<div class="epkb-daca__button">
					<button><?php _e( 'Close this Message', 'echo-knowledge-base' ); ?></button>
				</div>
			</div>

		</div> <?php 
	}
	
	public static function show_loader_html() { ?>
		
		<div class="epkb-admin-dialog-box-loading">
			<div class="epkb-admin-dbl__header">
				<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>
				<div class="<div class="epkb-admin-dbl-text"><?php _e( 'Loading...', 'echo-knowledge-base' ); ?></div>
			</div>
		</div>
		<div class="epkb-admin-dialog-box-overlay"></div> <?php
	}
	
	/**
     * Get demo icons type based on user's categories icons
     * @param $kb_id
     * @return string
    */
	public static function get_demo_icons_type( $kb_id ) {
		$font_terms_count = 0;
		$image_terms_count = 0;
		$no_icon_terms_count = 0;

		$kb_terms = EPKB_Categories_DB::get_top_level_categories( $kb_id );
		if ( empty($kb_terms) ) {
		    return 'font';  // default
		}

        $categories_icons = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Icons::CATEGORIES_ICONS, array(), true );

        foreach ( $kb_terms as $term ) {
            $icon = EPKB_KB_Config_Category::get_category_icon( $term->term_id, $categories_icons );
            if ( $icon['type'] == 'font' ) {
                $font_terms_count ++;
            } else if ( $icon['type'] == 'image' ) {
                $image_terms_count ++;
            } else {
                $no_icon_terms_count ++;
            }
        }

        // if there are no icons and at least one image return image format
        if ( empty($font_terms_count) && $image_terms_count > 1 ) {
            return 'image';
        }

		return 'font'; // default
	}

	/**
	 * Is this existing KB?
	 * @param $kb_id
	 * @return bool|WP_Error
	 */
	public static function is_existing_KB( $kb_id ) {
		$is_blank = self::is_blank_KB( $kb_id );
		if ( is_wp_error($is_blank) ) {
			return $is_blank;
		}

		return ! $is_blank && EPKB_Articles_DB::get_count_of_all_kb_articles( $kb_id, false ) >= 10;
	}

	/**
	 * Check if KB needs to be setup
	 * @param $kb_id
	 * @return bool|WP_Error
	 */
	public static function is_blank_KB( $kb_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$kb_id = ( $kb_id === EPKB_KB_Config_DB::DEFAULT_KB_ID ) ? $kb_id : EPKB_Utilities::sanitize_get_id( $kb_id );
		if ( is_wp_error($kb_id) ) {
			return $kb_id;
		}

		// retrieve specific KB configuration
		$kb_config = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '" . EPKB_KB_Config_DB::KB_CONFIG_PREFIX . $kb_id . "'" );
		if ( ! empty($kb_config) ) {
			$kb_config = maybe_unserialize( $kb_config );
		}

		if ( empty($kb_config) || ! is_array($kb_config) || count($kb_config) < 100 ) {
			EPKB_Logging::add_log("Did not find KB configuration (DB331).", $kb_id);
			return new WP_Error('DB331', __( "Did not find KB configuration", 'echo-knowledge-base' ));
		}

		return $kb_config['status'] == EPKB_KB_Status::BLANK;
	}

}
