<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * THEME WIZARD - color fields
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Colors {

	function __construct() {
		add_action( 'epkb-wizard-main-page-color-selection-container',      array( $this, 'wizard_main_page_colors_sidebar'), 10, 3 );
		add_action( 'epkb-wizard-article-page-color-selection-container',   array( $this, 'wizard_article_page_colors'), 10, 3 );
	}

	/**
	 * Show Wizard page colors for Main Page
	 *
	 * @param $args
	 *
	 * @noinspection PhpUnused*/
	public function wizard_main_page_colors_sidebar( $args ) {

		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();
		
		// SECTION
		$arg1_sub_category = $feature_specs['section_category_font_color'] + array( 
			'value' => $kb_config['section_category_font_color'], 
			'current' => $kb_config['section_category_font_color'], 
			'class' => 'ekb-color-picker',
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-category-level-2-3__cat-name, .eckb-wizard-step-3 .epkb-category-level-2-3__cat-name a',
				'style_name' => 'color'
			)
		);
		
		$arg2_sub_category = $feature_specs['section_category_icon_color'] + array( 
			'value' => $kb_config['section_category_icon_color'], 
			'current' => $kb_config['section_category_icon_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-category-level-2-3>i',
				'style_name' => 'color'
			)
		);

		$arg1_category_box_heading = $feature_specs['section_head_font_color'] + array( 
			'value' => $kb_config['section_head_font_color'],
			'current' => $kb_config['section_head_font_color'],
			'class' => 'ekb-color-picker',
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .section-head .epkb-cat-name, .eckb-wizard-step-3 .section-head .epkb-cat-name a, .eckb-wizard-step-3  div>.epkb-category-level-1',
				'style_name' => 'color'
			)
		);
		
		$arg2_category_box_heading = $feature_specs['section_head_background_color']  + array( 
			'value' => $kb_config['section_head_background_color'],
			'current' => $kb_config['section_head_background_color'],
			'class' => 'ekb-color-picker',
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-top-category-box .section-head',
				'style_name' => 'background-color'
			)
		);

		$arg1_article_list = $feature_specs['section_body_background_color'] + array( 
			'value' => $kb_config['section_body_background_color'],
			'current' => $kb_config['section_body_background_color'],
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-top-category-box',
				'style_name' => 'background-color'
			)
		);
		
		$arg2_article_list = $feature_specs['section_border_color'] + array( 
			'value' => $kb_config['section_border_color'],
			'current' => $kb_config['section_border_color'],
			'class' => 'ekb-color-picker',
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-top-category-box',
				'style_name' => 'border-color'
			)
		);

		// ARTICLES
		$arg1_articles = $feature_specs['article_font_color'] + array( 
			'value' => $kb_config['article_font_color'], 
			'current' => $kb_config['article_font_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .eckb-article-title',
				'style_name' => 'color'
			)
		);
		$arg2_articles = $feature_specs['article_icon_color'] + array( 
			'value' => $kb_config['article_icon_color'], 
			'current' => $kb_config['article_icon_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color' ,
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .eckb-article-title>i',
				'style_name' => 'color'
			)
		);
		
		// SEARCH BOX
		$arg1_input_text_field = $feature_specs['search_text_input_background_color'] + array( 
			'value' => $kb_config['search_text_input_background_color'], 
			'current' => $kb_config['search_text_input_background_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-search-box input[type=text]',
				'style_name' => 'background'
			)
		);
			
		$arg2_input_text_field = $feature_specs['search_text_input_border_color']     + array( 
			'value' => $kb_config['search_text_input_border_color'], 
			'current' => $kb_config['search_text_input_border_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-search-box input[type=text]',
				'style_name' => 'border-color'
			)
		);

		$arg1_button = $feature_specs['search_btn_background_color']  + array( 
			'value' => $kb_config['search_btn_background_color'],
			'current' => $kb_config['search_btn_background_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-search-box button',
				'style_name' => 'background'
			)
		);
			
		$arg2_button = $feature_specs['search_btn_border_color'] + array( 
			'value' => $kb_config['search_btn_border_color'],
			'current' => $kb_config['search_btn_border_color'],
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-search-box button',
				'style_name' => 'border-color'
			)
		);

		do_action( 'epkb_theme_wizard_before_main_page_colors', $kb_config['id'] );

		// SEARCH BOX
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Search Box', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body',
			'depends'        => array(
				'hide_when' => array(
					'advanced_search_mp_show_top_category' => 'on|off',  // true if Advanced Search is enabled
					'kb_main_page_layout' => 'Grid|Sidebar'
				)
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['search_title_font_color'] + array(
						'value'             => $kb_config['search_title_font_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-3 .epkb-doc-search-container__title',
							'style_name' => 'color',
						)
					) ),
				'1' => $form->text( $feature_specs['search_background_color'] + array(
						'value'             => $kb_config['search_background_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-3 .epkb-doc-search-container',
							'style_name' => 'background-color'
						)
					) ),
				'2' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __( 'Input Text Field', 'echo-knowledge-base' ),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_input_text_field, $arg2_input_text_field ),
				'3' => $form->text_fields_horizontal( array(
					'id'                => 'button',
					'input_class'       => 'ekb-color-picker',
					'label'             => __( 'Search Button', 'echo-knowledge-base' ),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_button, $arg2_button ),
			)
		));

		// TABS 
		$this->get_tabs_color_set( $kb_config, $feature_specs );
		
		// CATEGORIES
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Categories', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body epkb-categories',
			'depends'          => array(
				'hide_when' => array(
					'kb_main_page_layout' => 'Grid|Sidebar'
				)
			),
			'inputs'            => array (
				'0' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __('Category Box', 'echo-knowledge-base'),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_category_box_heading, $arg2_category_box_heading ),
				'3' => $form->text( $feature_specs['section_divider_color'] + array(
						'value'             => $kb_config['section_divider_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-3 .epkb-top-category-box .section-head',
							'style_name' => 'border-bottom-color',
							'example_image'     =>      'theme-wizard/wizard-screenshot-main-page-divider.jpg'
						)
					) ),
				'4' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __( 'Sub Category', 'echo-knowledge-base' ),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_sub_category, $arg2_sub_category ),
			)
		));
		
		// CATEGORIES
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Top Categories: Icon', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body epkb-categories',
			'depends'          => array(
				'hide_when' => array(
					'section_head_category_icon_location' => 'no_icons',
					'theme_name' => 'theme_image',
					'kb_main_page_layout' => 'Sidebar|Grid'
				)
			),
			'inputs'            => array (
				
				'1' => $form->text( $feature_specs['section_head_category_icon_color'] + array(
						'value'             => $kb_config['section_head_category_icon_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-3 .section-head .epkb-cat-icon',
							'style_name' => 'color'
						)
					) ),
			)
		));
		
				// CATEGORIES
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Top Categories: Description', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body epkb-categories',
			'depends'          => array(
				'show_when' => array(
					'section_desc_text_on' => 'on'
				)
			),
			'inputs'            => array (
				'0' => $form->text( $feature_specs['section_head_description_font_color'] + array(
						'value'             => $kb_config['section_head_description_font_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-3 .epkb-category-level-1+p',
							'style_name' => 'color'
						)
					) ),
			)
		));
		
		// ARTICLES
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Articles', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body',
			'depends'          => array(
				'show_when' => array(
					'kb_main_page_layout' => 'Basic|Tabs|Categories' )
			),
			'inputs'            => array (
				'1' => $form->text( $feature_specs['background_color'] + array(
						'value'             => $kb_config['background_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-3 #epkb-content-container',
							'style_name' => 'background-color'
						)
					) ),
				'2' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __('Article Title', 'echo-knowledge-base'),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_articles, $arg2_articles ),
				'3' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __('Article List', 'echo-knowledge-base'),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_article_list, $arg2_article_list ),
			)
		));
		
		do_action( 'epkb_theme_wizard_after_main_page_colors', $kb_config['id'] );
	}

	private function get_tabs_color_set( $kb_config, $feature_specs ) {

		$form = new EPKB_KB_Config_Elements();

		$arg1_active_tab = $feature_specs['tab_nav_active_font_color'] + array( 
			'value' => $kb_config['tab_nav_active_font_color'], 
			'current' => $kb_config['tab_nav_active_font_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => 'tab_nav_active_font_color',
			)
		);
		
		$arg2_active_tab = $feature_specs['tab_nav_active_background_color'] + array( 
			'value' => $kb_config['tab_nav_active_background_color'], 
			'current' => $kb_config['tab_nav_active_background_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => 'tab_nav_active_background_color',
			)
		);
		
		$arg3_active_tab = $feature_specs['tab_nav_border_color'] + array( 
			'value' => $kb_config['tab_nav_border_color'], 
			'current' => $kb_config['tab_nav_border_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => 'tab_nav_border_color',
			)
		);

		$arg1_inactive_tabs = $feature_specs['tab_nav_font_color'] + array( 
			'value' => $kb_config['tab_nav_font_color'], 
			'current' => $kb_config['tab_nav_font_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-nav-tabs .epkb-category-level-1, .eckb-wizard-step-3 .epkb-nav-tabs .epkb-category-level-1+p',
				'style_name' => 'color'
			)
		); 
		
		$arg2_inactive_tabs = $feature_specs['tab_nav_background_color'] + array( 
			'value' => $kb_config['tab_nav_background_color'], 
			'current' => $kb_config['tab_nav_background_color'], 
			'class' => 'ekb-color-picker',
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => '.eckb-wizard-step-3 .epkb-main-nav, .eckb-wizard-step-3 .epkb-nav-tabs',
				'style_name' => 'background-color'
			)
		);

		// TABS - Colors
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Tabs', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body tabs-colors',
			'depends'          => array(
				'show_when' => array(
					'kb_main_page_layout' => 'Tabs'
				)
			),
			'inputs'            => array(
				'0' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __('Active Tab', 'echo-knowledge-base'),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_active_tab, $arg2_active_tab, $arg3_active_tab ),
				'1' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __('Inactive Tabs', 'echo-knowledge-base'),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_inactive_tabs, $arg2_inactive_tabs )
			)
		));
	}

	/**
	 * Wizard colors for Article Page
	 * @param $args
	 */
	public function wizard_article_page_colors( $args ) {

		$kb_config = $args['config'];
		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();

		// FEATURES - TOC - COLORS
		$arg1_active_heading = $feature_specs['article_toc_active_bg_color'] + array( 
			'value' => $kb_config['article_toc_active_bg_color'],
		    'current' => $kb_config['article_toc_active_bg_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => 'article_toc_active_bg_color',
			)
		);
		
		$arg2_active_heading = $feature_specs['article_toc_active_text_color'] + array( 
			'value' => $kb_config['article_toc_active_text_color'],
		    'current' => $kb_config['article_toc_active_text_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => 'article_toc_active_text_color',
			)
		);
		
		$arg1_cursor_hover = $feature_specs['article_toc_cursor_hover_bg_color'] + array( 
			'value' => $kb_config['article_toc_cursor_hover_bg_color'],
			'current' => $kb_config['article_toc_cursor_hover_bg_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => 'article_toc_cursor_hover_bg_color',
				'style_name' => 'background-color'
			)
		);
		
		$arg2_cursor_hover = $feature_specs['article_toc_cursor_hover_text_color'] + array( 
			'value' => $kb_config['article_toc_cursor_hover_text_color'],
			'current' => $kb_config['article_toc_cursor_hover_text_color'], 
			'class' => 'ekb-color-picker', 
			'text_class' => 'eckb-wizard-single-color',
			'data' => array(
				'wizard_input' => '1',
				'target_selector' => 'article_toc_cursor_hover_text_color',
				'style_name' => 'color'
			)
		);

		do_action( 'epkb_theme_wizard_before_article_page_colors', $kb_config['id'] );

		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Table of Content', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body',
			'inputs'            => array(
				'1' => $form->text( $feature_specs['article_toc_text_color'] + array(
						'value'             => $kb_config['article_toc_text_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-article-toc__inner a',
							'style_name' => 'color',
							//'example_image' => 'search_box_title.png'
						)
					) ),
				'2' => $form->text( $feature_specs['article_toc_background_color'] + array(
						'value'             => $kb_config['article_toc_background_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-article-toc__inner',
							'style_name' => 'background-color'
						)
					) ),
				'3' => $form->text( $feature_specs['article_toc_border_color'] + array(
						'value'             => $kb_config['article_toc_border_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-article-toc__inner',
							'style_name' => 'border-color'
						)
					) ),
				'4' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __('Active Heading', 'echo-knowledge-base'),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_active_heading, $arg2_active_heading ),
				'5' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_class'       => 'ekb-color-picker',
					'label'             => __('Cursor Hover', 'echo-knowledge-base'),
					'input_group_class' => 'epkb-wizard-dual-color',
				), $arg1_cursor_hover, $arg2_cursor_hover )
			)
		));

		// BACK NAVIGATION - COLORS
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Back Navigation', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->text( $feature_specs['back_navigation_text_color'] + array(
						'value'             => $kb_config['back_navigation_text_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-navigation-back .eckb-navigation-button a, .eckb-wizard-step-4 .eckb-navigation-back .eckb-navigation-button',
							'style_name' => 'color'
						)
					) ),
				'1' => $form->text( $feature_specs['back_navigation_bg_color'] + array(
						'value'             => $kb_config['back_navigation_bg_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-navigation-back .eckb-navigation-button a, .eckb-wizard-step-4 .eckb-navigation-back .eckb-navigation-button',
							'style_name' => 'background-color'
						)
					) ),
				'2' => $form->text( $feature_specs['back_navigation_border_color'] + array(
						'value'             => $kb_config['back_navigation_border_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-navigation-back .eckb-navigation-button a, .eckb-wizard-step-4 .eckb-navigation-back .eckb-navigation-button',
							'style_name' => 'border-color'
						)
					) )
			)
		));

		// FEATURES - Breadcrumb - COLORS
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Breadcrumb', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->text( $feature_specs['breadcrumb_text_color'] + array(
						'value'             => $kb_config['breadcrumb_text_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-breadcrumb-link span:not(.eckb-breadcrumb-link-icon)',
							'style_name' => 'color'
						)
					) )
			)
		));
		
		// FEATURES - Sidebar Background Color
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Layout', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body',
			'depends'        => array(
				'hide_when' => array(
					'article-structure-version' => 'version-1',
				)
			),
			'inputs'            => array(
				'0' => $form->text( $feature_specs['article-left-sidebar-background-color-v2'] + array(
						'value'             => $kb_config['article-left-sidebar-background-color-v2'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 #eckb-article-left-sidebar',
							'style_name' => 'background-color'
						)
					) ),
				'1' => $form->text( $feature_specs['article-content-background-color-v2'] + array(
						'value'             => $kb_config['article-content-background-color-v2'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 #eckb-article-content',
							'style_name' => 'background-color'
						)
					) ),
				'2' => $form->text( $feature_specs['article-right-sidebar-background-color-v2'] + array(
						'value'             => $kb_config['article-right-sidebar-background-color-v2'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 #eckb-article-right-sidebar',
							'style_name' => 'background-color'
						)
					) ),
			)
		));
	
		// Category Box 
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __('Categories List', 'echo-knowledge-base'),
			'class'             => 'eckb-wizard-colors eckb-wizard-accordion__body',
			'depends'        => array(
				'show_when' => array(
					'kb_main_page_layout' => EPKB_KB_Config_Layout_Categories::LAYOUT_NAME
				)
			),
			'inputs'            => array(
				'0' => $form->text( $feature_specs['category_box_title_text_color'] + array(
						'value'             => $kb_config['category_box_title_text_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-acll__title',
							'style_name' => 'color' //TODO change
						)
					) ),
				'1' => $form->text( $feature_specs['category_box_container_background_color'] + array(
						'value'             => $kb_config['category_box_container_background_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-article-cat-layout-list',
							'style_name' => 'background-color' //TODO change
						)
					) ),
				'2' => $form->text( $feature_specs['category_box_category_text_color'] + array(
						'value'             => $kb_config['category_box_category_text_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-acll__cat-item__name',
							'style_name' => 'color' //TODO change
						)
					) ),
				'3' => $form->text( $feature_specs['category_box_count_background_color'] + array(
						'value'             => $kb_config['category_box_count_background_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-acll__cat-item__count',
							'style_name' => 'background-color' //TODO change
						)
					) ),
				'4' => $form->text( $feature_specs['category_box_count_text_color'] + array(
						'value'             => $kb_config['category_box_count_text_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-acll__cat-item__count',
							'style_name' => 'color' //TODO change
						)
					) ),
				'5' => $form->text( $feature_specs['category_box_count_border_color'] + array(
						'value'             => $kb_config['category_box_count_border_color'],
						'input_class'       => 'ekb-color-picker',
						'input_group_class' => 'eckb-wizard-single-color',
						'data' => array(
							'wizard_input' => '1',
							'target_selector' => '.eckb-wizard-step-4 .eckb-acll__cat-item__count',
							'style_name' => 'border-color' //TODO change
						)
					) ),
				
			)
		));
	
		do_action( 'epkb_wizard_after_article_page_colors', $kb_config['id'] ); // support old addons, delete on future 
		do_action( 'epkb_theme_wizard_after_article_page_colors', $kb_config['id'] );
	}
}