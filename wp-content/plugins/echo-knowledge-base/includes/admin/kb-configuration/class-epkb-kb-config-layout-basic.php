<?php

/**
 * Lists settings, default values and display of BASIC layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Layout_Basic {

    const LAYOUT_NAME = 'Basic';
	const CATEGORY_LEVELS = 6;

	// styles available for this layout
	const LAYOUT_STYLE_1 = 'Basic';
	const LAYOUT_STYLE_2 = 'Boxed';
	const LAYOUT_STYLE_3 = 'Style3';
	const DEMO_STYLE_1   = 'Demo1';

	// search box styles available for this layout
	const SEARCH_BOX_LAYOUT_STYLE_1 = 'Basic';
	const SEARCH_BOX_LAYOUT_STYLE_2 = 'todo1';
	const SEARCH_BOX_LAYOUT_STYLE_3 = 'todo2';
	const SEARCH_BOX_LAYOUT_STYLE_4 = 'todo4';


	/**
	 * Defines KB configuration for this theme.
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => 'false' )
	 *
	 * @return array with both basic and theme-specific configuration
	 */
	public static function get_fields_specification() {

        $config_specification = array(
        );

		return $config_specification;
	}

	/**
	 * Return HTML for settings controlling the Layout style
	 *
	 * @param $kb_page_layout
	 * @param $kb_config
	 * @return String $kb_main_page_layout
	 */
	public static function get_kb_config_style( $kb_page_layout, $kb_config ) {

		if ( $kb_page_layout != self::LAYOUT_NAME ) {
			return $kb_page_layout;
		}

		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();

		//Arg1 / Arg2  for text_and_select_fields_horizontal
		$arg1 = $feature_specs['section_body_height'] + array( 'value' => $kb_config['section_body_height'], 'current' => $kb_config['section_body_height'], 'input_group_class' => 'config-col-6', 'input_class' => 'config-col-12' );
		$arg2 = $feature_specs['section_box_height_mode'] + array( 'value'    => $kb_config['section_box_height_mode'], 'current'  => $kb_config['section_box_height_mode'], 'input_group_class' => 'config-col-6', 'input_class' => 'config-col-12' );

		//Advanced Settings
		$arg1_search_box_padding_vertical   = $feature_specs['search_box_padding_top'] + array( 'value' => $kb_config['search_box_padding_top'], 'current' => $kb_config['search_box_padding_top'], 'text_class' => 'config-col-6' );
		$arg2_search_box_padding_vertical   = $feature_specs['search_box_padding_bottom'] + array( 'value' => $kb_config['search_box_padding_bottom'], 'current' => $kb_config['search_box_padding_bottom'], 'text_class' => 'config-col-6' );
		$arg1_search_box_padding_horizontal = $feature_specs['search_box_padding_left'] + array( 'value' => $kb_config['search_box_padding_left'], 'current' => $kb_config['search_box_padding_left'], 'text_class' => 'config-col-6' );
		$arg2_search_box_padding_horizontal = $feature_specs['search_box_padding_right'] + array( 'value' => $kb_config['search_box_padding_right'], 'current' => $kb_config['search_box_padding_right'], 'text_class' => 'config-col-6' );
		$arg1_search_box_margin_vertical = $feature_specs['search_box_margin_top'] + array( 'value' => $kb_config['search_box_margin_top'], 'current' => $kb_config['search_box_margin_top'], 'text_class' => 'config-col-6' );
		$arg2_search_box_margin_vertical = $feature_specs['search_box_margin_bottom'] + array( 'value' => $kb_config['search_box_margin_bottom'], 'current' => $kb_config['search_box_margin_bottom'], 'text_class' => 'config-col-6' );

		$arg1_box_border = $feature_specs['section_border_radius'] + array( 'value' => $kb_config['section_border_radius'], 'current' => $kb_config['section_border_radius'], 'text_class' => 'config-col-6' );
		$arg2_box_border = $feature_specs['section_border_width'] + array( 'value' => $kb_config['section_border_width'], 'current' => $kb_config['section_border_width'], 'text_class' => 'config-col-6' );

		$arg1_section_head_padding_vertical = $feature_specs['section_head_padding_top'] + array( 'value' => $kb_config['section_head_padding_top'], 'current' => $kb_config['section_head_padding_top'], 'text_class' => 'config-col-6' );
		$arg2_section_head_padding_vertical = $feature_specs['section_head_padding_bottom'] + array( 'value' => $kb_config['section_head_padding_bottom'], 'current' => $kb_config['section_head_padding_bottom'], 'text_class' => 'config-col-6' );
		$arg1_section_head_padding_horizontal = $feature_specs['section_head_padding_left'] + array( 'value' => $kb_config['section_head_padding_left'], 'current' => $kb_config['section_head_padding_left'], 'text_class' => 'config-col-6' );
		$arg2_section_head_padding_horizontal = $feature_specs['section_head_padding_right'] + array( 'value' => $kb_config['section_head_padding_right'], 'current' => $kb_config['section_head_padding_right'], 'text_class' => 'config-col-6' );

		$arg1_section_body_padding_vertical = $feature_specs['section_body_padding_top'] + array( 'value' => $kb_config['section_body_padding_top'], 'current' => $kb_config['section_body_padding_top'], 'text_class' => 'config-col-6' );
		$arg2_section_body_padding_vertical = $feature_specs['section_body_padding_bottom'] + array( 'value' => $kb_config['section_body_padding_bottom'], 'current' => $kb_config['section_body_padding_bottom'], 'text_class' => 'config-col-6' );
		$arg1_section_body_padding_horizontal = $feature_specs['section_body_padding_left'] + array( 'value' => $kb_config['section_body_padding_left'], 'current' => $kb_config['section_body_padding_left'], 'text_class' => 'config-col-6' );
		$arg2_section_body_padding_horizontal = $feature_specs['section_body_padding_right'] + array( 'value' => $kb_config['section_body_padding_right'], 'current' => $kb_config['section_body_padding_right'], 'text_class' => 'config-col-6' );

		$search_input_input_arg1 = $feature_specs['search_box_input_width'] + array(
				'value'             => $kb_config['search_box_input_width'],
				'input_group_class' => 'config-col-12',
				'label_class'       => 'config-col-6',
				'input_class'       => 'config-col-2'

			);
		$search_input_input_arg2 = $feature_specs['search_input_border_width'] + array(
				'value' => $kb_config['search_input_border_width'],
				'input_group_class' => 'config-col-12',
				'label_class'       => 'config-col-6',
				'input_class'       => 'config-col-2'
			);

		$article_spacing_arg1 = $feature_specs['article_list_margin'] +  array(
				'value'             => $kb_config['article_list_margin'],
				'id'                => 'article_list_margin',
				'input_group_class' => 'config-col-12',
				'label_class'       => 'config-col-5',
				'input_class'       => 'config-col-3'
			);
		$article_spacing_arg2 = $feature_specs['article_list_spacing'] +  array(
				'value'             => $kb_config['article_list_spacing'],
				'id'                => 'article_list_spacing',
				'input_group_class' => 'config-col-12',
				'label_class'       => 'config-col-5',
				'input_class'       => 'config-col-3'
			);

		// SEARCH BOX - Layout
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'Search Layout', 'echo-knowledge-base' ),
			'class'        => 'eckb-mm-mp-links-tuning-searchbox-layout',
			'inputs' => array(
				'0' => $form->dropdown( $feature_specs['search_layout'] + array(
						'value' => $kb_config['search_layout'],
						'current' => $kb_config['search_layout'],
						'label_class' => 'config-col-3',
						'input_class' => 'config-col-7'
					) )
			)
		), $kb_page_layout);


		// SEARCH BOX - Advanced Style
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'Search Box - Advanced Style', 'echo-knowledge-base' ),
			'class'        => 'eckb-mm-mp-links-tuning-searchbox-advanced',
			'inputs' => array(
				'0' => $form->multiple_number_inputs(
					array(
						'id'                => 'search_box_padding',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Padding( px )', 'echo-knowledge-base' )
					),
					array( $arg1_search_box_padding_vertical, $arg2_search_box_padding_vertical ,$arg1_search_box_padding_horizontal, $arg2_search_box_padding_horizontal )
				),
				'1' => $form->multiple_number_inputs(
					array(
						'id'                => 'search_box_margin',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Margin( px )', 'echo-knowledge-base' )
					),
					array( $arg1_search_box_margin_vertical, $arg2_search_box_margin_vertical )
				),
				'2' => $form->multiple_number_inputs(
					array(
						'id'                => 'search_box_input_width_group',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Search Box Input ( % ) ( px )', 'echo-knowledge-base' )
					),
					array( $search_input_input_arg1, $search_input_input_arg2 )
				),
				'3' => $form->checkbox( $feature_specs['search_box_results_style'] + array(
						'value'             => $kb_config['search_box_results_style'],
						'id'                => 'search_box_results_style',
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2'
					) ),
				'4' => $form->text( $feature_specs['search_title_html_tag'] + array(
			            'value'             => $kb_config['search_title_html_tag'],
			            'input_group_class' => 'config-col-12',
			            'label_class'       => 'config-col-5',
			            'input_class'       => 'config-col-2'
		            ) ),
				'5' => $form->text( $feature_specs['search_title_font_size'] + array(
			            'value'             => $kb_config['search_title_font_size'],
			            'input_group_class' => 'config-col-12',
			            'label_class'       => 'config-col-5',
			            'input_class'       => 'config-col-2'
					) ),
			)), $kb_page_layout);

		// CONTENT - Style
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'Content - Style', 'echo-knowledge-base' ),
			'class'          => 'eckb-mm-mp-links-tuning-content-style',
			'inputs' => array(
				'0' => $form->dropdown( $feature_specs['width'] + array(
						'value' => $kb_config['width'],
						'current' => $kb_config['width'],
						'input_group_class' => 'config-col-12',
						'main_label_class'  => 'config-col-3',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4'
					) ),
				'1' => $form->radio_buttons_horizontal( $feature_specs['nof_columns'] + array(
						'id'        => 'front-end-columns',
						'value'     => $kb_config['nof_columns'],
						'current'   => $kb_config['nof_columns'],
						'input_group_class' => 'config-col-12',
						'main_label_class'  => 'config-col-5',
						'input_class'       => 'config-col-6',
						'radio_class'       => 'config-col-3'
					) ),
				'2' => $form->dropdown( $feature_specs['section_font_size'] + array(
						'value' => $kb_config['section_font_size'],
						'current' => $kb_config['section_font_size'],
						'input_group_class' => 'config-col-12',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4'
					) ),
				'3' => $form->radio_buttons_vertical( $feature_specs['show_articles_before_categories'] + array(
						'value'     => $kb_config['show_articles_before_categories'],
						'current'   => $kb_config['show_articles_before_categories'],
						'input_group_class' => 'config-col-12',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12'
					) ),
			)));

		// LIST OF ARTICLES - Style
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'List of Articles - Style', 'echo-knowledge-base' ),
			'class'        => 'eckb-mm-mp-links-tuning-listofarticles-style',
			'inputs' => array(
				'0' => $form->text( $feature_specs['nof_articles_displayed'] + array(
						'value' => $kb_config['nof_articles_displayed'],
						'input_group_class' => 'config-col-12',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-2'
					) ),
				'1' => $form->dropdown( $feature_specs['expand_articles_icon'] + array(
						'value' => $kb_config['expand_articles_icon'],
						'current' => $kb_config['expand_articles_icon'],
						'input_group_class' => 'config-col-12',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4'
					) ),
				'2' => $form->text_and_select_fields_horizontal( array(
					'id'                => 'list_height',
					'input_group_class' => 'config-col-12',
					'main_label_class'  => 'config-col-5',
					'label'             => __( 'Articles List Height', 'echo-knowledge-base' ),
					'input_class'       => 'config-col-6',
				), $arg1, $arg2 )
			)
		));

		// LIST OF ARTICLES - Advanced Style
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'List of Articles - Advanced Style', 'echo-knowledge-base' ),
			'class'        => 'eckb-mm-mp-links-tuning-listofarticles-advanced',
			'inputs' => array(

				'0' => $form->checkbox( $feature_specs['section_article_underline'] + array(
						'value'             => $kb_config['section_article_underline'],
						'id'                => 'section_article_underline',
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2'
					) ),
				'1' => $form->multiple_number_inputs(
					array(
						'id'                => 'article_list_group',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Article Spacing ( px )', 'echo-knowledge-base' )
					),
					array( $article_spacing_arg1, $article_spacing_arg2 )
				),
				'2' => $form->multiple_number_inputs(
					array(
						'id'                => 'section_body_padding',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Section Body Padding( px )', 'echo-knowledge-base' )
					),
					array( $arg1_section_body_padding_vertical, $arg2_section_body_padding_vertical, $arg1_section_body_padding_horizontal, $arg2_section_body_padding_horizontal)
				),
			)
		));

        // CATEGORIES - Style
        $form->option_group_filter( $kb_config, $feature_specs, array(
            'option-heading' => __( 'Categories - Style', 'echo-knowledge-base' ),
            'class'        => 'eckb-mm-mp-links-tuning-categories-style',
            'inputs' => array(
                    '0' => $form->dropdown( $feature_specs['section_head_alignment'] + array(
                        'value' => $kb_config['section_head_alignment'],
                        'current' => $kb_config['section_head_alignment'],
                        'input_group_class' => 'config-col-12',
                        'label_class'       => 'config-col-5',
                        'input_class'       => 'config-col-3'
                        ) ),
                    '1' => $form->checkbox( $feature_specs['section_divider'] + array(
                        'value'             => $kb_config['section_divider'],
                        'input_group_class' => 'config-col-12',
                        'label_class'       => 'config-col-5',
                        'input_class'       => 'config-col-2'
                        ) ),
                    '2' => $form->text( $feature_specs['section_divider_thickness'] + array(
                        'value'             => $kb_config['section_divider_thickness'],
                        'input_group_class' => 'config-col-12',
                        'label_class'       => 'config-col-5',
                        'input_class'       => 'config-col-2'
                    ) ),
                    '3' => $form->checkbox( $feature_specs['section_desc_text_on'] + array(
                            'value'             => $kb_config['section_desc_text_on'],
                            'input_group_class' => 'config-col-12',
                            'label_class'       => 'config-col-5',
                            'input_class'       => 'config-col-2'
                        ) ),
		            '4' => $form->checkbox( $feature_specs['section_hyperlink_text_on'] + array(
				            'value'             => $kb_config['section_hyperlink_text_on'],
				            'input_group_class' => 'config-col-12',
				            'label_class'       => 'config-col-5',
				            'input_class'       => 'config-col-2'
			            ) ),
            )
        ));

		// CATEGORIES - Style
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'Top Category Icon', 'echo-knowledge-base' ),
			'class'        => 'eckb-mm-mp-links-tuning-categories-style',
			'inputs' => array(
				'0' => $form->dropdown( $feature_specs['section_head_category_icon_location'] + array(
						'value' => $kb_config['section_head_category_icon_location'],
						'current' => $kb_config['section_head_category_icon_location'],
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-6'
					)),
				'1' => $form->text( $feature_specs['section_head_category_icon_size'] + array(
						'value'             => $kb_config['section_head_category_icon_size'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-2'
					) ),
			)
		));
	
		// CATEGORIES - Advanced Style
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'Categories - Advanced Style', 'echo-knowledge-base' ),
			'class'        => 'eckb-mm-mp-links-tuning-categories-advanced',
			'inputs' => array(
				'0' => $form->dropdown( $feature_specs['section_box_shadow'] + array(
						'value'             => $kb_config['section_box_shadow'],
						'current'           => $kb_config['section_box_shadow'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-6'
					) ),
				'1' => $form->multiple_number_inputs(
					array(
						'id'                => 'section_head_padding',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Section Head Padding( px )', 'echo-knowledge-base' )
					),
					array( $arg1_section_head_padding_vertical, $arg2_section_head_padding_vertical, $arg1_section_head_padding_horizontal, $arg2_section_head_padding_horizontal  )
				),

				'2' => $form->multiple_number_inputs(
					array(
						'id'                => 'box_border',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Box Border ( px )', 'echo-knowledge-base' )
					),
					array( $arg1_box_border, $arg2_box_border )
				)
			)
		));

		return $kb_page_layout;
	}

	/**
	 * Return HTML for settings controlling the Layout colors
	 *
	 * @param $kb_page_layout
	 * @param $kb_config
	 * @return String $kb_main_page_layout
	 */
	public static function get_kb_config_colors( $kb_page_layout, $kb_config ) {

		if ( $kb_page_layout != self::LAYOUT_NAME ) {
			return $kb_page_layout;
		}

		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();

		$arg1_input_text_field = $feature_specs['search_text_input_background_color'] + array( 'value' => $kb_config['search_text_input_background_color'], 'current' => $kb_config['search_text_input_background_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
		$arg2_input_text_field = $feature_specs['search_text_input_border_color'] + array( 'value' => $kb_config['search_text_input_border_color'], 'current' => $kb_config['search_text_input_border_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
		$arg1_button = $feature_specs['search_btn_background_color'] + array( 'value' => $kb_config['search_btn_background_color'], 'current' => $kb_config['search_btn_background_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
		$arg2_button = $feature_specs['search_btn_border_color'] + array( 'value' => $kb_config['search_btn_border_color'], 'current' => $kb_config['search_btn_border_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );

		$arg1_category_box_heading = $feature_specs['section_head_font_color'] + array( 'value' => $kb_config['section_head_font_color'], 'current' => $kb_config['section_head_font_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
		$arg2_category_box_heading = $feature_specs['section_head_background_color'] + array( 'value' => $kb_config['section_head_background_color'], 'current' => $kb_config['section_head_background_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );

		$arg1_sub_category = $feature_specs['section_category_font_color'] + array( 'value' => $kb_config['section_category_font_color'], 'current' => $kb_config['section_category_font_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
		$arg2_sub_category = $feature_specs['section_category_icon_color'] + array( 'value' => $kb_config['section_category_icon_color'], 'current' => $kb_config['section_category_icon_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );

		$arg1_article_list = $feature_specs['section_body_background_color'] + array( 'value' => $kb_config['section_body_background_color'], 'current' => $kb_config['section_body_background_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
		$arg2_article_list = $feature_specs['section_border_color'] + array( 'value' => $kb_config['section_border_color'], 'current' => $kb_config['section_border_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );

		$arg1_articles = $feature_specs['article_font_color'] + array( 'value' => $kb_config['article_font_color'], 'current' => $kb_config['article_font_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );
		$arg2_articles = $feature_specs['article_icon_color'] + array( 'value' => $kb_config['article_icon_color'], 'current' => $kb_config['article_icon_color'], 'class' => 'ekb-color-picker', 'text_class' => 'config-col-6' );

		// SEARCH BOX - Colors
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading'    => __( 'Search Box - Colors', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-mp-links-tuning-searchbox-colors',
			'inputs' => array(
				'0' => $form->text( $feature_specs['search_title_font_color'] + array(
						'value'             => $kb_config['search_title_font_color'],
						'input_group_class' => 'config-col-12',
						'class'             => 'ekb-color-picker',
						'label_class'       => 'config-col-4',
						'input_class'       => 'config-col-8 ekb-color-picker'
					) ),
				'1' => $form->text( $feature_specs['search_background_color'] + array(
						'value' => $kb_config['search_background_color'],
						'input_group_class' => 'config-col-12',
						'class'             => 'ekb-color-picker',
						'label_class'       => 'config-col-4',
						'input_class'       => 'config-col-8 ekb-color-picker'
					) ),
				'2' => $form->text_fields_horizontal( array(
					'id'                => 'input_text_field',
					'input_group_class' => 'config-col-12',
					'main_label_class'  => 'config-col-4',
					'input_class'       => 'config-col-7 ekb-color-picker',
					'label'             => __( 'Input Text Field', 'echo-knowledge-base' )
				), $arg1_input_text_field, $arg2_input_text_field ),
				'3' => $form->text_fields_horizontal( array(
					'id'                => 'button',
					'input_group_class' => 'config-col-12',
					'main_label_class'  => 'config-col-4',
					'input_class'       => 'config-col-7 ekb-color-picker',
					'label'             =>__( 'Button', 'echo-knowledge-base' )
				), $arg1_button, $arg2_button ) )
		), $kb_page_layout);

		// CONTENT - Colors
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading'    => __( 'Content - Colors', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-mp-links-tuning-content-colors',
			'inputs'            => array(
				'0' => $form->text( $feature_specs['background_color'] + array(
						'value' => $kb_config['background_color'],
						'input_group_class' => 'config-col-12',
						'class'             => 'ekb-color-picker',
						'label_class'       => 'config-col-4',
						'input_class'       => 'config-col-8 ekb-color-picker'
					) ) )
		));

		// LIST OF ARTICLES - Colors
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading'    => __( 'List of Articles - Colors', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-mp-links-tuning-listofarticles-colors',
			'inputs'            => array(
				'0' => $form->text_fields_horizontal( array(
					'id'                => 'article_list',
					'input_group_class' => 'config-col-12',
					'main_label_class'  => 'config-col-4',
					'input_class'       => 'config-col-7 ekb-color-picker',
					'label'             => __( 'Article List', 'echo-knowledge-base' )
				), $arg1_article_list, $arg2_article_list ),
				'1' => $form->text_fields_horizontal( array(
					'id'                => 'articles',
					'input_group_class' => 'config-col-12',
					'main_label_class'  => 'config-col-4',
					'input_class'       => 'config-col-7 ekb-color-picker',
					'label'             => __( 'Articles', 'echo-knowledge-base' )
				), $arg1_articles, $arg2_articles )
			)
		));

		// CATEGORIES - Colors
		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading'    => __( 'Categories - Colors', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-mp-links-tuning-categories-colors',
			'inputs'            => array(
				'0' => $form->text( $feature_specs['section_head_category_icon_color'] + array(
					'value'             => $kb_config['section_head_category_icon_color'],
					'class'             => 'ekb-color-picker',
					'input_group_class' => 'config-col-12',
					'label_class'       => 'config-col-4',
					'input_class'       => 'config-col-7 ekb-color-picker'
				) ),
				'1' => $form->text_fields_horizontal( array(
				'id'                => 'sub_category',
				'input_group_class' => 'config-col-12',
				'main_label_class'  => 'config-col-4',
				'input_class'       => 'config-col-7 ekb-color-picker',
				'label'             =>__(  'Sub-category', 'echo-knowledge-base' )
			), $arg1_sub_category, $arg2_sub_category ),
				'2' => $form->text( $feature_specs['section_divider_color'] + array(
					'value' => $kb_config['section_divider_color'],
					'class'             => 'ekb-color-picker',
					'input_group_class' => 'config-col-12',
					'label_class'       => 'config-col-4',
					'input_class'       => 'config-col-7 ekb-color-picker'
				) ),
				'3' => $form->text_fields_horizontal( array(
				'id'                => 'category_box_heading',
				'input_group_class' => 'config-col-12',
				'main_label_class'  => 'config-col-4',
				'input_class'       => 'config-col-7 ekb-color-picker',
				'label'             => __( 'Category Box Heading', 'echo-knowledge-base' )
			), $arg1_category_box_heading, $arg2_category_box_heading ),
				'4' => $form->text( $feature_specs['section_head_description_font_color'] + array(
					'value'             => $kb_config['section_head_description_font_color'],
					'class'             => 'ekb-color-picker',
					'input_group_class' => 'config-col-12',
					'label_class'       => 'config-col-4',
					'input_class'       => 'config-col-7 ekb-color-picker'
				) )
		)
	));

	return $kb_page_layout;
	}

	/**
	 * Return HTML for settings controlling the Layout Text
	 *
	 * @param $kb_page_layout
	 * @param $kb_config
	 * @return String $kb_page_layout
	 */
	public static function get_kb_config_text( $kb_page_layout, $kb_config ) {

		if ( $kb_page_layout != self::LAYOUT_NAME ) {
			return $kb_page_layout;
		}

		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();

		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading' => __( 'Search Box - Text', 'echo-knowledge-base' ),
			'class'        => 'eckb-mm-mp-links-alltext-text-searchbox eckb-mm-mp-links-tuning-searchbox-text',
			'inputs' => array(
				'0' => $form->text( $feature_specs['search_title'] +
					array( 'value' => $kb_config['search_title'], 'current' => $kb_config['search_title'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'   ) ),
				'1' => $form->text( $feature_specs['search_box_hint'] +
					array( 'value' => $kb_config['search_box_hint'], 'current' => $kb_config['search_box_hint'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'   ) ),
				'2' => $form->text( $feature_specs['search_button_name'] +
					array( 'value' => $kb_config['search_button_name'], 'current' => $kb_config['search_button_name'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'       ) ),
				'3' => $form->text( $feature_specs['search_results_msg'] +
					array( 'value' => $kb_config['search_results_msg'], 'current' => $kb_config['search_results_msg'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'       ) ),
				'4' => $form->text( $feature_specs['no_results_found'] +
					array( 'value' => $kb_config['no_results_found'], 'current' => $kb_config['no_results_found'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'   ) ),
				'5' => $form->text( $feature_specs['min_search_word_size_msg'] +
					array( 'value' => $kb_config['min_search_word_size_msg'], 'current' => $kb_config['min_search_word_size_msg'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'   ) )
			)
		), $kb_page_layout);

		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading'    => __( 'Categories - Text', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-mp-links-alltext-text-categories eckb-mm-mp-links-tuning-categories-text',
			'inputs' => array(
				'1' => $form->text( $feature_specs['category_empty_msg'] +
					array( 'value' => $kb_config['category_empty_msg'], 'current' => $kb_config['category_empty_msg'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'       ) )
			)
		));

		$form->option_group_filter( $kb_config, $feature_specs, array(
			'option-heading'    => __( 'Articles - Text', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-mp-links-alltext-text-articles eckb-mm-mp-links-tuning-listofarticles-text',
			'inputs' => array(
				'1' => $form->text( $feature_specs['collapse_articles_msg'] +
					array( 'value' => $kb_config['collapse_articles_msg'], 'current' => $kb_config['collapse_articles_msg'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'       ) ),
				'2' => $form->text( $feature_specs['show_all_articles_msg']
					+ array( 'value' => $kb_config['show_all_articles_msg'], 'current' => $kb_config['show_all_articles_msg'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-3',
						'input_class'       => 'config-col-9'       ) )
			)
		));

		return $kb_page_layout;
	}

	/**
	 * Return colors set based on selected layout and colors
	 *
	 * @param $colors_set
	 * @param $layout_name
	 * @param $set_name
	 *
	 * @return array
	 */
	public static function get_colors_set( $colors_set, $layout_name, $set_name ) {

		if ( $layout_name != self::LAYOUT_NAME ) {
			return $colors_set;
		}

		switch( $set_name ) {
			case 'black-white1':
			default:
				return self::color_reset_black_1();
				break;
			case 'black-white2':
				return self::color_reset_black_2();
				break;
			case 'black-white3':
				return self::color_reset_black_3();
				break;
			case 'black-white4':
				return self::color_reset_black_4();
				break;
			case 'blue1':
				return self::color_reset_blue_1();
				break;
			case 'blue2':
				return self::color_reset_blue_2();
				break;
			case 'blue3':
				return self::color_reset_blue_3();
				break;
			case 'blue4':
				return self::color_reset_blue_4();
				break;
			case 'green1':
				return self::color_reset_green_1();
				break;
			case 'green2':
				return self::color_reset_green_2();
				break;
			case 'green3':
				return self::color_reset_green_3();
				break;
			case 'green4':
				return self::color_reset_green_4();
				break;
			case 'red1':
				return self::color_reset_red_1();
				break;
			case 'red2':
				return self::color_reset_red_2();
				break;
			case 'red3':
				return self::color_reset_red_3();
				break;
			case 'red4':
				return self::color_reset_red_4();
				break;
			case 'demo_1':
				return self::demo_1_colors();
				break;
		}
	}

	/**
	 * DEFAULT for COLOR SETTINGS
	 *
	 * @return array
	 */
	private static function color_reset_black_1() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#686868',
			'search_background_color'               =>  '#FBFBFB',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#FFFFFF',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#827a74',
			'section_head_background_color'         =>  '#FFFFFF',
			'section_head_description_font_color'   =>  '#B3B3B3',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#DBDBDB',
			'section_divider_color'                 =>  '#DADADA',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#868686',
			'article_font_color'                    =>  '#000000',
			'article_icon_color'                    =>  '#B3B3B3'
		);
	}

	/*****************************************************************
	 *
	 *   USE AS DEFAULT FOR KB CONFIGURATION
	 *
	 ****************************************************************/
	public static function demo_1_colors() {  // needs to be Public

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#f7941d',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#CCCCCC',
			'search_btn_background_color'           =>  '#40474f',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#40474f',
			'section_head_background_color'         =>  '#FFFFFF',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#edf2f6',
			'section_category_font_color'           =>  '#40474f',
			'section_category_icon_color'           =>  '#f7941d',
			'section_head_category_icon_color'      =>  '#f7941d',
			'article_font_color'                    =>  '#459fed',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	public static function color_reset_black_2() {  // needs to be Public

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#000000',
			'search_background_color'               =>  '#F7F7F7',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#CCCCCC',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#8D8B8D',
			'section_head_background_color'         =>  '#F7F7F7',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#CDCDCD',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#868686',
			'article_font_color'                    =>  '#000000',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_black_3() {

		return array(
			//KB Main Page -> Colors -> General
			'background_color'                      =>  '#FFFFFF',

			//KB Main Page -> Colors -> Search Box
			'search_title_font_color'               =>  '#686868',
			'search_background_color'               =>  '#f1f1f1',
			'search_text_input_background_color'    =>  '#ffffff',
			'search_text_input_border_color'        =>  '#FFFFFF',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//KB Main Page -> Colors -> Articles Listed in Category Box
			'section_head_font_color'               =>  '#525252',
			'section_head_background_color'         =>  '#f1f1f1',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_body_background_color'         =>  '#fdfdfd',
			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#CDCDCD',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'article_font_color'                    =>  '#000000',
			'article_icon_color'                    =>  '#525252'
		);
	}
	private static function color_reset_black_4() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#000000',
			'search_background_color'               =>  '#e0e0e0',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#FFFFFF',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#ffffff',
			'section_head_background_color'         =>  '#7d7d7d',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_body_background_color'         =>  '#e0e0e0',
			'section_border_color'                  =>  '#7d7d7d',
			'section_divider_color'                 =>  '#FFFFFF',
			'section_category_font_color'           =>  '#000000',
			'section_category_icon_color'           =>  '#ffffff',
			'section_head_category_icon_color'      =>  '#ffffff',
			'article_font_color'                    =>  '#000000',
			'article_icon_color'                    =>  '#525252'
		);
	}

	private static function color_reset_red_1() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#fb8787',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#af1e1e',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#fb8787',
			'section_head_background_color'         =>  '#FFFFFF',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#fb8787',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_red_2() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#CC0000',
			'search_background_color'               =>  '#f9e5e5',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#FFFFFF',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#CC0000',
			'section_head_background_color'         =>  '#f9e5e5',
			'section_head_description_font_color'   =>  '#e57f7f',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#CDCDCD',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_red_3() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#CC0000',
			'search_background_color'               =>  '#f4c6c6',
			'search_text_input_background_color'    =>  '#ffffff',
			'search_text_input_border_color'        =>  '#FFFFFF',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#CC0000',
			'section_head_background_color'         =>  '#ffffff',
			'section_head_description_font_color'   =>  '#e57f7f',
			'section_body_background_color'         =>  '#fefcfc',
			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#CC0000',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#CC0000',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#CC0000'
		);
	}
	private static function color_reset_red_4() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#ffffff',
			'search_background_color'               =>  '#fb6262',
			'search_text_input_background_color'    =>  '#ffffff',
			'search_text_input_border_color'        =>  '#FFFFFF',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#ffffff',
			'section_head_background_color'         =>  '#fb6262',
			'section_head_description_font_color'   =>  '#ffffff',
			'section_body_background_color'         =>  '#fefcfc',
			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#CDCDCD',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#ffffff',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}

	private static function color_reset_blue_1() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#ffffff',
			'search_background_color'               =>  '#53ccfb',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#3093ba',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#53ccfb',
			'section_head_background_color'         =>  '#ffffff',
			'section_head_description_font_color'   =>  '#b3b3b3',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#53ccfb',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_blue_2() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#53ccfb',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#3093ba',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#ffffff',
			'section_head_background_color'         =>  '#53ccfb',
			'section_head_description_font_color'   =>  '#ffffff',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#ffffff',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_blue_3() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#11b3f2',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#3093ba',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#11b3f2',
			'section_head_background_color'         =>  '#ffffff',
			'section_head_description_font_color'   =>  '#ffffff',
			'section_body_background_color'         =>  '#fcfcfc',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#11b3f2',
			'section_head_category_icon_color'      =>  '#11b3f2',
			'article_font_color'                    =>  '#212121',
			'article_icon_color'                    =>  '#11b3f2'
		);
	}
	private static function color_reset_blue_4() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#ffffff',
			'search_background_color'               =>  '#4398ba',
			'search_text_input_background_color'    =>  '#ffffff',
			'search_text_input_border_color'        =>  '#FFFFFF',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#F1F1F1',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#ffffff',
			'section_head_background_color'         =>  '#4398ba',
			'section_head_description_font_color'   =>  '#ffffff',
			'section_body_background_color'         =>  '#f9f9f9',
			'section_border_color'                  =>  '#F7F7F7',
			'section_divider_color'                 =>  '#CDCDCD',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#ffffff',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}

	private static function color_reset_green_1() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#ffffff',
			'search_background_color'               =>  '#bfdac1',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#4a714e',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#4a714e',
			'section_head_background_color'         =>  '#ffffff',
			'section_head_description_font_color'   =>  '#bfdac1',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#b1d8b4',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#b1d8b4',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_green_2() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#9cb99f',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#4a714e',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#ffffff',
			'section_head_background_color'         =>  '#9cb99f',
			'section_head_description_font_color'   =>  '#ffffff',
			'section_body_background_color'         =>  '#FFFFFF',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#ffffff',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_green_3() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#769679',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#4a714e',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#ffffff',
			'section_head_background_color'         =>  '#769679',
			'section_head_description_font_color'   =>  '#ffffff',
			'section_body_background_color'         =>  '#edf4ee',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#ffffff',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}
	private static function color_reset_green_4() {

		return array(
			//General
			'background_color'                      =>  '#FFFFFF',

			//Search Box
			'search_title_font_color'               =>  '#FFFFFF',
			'search_background_color'               =>  '#628365',
			'search_text_input_background_color'    =>  '#FFFFFF',
			'search_text_input_border_color'        =>  '#DDDDDD',
			'search_btn_background_color'           =>  '#686868',
			'search_btn_border_color'               =>  '#DDDDDD',

			//Articles Listed In Category Box
			'section_head_font_color'               =>  '#ffffff',
			'section_head_background_color'         =>  '#628365',
			'section_head_description_font_color'   =>  '#ffffff',
			'section_body_background_color'         =>  '#edf4ee',
			'section_border_color'                  =>  '#dbdbdb',
			'section_divider_color'                 =>  '#c5c5c5',
			'section_category_font_color'           =>  '#868686',
			'section_category_icon_color'           =>  '#868686',
			'section_head_category_icon_color'      =>  '#ffffff',
			'article_font_color'                    =>  '#b3b3b3',
			'article_icon_color'                    =>  '#b3b3b3'
		);
	}

	/**
	 * Return Style set based on selected layout
	 *
	 * @param $style_set
	 * @param $layout_name
	 * @param $set_name
	 *
	 * @return array
	 */
	public static function get_style_set( $style_set, $layout_name, $set_name ) {

		if ( $layout_name != self::LAYOUT_NAME ) {
			return $style_set;
		}

		switch( $set_name) {
			case self::DEMO_STYLE_1:
				return self::demo_1_set();
				break;
			case self::LAYOUT_STYLE_2:
				return self::get_style_2_set();
				break;
			case self::LAYOUT_STYLE_3:
				return self::get_style_3_set();
				break;
			case self::LAYOUT_STYLE_1:
			default:
				return self::get_style_1_set();
				break;
		}
	}

    private static function get_style_1_set() {
        return array(
            //General
            'width'                         =>  'epkb-boxed',
            'section_font_size'             =>  'section_medium_font',
			'show_articles_before_categories'  =>  'on',
            'nof_articles_displayed'        =>  8,
            'expand_articles_icon'          =>  'ep_font_icon_arrow_carrot_right',
            'section_body_height'           =>  350,
            'section_box_height_mode'       =>  'section_no_height',

            'nof_columns'                   =>  'three-col',

            //Search Box
            'search_layout'                 =>  'epkb-search-form-1',
            'search_input_border_width'     =>  1,

            //Advanced Configuration

            // - Section
            'section_box_shadow'            =>  'no_shadow',
            'section_border_width'          =>  '0',
            'section_border_radius'         =>  '4',

            // - Section Head
            'section_head_alignment'        =>  'left',
            'section_divider'               =>  'on',
            'section_divider_thickness'     =>  2,
            'section_head_padding_top'      =>  20,
            'section_head_padding_bottom'   =>  20,
            'section_head_padding_left'     =>  10,
            'section_head_padding_right'    =>  0,

            // - Section Body
            'article_list_margin'           =>  10,
            'article_list_spacing'          =>  8,
            'section_article_underline'     =>  'on',
            'section_body_padding_top'      =>  4,
            'section_body_padding_bottom'   =>  4,
            'section_body_padding_left'     =>  4,
            'section_body_padding_right'    =>  4,

	        //Features
	   /*     'back_navigation_toggle'         => 'on',
	        'back_navigation_mode'           => 'navigate_browser_back',
	        'back_navigation_text_color'     => '#666666',
	        'back_navigation_bg_color'       => '#ffffff',
	        'back_navigation_border_color'   => '#dcdcdc',
	        'back_navigation_font_size'      => '16',
	        'back_navigation_border'         => 'solid',
	        'back_navigation_border_radius'  => '3',
	        'back_navigation_border_width'   => '1',
	        'back_navigation_margin_top'     => '4',
	        'back_navigation_margin_bottom'  => '4',
	        'back_navigation_margin_left'    => '4',
	        'back_navigation_margin_right'   => '4',
	        'back_navigation_padding_top'    => '4',
	        'back_navigation_padding_bottom' => '4',
	        'back_navigation_padding_left'   => '4',
	        'back_navigation_padding_right'  => '4', */
        );
    }

	/*****************************************************************
	 *
	 *   USE AS DEFAULT FOR KB CONFIGURATION
	 *
	 ****************************************************************/
	public static function demo_1_set() {
		return array(

			'templates_display_main_page_main_title'    => 'off',
			'templates_for_kb_padding_top'              => '',
			'categories_layout_list_mode'                    => 'list_top_categories',

			//KB Main Page -> General
			'width'                                     =>  'epkb-full',
			'section_font_size'                         =>  'section_medium_font',
			'show_articles_before_categories'           =>  'on',
			'nof_columns'                               =>  'three-col',
			'nof_articles_displayed'                    =>  8,
			'expand_articles_icon'                      =>  'ep_font_icon_arrow_carrot_right',

			//KB Main Page -> Search Box
			'search_layout'                             =>  'epkb-search-form-1',
			'search_input_border_width'                 =>  1,
			'search_box_padding_top'                    =>  50,
			'search_box_padding_bottom'                 =>  50,
			'search_box_padding_left'                   =>  0,
			'search_box_padding_right'                  =>  0,
			'search_box_margin_top'                     =>  0,
			'search_box_margin_bottom'                  =>  40,
			'search_box_input_width'                    =>  50,
			'search_box_results_style'                  =>  'off',
			'search_title_html_tag'                     => 'h2',
			'search_title_font_size'                    => '36',

			//KB Main Page -> Articles Listed in Sub-Category
			'section_head_alignment'                =>  'left',
			'section_head_padding_top'              =>  20,
			'section_head_padding_bottom'           =>  20,
			'section_head_padding_left'             =>  4,
			'section_head_padding_right'            =>  4,
			'section_head_category_icon_location'   => 'left',
			'section_head_category_icon_size'       => 21,
			'section_desc_text_on'                  =>  'off',
			'section_hyperlink_text_on'             =>  'off',
			'section_border_radius'                 =>  4,
			'section_border_width'                  =>  0,
			'section_box_shadow'                    =>  'no_shadow',
			'section_divider'                       =>  'on',
			'section_divider_thickness'             =>  5,
			'section_box_height_mode'               =>  'section_no_height',
			'section_body_height'                   =>  350,
			'section_body_padding_top'              =>  4,
			'section_body_padding_bottom'           =>  4,
			'section_body_padding_left'             =>  10,
			'section_body_padding_right'            =>  10,
			'section_article_underline'             =>  'on',
			'article_list_margin'                   =>  10,
			'article_list_spacing'                  =>  8,


			//Features
			/* 'back_navigation_toggle'         => 'on',
			 'back_navigation_mode'           => 'navigate_browser_back',
			 'back_navigation_text_color'     => '#666666',
			 'back_navigation_bg_color'       => '#ffffff',
			 'back_navigation_border_color'   => '#dcdcdc',
			 'back_navigation_font_size'      => '16',
			 'back_navigation_border'         => 'solid',
			 'back_navigation_border_radius'  => '3',
			 'back_navigation_border_width'   => '1',
			 'back_navigation_margin_top'     => '4',
			 'back_navigation_margin_bottom'  => '4',
			 'back_navigation_margin_left'    => '4',
			 'back_navigation_margin_right'   => '4',
			 'back_navigation_padding_top'    => '4',
			 'back_navigation_padding_bottom' => '4',
			 'back_navigation_padding_left'   => '4',
			 'back_navigation_padding_right'  => '4', */
		);
	}


    public static function get_style_2_set() {
		return array(

			//KB Main Page -> General
			'width'                         =>  'epkb-boxed',
			'section_font_size'             =>  'section_medium_font',
			'show_articles_before_categories'  =>  'on',
			'nof_columns'                   =>  'three-col',
			'nof_articles_displayed'        =>  8,
			'expand_articles_icon'          =>  'ep_font_icon_arrow_carrot_right',

			//KB Main Page -> Search Box
			'search_layout'                 =>  'epkb-search-form-1',
			'search_input_border_width'     =>  1,
			'search_box_padding_top'        =>  50,
			'search_box_padding_bottom'     =>  50,
			'search_box_padding_left'       =>  0,
			'search_box_padding_right'      =>  0,
			'search_box_margin_top'         =>  0,
			'search_box_margin_bottom'      =>  40,
			'search_box_input_width'        =>  50,
			'search_box_results_style'      =>  'off',
			'search_title_html_tag'         => 'h2',
			'search_title_font_size'        => '36',

			//KB Main Page -> Articles Listed in Sub-Category
			'section_head_alignment'        =>  'center',
			'section_head_padding_top'      =>  20,
			'section_head_padding_bottom'   =>  20,
			'section_head_padding_left'     =>  4,
			'section_head_padding_right'    =>  4,
			'section_desc_text_on'          =>  'off',
			'section_hyperlink_text_on'     =>  'off',
			'section_border_radius'         =>  4,
			'section_border_width'          =>  1,
			'section_box_shadow'            =>  'section_light_shadow',
			'section_divider'               =>  'on',
			'section_divider_thickness'     =>  1,
			'section_box_height_mode'       =>  'section_no_height',
			'section_body_height'           =>  350,
			'section_body_padding_top'      =>  4,
			'section_body_padding_bottom'   =>  4,
			'section_body_padding_left'     =>  10,
			'section_body_padding_right'    =>  10,
			'section_article_underline'     =>  'on',
			'article_list_margin'           =>  10,
			'article_list_spacing'          =>  8,
			);
	}

	//Not used
	private static function get_style_3_set() {
		return array(
			//Articles Listed In Category Box
			'section_border_width'          => '1',

			//Features
			/* 'back_navigation_toggle'         => 'on',
			 'back_navigation_mode'           => 'navigate_browser_back',
			 'back_navigation_text_color'     => '#666666',
			 'back_navigation_bg_color'       => '#ffffff',
			 'back_navigation_border_color'   => '#dcdcdc',
			 'back_navigation_font_size'      => '16',
			 'back_navigation_border'         => 'solid',
			 'back_navigation_border_radius'  => '3',
			 'back_navigation_border_width'   => '1',
			 'back_navigation_margin_top'     => '4',
			 'back_navigation_margin_bottom'  => '4',
			 'back_navigation_margin_left'    => '4',
			 'back_navigation_margin_right'   => '4',
			 'back_navigation_padding_top'    => '4',
			 'back_navigation_padding_bottom' => '4',
			 'back_navigation_padding_left'   => '4',
			 'back_navigation_padding_right'  => '4', */
		);
	}

	/**
	 * Return search box Style set based on selected layout
	 *
	 * @param $style_set
	 * @param $layout_name
	 * @param $set_name
	 *
	 * @return array
	 */
	public static function get_search_box_style_set( $style_set, $layout_name, $set_name ) {

		if ( $layout_name != self::LAYOUT_NAME ) {
			return $style_set;
		}

		switch( $set_name) {
			case self::SEARCH_BOX_LAYOUT_STYLE_2:
				return self::get_search_box_style_2_set();
				break;
			case self::SEARCH_BOX_LAYOUT_STYLE_3:
				return self::get_search_box_style_3_set();
				break;
			case self::SEARCH_BOX_LAYOUT_STYLE_4:
				return self::get_search_box_style_4_set();
				break;
			case self::SEARCH_BOX_LAYOUT_STYLE_1:
			default:
				return self::get_search_box_style_1_set();
				break;
		}
	}

	private static function get_search_box_style_1_set() {
		return array(

			//Layout
			'search_layout'                 =>  'epkb-search-form-1',
			//Padding
			'search_box_padding_top'        =>  40,
			'search_box_padding_bottom'     =>  40,
			'search_box_padding_left'       =>  0,
			'search_box_padding_right'      =>  0,
			//Margin
			'search_box_margin_top'         =>  40,
			'search_box_margin_bottom'      =>  40,
			'search_box_margin_left'        =>  0,
			'search_box_margin_right'       =>  0,
			//Search Input Width
			'search_box_input_width'        =>  50,
			'search_box_results_style'      =>  'off',
			'search_title_html_tag'         => 'h2',
			'search_title_font_size'        => '36',

			//Search Input Border Width
			'search_input_border_width'     =>  1

		);
	}

	private static function get_search_box_style_2_set() {
		return array(
			//Layout
			'search_layout'                 =>  'epkb-search-form-1',
			//Padding
			'search_box_padding_top'        =>  40,
			'search_box_padding_bottom'     =>  40,
			'search_box_padding_left'       =>  0,
			'search_box_padding_right'      =>  0,
			//Margin
			'search_box_margin_top'         =>  40,
			'search_box_margin_bottom'      =>  40,
			'search_box_margin_left'        =>  0,
			'search_box_margin_right'       =>  0,
			//Search Input Width
			'search_box_input_width'        =>  50,
			'search_box_results_style'      =>  'off',
			'search_title_html_tag'         => 'h2',
			'search_title_font_size'        => '36',

			//Search Input Border Width
			'search_input_border_width'     =>  1
		);
	}

	private static function get_search_box_style_3_set() {
		return array(
			//Layout
			'search_layout'                 =>  'epkb-search-form-1',
			//Padding
			'search_box_padding_top'        =>  40,
			'search_box_padding_bottom'     =>  40,
			'search_box_padding_left'       =>  0,
			'search_box_padding_right'      =>  0,
			//Margin
			'search_box_margin_top'         =>  40,
			'search_box_margin_bottom'      =>  40,
			'search_box_margin_left'        =>  0,
			'search_box_margin_right'       =>  0,
			//Search Input Width
			'search_box_input_width'        =>  50,
			'search_box_results_style'      =>  'off',
			'search_title_html_tag'         => 'h2',
			'search_title_font_size'        => '36',

			//Search Input Border Width
			'search_input_border_width'     =>  1
		);
	}

	private static function get_search_box_style_4_set() {
		return array(
			//Layout
			'search_layout'                 =>  'epkb-search-form-1',
			//Padding
			'search_box_padding_top'        =>  40,
			'search_box_padding_bottom'     =>  40,
			'search_box_padding_left'       =>  0,
			'search_box_padding_right'      =>  0,
			//Margin
			'search_box_margin_top'         =>  40,
			'search_box_margin_bottom'      =>  40,
			'search_box_margin_left'        =>  0,
			'search_box_margin_right'       =>  0,
			//Search Input Width
			'search_box_input_width'        =>  50,
			'search_box_results_style'      =>  'off',
			'search_title_html_tag'         => 'h2',
			'search_title_font_size'        => '36',
			//Search Input Border Width
			'search_input_border_width'     =>  1
		);
	}
}
