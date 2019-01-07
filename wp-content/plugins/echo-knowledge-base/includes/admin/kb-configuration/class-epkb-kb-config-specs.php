<?php

/**
 * Lists all KB configuration settings and adds filter to get configuration from add-ons.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Specs {
	
	private static $cached_specs = array();

	public static function get_categories_display_order() {
		$base_order = array( 'alphabetical-title' => __( 'Alphabetical by Name', 'echo-knowledge-base' ),
							 'created-date' => __( 'Chronological by Date Created', 'echo-knowledge-base' ),
							 'user-sequenced' => __( 'Custom - Drag and Drop Categories on the Left', 'echo-knowledge-base' ) );
		return apply_filters( 'epkb_categories_display_order', $base_order );
	}

	public static function get_articles_display_order() {
		$base_order = array( 'alphabetical-title' => __( 'Alphabetical by Title', 'echo-knowledge-base' ),
		                     'created-date' => __( 'Chronological by Date Created', 'echo-knowledge-base' ),
		                     'user-sequenced' => __( 'Custom - Drag and Drop articles on the Left', 'echo-knowledge-base' ) );
		return apply_filters( 'epkb_articles_display_order', $base_order );
	}

	/**
	 * Defines how KB configuration fields will be displayed, initialized and validated/sanitized
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @param int $kb_id is the ID of knowledge base to get default config for
	 * @return array with KB config specification
	 */
	public static function get_fields_specification( $kb_id ) {

		// if kb_id is invalid use default KB
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'setting kb_id to 0 because kb_id is not positive int', $kb_id );
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// retrieve settings if already cached
		if ( ! empty(self::$cached_specs[$kb_id]) && is_array(self::$cached_specs[$kb_id]) ) {
			return self::$cached_specs[$kb_id];
		}


		// all CORE settings are listed here; 'name' used for HTML elements
		$config_specification = array(

			/******************************************************************************
			 *
			 *  Internal settings
			 *
			 ******************************************************************************/

			'id' => array(
				'label'       => 'kb_id',
				'type'        => EPKB_Input_Filter::ID,
				'internal'    => true,
				'default'     => $kb_id
			),
			/* 'default_category_id' => array(
				'label'       => 'default_category_id',
				'type'        => EPKB_Input_Filter::ID,
				'internal'    => true,
				'default'     => 0
			), */
			'status' => array(
				'label'       => 'status',
				'type'        => EPKB_Input_Filter::ENUMERATION,
				'options'     => array( EPKB_KB_Status::PUBLISHED, EPKB_KB_Status::ARCHIVED ),
				'internal'    => true,
				'default'     => EPKB_KB_Status::PUBLISHED
			),
			'kb_main_pages' => array(
				'label'       => 'kb_main_pages',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),


			/******************************************************************************
			 *
			 *  Overview
			 *
			 ******************************************************************************/

			'kb_name' => array(
				'label'       => __( 'CPT Name', 'echo-knowledge-base' ),
				'name'        => 'kb_name',
				'info'        => __( 'The name of this Knowledge Base and its Custom Post Type.' ),
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'reload'      => true,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == 1 ? '' : ' ' . $kb_id)
			),
			'kb_articles_common_path' => array(
				'label'       => __( 'Common Path for Articles', 'echo-knowledge-base' ),
				'name'        => 'kb_articles_common_path',
				'info'        => __( 'Each KB article URL with share this common base in its URL e.g. See online help for details.' ),
				'size'        => '20',
				'max'         => '70',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::URL,
				'default'     => EPKB_KB_Handler::get_default_slug( $kb_id )
			),
			'kb_main_page_layout' => array(
				'label'       => __( 'Main Page Layout', 'echo-knowledge-base' ),
				'name'        => 'kb_main_page_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => EPKB_KB_Config_Layouts::get_main_page_layout_name_value(),
				'default'     => EPKB_KB_Config_Layout_Basic::LAYOUT_NAME,
				'info'        => __( '• <strong>Basic layout</strong> – This is a typical knowledge base layout, listing categories in rows and columns.<br/><br/>' .
				                 '• <strong>Tabs layout</strong> – This layout has additional top-level categories listed in tabs at the top of KB. Each tab ' .
				                 'displays a list of categories. This is useful e.g. when you need to separate articles into different groups (tabs) based on ' .
				                 'different services or products supported.<br/><br/>Additional layouts are available through our add-on plugins.' ),
			),
			'kb_main_page_category_link' => array(
				'label'       => __( 'Main Page Category Link', 'echo-knowledge-base' ),
				'name'        => 'kb_main_page_category_link',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array('default' => 'Default', 'category_archive' => 'Category Archive Page' ),
				'default'     => 'default',
				'info'        => __( 'Whether on the KB Main Page each Category should link to its Category Archive Page.' ),
			),
			'kb_article_page_layout' => array(
				'label'       => __( 'Article Page Layout', 'echo-knowledge-base' ),
				'name'        => 'kb_article_page_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => EPKB_KB_Config_Layouts::get_article_page_layout_names(),
				'default'     => EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT,
				'info'        => __( 'Layout chosen for Article pages.' ),
			),
			'categories_display_sequence' => array(
				'label'       => __( 'Categories Sequence', 'echo-knowledge-base' ),
				'name'        => 'categories_display_sequence',
				'info'        => __( 'Order in which categories will appear on the KB main page.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_categories_display_order(),
				'default'     => 'alphabetical-title'
			),
			'articles_display_sequence' => array(
				'label'       => __( 'Articles Sequence', 'echo-knowledge-base' ),
				'name'        => 'articles_display_sequence',
				'info'        => __( 'An order in which articles will be listed within each category on Knowledge Base main page.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_articles_display_order(),
				'default'     => 'alphabetical-title'
			),
			'css_version' => array(
				'label'       => __( 'CSS Version', 'echo-knowledge-base' ),
				'name'        => 'css_version',
				'info'        => __( 'Use Current Version unless you have an older installation and need to keep using the Legacy Version of KB CSS.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'css-current'   => _x( 'Current Version', 'echo-knowledge-base' ),
					'css-legacy'    => _x( 'Legacy Version', 'echo-knowledge-base' ) ),
				'default'     => 'css-current'
			),
			'templates_for_kb' => array(
				'label'       => __( 'Preview mode is not available for this option. View front-end to see the template in action.', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb',
				'info'        => __( 'Description.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'current_theme_templates'    => __( 'Current Theme Templates Used for Posts and Pages' ),
					'kb_templates'       => __( 'Knowledge Base Templates Designed for Articles' ),
				),
				'default'     => 'current_theme_templates'
			),
			

			/******************************************************************************
			 *
			 *  KB TEMPLATE settings
			 *
			 ******************************************************************************/

			//Main Page

			'templates_display_main_page_main_title' => array(
				'label'       => __( 'Display Main Title', 'echo-knowledge-base' ),
				'name'        => 'templates_display_main_page_main_title',
				'info'        => __( 'If KB Template is active, then choose whether or not to display the Main title on the main page.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
            'templates_for_kb_padding_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_top',
                'info'        => __( 'Adds spacing above all the KB Content' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '30'
            ),
            'templates_for_kb_padding_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_bottom',
                'info'        => __( 'Adds spacing below all the KB Content' ),
                'max'         => '500',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '50'
            ),
            'templates_for_kb_padding_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_left',
                'info'        => __( 'Adds spacing left of all the KB Content' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'templates_for_kb_padding_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_right',
                'info'        => __( 'Adds spacing right of all the KB Content' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'templates_for_kb_margin_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_top',
                'info'        => __( 'Adds Padding Top.' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'templates_for_kb_margin_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_bottom',
                'info'        => __( 'Adds Padding Bottom.' ),
                'max'         => '500',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '50'
            ),
            'templates_for_kb_margin_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_left',
                'info'        => __( 'Adds Padding Left.' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'templates_for_kb_margin_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_right',
                'info'        => __( 'Adds Padding Right.' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),

			//Article Page
			'templates_for_kb_article_reset'            => array(
				'label'       => __( 'Article CSS Reset', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_reset',
				'info'        => __( 'Adds a CSS reset that nullifies all CSS being applied to KB Article elements by CSS of a current theme or installed plugins. ' .
					'Switching on CSS reset will remove any undesirable effects from KB articles.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'templates_for_kb_article_defaults'         => array(
				'label'       => __( 'Article CSS Defaults', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_defaults',
				'info'        => __( 'Adds a CSS defaults styling to KB article elements in a format that is the best for writing documentation.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'templates_for_kb_article_padding_top'      => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_top',
				'info'        => __( 'Adds spacing above the KB article' ),
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '30'
			),
			'templates_for_kb_article_padding_bottom'   => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_bottom',
				'info'        => __( 'Adds spacing below the KB article' ),
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '50'
			),
			'templates_for_kb_article_padding_left'     => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_left',
				'info'        => __( 'Adds spacing to the left of the KB article' ),
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'templates_for_kb_article_padding_right'    => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_right',
				'info'        => __( 'Adds spacing to the right of the KB article' ),
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'templates_for_kb_article_margin_top'       => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_top',
				'info'        => __( 'Adds spacing at the top of the KB article' ),
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'templates_for_kb_article_margin_bottom'    => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_bottom',
				'info'        => __( 'Adds spacing at the bottom of the KB article.' ),
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '50'
			),
			'templates_for_kb_article_margin_left'      => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_left',
				'info'        => __( 'Adds spacing on the left side of the KB article.' ),
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'templates_for_kb_article_margin_right'     => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_right',
				'info'        => __( 'Adds spacing on the right side of the KB article.' ),
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),

			//Category Archive Page
			'templates_for_kb_category_archive_page_style' => array(
				'label'       => __( 'Style for Category Archive Page', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_category_archive_page_style',
				'info'        => __( 'Change the look of the Category Archive Page.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-category-archive-style-1' => __( 'Style 1 ( Basic List )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-2' => __( 'Style 2 ( Standard )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-3' => __( 'Style 3 ( Standard 2 )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-4' => __( 'Style 4 ( Box )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-5' => __( 'Style 5 ( Grid )', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-category-archive-style-1'
			),
			'templates_for_kb_category_archive_page_heading_description' => array(
				'label'       => __( 'Heading Description', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_category_archive_page_heading_description',
				'info'        => __( 'Heading Description' ),
				'size'        => '30',
				'max'         => '30',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Category', 'echo-knowledge-base' )
			),

			/******************************************************************************
			 *
			 *  ARTICLES FEATURES settings
			 *
			 ******************************************************************************/

			/******   COMMENTS   ******/
			'articles_comments_global' => array(
				'label'       => __( 'Comments', 'echo-knowledge-base' ),
				'name'        => 'articles_comments_global',
				'info'        => __( 'Controls whether article comments will be available or not. If this option is "on" then when and how comments will be displayed ' .
				                     'is controlled by:<br/><br/>
				                      • your theme,<br/>  •  the WordPress Discussion settings<br/>  • per-article settings.<br/><br/> See our documentation <lin> for further details.'),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			/******   BREADCRUMB   ******/
			'breadcrumb_toggle' => array(
				'label'       => __( 'Show Breadcrumbs', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_toggle',
				'info'        => __( 'Whether or not to display breadcrumbs above articles.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'breadcrumb_icon_separator' => array(
				'label'       => __( 'Breadcrumb Separator', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_icon_separator',
				'info'        => __( 'Icon that is used to separate individual breadcrumb links.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'ep_font_icon_none'    => __( '-- No Icon --',   'echo-knowledge-base' ),
					'ep_font_icon_right_arrow'   => __( 'Right Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_left_arrow'    => __( 'Left Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right_circle'    => __( 'Arrow Right Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left_circle'    => __( 'Arrow Left Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left'    => __( 'Arrow Carrot Left',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right'    => __( 'Arrow Carrot Right',   'echo-knowledge-base' ),
				),
				'default'     => 'ep_font_icon_right_arrow'
			),
            'breadcrumb_padding_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_top',
                'info'        => __( 'Adds Padding Top.' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'breadcrumb_padding_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_bottom',
                'info'        => __( 'Adds Padding Bottom.' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'breadcrumb_padding_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_left',
                'info'        => __( 'Adds Padding Left.' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'breadcrumb_padding_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_right',
                'info'        => __( 'Adds Padding Right.' ),
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
			'breadcrumb_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_top',
				'info'        => __( 'Adds margin Top.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_bottom',
				'info'        => __( 'Adds margin Bottom.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '20'
			),
			'breadcrumb_margin_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_left',
				'info'        => __( 'Adds margin Left.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_right',
				'info'        => __( 'Adds margin Right.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
            'breadcrumb_text_color' => array(
                'label'       => __( 'Breadcrumb Text Color', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_text_color',
                'info'        => __( 'Color of Breadcrumb Text' ),
                'size'        => '10',
                'max'         => '7',
                'min'         => '7',
                'type'        => EPKB_Input_Filter::COLOR_HEX,
                'default'     => '#000000'
            ),
			'breadcrumb_description_text' => array(
				'label'       => __( 'Breadcrumb Description', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_description_text',
				'info'        => __( 'Text appearing before the breadcrumb navigation links.' ),
				'size'        => '50',
				'max'         => '50',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'You are here:', 'echo-knowledge-base' )
			),
			'breadcrumb_home_text' => array(
				'label'       => __( 'Breadcrumb Home Text', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_home_text',
				'info'        => __( 'Text of a link that goes to the KB Main Page and is shown as the root of the breadcrumb.' ),
				'size'        => '50',
				'max'         => '50',
				'min'         => '2',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'KB Home', 'echo-knowledge-base' )
			),
			'breadcrumb_font_size' => array(
				'label'       => __( 'Relative Text Size', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_font_size',
				'info'        => __( 'Sets overall text size for breadcrumbs.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'10' => _x( 'Extra Small', 'font size', 'echo-knowledge-base' ),
					'12' => _x( 'Small', 'font size', 'echo-knowledge-base' ),
					'14' => _x( 'Medium', 'font size', 'echo-knowledge-base' ),
					'16' => _x( 'Large', 'font size', 'echo-knowledge-base' ) ),
				'default'     => '12'
			),


			/******   BACK NAVIGATION   ******/
            'back_navigation_toggle' => array(
                'label'       => __( 'Show Back Button', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_toggle',
                'info'        => __( 'Whether or not to display back button on the article page.' ),
                'type'        => EPKB_Input_Filter::CHECKBOX,
                'default'     => 'on'
            ),
            'back_navigation_mode' => array(
                'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_mode',
                'info'        => __( 'Where user will be redirected to after clicking the back button.' ),
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'navigate_browser_back'   => __( 'Browser Go Back Action',   'echo-knowledge-base' ),
                    'navigate_kb_main_page'   => __( 'Redirect to KB Main Page', 'echo-knowledge-base' ),
                ),
                'default'     => 'navigate_browser_back'
            ),
            'back_navigation_text' => array(
                'label'       => __( 'Text', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_text',
                'info'        => __( 'Navigation Text' ),
                'size'        => '30',
                'max'         => '30',
                'min'         => '1',
                'mandatory'   => false,
                'type'        => EPKB_Input_Filter::TEXT,
                'default'     => __( '< Back', 'echo-knowledge-base' )
            ),
            'back_navigation_text_color' => array(
				'label'       => __( 'Text Color', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_text_color',
				'info'        => __( 'Text color of the back button.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#666666'
			),
			'back_navigation_bg_color' => array(
				'label'       => __( 'Background Color', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_bg_color',
				'info'        => __( 'Background color of the back button' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
            'back_navigation_border_color' => array(
                'label'       => __( 'Border Color', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_border_color',
                'info'        => __( 'Border color of the back button' ),
                'size'        => '10',
                'max'         => '7',
                'min'         => '7',
                'type'        => EPKB_Input_Filter::COLOR_HEX,
                'default'     => '#dcdcdc'
            ),
			'back_navigation_font_size' => array(
				'label'       => __( 'Text Size', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_font_size',
				'info'        => __( 'Size of the button text.' ),
				'size'        => '50',
				'max'         => '50',
				'min'         => '2',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( '16', 'echo-knowledge-base' )
			),
            'back_navigation_border' => array(
                'label'       => __( 'Button Border', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_border',
                'info'        => __( 'Border for the back button.' ),
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'none'    => __( '-- No Border --', 'echo-knowledge-base' ),
                    'solid'   => __( 'Solid', 'echo-knowledge-base' ),
                ),
                'default'     => 'solid'
            ),
			'back_navigation_border_radius' => array(
				'label'       => __( 'Border Radius', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_radius',
				'info'        => __( 'The round curve of the box corners. The higher the number, the more curved it becomes.' ),
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( '3', 'echo-knowledge-base' )
			),
			'back_navigation_border_width' => array(
				'label'       => __( 'Border Width', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_width',
				'info'        => __( 'Width of the border around the back button.' ),
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( '1', 'echo-knowledge-base' )
			),
			'back_navigation_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_top',
				'info'        => __( 'Adds Margin Top.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_bottom',
				'info'        => __( 'Adds Margin Bottom.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_left',
				'info'        => __( 'Adds Margin Left.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_right',
				'info'        => __( 'Adds Margin Right.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_top',
				'info'        => __( 'Adds Padding Top.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_bottom',
				'info'        => __( 'Adds Padding Bottom.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_left',
				'info'        => __( 'Adds Padding Left.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_right',
				'info'        => __( 'Adds Padding Right.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),

			/******   OTHER   ******/
			'last_udpated_on' => array(
				'label'       => __( 'Last Updated Display', 'echo-knowledge-base' ),
				'name'        => 'last_udpated_on',
				'info'        => __( 'When was the post last modified.', 'echo-knowledge-base' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'           => __( 'Not Displayed', 'echo-knowledge-base' ),
					'article_top'    => __( 'Show Above Article', 'echo-knowledge-base' ),
					'article_bottom' => __( 'Show Below Article', 'echo-knowledge-base' )
				),
				'default'     => 'none'
			),
			'last_udpated_on_text' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'last_udpated_on_text',
				'info'        => __( 'Text displayed in front last modified date of a post.' ),
				'size'        => '30',
				'max'         => '60',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Last Updated On', 'echo-knowledge-base' )
			),

            /******   TAGS   ******/
            /* do we need this? 'tags_toggle' => array(
                'label'       => __( 'Show Tags', 'echo-knowledge-base' ),
                'name'        => 'tags_toggle',
                'info'        => __( 'Whether or not to display article tags.' ),
                'type'        => EPKB_Input_Filter::CHECKBOX,
                'default'     => 'on'
            ), */


		);

		// add CORE LAYOUTS SHARED configuration
		$config_specification = array_merge( $config_specification, self::shared_configuration() );

		// add CORE LAYOUTS non-shared configuration
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Basic::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Tabs::get_fields_specification() );

		self::$cached_specs[$kb_id] = empty($config_specification_temp) || count($config_specification) > count($config_specification_temp)
								? $config_specification : $config_specification_temp;

		return self::$cached_specs[$kb_id];
	}

	/**
	 * Shared STYLE, COLOR and TEXT configuration between CORE LAYOUTS
	 *
	 * @return array
	 */
	public static function shared_configuration() {

		$default_style = EPKB_KB_Config_Layout_Basic::get_style_2_set();
		$default_color = EPKB_KB_Config_Layout_Basic::color_reset_black_2();

		/**
		 * Layout/color settings shared among layouts and color sets are listed here.
		 * If a setting becomes unique to color/layout, move it to its file.
		 * If a setting becomes common, move it from its file to this file.
		 */
		$shared_specification = array(

			/******************************************************************************
			 *
			 *  KB Main Layout - Layout and Style
			 *
			 ******************************************************************************/

			/***  KB Main Page -> General ***/

			'width' => array(
				'label'       => __( 'Page Width', 'echo-knowledge-base' ),
				'name'        => 'width',
				'info'        => __( 'Full Width will expand as far as the page allows for the active theme.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-boxed' => __( 'Boxed Width', 'echo-knowledge-base' ),
					'epkb-full' => __( 'Full Width', 'echo-knowledge-base' ) ),
				'default'     => $default_style['width']
			),
			'section_font_size' => array(
				'label'       => __( 'Relative Text Size', 'echo-knowledge-base' ),
				'name'        => 'section_font_size',
				'info'        => __( 'Sets overall text size that affects Category and Article titles.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'section_xsmall_font' => _x( 'Extra Small', 'font size', 'echo-knowledge-base' ),
					'section_small_font' => _x( 'Small', 'font size', 'echo-knowledge-base' ),
					'section_medium_font' => _x( 'Medium', 'font size', 'echo-knowledge-base' ),
					'section_large_font' => _x( 'Large', 'font size', 'echo-knowledge-base' ) ),
				'default'     => $default_style['section_font_size']
			),
			'nof_columns' => array(
				'label'       => __( 'Number of Columns', 'echo-knowledge-base' ),
				'name'        => 'nof_columns',
				'info'        => __( 'Each sub-category will list its articles. How many sub-categories will be displayed accross a page can be ' .
				                     'controlled by this configuration. The higher the number the more sub-categories will fit into one row ' .
				                     '(and sub-categories that do not fit will show in the next row). However more columns in a row will make each' .
				                     ' sub-category narrower and will cause articles with longer titles to wrap.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'one-col' => '1', 'two-col' => '2', 'three-col' => '3', 'four-col' => '4' ),
				'default'     => $default_style['nof_columns']
			),
			'nof_articles_displayed' => array(
				'label'       => __( 'Number of Articles Listed', 'echo-knowledge-base' ),
				'name'        => 'nof_articles_displayed',
				'info'        => __( 'The number of articles that will be displayed in a list under each sub-category.' ),
				'max'         => '200',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['nof_articles_displayed'],
			),
			'expand_articles_icon' => array(
				'label'       => __( 'Icon to Expand/Collapse Articles', 'echo-knowledge-base' ),
				'name'        => 'expand_articles_icon',
				'info'        => __( 'When sub-category has articles listed below it this icon allows user to expand and collapse that list.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'ep_font_icon_plus_box' => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_plus' => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_right_arrow' => _x( 'Arrow Triangle', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right' => _x( 'Arrow Carrot', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right_circle' => _x( 'Arrow Carrot 2', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_folder_add' => _x( 'Folder', 'icon type', 'echo-knowledge-base' ) ),
				'default'     => $default_style['expand_articles_icon']
			),


			/***  KB Main Page -> Search Box ***/

			'search_layout' => array(
				'label'       => __( 'Layout', 'echo-knowledge-base' ),
				'name'        => 'search_layout',
				'info'        => __( 'Position / shape of the search input field and search button within the search box.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-search-form-1' => __( 'Rounded search button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-4' => __( 'Squared search Button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-2' => __( 'Search button is below', 'echo-knowledge-base' ),
					'epkb-search-form-3' => __( 'No search button', 'echo-knowledge-base' ),
					'epkb-search-form-0' => __( 'No search box', 'echo-knowledge-base' )
				),
				'default'     => $default_style['search_layout']
			),
			'search_input_border_width' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_input_border_width',
				'info'        => __( 'Border width of the search input field.' ),
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_input_border_width']
			),
			'search_box_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_top',
				'info'        => __( 'Adds padding space between the search box title and the box top edge.' ),
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_top']
			),
			'search_box_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_bottom',
				'info'        => __( 'Adds padding space between the search box title and the box bottom edge.' ),
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_bottom']
			),
			'search_box_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_left',
				'info'        => __( 'Adds padding space between the search input field and the box left edge.' ),
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_left']
			),
			'search_box_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_right',
				'info'        => __( 'Adds padding space between the search input field and the box right edge.' ),
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_right']
			),
			'search_box_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_top',
				'info'        => __( 'Adds margin space above the search box edge.' ),
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_margin_top']
			),
			'search_box_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_bottom',
				'info'        => __( 'Adds margin space below the search box edge.' ),
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_margin_bottom']
			),
			'search_box_input_width' => array(
				'label'       => __( 'Width', 'echo-knowledge-base' ),
				'name'        => 'search_box_input_width',
				'info'        => __( 'Sets the width of the input search field.' ),
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_input_width']
			),
			'search_box_results_style' => array(
				'label'       => __( 'Search Results: Match Article Colors', 'echo-knowledge-base' ),
				'name'        => 'search_box_results_style',
				'info'        => __( 'Matches the Search result colors from the articles listed below.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['search_box_results_style']
			),

			/***   KB Main Page -> Tuning -> Categories ***/

			// Style
			'section_head_alignment' => array(
				'label'       => __( 'Head Text Alignment', 'echo-knowledge-base' ),
				'name'        => 'section_head_alignment',
				'info'        => __( 'Set the Section heading alignment.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left' => __( 'Left', 'echo-knowledge-base' ),
					'center' => __( 'Centered', 'echo-knowledge-base' ),
					'right' => __( 'Right', 'echo-knowledge-base' )
				),
				'default'     => $default_style['section_head_alignment']
			),

			// Style - Icons
			'section_head_category_icon_location' => array(
				'label'       => __( 'Icons Location', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_location',
				'info'        => __( 'Location of icons in relation to Category box.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_icons' => __( 'No Icons', 'echo-knowledge-base' ),
					'top'   => __( 'Top',   'echo-knowledge-base' ),
					'left'  => __( 'Left',  'echo-knowledge-base' ),
					'right' => __( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'no_icons'
			),
			'section_head_category_icon_size' => array(
				'label'       => __( 'Icon Size ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_size',
				'info'        => __( 'Sets the size of the icon.' ),
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '21'
			),
			'section_head_category_icon' => array(
				'label'       => __( 'Category Icon for: <span class="epkb-config-category-name"></span>', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon',
				'info'        => __( 'Icon that is assigned to this Category.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => EPKB_Icons::get_all_icons(),
				'default'     => 'ep_font_icon_none'
			),

			//TODO Icon Type

			'section_divider' => array(
				'label'       => __( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider',
				'info'        => __( 'Displays dividing line between sub-category and list of articles.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['section_divider']
			),
			'section_divider_thickness' => array(
				'label'       => __( 'Divider Thickness ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_divider_thickness',
				'info'        => __( 'Sets the thickness of the divider between the head section and body section' ),
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_divider_thickness']
			),
			'section_desc_text_on' => array(
				'label'       => __( 'Description', 'echo-knowledge-base' ),
				'name'        => 'section_desc_text_on',
				'info'        => __( 'Controls the displays category description.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['section_desc_text_on']
			),

			//Advanced
			'section_box_shadow' => array(
				'label'       => __( 'Article List Shadow', 'echo-knowledge-base' ),
				'name'        => 'section_box_shadow',
				'info'        => __( 'Adds shadow around boxed list of articles.' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_shadow' => 'No Shadow',
					'section_light_shadow' => __( 'Light Shadow', 'echo-knowledge-base' ),
					'section_medium_shadow' => __( 'Medium Shadow', 'echo-knowledge-base' ),
					'section_bottom_shadow' => __( 'Bottom Shadow', 'echo-knowledge-base' )
				),
				'default'     => $default_style['section_box_shadow']
			),
			'section_head_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_top',
				'info'        => __( 'Adds Padding Top.' ),
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_top']
			),
			'section_head_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_bottom',
				'info'        => __( 'Adds Padding Bottom.' ),
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_bottom']
			),
			'section_head_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_left',
				'info'        => __( 'Adds Padding Left.' ),
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_left']
			),
			'section_head_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_right',
				'info'        => __( 'Adds Padding Right.' ),
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_right']
			),
            'section_border_radius' => array(
				'label'       => __( 'Radius', 'echo-knowledge-base' ),
				'name'        => 'section_border_radius',
				'info'        => __( 'Border curve and width for the boxed list of articles.' ),
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_border_radius']
			),
			'section_border_width' => array(
				'label'       => __( 'Width', 'echo-knowledge-base' ),
				'name'        => 'section_border_width',
				'info'        => '',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_border_width']
			),

			/***   KB Main Page -> Articles Listed in Sub-Category ***/
			'section_box_height_mode' => array(
				'label'       => __( 'Height Mode', 'echo-knowledge-base' ),
				'name'        => 'section_box_height_mode',
				'info'        => __( 'Sets the height of article list sections:
                                        <ul>
                                            <li>Variable - height will be equal to the height of opened categories.</li>
                                            <li>Minimum - height will be no smaller then set height but can be larger.</li>
                                            <li>Maximum height - scroll bar will appear if the categories are expanded beyond this maximum height.</li>
                                        </ul>                                                                        
                                    ' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'section_no_height' => __( 'Variable', 'echo-knowledge-base' ),
					'section_min_height' => __( 'Minimum', 'echo-knowledge-base' ),
					'section_fixed_height' => __( 'Maximum', 'echo-knowledge-base' )  ),
				'default'     => $default_style['section_box_height_mode']
			),
			'section_body_height' => array(
				'label'       => __( 'Height ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_body_height',
				'info'        => '',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_height']
			),
			'section_body_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_top',
				'info'        => __( 'Adds Padding Top.' ),
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_top']
			),
			'section_body_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_bottom',
				'info'        => __( 'Adds Padding Bottom.' ),
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_bottom']
			),
			'section_body_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_left',
				'info'        => __( 'Adds Padding Left.' ),
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_left']
			),
			'section_body_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_right',
				'info'        => __( 'Adds Padding Right.' ),
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_right']
			),
			'section_article_underline' => array(
				'label'       => __( 'Article Underline Hover', 'echo-knowledge-base' ),
				'name'        => 'section_article_underline',
				'info'        => __( 'Shows underline when user hovers mouse over an article link.' ),
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['section_article_underline']
			),
			'article_list_margin' => array(
				'label'       => __( 'Margin', 'echo-knowledge-base' ),
				'name'        => 'article_list_margin',
				'info'        => __( 'Sets the Top, left, bottom, right margin.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['article_list_margin']
			),
			'article_list_spacing' => array(
				'label'       => __( 'Between', 'echo-knowledge-base' ),
				'name'        => 'article_list_spacing',
				'info'        => __( 'Sets the space between each article.' ),
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['article_list_spacing']
			),


			/******************************************************************************
			 *
			 *  KB Main Colors - All Colors Settings
			 *  Main Page -> Tuning
			 *
			 ******************************************************************************/

			/***  Search Box ***/
			'search_title_font_color' => array(
				'label'       => __( 'Title', 'echo-knowledge-base' ),
				'name'        => 'search_title_font_color',
				'info'        => __( 'Text color of the search box title.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_title_font_color']
			),
			'search_background_color' => array(
				'label'       => __( 'Search Background', 'echo-knowledge-base' ),
				'name'        => 'search_background_color',
				'info'        => __( 'Background color around the search input box.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_background_color']
			),
			'search_text_input_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_background_color',
				'info'        => __( 'Search input field background color' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_text_input_background_color']
			),
			'search_text_input_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_border_color',
				'info'        => 'Search input field border color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_text_input_border_color']
			),
			'search_btn_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'search_btn_background_color',
				'info'        => __( 'Search button background color.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_btn_background_color']
			),
			'search_btn_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_btn_border_color',
				'info'        => 'Search button color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_btn_border_color']
			),

			/***  Content ***/
			'background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'background_color',
				'info'        => __( 'Background color of the knowledge base main page.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['background_color']
			),

			/***  List of Articles ***/
			'article_font_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'article_font_color',
				'info'        => __( 'Text color of listed articles.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['article_font_color']
			),
			'article_icon_color' => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'article_icon_color',
				'info'        => __( 'Icon color of listed articles.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['article_icon_color']
			),
			'section_body_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'section_body_background_color',
				'info'        => __( 'Background color for the boxed list of articles.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_body_background_color']
			),
			'section_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'section_border_color',
				'info'        => __( 'Border color for the boxed list of articles.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_border_color']
			),

			/***  Categories ***/
			'section_head_font_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'section_head_font_color',
				'info'        => __( 'Text color of category heading for list of articles.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_head_font_color']
			),
			'section_head_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'section_head_background_color',
				'info'        => __('Background color of category heading for list of articles.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_head_background_color']
			),
			'section_head_description_font_color' => array(
				'label'       => __( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'section_head_description_font_color',
				'info'        => __( 'Color of category description.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_head_description_font_color']
			),
			'section_divider_color' => array(
				'label'       => __( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider_color',
				'info'        => __( 'Color of dividing line between sub-category and list of articles' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_divider_color']
			),
			'section_category_font_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'section_category_font_color',
				'info'        => __( 'Sub-category Text color and icon color.' ),
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_category_font_color']
			),
			'section_category_icon_color' => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'section_category_icon_color',
				'info'        => '',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_category_icon_color']
			),
			'section_head_category_icon_color' => array(
				'label'       => __( 'Top Level Category Icon', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_color',
				'info'        => 'Color of category icons',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_head_category_icon_color']
			),

			/******************************************************************************
			 *
			 *  Front-End Text
			 *
			 ******************************************************************************/

            /***   Search  ***/

			'search_title' => array(
				'label'       => __( 'Search Title', 'echo-knowledge-base' ),
				'name'        => 'search_title',
				'info'        => __( 'Title appears above the search field.' ),
				'size'        => '60',
				'max'         => '60',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search Knowledge Base by Keyword', 'echo-knowledge-base' )
			),
			'search_box_hint' => array(
				'label'       => __( 'Search Hint', 'echo-knowledge-base' ),
				'name'        => 'search_box_hint',
				'info'        => __( 'Hint text appears in the search input field while the field is empty.' ),
				'size'        => '60',
				'max'         => '60',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search the documentation...', 'echo-knowledge-base' )
			),
			'search_button_name' => array(
				'label'       => __( 'Search Button Name', 'echo-knowledge-base' ),
				'name'        => 'search_button_name',
				'info'        => __( 'Name for the search button.' ),
				'size'        => '25',
				'max'         => '25',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search', 'echo-knowledge-base' )
			),
			'search_results_msg' => array(
				'label'       => __( 'Search Results Message', 'echo-knowledge-base' ),
				'name'        => 'search_results_msg',
				'info'        => __( 'Search results title message.' ),
				'size'        => '60',
				'max'         => '60',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search Results for', 'echo-knowledge-base' )
			),
			'no_results_found' => array(
				'label'       => __( 'No Matches Found Text', 'echo-knowledge-base' ),
				'name'        => 'no_results_found',
				'info'        => __( 'If the search did not find any results, show this text.' ),
				'size'        => '80',
				'max'         => '80',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'No matches found', 'echo-knowledge-base' )
			),
			'min_search_word_size_msg' => array(
				'label'       => __( 'Minimum Search Word Size Message', 'echo-knowledge-base' ),
				'name'        => 'min_search_word_size_msg',
				'info'        => __( 'If the user tries to search for a word with less than 3 characters, show this message.' ),
				'size'        => '60',
				'max'         => '60',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Enter a word with at least one character.', 'echo-knowledge-base' )
			),
			
			
            /***   Categories and Articles ***/

			'category_empty_msg' => array(
				'label'       => __( 'Empty Category Notice', 'echo-knowledge-base' ),
				'name'        => 'category_empty_msg',
				'info'        => __( 'If a category has no articles this notice will be displayed below the category name.' ),
				'size'        => '60',
				'max'         => '60',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Articles coming soon', 'echo-knowledge-base' )
			),
			'collapse_articles_msg' => array(
				'label'       => __( 'Collapse Articles Text', 'echo-knowledge-base' ),
				'name'        => 'collapse_articles_msg',
				'info'        => __( 'If alist of articles can be collapsed, the user will see this text.' ),
				'size'        => '60',
				'max'         => '60',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Collapse Articles', 'echo-knowledge-base' )
			),
			'show_all_articles_msg' => array(
				'label'       => __( 'Show All Articles Text', 'echo-knowledge-base' ),
				'name'        => 'show_all_articles_msg',
				'info'        => __( 'If a list of articles can be expanded, the user will see this text.' ),
				'size'        => '60',
				'max'         => '60',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Show all articles', 'echo-knowledge-base' )
			),
			'choose_main_topic' => array(
				'label'       => __( 'Choose Main Topic', 'echo-knowledge-base' ),
				'name'        => 'choose_main_topic',
				'info'        => __( 'In Tabs Layout, if a drop down of top categories is displayed, it will show this help text above it.' ),
				'size'        => '60',
				'max'         => '60',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Choose a Main Topic', 'echo-knowledge-base' )
			),
		);

		return $shared_specification;
	}

	/**
	 * Get KB default configuration
	 *
	 * @param int $kb_id is the ID of knowledge base to get default config for
	 * @return array contains default values for KB configuration
	 */
	public static function get_default_kb_config( $kb_id ) {
		$config_specs = self::get_fields_specification( $kb_id );

		$default_configuration = array();
		foreach( $config_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}

	/**
	 * Get names of all configuration items for KB configuration
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID ) );
	}

	/**
	 * Return default values from given specification.
	 * @param $config_specs
	 * @return array
	 */
	public static function get_specs_defaults( $config_specs ) {
		$default_configuration = array();
		foreach( $config_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}
		return $default_configuration;
	}
}

/** used by MKB as well */
abstract class EPKB_KB_Status
{
	const ARCHIVED = 'archived';
	const PUBLISHED = 'published';
}
