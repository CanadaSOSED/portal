<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Store Wizard theme data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Themes {

	/**
	 * Retreive themes-specific configuration for core and add-ons
	 * @return array|mixed
	 */
	public static function get_all_themes() {

		// retrieve theme specific configuration from add-ons
		$themes = apply_filters( 'epkb_theme_wizard_get_themes', self::$themes );
		if ( empty($themes) || ! is_array($themes) ) {
			return array();
		}
		
		// add here all Wizard theme options that should be translated
		$translate_fields = array(
			'kb_name',
			'theme_desc',
			'theme_category',
			'search_title',
		);
		
		foreach ($themes as &$theme) {
			foreach ( $translate_fields as $field ) {
				if ( isset( $theme[$field] ) ) {
					$theme[$field] = __( $theme[$field], 'echo-knowledge-base');
				}
			}
		}

		// if Elegant Layout is enabled then for Basic/Tabs/Categories we need the middle content to be 60%
		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		if ( is_wp_error($kb_config) ) {
			return array();
		}

		return $themes;
	}

	/**
	 * Return specific theme configuration + all other core and add-ons configuration so we can display preview
	 *
	 * @param $theme_id
	 * @param string $article_structure_version
	 * @return array
	 */
	public static function get_theme( $theme_id, $article_structure_version = '' ) {

		// current = 0, theme_standard = 1
		$themes = self::get_all_themes();

		$theme_config = empty($themes[$theme_id]) ? array() : $themes[$theme_id];
		$theme_config['theme_name'] = $theme_id;

		// get all configuration defaults (core and add-ons)
		$all_default_configuration = self::get_all_configuration_defaults();

		// overwrite default configuration with theme-specific settings
		$new_config = array_merge($all_default_configuration, $theme_config);
		if ( $article_structure_version ) {
			$new_config["article-structure-version"] = $article_structure_version;
		}
		
		return $new_config;
	}

	/**
	 * Get default values for themes for both core and add-ons
	 * @return array
	 */
	public static function get_all_configuration_defaults() {

		$kb_defaults = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// some configuration is temporary set to certain value for Wizard display purposes
		$wizard_defaults = array (
			'id'                                        => EPKB_KB_Config_DB::DEFAULT_KB_ID,
			'status'			                        => EPKB_KB_Status::PUBLISHED,
			'kb_main_pages'                             => array(),
			'kb_name'			                        => 'Default',
			'theme_desc'			                    => '',
			'categories_display_sequence'			    => 'user-sequenced',
			'nof_articles_displayed'			        => '3',
			'article_toc_media_3'                       => '500',
			'article_toc_position_from_top'             => '0',
			'article_toc_scroll_offset'                 => '0',
			'search_box_padding_top'			        => '20',
			'search_box_padding_bottom'			        => '20',
			'breadcrumb_description_text'		        => '',
			'breadcrumb_padding_left'			        => '4',
			'breadcrumb_padding_right'			        => '4',
			'article_toc_enable'                        => 'on',
			'article-left-sidebar-background-color-v2'  => '#FFFFFF',
			'article-content-background-color-v2'       => '#FFFFFF',
			'article-right-sidebar-background-color-v2' => '#FFFFFF',
			'kb_article_page_layout'                    => EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT,
		);

		// add all configuration defaults from addons
		$template_defaults = apply_filters( 'epkb_all_wizards_configuration_defaults', $wizard_defaults );

		return array_merge($kb_defaults, $template_defaults);
	}

	/**
	 * Get JSON string with default theme data ready to use in html
	 *
	 * @param $theme
	 * @return string
	 */
	public static function get_theme_data( $theme ) {
		return htmlspecialchars( json_encode( $theme ), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * THis configuration defines fields that are part of this wizard configuration related to layout and colors.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	// TODO remove advanced search and elegant layout fields
	public static $theme_fields = array(

		// GENERAL
		'kb_main_page_layout',
		'kb_article_page_layout',
		'templates_for_kb',
		'width',
		
		// CORE MAIN PAGE
		'search_title_html_tag',
		
		// OTHER
		'show_articles_before_categories',
		'nof_columns',
		'expand_articles_icon',
		'nof_articles_displayed',
		'templates_for_kb_category_archive_page_style',
		'section_font_size',

		// TEMPLATE FOR MAIN PAGE
		'templates_for_kb_padding_top',
		'templates_for_kb_padding_bottom',
		'templates_for_kb_padding_left',
		'templates_for_kb_padding_right',
		'templates_for_kb_margin_top',
		'templates_for_kb_margin_bottom',
		'templates_for_kb_margin_left',
		'templates_for_kb_margin_right',

		// TEMPLATE FOR ARTICLE PAGE
		'templates_for_kb_article_reset',
		'templates_for_kb_article_defaults',
		'templates_for_kb_article_padding_top',
		'templates_for_kb_article_padding_bottom',
		'templates_for_kb_article_padding_left',
		'templates_for_kb_article_padding_right',
		'templates_for_kb_article_margin_top',
		'templates_for_kb_article_margin_bottom',
		'templates_for_kb_article_margin_left',
		'templates_for_kb_article_margin_right',

		// TABS LAYOUT
		'tab_font_size',
		'tab_down_pointer',
		'tab_nav_active_font_color',
		'tab_nav_active_background_color',
		'tab_nav_font_color',
		'tab_nav_background_color',
		'tab_nav_border_color',
		'section_desc_text_on',

		// SEARCH
		'search_layout',
		'search_input_border_width',
		'search_box_padding_top',
		'search_box_padding_bottom',
		'search_box_margin_bottom',
		'search_box_margin_top',
		'search_title',		// main search title each theme sets; keep
		'search_title_font_size',

		// SECTION HEAD
		'section_head_alignment',
		'section_head_category_icon_location',
		'section_head_category_icon_size',
		'section_head_category_icon', // DEPRECATED: category icons
		'section_divider',
		'section_divider_thickness',
		'section_box_shadow',
		'section_head_padding_top',
		'section_head_padding_bottom',
		'section_head_padding_left',
		'section_head_padding_right',
		'section_border_width',
		'section_box_height_mode',
		'section_body_height',
		'section_body_padding_top',
		'section_body_padding_bottom',
		'section_body_padding_left',
		'section_body_padding_right',
		'article_list_spacing',

		// COLORS
		'search_title_font_color',
		'search_background_color',
		'search_text_input_background_color',
		'search_text_input_border_color',
		'search_btn_background_color',
		'search_btn_border_color',
		'search_box_input_width',
		'background_color',
		'article_font_color',
		'article_icon_color',
		'section_body_background_color',
		'section_border_color',
		'section_head_font_color',
		'section_head_background_color',
		'section_head_description_font_color',
		'section_divider_color',
		'section_category_font_color',
		'section_category_icon_color',
		'section_head_category_icon_color',
		'category_box_heading_group',

		// TOC
		'article_toc_text_color',
		'article_toc_active_bg_color',
		'article_toc_active_text_color',
		'article_toc_cursor_hover_bg_color',
		'article_toc_cursor_hover_text_color',
		'article_toc_border_color',
		'article_toc_scroll_offset',
		'article_toc_position_from_top',
		'article_toc_background_color',

		// BREADCRUMB
		'breadcrumb_icon_separator',
		'breadcrumb_text_color',
		'breadcrumb_font_size',

		// BACK NAVIGATION
		'back_navigation_text_color',
		'back_navigation_bg_color',
		'back_navigation_border_color',

		// GRID COLORS
		'grid_background_color',
		'grid_search_title_font_color',
		'grid_search_background_color',
		'grid_search_text_input_background_color',
		'grid_search_text_input_border_color',
		'grid_search_btn_background_color',
		'grid_search_btn_border_color',
		'grid_section_head_font_color',
		'grid_section_head_background_color',
		'grid_section_head_description_font_color',
		'grid_section_body_background_color',
		'grid_section_border_color',
		'grid_section_divider_color',
		'grid_section_head_icon_color',
		'grid_section_body_text_color',
		
		// CATEGORY BOX
		'category_box_title_text_color',
		'category_box_container_background_color',
		'category_box_category_text_color',
		'category_box_count_background_color',
		'category_box_count_text_color',
		'category_box_count_border_color',

		// SIDEBAR COLORS
		'sidebar_background_color',
		'sidebar_search_title_font_color',
		'sidebar_search_background_color',
		'sidebar_search_text_input_background_color',
		'sidebar_search_text_input_border_color',
		'sidebar_search_btn_background_color',
		'sidebar_search_btn_border_color',
		'sidebar_article_font_color',
		'sidebar_article_icon_color',
		'sidebar_article_active_font_color',
		'sidebar_article_active_background_color',
		'sidebar_section_head_font_color',
		'sidebar_section_head_background_color',
		'sidebar_section_head_description_font_color',
		'sidebar_section_border_color',
		'sidebar_section_divider_color',
		'sidebar_section_category_font_color',
		'sidebar_section_category_icon_color',
		
		// GRID STYLE
		'grid_section_font_size',
		'grid_nof_columns',
		'grid_category_icon_location',
		'grid_category_icon_thickness',
		'grid_section_icon_size',
		'grid_section_article_count',
		'grid_search_layout',
		'grid_search_input_border_width',
		'grid_search_box_padding_top',
		'grid_search_box_padding_bottom',
		'grid_search_box_padding_left',
		'grid_search_box_padding_right',
		'grid_search_box_margin_top',
		'grid_search_box_margin_bottom',
		'grid_search_box_input_width',
		'grid_section_head_alignment',
		'grid_section_head_padding_top',
		'grid_section_head_padding_bottom',
		'grid_section_head_padding_left',
		'grid_section_head_padding_right',
		'grid_section_body_alignment',
		'grid_section_cat_name_padding_top',
		'grid_section_cat_name_padding_bottom',
		'grid_section_cat_name_padding_left',
		'grid_section_cat_name_padding_right',
		'grid_section_desc_padding_top',
		'grid_section_desc_padding_bottom',
		'grid_section_desc_padding_left',
		'grid_section_desc_padding_right',
		'grid_section_border_radius',
		'grid_section_border_width',
		'grid_section_box_shadow',
		'grid_section_box_hover',
		'grid_section_divider',
		'grid_section_divider_thickness',
		'grid_section_box_height_mode',
		'grid_section_body_height',
		'grid_section_body_padding_top',
		'grid_section_body_padding_bottom',
		'grid_section_body_padding_left',
		'grid_section_body_padding_right',
		'grid_article_list_spacing',
		'grid_section_icon_padding_top',
		'grid_section_icon_padding_bottom',
		'grid_section_icon_padding_left',
		'grid_section_icon_padding_right',

		// SIDEBAR STYLE
		'sidebar_side_bar_width',
		'sidebar_side_bar_height_mode',
		'sidebar_side_bar_height',
		'sidebar_scroll_bar',
		'sidebar_section_font_size',
		'sidebar_top_categories_collapsed',
		'sidebar_nof_articles_displayed',
		'sidebar_show_articles_before_categories',
		'sidebar_expand_articles_icon',
		'sidebar_search_layout',
		'sidebar_search_box_collapse_mode',
		'sidebar_search_input_border_width',
		'sidebar_search_box_padding_top',
		'sidebar_search_box_padding_bottom',
		'sidebar_search_box_padding_left',
		'sidebar_search_box_padding_right',
		'sidebar_search_box_margin_top',
		'sidebar_search_box_margin_bottom',
		'sidebar_search_box_input_width',
		'sidebar_search_box_results_style',
		'sidebar_section_head_alignment',
		'sidebar_section_head_padding_top',
		'sidebar_section_head_padding_bottom',
		'sidebar_section_head_padding_left',
		'sidebar_section_head_padding_right',
		'sidebar_section_border_radius',
		'sidebar_section_border_width',
		'sidebar_section_box_shadow',
		'sidebar_section_divider',
		'sidebar_section_divider_thickness',
		'sidebar_section_box_height_mode',
		'sidebar_section_body_height',
		'sidebar_section_body_padding_top',
		'sidebar_section_body_padding_bottom',
		'sidebar_section_body_padding_left',
		'sidebar_section_body_padding_right',
		'sidebar_article_underline',
		'sidebar_article_active_bold',
		'sidebar_article_list_margin',
		'sidebar_article_list_spacing',

		// ADVANCED SEARCH COLORS - MAIN PAGE
		'advanced_search_mp_title_text_shadow_toggle',
		'advanced_search_mp_title_font_color',
		'advanced_search_mp_title_font_shadow_color',
		'advanced_search_mp_description_below_title_font_shadow_color',
		'advanced_search_mp_link_font_color',
		'advanced_search_mp_background_color',
		'advanced_search_mp_text_input_background_color',
		'advanced_search_mp_text_input_border_color',
		'advanced_search_mp_btn_background_color',
		'advanced_search_mp_btn_border_color',
		'advanced_search_mp_background_gradient_from_color',
		'advanced_search_mp_background_gradient_to_color',
		'advanced_search_mp_filter_box_font_color',
		'advanced_search_mp_filter_box_background_color',
		'advanced_search_mp_search_result_category_color',
		'advanced_search_mp_show_top_category', // need to hide default search 
		'advanced_search_mp_background_image_url',

		'advanced_search_mp_input_box_shadow_x_offset',
		'advanced_search_mp_input_box_shadow_y_offset',
		'advanced_search_mp_input_box_shadow_blur',
		'advanced_search_mp_input_box_shadow_spread',
		'advanced_search_mp_input_box_shadow_rgba',
		'advanced_search_mp_input_box_shadow_position_group',
		'advanced_search_mp_input_box_shadow_position_group',
		'advanced_search_mp_background_image_position_x',
		'advanced_search_mp_background_image_position_y',
		'advanced_search_mp_background_pattern_image_url',
		'advanced_search_mp_background_pattern_image_position_x',
		'advanced_search_mp_background_pattern_image_position_y',
		'advanced_search_mp_background_pattern_image_opacity',
		'advanced_search_mp_background_gradient_degree',
		'advanced_search_mp_background_gradient_opacity',
		'advanced_search_mp_description_below_title',
		'advanced_search_mp_description_below_input',
		'advanced_search_mp_background_gradient_toggle',
		'advanced_search_mp_text_title_shadow_position_group',
		'advanced_search_mp_title_text_shadow_x_offset',
		'advanced_search_mp_title_text_shadow_y_offset',
		'advanced_search_mp_title_text_shadow_blur',
		'advanced_search_mp_description_below_title_text_shadow_x_offset',
		'advanced_search_mp_description_below_title_text_shadow_y_offset',
		'advanced_search_mp_description_below_title_text_shadow_blur',
		'advanced_search_mp_description_below_title_text_shadow_toggle',
		'advanced_search_mp_box_visibility', 
		'advanced_search_mp_input_box_radius',
		// ADVANCED SEARCH COLORS - ARTICLE PAGE
		'advanced_search_ap_title_text_shadow_toggle',
		'advanced_search_ap_title_font_color',
		'advanced_search_ap_title_font_shadow_color',
		'advanced_search_ap_description_below_title_font_shadow_color',
		'advanced_search_ap_link_font_color',
		'advanced_search_ap_background_color',
		'advanced_search_ap_text_input_background_color',
		'advanced_search_ap_text_input_border_color',
		'advanced_search_ap_btn_background_color',
		'advanced_search_ap_btn_border_color',
		'advanced_search_ap_background_gradient_from_color',
		'advanced_search_ap_background_gradient_to_color',
		'advanced_search_ap_filter_box_font_color',
		'advanced_search_ap_filter_box_background_color',
		'advanced_search_ap_search_result_category_color',
		'advanced_search_ap_background_gradient_toggle',
		'advanced_search_ap_background_image_url',
		'advanced_search_ap_input_box_radius',
		'advanced_search_ap_text_title_shadow_position_group',
		'advanced_search_ap_title_text_shadow_x_offset',
		'advanced_search_ap_title_text_shadow_y_offset',
		'advanced_search_ap_title_text_shadow_blur',
		'advanced_search_ap_input_box_shadow_x_offset',
		'advanced_search_ap_input_box_shadow_y_offset',
		'advanced_search_ap_input_box_shadow_blur',
		'advanced_search_ap_input_box_shadow_spread',
		'advanced_search_ap_input_box_shadow_rgba',
		'advanced_search_ap_input_box_shadow_position_group',
		'advanced_search_ap_background_image_position_x',
		'advanced_search_ap_background_image_position_y',
		'advanced_search_ap_background_pattern_image_url',
		'advanced_search_ap_background_pattern_image_position_x',
		'advanced_search_ap_background_pattern_image_position_y',
		'advanced_search_ap_background_pattern_image_opacity',
		'advanced_search_ap_background_gradient_degree',
		'advanced_search_ap_background_gradient_opacity',
		'advanced_search_ap_description_below_title_text_shadow_x_offset',
		'advanced_search_ap_description_below_title_text_shadow_y_offset',
		'advanced_search_ap_description_below_title_text_shadow_blur',
		'advanced_search_ap_description_below_title_text_shadow_toggle',
		'advanced_search_ap_filter_indicator_text',
		'advanced_search_ap_box_visibility',
		'advanced_search_ap_description_below_title',
		'advanced_search_ap_description_below_input',
			
		// RATING ARTICLE
		'rating_element_color',
		'rating_like_color',
		'rating_dislike_color',
		'rating_text_color',
		'rating_dropdown_color',
		'rating_feedback_button_color',
		
		// Theme Name
		'theme_name',
		
		// article v2 template 
		'article-left-sidebar-background-color-v2',
		'article-content-background-color-v2',
		'article-right-sidebar-background-color-v2',
		'article-left-sidebar-desktop-width-v2',
		'article-left-sidebar-tablet-width-v2',
		'article-content-desktop-width-v2',
		'article-content-tablet-width-v2',
		'article_sidebar_component_priority',
		'article-right-sidebar-desktop-width-v2',
		'article-right-sidebar-tablet-width-v2',
		
		// Widgets
		'widg_search_preset_styles',
	);

	public static $themes = array(

		// BASIC LAYOUT

		'theme_standard' => array(
			'kb_name'			                    => 'Standard', // ANY CHANGE HAS TO BE APPLIED IN LAST FUNCTION
			'theme_desc'			                => 'Initial KB setup',  // ANY CHANGE HAS TO BE APPLIED IN LAST FUNCTION
			'theme_category'                        => 'Basic Layout',  // ANY CHANGE HAS TO BE APPLIED IN LAST FUNCTION
			//General
			'background_color'                      => '#FFFFFF',

			//Search Box
			'search_title_font_color'               => '#FFFFFF',
			'search_background_color'               => '#f7941d',
			'search_text_input_background_color'    => '#FFFFFF',
			'search_text_input_border_color'        => '#CCCCCC',
			'search_btn_background_color'           => '#40474f',
			'search_btn_border_color'               => '#F1F1F1',
			'search_box_input_width'			    => '50',

			//Articles Listed In Category Box
			'section_head_font_color'               => '#40474f',
			'section_head_background_color'         => '#FFFFFF',
			'section_head_description_font_color'   => '#b3b3b3',
			'section_body_background_color'         => '#FFFFFF',
			'section_border_color'                  => '#F7F7F7',
			'section_divider_color'                 => '#edf2f6',
			'section_category_font_color'           => '#40474f',
			'section_category_icon_color'           => '#f7941d',
			'section_head_category_icon_color'      => '#f7941d',
			'article_font_color'                    => '#459fed',
			'article_icon_color'                    => '#b3b3b3',
			'article_sidebar_component_priority'    => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_spacious' => array(
			'kb_name'			                        => 'Spacious',
			'theme_desc'			                    => 'Big icons for easy navigation',
			'theme_category'                            => 'Basic Layout',
			'templates_for_kb_padding_left'			    => '4',
			'templates_for_kb_padding_right'			=> '4',
			'templates_for_kb_margin_left'			    => '4',
			'templates_for_kb_margin_right'			    => '4',
			'templates_for_kb_category_archive_page_style' => 'eckb-category-archive-style-2',
			'breadcrumb_text_color'			            => '#1e73be',
			'back_navigation_text_color'			    => '#1e73be',
			'width'			                            => 'epkb-boxed',
			'search_box_input_width'			        => '50',
			'section_head_alignment'			        => 'center',
			'section_head_category_icon_location'		=> 'top',
			'section_head_category_icon_size'			=> '30',
			'section_divider_thickness'			        => '1',
			'section_border_width'			            => '1',
			'section_body_height'                       => 150,
			'search_title_font_color'			        => '#000000',
			'search_background_color'			        => '#ffffff',
			'search_text_input_border_color'			=> '#CCCCCC',
			'search_btn_background_color'			    => '#1168bf',
			'search_btn_border_color'			        => '#F1F1F1',
			'article_font_color'			            => '#000000',
			'article_icon_color'			            => '#1168bf',
			'section_border_color'			            => '#d1d1d1',
			'section_head_font_color'			        => '#000000',
			'section_divider_color'			            => '#CDCDCD',
			'section_category_font_color'			    => '#000000',
			'section_category_icon_color'			    => '#1e73be',
			'section_head_category_icon_color'			=> '#1e73be',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_informative' => array(
			'kb_name'			                    => 'Informative',
			'theme_desc'			                => 'Categories with description',
			'theme_category'                        => 'Basic Layout',
			'kb_main_page_layout'			        => 'Basic',
			'templates_for_kb_padding_left'         => '4',
			'templates_for_kb_padding_right'        => '4',
			'templates_for_kb_margin_left'          => '4',
			'templates_for_kb_margin_right'         => '4',
			'nof_articles_displayed'                => '8',
			'search_box_input_width'                => '50',
			'section_head_alignment'			    => 'center',
			'section_head_category_icon_location'	=> 'top',
			'section_head_category_icon_size'	    => 50,
			'section_divider_thickness'			    => '0',
			'section_box_shadow'                    =>  'section_light_shadow',
			'section_border_width'                  => '0',
			'section_body_padding_top'              => '5',
			'section_body_height'                   => 150,
			'search_title_font_color'               => '#ffffff',
			'search_background_color'			    => '#904e95',
			'search_text_input_border_color'        => '#CCCCCC',
			'search_btn_background_color'			=> '#686868',
			'search_btn_border_color'			    => '#F1F1F1',
			'article_font_color'			        => '#606060',
			'article_icon_color'			        => '#904e95',
			'section_border_color'                  => '#DBDBDB',
			'section_head_font_color'               => '#827a74',
			'section_divider_color'                 => '#DADADA',
			'section_category_font_color'			=> '#868686',
			'section_category_icon_color'			=> '#868686',
			'section_desc_text_on'			        => 'on',
			'section_body_padding_left'             => 30,
			'category_box_heading_group'			=> '#904e95',
			'section_head_category_icon_color'		=> '#904e95',
			'search_title_font_size'			    => 40,
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_image' => array(
			'kb_name'			                    => 'Image',
			'theme_desc'			                => 'Categories with description',
			'theme_category'                        => 'Basic Layout',
			'kb_main_page_layout'			        => 'Basic',
			'templates_for_kb_padding_left'         => '4',
			'templates_for_kb_padding_right'        => '4',
			'templates_for_kb_margin_left'          => '4',
			'templates_for_kb_margin_right'         => '4',
			'nof_articles_displayed'                => '8',
			'search_box_input_width'                => '50',
			'section_head_alignment'			    => 'center',
			'section_head_category_icon_location'	=> 'top',
			'section_head_category_icon_size'	    => 150,
			'section_divider_thickness'			    => '0',
			'section_box_shadow'                    =>  'section_light_shadow',
			'section_border_width'                  => '0',
			'section_body_padding_top'              => '5',
			'section_body_height'                   => 150,
			'search_title_font_color'               => '#ffffff',
			'search_background_color'			    => '#B1D5E1',
			'search_text_input_border_color'        => '#CCCCCC',
			'search_btn_background_color'			=> '#686868',
			'search_btn_border_color'			    => '#F1F1F1',
			'article_font_color'			        => '#606060',
			'article_icon_color'			        => '#904e95',
			'section_border_color'                  => '#DBDBDB',
			'section_head_font_color'               => '#827a74',
			'section_divider_color'                 => '#DADADA',
			'section_category_font_color'			=> '#868686',
			'section_category_icon_color'			=> '#868686',
			'section_desc_text_on'			        => 'on',
			'section_body_padding_left'             => 30,
			'category_box_heading_group'			=> '#904e95',
			'section_head_category_icon_color'		=> '#904e95',
			'search_title_font_size'			    => 40,
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_modern' => array(
			'kb_name'			                    => 'Modern',
			'theme_desc'			                => 'Modern simple look',
			'theme_category'                        => 'Basic Layout',
			'kb_main_page_layout'			        => 'Basic',
			'templates_for_kb_padding_left'         => '4',
			'templates_for_kb_padding_right'        => '4',
			'templates_for_kb_margin_left'          => '4',
			'templates_for_kb_margin_right'         => '4',
			'nof_articles_displayed'                => '8',
			//Search
			'search_box_input_width'                => '50',
			'search_title'			                => 'Have a Question?',
			'search_title_font_size'			    => 40,
			'search_title_font_color'               => '#40474f',
			'search_background_color'			    => '#FFFFFF',
			'search_text_input_border_color'        => '#40474f',
			'search_btn_background_color'			=> '#40474f',
			'search_btn_border_color'			    => '#40474f',
			'search_input_border_width'             => 3,

			//Section
			'section_head_alignment'			    => 'center',
			'section_head_category_icon_location'	=> 'no_icons',
			'section_head_category_icon_size'	    => 50,
			'section_head_padding_left'             => 30,
			'section_divider_thickness'			    => '1',
			'section_box_shadow'                    =>  'section_light_shadow',
			'section_border_width'                  => '0',
			'section_body_padding_top'              => '5',
			'section_body_height'                   => 150,
			'section_border_color'                  => '#DBDBDB',
			'section_head_font_color'               => '#827a74',
			'section_divider_color'                 => '#DADADA',
			'section_category_font_color'			=> '#868686',
			'section_category_icon_color'			=> '#868686',
			'section_body_padding_left'             => 30,
			'category_box_heading_group'			=> '#904e95',
			'section_head_category_icon_color'		=> '#904e95',
			//Articles
			'article_font_color'			        => '#606060',
			'article_icon_color'			        => '#81d742',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_bright' => array(
			'kb_name'			                    => 'Bright',
			'theme_desc'			                => 'Focus on colors',
			'theme_category'                        => 'Basic Layout',
			'kb_main_page_layout'			        => 'Basic',
			'templates_for_kb_padding_left'         => '4',
			'templates_for_kb_padding_right'        => '4',
			'templates_for_kb_margin_left'          => '4',
			'templates_for_kb_margin_right'         => '4',
			'nof_articles_displayed'                => '8',

			//Search
			'search_box_input_width'                => '50',
			'search_title'			                => 'What are you looking for?',
			'search_title_font_size'			    => 40,
			'search_title_font_color'               => '#f4c60c',
			'search_background_color'			    => '#FFFFFF',
			'search_text_input_border_color'        => '#f4c60c',
			'search_btn_background_color'			=> '#f4c60c',
			'search_btn_border_color'			    => '#f4c60c',
			'search_input_border_width'             => 3,

			//Section
			'section_head_background_color'         =>  '#fcfcfc',
			'section_head_alignment'			    => 'left',
			'section_head_category_icon_location'	=> 'left',
			'section_head_category_icon_size'	    => 25,
			'section_head_category_icon_color'		=> '#f4c60c',
			'section_head_font_color'               => '#0b6ea0',

			'section_divider_thickness'			    => '2',
			'section_box_shadow'                    =>  'no_shadow',
			'section_border_width'                  => '0',
			'section_body_padding_top'              => '5',
			'section_body_height'                   => 150,
			'section_border_color'                  => '#DBDBDB',
			'section_divider_color'                 => '#edf2f6',
			'section_category_font_color'			=> '#868686',
			'section_category_icon_color'			=> '#dddddd',
			'section_body_padding_left'             => 10,
			'category_box_heading_group'			=> '#904e95',

			//Articles
			'article_font_color'			        => '#0bcad9',
			'article_icon_color'			        => '#1e1e1e',
			'expand_articles_icon'			        => 'ep_font_icon_right_arrow',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_formal' => array(
			'kb_name'			                    => 'Formal',
			'theme_desc'			                => 'Left alligned',
			'theme_category'                        => 'Basic Layout',
			'kb_main_page_layout'			        => 'Basic',
			'templates_for_kb_padding_left'         => '4',
			'templates_for_kb_padding_right'        => '4',
			'templates_for_kb_margin_left'          => '4',
			'templates_for_kb_margin_right'         => '4',
			'nof_articles_displayed'                => '8',
			//Search
			'search_box_input_width'                => '50',
			'search_title'			                => 'Welcome to our Knowledge Base',
			'search_title_font_size'			    => 40,
			'search_title_font_color'               => '#000000',
			'search_background_color'			    => '#edf2f6',
			'search_text_input_border_color'        => '#d1d1d1',
			'search_btn_background_color'			=> '#666666',
			'search_btn_border_color'			    => '#666666',
			'search_input_border_width'             => 1,
			'search_box_padding_top'			    => '50',
			'search_box_padding_bottom'			    => '50',

			//Section
			'section_head_alignment'			    => 'left',
			'section_head_category_icon_location'	=> 'left',
			'section_head_category_icon_size'	    => 25,
			'section_head_category_icon_color'		=> '#e3474b',
			'section_head_font_color'               => '#e3474b',

			'section_divider_thickness'			    => '2',
			'section_box_shadow'                    =>  'no_shadow',
			'section_border_width'                  => '0',
			'section_body_padding_top'              => '5',
			'section_body_height'                   => 150,
			'section_border_color'                  => '#DBDBDB',
			'section_divider_color'                 => '#edf2f6',
			'section_category_font_color'			=> '#868686',
			'section_category_icon_color'			=> '#e3474b',
			'section_body_padding_left'             => 10,
			'category_box_heading_group'			=> '#904e95',

			//Articles
			'article_font_color'			        => '#616161',
			'article_icon_color'			        => '#000000',
			'expand_articles_icon'			        => 'ep_font_icon_right_arrow',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_disctinct' => array(
			'kb_name'			                    => 'Distinct',
			'theme_desc'			                => 'Categories above articles',
			'theme_category'                        => 'Basic Layout',
			'kb_main_page_layout'			        => 'Basic',
			'templates_for_kb_padding_left'         => '4',
			'templates_for_kb_padding_right'        => '4',
			'templates_for_kb_margin_left'          => '4',
			'templates_for_kb_margin_right'         => '4',
			'nof_articles_displayed'                => '8',
			'show_articles_before_categories'       => 'off',
			//Search
			'search_box_input_width'                => '50',
			'search_title'			                => 'Self Help Documentation',
			'search_title_font_size'			    => 40,
			'search_title_font_color'               => '#528ffe',
			'search_background_color'			    => '#f4f8ff',
			'search_text_input_border_color'        => '#bf25ff',
			'search_btn_background_color'			=> '#bf25ff',
			'search_btn_border_color'			    => '#bf25ff',
			'search_input_border_width'             => 1,
			'search_box_padding_top'                => 30,
			'search_box_padding_bottom'             => 30,

			//Section
			'section_head_alignment'			    => 'center',
			'section_head_category_icon_location'	=> 'left',
			'section_head_category_icon_size'	    => 25,
			'section_head_category_icon_color'		=> '#bf25ff',
			'section_head_font_color'               => '#528ffe',

			'section_divider_thickness'			    => '2',
			'section_box_shadow'                    =>  'no_shadow',
			'section_border_width'                  => '0',
			'section_body_padding_top'              => '5',
			'section_body_height'                   => 150,
			'section_box_height_mode'               => 'section_no_height',
			'section_border_color'                  => '#528ffe',
			'section_divider_color'                 => '#528ffe',
			'section_category_font_color'			=> '#868686',
			'section_category_icon_color'			=> '#528ffe',
			'section_body_padding_left'             => 10,
			'category_box_heading_group'			=> '#904e95',

			//Articles
			'article_font_color'			        => '#566e8b',
			'article_icon_color'			        => '#566e8b',
			'expand_articles_icon'			        => 'ep_font_icon_plus_box',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_faqs' => array(
			'kb_name'			                    => 'FAQs',
			'theme_desc'			                => 'List of topics and articles',
			'theme_category'                        => 'Basic Layout',
			'kb_main_page_layout'			        => 'Basic',
			'templates_for_kb_padding_left'         => '4',
			'templates_for_kb_padding_right'        => '4',
			'templates_for_kb_margin_left'          => '4',
			'templates_for_kb_margin_right'         => '4',
			'nof_articles_displayed'                => '8',
			'nof_columns'                           => 'two-col',

			//Search
			'search_box_input_width'                => '50',
			'search_title'			                => 'Support Center',
			'search_title_font_size'			    => 40,
			'search_title_font_color'               => '#ffffff',
			'search_background_color'			    => '#37de89',
			'search_text_input_border_color'        => '#37de89',
			'search_btn_background_color'			=> '#666666',
			'search_btn_border_color'			    => '#666666',
			'search_input_border_width'             => 1,
			'search_box_padding_top'                => 30,
			'search_box_padding_bottom'             => 30,
			'search_layout'                         => 'epkb-search-form-0',

			//Section
			'section_head_alignment'			    => 'left',
			'section_head_category_icon_location'	=> 'left',
			'section_head_category_icon_size'	    => 25,
			'section_head_category_icon_color'		=> '#bf25ff',
			'section_head_font_color'               => '#528ffe',

			'section_divider_thickness'			    => '2',
			'section_box_shadow'                    =>  'no_shadow',
			'section_border_width'                  => '0',
			'section_body_padding_top'              => '5',
			'section_body_height'                   => 90,
			'section_border_color'                  => '#528ffe',
			'section_divider_color'                 => '#528ffe',
			'section_category_font_color'			=> '#868686',
			'section_category_icon_color'			=> '#528ffe',
			'section_body_padding_left'             => 10,
			'section_body_padding_bottom'           => 0,
			'category_box_heading_group'			=> '#904e95',

			//Articles
			'article_font_color'			        => '#566e8b',
			'article_icon_color'			        => '#566e8b',
			'expand_articles_icon'			        => 'ep_font_icon_right_arrow',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),


		// TABS LAYOUT

		'theme_organized' => array(
			'kb_name'			                        => 'Organized',
			'theme_desc'			                    => 'Divide into tabs and categories',
			'theme_category'                            => 'Tabs Layout',
			'kb_main_page_layout'			            => 'Tabs',
			'templates_for_kb_category_archive_page_style'			 => 'eckb-category-archive-style-3',
			'breadcrumb_text_color'			            => '#8c1515',
			'back_navigation_text_color'			    => '#8c1515',
			'width'			                            => 'epkb-boxed',
			'section_font_size'			                => 'section_small_font',
			'expand_articles_icon'			            => 'ep_font_icon_plus_box',
			'search_box_input_width'			        => '50',
			'section_head_alignment'			        => 'center',
			'section_head_category_icon_location'		=> 'top',
			'section_head_category_icon'			    => 'ep_font_icon_pencil', // DEPRECATED: category icons
			'section_divider'			                => 'off',
			'section_divider_thickness'			        => '1',
			'section_box_shadow'			            => 'section_light_shadow',
			'section_body_height'                       => 150,
			'section_head_padding_top'			        => '10',
			'section_head_padding_bottom'			    => '10',
			'section_border_width'			            => '1',
			'section_body_padding_left'			        => '22',
			'section_body_padding_right'			    => '4',
			'article_list_spacing'			            => '4',
			'search_title'			                    => 'Have a Question?',
			'search_title_font_color'                   => '#ffffff',
			'search_background_color'			        => '#8c1515',
			'search_text_input_border_color'			=> '#000000',
			'search_btn_background_color'			    => '#878787',
			'search_btn_border_color'			        => '#000000',
			'article_font_color'			            => '#8c1515',
			'article_icon_color'			            => '#000000',
			'section_border_color'			            => '#bababa',
			'section_head_font_color'			        => '#000000',
			'section_head_background_color'			    => '#eeeeee',
			'section_divider_color'			            => '#CDCDCD',
			'section_category_font_color'			    => '#868686',
			'section_category_icon_color'			    => '#8c1515',
			'section_head_category_icon_color'			=> '#8c1515',
			'tab_down_pointer'			                => 'on',
			'tab_nav_active_font_color'			        => '#8c1515',
			'tab_nav_active_background_color'			=> '#F1F1F1',
			'tab_nav_font_color'			            => '#686868',
			'tab_nav_border_color'			            => '#000000',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_organized_2' => array(
			'kb_name'			                    => 'Organized 2',
			'theme_desc'			                => 'Tabs for each section',
			//'section_desc_text_on'			        => 'on',
			'kb_main_page_layout'			        => 'Tabs',
			'theme_category'                        => 'Tabs Layout',
			'templates_for_kb_category_archive_page_style'			 => 'eckb-category-archive-style-4',
			'breadcrumb_text_color'			        => '#00b4b3',
			'back_navigation_text_color'			=> '#00b4b3',
			'nof_articles_displayed'			    => '10',
			'search_box_input_width'			        => '50',
			'section_box_height_mode'               => 'section_no_height',
			//'section_body_height'                   => '10',
			'expand_articles_icon'			        => 'ep_font_icon_right_arrow',
			'search_layout'			                => 'epkb-search-form-3',
			'search_input_border_width'			    => '5',
			'search_box_padding_top'			    => '50',
			'search_box_padding_bottom'			    => '50',
			'section_head_alignment'			    => 'center',
			'section_head_category_icon_location'	=> 'no_icons',
			'section_head_padding_left'             => 30,
			'section_divider_thickness'			    => '1',
			'section_border_width'			        => '1',
			'section_body_padding_left'			    => '22',
			'section_body_padding_right'			=> '4',
			'article_list_spacing'			        => '4',
			'search_background_color'			    => '#00b4b3',
			'search_text_input_border_color'		=> '#00c6c6',
			'search_btn_background_color'			=> '#686868',
			'search_btn_border_color'			    => '#F1F1F1',
			'article_font_color'			        => '#000000',
			'article_icon_color'			        => '#00b4b3',
			'section_head_font_color'			    => '#ffffff',
			'section_head_background_color'			=> '#00b4b3',
			'section_divider_color'			        => '#CDCDCD',
			'section_category_font_color'			=> '#000000',
			'section_category_icon_color'			=> '#00b4b3',
			'section_head_category_icon_color'		=> '#868686',
			'search_title'			                => 'Help Center',
			'tab_nav_active_font_color'			    => '#ffffff',
			'tab_nav_active_background_color'		=> '#00b4b3',
			'tab_nav_font_color'			        => '#686868',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),
		),

		'theme_products_style' => array(
			'kb_name'			                                => 'Product style',
			'theme_desc'			                            => 'Tabs for each Product',
			'section_desc_text_on'			                    => 'on',
			'kb_main_page_layout'			                    => 'Tabs',
			'theme_category'                                    => 'Tabs Layout',
			'templates_for_kb_category_archive_page_style'	    => 'eckb-category-archive-style-4',
			'breadcrumb_text_color'			                    => '#00b4b3',
			'back_navigation_text_color'			            => '#00b4b3',
			'nof_articles_displayed'			                => '10',
			'search_box_input_width'			                => '50',
			'section_box_height_mode'                           => 'section_no_height',

			// Search
			'search_background_color'               => '#6e6767',
			'search_text_input_border_color'        => '#000000',
			'search_title'			                => 'Help Center',
			'search_btn_background_color'			=> '#686868',
			'search_btn_border_color'			    => '#F1F1F1',
			'search_layout'			                => 'epkb-search-form-3',
			'search_input_border_width'			    => '5',
			'search_box_padding_top'			    => '50',
			'search_box_padding_bottom'			    => '50',

			// Tabs
			'tab_nav_active_font_color'			    => '#ffffff',
			'tab_nav_active_background_color'       => '#6e6767',
			'tab_nav_font_color'                    => '#686868',
			'tab_nav_background_color'              => '#f7f7f7',
			'section_head_description_font_color'   => '#828282',
			'tab_nav_border_color'                  => '#1e73be',


			// Categories
			'section_head_background_color'         => '#6e6767',
			'section_head_font_color'			    => '#ffffff',
			'section_head_padding_left'             => 30,
			'section_divider_color'                 => '#1e73be',

			// Articles
			'article_icon_color'                    => '#1e73be',
			'section_body_background_color'         => '#f9f9f9',

			// Other
			'expand_articles_icon'			        => 'ep_font_icon_right_arrow',
			'section_head_alignment'			    => 'center',
			'section_head_category_icon_location'	=> 'no_icons',
			'section_divider_thickness'			    => '2',
			'section_border_width'			        => '1',
			'section_body_padding_left'			    => '22',
			'section_body_padding_right'			=> '4',
			'article_list_spacing'			        => '4',
			'article_font_color'			        => '#000000',
			'section_category_font_color'			=> '#000000',
			'section_category_icon_color'			=> '#00b4b3',
			'section_head_category_icon_color'		=> '#868686',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),

		),

		'theme_tabs_clean_style' => array(
			'kb_name'			                                => 'Clean',
			'theme_desc'			                            => 'Clean style',
			'kb_main_page_layout'			                    => 'Tabs',
			'theme_category'                                    => 'Tabs Layout',
			'templates_for_kb_category_archive_page_style'	    => 'eckb-category-archive-style-4',
			'breadcrumb_text_color'			                    => '#00b4b3',
			'back_navigation_text_color'			            => '#00b4b3',
			'nof_articles_displayed'			                => '10',
			'search_box_input_width'			                => '50',
			'section_box_height_mode'                           => 'section_no_height',
			'nof_columns'                                       => 'two-col',

			// Search
			'search_background_color'               => '#f2f2f2',
			'search_text_input_border_color'        => '#000000',
			'search_input_border_width'             => '1',
			'search_title_font_color'               => '#000000',
			'search_btn_border_color'               => '#000000',
			'search_btn_background_color'           => '#000000',

			// Tabs
			'tab_nav_active_font_color'			    => '#000000',
			'tab_nav_active_background_color'       => '#ffffff',
			'tab_nav_font_color'                    => '#adadad',
			'tab_nav_background_color'              => '#ffffff',
			'section_head_description_font_color'   => '#828282',
			'tab_nav_border_color'                  => '#888888',


			// Categories
			'section_head_background_color'         => '#ffffff',
			'section_head_font_color'			    => '#000000',
			'section_head_padding_left'             => 30,
			'section_divider_color'                 => '#888888',

			// Articles
			'article_icon_color'                    => '#adadad',
			'section_body_background_color'         => '#ffffff',

			'expand_articles_icon'			        => 'ep_font_icon_right_arrow',
			'search_layout'			                => 'epkb-search-form-1',
			'search_box_padding_top'			    => '50',
			'search_box_padding_bottom'			    => '50',
			'section_head_alignment'			    => 'center',
			'section_head_category_icon_location'	=> 'no_icons',
			'section_divider_thickness'			    => '2',
			'section_border_width'			        => '1',
			'section_body_padding_left'			    => '22',
			'section_body_padding_right'			=> '4',
			'article_list_spacing'			        => '4',
			'article_font_color'			        => '#000000',

			'section_category_font_color'			=> '#000000',
			'section_category_icon_color'			=> '#00b4b3',
			'section_head_category_icon_color'		=> '#868686',
			'search_title'			                => 'Help Center',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '1',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '0',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '1'
			),

		),

		// Category Focused LAYOUT
		'standard_2' => array(
			'kb_name'			                        => 'Standard',
			'theme_desc'			                    => 'Typical setup',
			'kb_main_page_layout'			            => 'Categories',
			'theme_category'                            => 'Category Focused Layout',
			'templates_for_kb_category_archive_page_style'			 => 'eckb-category-archive-style-3',

			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#2991a3',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#CCCCCC',
			'search_btn_background_color'           =>  '#40474f',
			'search_btn_border_color'               =>  '#F1F1F1',
			'search_box_input_width'			        => '50',
			'search_box_padding_top'			        => '50',
			'search_box_padding_bottom'			        => '50',

			//Articles Listed In Category Box

			// Section Head
			'section_head_font_color'               =>  '#40474f',
			'section_head_background_color'         =>  '#FFFFFF',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_head_padding_top'              =>  20,
			'section_head_padding_bottom'           =>  20,
			'section_head_padding_left'             =>  20,
			'section_head_padding_right'            =>  20,
			'section_head_category_icon_color'      =>  '#2991a3',

			// Section Body
			'section_body_background_color'         =>  '#FFFFFF',

			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#edf2f6',
			'section_category_font_color'           =>  '#40474f',
			'section_category_icon_color'           =>  '#2991a3',
			'section_head_category_icon_size'       =>  '30',
			'article_font_color'                    =>  '#666666',
			'article_icon_color'                    =>  '#2991a3',

			'breadcrumb_text_color'			            => '#8c1515',
			'back_navigation_text_color'			    => '#8c1515',
			'width'			                            => 'epkb-boxed',
			'section_font_size'			                => 'section_small_font',
			'expand_articles_icon'			            => 'ep_font_icon_plus_box',
			'section_head_alignment'			        => 'left',
			'section_head_category_icon_location'		=> 'left',
			'section_head_category_icon'			    => 'ep_font_icon_pencil', // DEPRECATED: category icons
			'section_divider'			                => 'on',
			'section_divider_thickness'			        => '1',
			'section_box_shadow'			            => 'section_light_shadow',
			'section_body_height'                       => 150,
			'section_border_width'			            => '1',
			'section_body_padding_left'			        => '22',
			'section_body_padding_right'			    => '4',
			'article_list_spacing'			            => '4',
			'search_title'			                    => 'Have a Question?',
			'tab_down_pointer'			                => 'on',
			'tab_nav_active_font_color'			        => '#8c1515',
			'tab_nav_active_background_color'			=> '#F1F1F1',
			'tab_nav_font_color'			            => '#686868',
			'tab_nav_border_color'			            => '#000000',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '0',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '1',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '0'
			),
		),

		'standard_3' => array(
			'kb_name'			                        => 'Icon Focused',
			'theme_desc'			                    => 'Large icons with separation',
			'kb_main_page_layout'			            => 'Categories',
			'theme_category'                            => 'Category Focused Layout',
			'templates_for_kb_category_archive_page_style'			 => 'eckb-category-archive-style-4',

			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#fcfcfc',
			'search_background_color'               =>  '#fcfcfc',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#0bcad9',
			'search_btn_background_color'           =>  '#f4c60c',
			'search_btn_border_color'               =>  '#0bcad9',
			'search_box_input_width'			        => '50',
			'search_box_padding_top'			        => '50',
			'search_box_padding_bottom'			        => '50',

			//Articles Listed In Category Box

			// Section Head
			'section_head_font_color'               =>  '#666666',
			'section_head_background_color'         =>  '#fcfcfc',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_head_padding_top'              =>  20,
			'section_head_padding_bottom'           =>  20,
			'section_head_padding_left'             =>  20,
			'section_head_padding_right'            =>  20,
			'section_head_category_icon_color'      =>  '#f4c60c',

			// Section Body
			'section_body_background_color'         =>  '#FFFFFF',

			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#0bcad9',
			'section_category_font_color'           =>  '#40474f',
			'section_category_icon_color'           =>  '#2991a3',
			'section_head_category_icon_size'       =>  '40',
			'article_font_color'                    =>  '#0bcad9',
			'article_icon_color'                    =>  '#2991a3',

			'breadcrumb_text_color'			            => '#8c1515',
			'back_navigation_text_color'			    => '#8c1515',
			'width'			                            => 'epkb-boxed',
			'section_font_size'			                => 'section_small_font',
			'expand_articles_icon'			            => 'ep_font_icon_plus_box',
			'section_head_alignment'			        => 'left',
			'section_head_category_icon_location'		=> 'top',
			'section_head_category_icon'			    => 'ep_font_icon_pencil', // DEPRECATED: category icons
			'section_divider'			                => 'on',
			'section_divider_thickness'			        => '2',
			'section_box_shadow'			            => 'section_light_shadow',
			'section_body_height'                       => 150,
			'section_border_width'			            => '1',
			'section_body_padding_left'			        => '22',
			'section_body_padding_right'			    => '4',
			'article_list_spacing'			            => '4',
			'search_title'			                    => 'Have a Question?',
			'tab_down_pointer'			                => 'on',
			'tab_nav_active_font_color'			        => '#8c1515',
			'tab_nav_active_background_color'			=> '#F1F1F1',
			'tab_nav_font_color'			            => '#686868',
			'tab_nav_border_color'			            => '#000000',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '0',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '1',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '0'
			),
		),

		'business' => array(
			'kb_name'			                        => 'Formal',
			'theme_desc'			                    => 'Simple style',
			'kb_main_page_layout'			            => 'Categories',
			'theme_category'                            => 'Category Focused Layout',
			'templates_for_kb_category_archive_page_style'			 => 'eckb-category-archive-style-4',

			//General
			'background_color'                      =>  '#fbfbfb',

			//Search Box
			'search_title_font_color'               =>  '#000000',
			'search_background_color'               =>  '#fbfbfb',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#CCCCCC',
			'search_btn_background_color'           =>  '#40474f',
			'search_btn_border_color'               =>  '#F1F1F1',
			'search_box_input_width'			    => '50',
			'search_box_padding_top'			    => '50',
			'search_box_padding_bottom'			    => '90',
			'search_box_margin_bottom'			    => '0',

			//Articles Listed In Category Box

			// Section Head
			'section_head_font_color'               =>  '#000000',
			'section_head_background_color'         =>  '#FFFFFF',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_head_padding_top'              =>  20,
			'section_head_padding_bottom'           =>  20,
			'section_head_padding_left'             =>  20,
			'section_head_padding_right'            =>  20,
			'section_head_category_icon_color'      =>  '#eb5a46',
			'section_desc_text_on'			        => 'on',

			// Section Body
			'section_body_background_color'         =>  '#FEFEFE',
			'section_box_height_mode'               =>  'section_min_height',
			'section_body_height'                   =>  130,
			'section_border_color'                  =>  '#CACACE',
			'section_divider_color'                 =>  '#FFFFFF',
			'section_category_font_color'           =>  '#40474f',
			'section_category_icon_color'           =>  '#eb5a46',
			'section_head_category_icon_size'       =>  '30',
			'article_font_color'                    =>  '#666666',
			'article_icon_color'                    =>  '#e8a298',

			'breadcrumb_text_color'			            => '#8c1515',
			'back_navigation_text_color'			    => '#8c1515',
			'width'			                            => 'epkb-boxed',
			'section_font_size'			                => 'section_small_font',
			'expand_articles_icon'			            => 'ep_font_icon_plus_box',
			'section_head_alignment'			        => 'left',
			'section_head_category_icon_location'		=> 'left',
			'section_head_category_icon'			    => 'ep_font_icon_pencil', // DEPRECATED: category icons
			'section_divider'			                => 'on',
			'section_divider_thickness'			        => '1',
			'section_box_shadow'			            => 'section_light_shadow',

			'section_border_width'			            => '1',
			'section_body_padding_left'			        => '22',
			'section_body_padding_right'			    => '4',
			'article_list_spacing'			            => '4',
			'search_title'			                    => 'Have a Question?',
			'tab_down_pointer'			                => 'on',
			'tab_nav_active_font_color'			        => '#8c1515',
			'tab_nav_active_background_color'			=> '#F1F1F1',
			'tab_nav_font_color'			            => '#686868',
			'tab_nav_border_color'			            => '#000000',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '0',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '1',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '0'
			),
		),

		'business_2' => array(
			'kb_name'			                        => 'Minimalistic',
			'theme_desc'			                    => 'No icons with category description',
			'kb_main_page_layout'			            => 'Categories',
			'theme_category'                            => 'Category Focused Layout',
			'templates_for_kb_category_archive_page_style'			 => 'eckb-category-archive-style-5',

			//General
			'background_color'                      =>  '#fbfbfb',

			//Search Box
			'search_title_font_color'               =>  '#6fb24c',
			'search_background_color'               =>  '#fbfbfb',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#6fb24c',
			'search_btn_background_color'           =>  '#6fb24c',
			'search_btn_border_color'               =>  '#6fb24c',
			'search_box_input_width'			    => '50',
			'search_box_padding_top'			    => '50',
			'search_box_padding_bottom'			    => '90',
			'search_box_margin_bottom'			    => '0',

			//Articles Listed In Category Box

			// Section Head
			'section_head_font_color'               =>  '#6fb24c',
			'section_head_background_color'         =>  '#FFFFFF',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_head_padding_top'              =>  20,
			'section_head_padding_bottom'           =>  20,
			'section_head_padding_left'             =>  20,
			'section_head_padding_right'            =>  20,
			'section_head_category_icon_color'      =>  '#4EB3C4',
			'section_desc_text_on'			        => 'on',

			// Section Body
			'section_body_background_color'         =>  '#FEFEFE',
			'section_box_height_mode'               =>  'section_min_height',
			'section_body_height'                   =>  130,
			'section_border_color'                  =>  '#CACACE',
			'section_divider_color'                 =>  '#FFFFFF',
			'section_category_font_color'           =>  '#40474f',
			'section_category_icon_color'           =>  '#6fb24c',
			'section_head_category_icon_size'       =>  '30',
			'article_font_color'                    =>  '#666666',
			'article_icon_color'                    =>  '#6fb24c',

			'breadcrumb_text_color'			            => '#8c1515',
			'back_navigation_text_color'			    => '#8c1515',
			'width'			                            => 'epkb-boxed',
			'section_font_size'			                => 'section_small_font',
			'expand_articles_icon'			            => 'ep_font_icon_plus_box',
			'section_head_alignment'			        => 'left',
			'section_head_category_icon_location'		=> 'no_icons',
			'section_head_category_icon'			    => 'ep_font_icon_pencil', // DEPRECATED: category icons
			'section_divider'			                => 'on',
			'section_divider_thickness'			        => '1',
			'section_box_shadow'			            => 'no_shadow',

			'section_border_width'			            => '1',
			'section_body_padding_left'			        => '22',
			'section_body_padding_right'			    => '4',
			'article_list_spacing'			            => '4',
			'search_title'			                    => 'Have a Question?',
			'tab_down_pointer'			                => 'on',
			'tab_nav_active_font_color'			        => '#8c1515',
			'tab_nav_active_background_color'			=> '#F1F1F1',
			'tab_nav_font_color'			            => '#686868',
			'tab_nav_border_color'			            => '#000000',
			'article_sidebar_component_priority' => array(
				'elay_sidebar_left' => '0',
				'toc_left' => '0',
				'kb_sidebar_left' => '0',
				'categories_left' => '1',
				'toc_content' => '0',
				'toc_right' => '1',
				'kb_sidebar_right' => '0',
				'categories_right' => '0'
			),
		),

	);
}
// add any strings from the themes settings to add them in the pot file
function epkb_dont_delete_text_from_arrays_for_translators() {
	return array(
		__( 'Standard', 'echo-knowledge-base' ),
		__( 'Spacious', 'echo-knowledge-base' ),
		__( 'Informative', 'echo-knowledge-base' ),
		__( 'Image', 'echo-knowledge-base' ),
		__( 'Modern', 'echo-knowledge-base' ),
		__( 'Bright', 'echo-knowledge-base' ),
		__( 'Formal', 'echo-knowledge-base' ),
		__( 'Distinct', 'echo-knowledge-base' ),
		__( 'FAQs', 'echo-knowledge-base' ),
		__( 'Organized', 'echo-knowledge-base' ),
		__( 'Organized 2', 'echo-knowledge-base' ),
		__( 'Icon Focused', 'echo-knowledge-base' ),
		__( 'Formal', 'echo-knowledge-base' ),
		__( 'Minimalistic', 'echo-knowledge-base' ),
		__( 'Informative', 'echo-knowledge-base' ),
		__( 'Simple', 'echo-knowledge-base' ),
		__( 'Left Icon Style', 'echo-knowledge-base' ),
		__( 'Collapsed', 'echo-knowledge-base' ),
		__( 'Formal', 'echo-knowledge-base' ),
		__( 'Compact', 'echo-knowledge-base' ),
		__( 'Plain', 'echo-knowledge-base' ),
		__( 'Simple layout and clean sidebar', 'echo-knowledge-base' ),
		__( 'Compact list of categories and articles', 'echo-knowledge-base' ),
		__( 'Structured look', 'echo-knowledge-base' ),
		__( 'All categories are collapsed', 'echo-knowledge-base' ),
		__( 'Simple layout', 'echo-knowledge-base' ),
		__( 'Minimal layout with icon on the left.', 'echo-knowledge-base' ),
		__( 'Minimal layout', 'echo-knowledge-base' ),
		__( 'Categories with descriptions', 'echo-knowledge-base' ),
		__( 'Big icons for easy navigation', 'echo-knowledge-base' ),
		__( 'No icons with category description', 'echo-knowledge-base' ),
		__( 'Simple style', 'echo-knowledge-base' ),
		__( 'Large icons with separation', 'echo-knowledge-base' ),
		__( 'Typical setup', 'echo-knowledge-base' ),
		__( 'Divide into tabs and categories', 'echo-knowledge-base' ),
		__( 'Tabs for each section', 'echo-knowledge-base' ),
		__( 'List of topics and articles', 'echo-knowledge-base' ),
		__( 'Categories above articles', 'echo-knowledge-base' ),
		__( 'Left alligned', 'echo-knowledge-base' ),
		__( 'Focus on colors', 'echo-knowledge-base' ),
		__( 'Modern simple look', 'echo-knowledge-base' ),
		__( 'Categories with description', 'echo-knowledge-base' ),
		__( 'Big icons for easy navigation', 'echo-knowledge-base' ),
		__( 'Initial KB setup', 'echo-knowledge-base' ),
		__( 'Basic Layout', 'echo-knowledge-base' ),
		__( 'Tabs Layout', 'echo-knowledge-base' ),
		__( 'Category Focused Layout', 'echo-knowledge-base' ),
		__( 'Product style', 'echo-knowledge-base' ),
		__( 'Clean', 'echo-knowledge-base' ),
		__( 'Help Center', 'echo-knowledge-base' ),
		__( 'Have a Question?', 'echo-knowledge-base' ),
		__( 'What are you looking for?', 'echo-knowledge-base' ),
		__( 'Welcome to our Knowledge Base', 'echo-knowledge-base' ),
		__( 'Self Help Documentation', 'echo-knowledge-base' ),
		__( 'Support Center', 'echo-knowledge-base' ),
		__( 'Tabs for each Product', 'echo-knowledge-base' ),
		__( 'Clean style', 'echo-knowledge-base' ),
	);
}