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
							 'user-sequenced' => __( 'Custom - Drag and Drop Categories', 'echo-knowledge-base' ) );
		return apply_filters( 'epkb_categories_display_order', $base_order );
	}

	public static function get_articles_display_order() {
		$base_order = array( 'alphabetical-title' => __( 'Alphabetical by Title', 'echo-knowledge-base' ),
		                     'created-date' => __( 'Chronological by Date Created', 'echo-knowledge-base' ),
		                     'user-sequenced' => __( 'Custom - Drag and Drop articles', 'echo-knowledge-base' ) );
		return apply_filters( 'epkb_articles_display_order', $base_order );
	}

	public static $sidebar_component_priority_defaults = array(
		'elay_sidebar_left' => '1',
		'toc_left' => '0',
		'kb_sidebar_left' => '0',
		'categories_left' => '1',
		'toc_content' => '0',
		'toc_right' => '1',
		'kb_sidebar_right' => '0',
		'categories_right' => '0'
	);

	public static function add_sidebar_component_priority_defaults( $article_sidebar_component_priority ) {
		return array_merge(self::$sidebar_component_priority_defaults, $article_sidebar_component_priority);
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
			'status' => array(
				'label'       => 'status',
				'type'        => EPKB_Input_Filter::ENUMERATION,
				'options'     => array( EPKB_KB_Status::BLANK, EPKB_KB_Status::PUBLISHED, EPKB_KB_Status::ARCHIVED ),
				'internal'    => true,
				'default'     => EPKB_KB_Status::PUBLISHED
			),
			'kb_main_pages' => array(
				'label'       => 'kb_main_pages',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'article_sidebar_component_priority' => array(
				'label'       => 'article_sidebar_component_priority',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => self::$sidebar_component_priority_defaults
			),


			/******************************************************************************
			 *
			 *  Overview
			 *
			 ******************************************************************************/

			'kb_name' => array(
				'label'       => __( 'CPT Name', 'echo-knowledge-base' ),
				'name'        => 'kb_name',
				'size'        => '50',
				'max'         => '70',
				'min'         => '1',
				'reload'      => true,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == 1 ? '' : ' ' . $kb_id)
			),
			'kb_articles_common_path' => array(
				'label'       => __( 'Common Path for Articles', 'echo-knowledge-base' ),
				'name'        => 'kb_articles_common_path',
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
			),
			'kb_article_page_layout' => array(
					'label'       => __( 'Article Page Layout', 'echo-knowledge-base' ),
					'name'        => 'kb_article_page_layout',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => EPKB_KB_Config_Layouts::get_article_page_layout_names(),
					'default'     => EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT,
			),
			'kb_sidebar_location' => array( // TODO in UI
					'label'       => __( 'Article Sidebar Location', 'echo-knowledge-base' ),
					'name'        => 'kb_sidebar_location',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => array(
							'left-sidebar'   => _x( 'Left Sidebar', 'echo-knowledge-base' ),
							'right-sidebar'  => _x( 'Right Sidebar', 'echo-knowledge-base' ),
							'no-sidebar'     => _x( 'No Sidebar', 'echo-knowledge-base' ) ),
					'default'     => 'no-sidebar'
			),

			/******************************************************************************
			 *
			 *  ARTICLE STRUCTURE v2
			 *
			 ******************************************************************************/

			'article-structure-version' => array(
					'label'       => __( 'Article Page Structure', 'echo-knowledge-base' ),
					'name'        => 'article-structure-version',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     =>
							array(
									'version-1' => __( 'Legacy Style', 'echo-knowledge-base' ),
									'version-2' => __( 'Modern Style (Recommended)', 'echo-knowledge-base' ),
							),
					'default'     => 'version-1',
			),

			// Article Version 2 settings -----------------------------------/
			'article-container-desktop-width-v2' => array(
				'label'       => __( 'Width', 'echo-knowledge-base' ),
				'name'        => 'article-container-desktop-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'article-container-desktop-width-units-v2' => array(
				'label'       => __( 'Width - Units', 'echo-knowledge-base' ),
				'name'        => 'article-container-desktop-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),
			'article-container-tablet-width-v2' => array(
				'label'       => __( 'Width (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-container-tablet-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'article-container-tablet-width-units-v2' => array(
				'label'       => __( 'Width - Units(Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-container-tablet-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),

			// Article Version 2 - Body Container
			'article-body-desktop-width-v2' => array(
				'label'       => __( 'Width', 'echo-knowledge-base' ),
				'name'        => 'article-body-desktop-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1140
			),
			'article-body-desktop-width-units-v2' => array(
				'label'       => __( 'Width Units', 'echo-knowledge-base' ),
				'name'        => 'article-body-desktop-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => 'px'
			),
			'article-body-tablet-width-v2' => array(
				'label'       => __( 'Width (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-body-tablet-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'article-body-tablet-width-units-v2' => array(
				'label'       => __( 'Width - Units (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-body-tablet-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),


			// Article Version 2 - Left Sidebar
			'article-left-sidebar-desktop-width-v2' => array(
				'label'       => __( 'Desktop Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-desktop-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article-left-sidebar-tablet-width-v2' => array(
				'label'       => __( 'Tablet Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-tablet-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article-left-sidebar-padding-v2' => array(
				'label'       => __( 'Left Sidebar Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-background-color-v2' => array(
				'label'       => __( 'Left Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-left-sidebar-starting-position' => array(
				'label'       => __( 'Top Offset ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-starting-position',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article-left-sidebar-match' => array(
				'label'       => __( 'Match to content', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-match',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			
			// Article Version 2 - Article Content
			'article-content-desktop-width-v2' => array(
				'label'       => __( 'Desktop Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-content-desktop-width-v2',
				'max'         => 100,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 60
			),
			'article-content-tablet-width-v2' => array(
				'label'       => __( 'Tablet Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-content-tablet-width-v2',
				'max'         => 100,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 60
			),

			'article-content-padding-v2' => array(
				'label'       => __( 'Content Area Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-content-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article-content-background-color-v2' => array(
				'label'       => __( 'Content Area Background', 'echo-knowledge-base' ),
				'name'        => 'article-content-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),

			// Article Version 2 - Right Sidebar
			'article-right-sidebar-desktop-width-v2' => array(
				'label'       => __( 'Desktop Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-desktop-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article-right-sidebar-tablet-width-v2' => array(
				'label'       => __( 'Tablet Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-tablet-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article-right-sidebar-padding-v2' => array(
				'label'       => __( 'Right Sidebar Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-background-color-v2' => array(
				'label'       => __( 'Right Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-right-sidebar-starting-position' => array(
				'label'       => __( 'Top Offset ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-starting-position',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article-right-sidebar-match' => array(
				'label'       => __( 'Match to content', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-match',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			// Article Version 2 - Advanced
			'article-mobile-break-point-v2' => array(
				'label'       => __( 'Mobile Screen Break point (px)', 'echo-knowledge-base' ),
				'name'        => 'article-mobile-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 768
			),
			
			'article-tablet-break-point-v2' => array(
				'label'       => __( 'Tablet Screen Break point (px)', 'echo-knowledge-base' ),
				'name'        => 'article-tablet-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1025
			),


			/******************************************************************************
			 *
			 *  CATEGORY ARCHIVE v2
			 *
			 ******************************************************************************/

			/* 'category-archive-structure-version' => array( // TODO NOT USED RIGHT NOW, not in UI, auto determined
					'label'       => __( 'Category Archive Structure', 'echo-knowledge-base' ),
					'name'        => 'category-archive-structure-version',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     =>
							array(
									'version-1' => 'Legacy Style',
									'version-2' => 'Modern Style (Recommended)'
							),
					'default'     => 'version-1',
			), */

			// Archive Version 2 settings -----------------------------------/
			'archive-container-width-v2' => array(
				'label'       => __( 'Archive Container Width', 'echo-knowledge-base' ),
				'name'        => 'archive-container-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1080
			),
			'archive-container-width-units-v2' => array(
				'label'       => __( 'Archive Container Width Units', 'echo-knowledge-base' ),
				'name'        => 'archive-container-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' ),

				),
				'default'     => 'px'
			),

			// Archive Version 2 - Left Sidebar
			/* 'archive-left-sidebar-on-v2' => array(
				'label'       => __( 'Turn on Left Sidebar', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-on-v2',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			), */
			'archive-left-sidebar-width-v2' => array(
				'label'       => __( 'Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-width-v2',
				'max'         => 80,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'archive-left-sidebar-padding-v2' => array(
				'label'       => __( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-left-sidebar-background-color-v2' => array(
				'label'       => __( 'Left Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),

			// Archive Version 2 - Archive Content
			'archive-content-width-v2' => array(
				'label'       => __( 'Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive-content-width-v2',
				'max'         => 100,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 80
			),
			'archive-content-padding-v2' => array(
				'label'       => __( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-content-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-content-background-color-v2' => array(
				'label'       => __( 'Content Background', 'echo-knowledge-base' ),
				'name'        => 'archive-content-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),

			// Archive Version 2 - Right Sidebar
			// We are hiding all the right sidebar settings for now since it will not be used for this release. We have to show these once we have
			// Enabled the KB Sidebar.

			/* 'archive-right-sidebar-on-v2' => array(
				'label'       => __( 'Turn on Right Sidebar', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-on-v2',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'archive-right-sidebar-width-v2' => array(
				'label'       => __( 'Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-width-v2',
				'max'         => 80,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'archive-right-sidebar-padding-v2' => array(
				'label'       => __( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-right-sidebar-background-color-v2' => array(
				'label'       => __( 'Right Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7f7f7'
			),*/

			// Archive Version 2 - Advanced
			'archive-mobile-break-point-v2' => array(
				'label'       => __( 'Small Screen Break point ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-mobile-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1000
			),
			
			/******************************************************************************
			 *
			 *  CATEGORIES BOX
			 *
			 ******************************************************************************/
			'categories_box_top_margin' => array(
                'label'       => __( 'Container Top Margin, (px)', 'echo-knowledge-base' ),
                'name'        => 'categories_box_top_margin',
                'max'         => '100',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
			'categories_box_font_size' => array(
                'label'       => __( 'Font Size, (px)', 'echo-knowledge-base' ),
                'name'        => 'categories_box_font_size',
                'max'         => '50',
                'min'         => '8',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '14'
            ),
			'category_box_title_text_color' => array(
				'label'       => __( 'Title Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_title_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#666666'
			),
			'category_box_container_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'      => 'category_box_container_background_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fcfcfc'
			),
			'category_box_category_text_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_category_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'category_box_count_background_color' => array(
				'label'       => __( 'Count Background', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_background_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'category_box_count_text_color' => array(
				'label'       => __( 'Count Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'category_box_count_border_color' => array(
				'label'       => __( 'Count Border', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_border_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CCCCCC'
			),
			
			
			/******************************************************************************
			 *
			 *  OTHER
			 *
			 ******************************************************************************/

			'categories_in_url_enabled' => array(
					'label'       => __( 'Categories in URL', 'echo-knowledge-base' ),
					'name'        => 'categories_in_url_enabled',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => array(
							'on'     => __( 'on', 'echo-knowledge-base' ),
							'off'    => __( 'off', 'echo-knowledge-base' )
					),
					'default'     => 'off'
			),
			'kb_main_page_category_link' => array(
					'label'       => __( 'Main Page Category Link', 'echo-knowledge-base' ),
					'name'        => 'kb_main_page_category_link',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     =>
							array(
									'default'          => __( 'Article Page', 'echo-knowledge-base' ),
									'category_archive' => __( 'Category Archive Page', 'echo-knowledge-base' )
							),
					'default'     => 'default',
			),
			'categories_display_sequence' => array(
				'label'       => __( 'Categories Sequence', 'echo-knowledge-base' ),
				'name'        => 'categories_display_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_categories_display_order(),
				'default'     => 'alphabetical-title'
			),
			'articles_display_sequence' => array(
				'label'       => __( 'Articles Sequence', 'echo-knowledge-base' ),
				'name'        => 'articles_display_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_articles_display_order(),
				'default'     => 'alphabetical-title'
			),
			'templates_for_kb' => array(
				'label'       => __( 'Choose Template', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'current_theme_templates'    => __( 'Current Theme', 'echo-knowledge-base'  ),
					'kb_templates'       => __( 'Knowledge Base Theme', 'echo-knowledge-base'  ),
				),
				'default'     => 'kb_templates'
			),
			'wpml_is_enabled' => array(
					'label'       => __( 'WPML is Enabled', 'echo-knowledge-base' ),
					'name'        => 'wpml_is_enabled',
					'type'        => EPKB_Input_Filter::CHECKBOX,
					//'internal'    => true,  // field update handled separately
					'default'     => 'off'
			),

			/******************************************************************************
			 *
			 *  KB TEMPLATE settings
			 *
			 ******************************************************************************/

			// TEMPLATES for Main Page
			'templates_display_main_page_main_title' => array(
				'label'       => __( 'Display Main Title', 'echo-knowledge-base' ),
				'name'        => 'templates_display_main_page_main_title',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
            'templates_for_kb_padding_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_top',
                'max'         => '300',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'templates_for_kb_padding_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_bottom',
                'max'         => '500',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '50'
            ),
            'templates_for_kb_padding_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_left',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'templates_for_kb_padding_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_padding_right',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'templates_for_kb_margin_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_top',
                'max'         => '300',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'templates_for_kb_margin_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_bottom',
                'max'         => '500',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '50'
            ),
            'templates_for_kb_margin_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_left',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'templates_for_kb_margin_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'templates_for_kb_margin_right',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),

			// TEMPLATES ofr Article Page
			'templates_for_kb_article_reset'            => array(
				'label'       => __( 'Article Content - Remove Theme Styling', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_reset',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'templates_for_kb_article_defaults'         => array(
				'label'       => __( 'Article Content - Add KB Styling', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_defaults',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'templates_for_kb_widget_sidebar_defaults'         => array(
				'label'       => __( 'Widget Sidebar Styling', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_widget_sidebar_defaults',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'templates_for_kb_article_padding_top'      => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_top',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'templates_for_kb_article_padding_bottom'   => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_bottom',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'templates_for_kb_article_padding_left'     => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_left',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'templates_for_kb_article_padding_right'    => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_padding_right',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'templates_for_kb_article_margin_top'       => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_top',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'templates_for_kb_article_margin_bottom'    => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_bottom',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '50'
			),
			'templates_for_kb_article_margin_left'      => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_left',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'templates_for_kb_article_margin_right'     => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_margin_right',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),

			//Category Archive Page
			'templates_for_kb_category_archive_page_style' => array(
				'label'       => __( 'Style for Category Archive Page', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_category_archive_page_style',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-category-archive-style-1' => __( 'Style 1 ( Basic List )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-2' => __( 'Style 2 ( Standard )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-3' => __( 'Style 3 ( Standard 2 )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-4' => __( 'Style 4 ( Box )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-5' => __( 'Style 5 ( Grid )', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-category-archive-style-2'
			),
			'templates_for_kb_category_archive_page_heading_description' => array(
				'label'       => __( 'Heading Description', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_category_archive_page_heading_description',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Category - ', 'echo-knowledge-base' )
			),
			
			'templates_for_kb_category_archive_read_more' => array(
				'label'       => __( 'Read More', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_category_archive_read_more',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Read More', 'echo-knowledge-base' )
			),
			
			'category_focused_menu_heading_text' => array(
				'label'       => __( 'Categories Heading', 'echo-knowledge-base' ),
				'name'        => 'category_focused_menu_heading_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Categories', 'echo-knowledge-base' )
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
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			/**** TOC ****/
			'article_toc_enable' => array(
				'label'       => __( 'Show Table of Contents', 'echo-knowledge-base' ),
				'name'        => 'article_toc_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'  /* TODO existing KB is off, new KB is on */
			),
			'article_toc_start_level' => array(
                'label'       => __( 'The Heading TOC Will Map To', 'echo-knowledge-base' ),
                'name'        => 'article_toc_start_level',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    '1'   => 'H1-H6',
                    '2'   => 'H2-H6',
					'3'   => 'H3-H6',
					'4'   => 'H4-H6',
					'5'   => 'H5-H6',
					'6'   => 'H6 only',
                ),
                'default'     => '2'
            ),
			'article_toc_exclude_class' => array(
                'label'       => __( 'CSS Class to exclude headers from the TOC', 'echo-knowledge-base' ),
                'name'        => 'article_toc_exclude_class',
                'size'        => '200',
				'max'         => '200',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => ''
            ),
			'article_toc_text_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'      => 'article_toc_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_active_bg_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_active_bg_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#1e73be'
			),
			'article_toc_active_text_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'      => 'article_toc_active_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_toc_cursor_hover_bg_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_cursor_hover_bg_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#e1ecf7'
			),
			'article_toc_cursor_hover_text_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'      => 'article_toc_cursor_hover_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_toc_scroll_offset' => array(
                'label'       => __( 'Selected Heading Offset from Top (px)', 'echo-knowledge-base' ),
                'name'        => 'article_toc_scroll_offset',
                'max'         => '200',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '130'
            ),
			'article_toc_position' => array(
                'label'       => __( 'Location', 'echo-knowledge-base' ),
                'name'        => 'article_toc_position',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'left'   => __( 'Left',   'echo-knowledge-base' ),
                    'right'   => __( 'Right', 'echo-knowledge-base' ),
					'middle'   => __( 'Middle', 'echo-knowledge-base' ),
                ),
                'default'     => 'right'
            ),
			'article_toc_border_mode' => array(
                'label'       => __( 'Border Style', 'echo-knowledge-base' ),
                'name'        => 'article_toc_border_mode',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'none'   => __( 'None',   'echo-knowledge-base' ),
                    'between'   => __( 'Between Article and TOC', 'echo-knowledge-base' ),
					'around'   => __( 'Around TOC', 'echo-knowledge-base' ),
                ),
                'default'     => 'between'
            ),
			'article_toc_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'      => 'article_toc_border_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_font_size' => array(
                'label'       => __( 'Font size (px)', 'echo-knowledge-base' ),
                'name'        => 'article_toc_font_size',
                'max'         => '200',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '14'
            ),
			'article_toc_position_from_top' => array(
                'label'       => __( 'Starting Position (px)', 'echo-knowledge-base' ),
                'name'        => 'article_toc_position_from_top',
                'max'         => '1000',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '300'
            ),
			/*'article_toc_position_from_content' => array(
                'label'       => __( 'Bottom padding ( px )', 'echo-knowledge-base' ),
                'name'        => 'article_toc_position_from_content',
                'max'         => '200',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),*/

			// Media Sizing
			'article_toc_width_1' => array(
				'label'       => __( 'TOC Width on laptops (px)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_width_1',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '100'
			),
			'article_toc_media_1' => array(
				'label'       => __( 'Starting resolution for large tablets (px)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_media_1',
				'max'         => '2000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '1367'
			),
			'article_toc_width_2' => array(
				'label'       => __( 'TOC Width on tablets (px)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_width_2',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '100'
			),
			'article_toc_media_2' => array(
				'label'       => __( 'Starting resolution for small tablets (px)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_media_2',
				'max'         => '2000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '1025'
			),
			'article_toc_media_3' => array(
				'label'       => __( 'Starting resolution for phones (px)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_media_3',
				'max'         => '2000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '800'
			),
			'article_toc_gutter' => array(
				'label'       => __( 'Gutter', 'echo-knowledge-base' ),
				'name'        => 'article_toc_gutter',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_toc_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_background_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fcfcfc'
			),
			'article_toc_title' => array(
				'label'       => __( 'Title (optional)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_title',
				'size'        => '200',
				'max'         => '200',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Table of Contents', 'echo-knowledge-base' )
			),

			/******   BREADCRUMB   ******/
			'breadcrumb_toggle' => array(
				'label'       => __( 'Show Breadcrumbs', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'breadcrumb_icon_separator' => array(
				'label'       => __( 'Breadcrumb Separator', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_icon_separator',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'ep_font_icon_none'    => __( '-- No Icon --',   'echo-knowledge-base' ),
					'ep_font_icon_right_arrow'   => __( 'Right Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_left_arrow'    => __( 'Left Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right_circle'    => __( 'Arrow Right Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left_circle'    => __( 'Arrow Left Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left'    => __( 'Arrow Caret Left',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right'    => __( 'Arrow Caret Right',   'echo-knowledge-base' ),
				),
				'default'     => 'ep_font_icon_arrow_carrot_right'
			),
            'breadcrumb_padding_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_top',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'breadcrumb_padding_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_bottom',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'breadcrumb_padding_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_left',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'breadcrumb_padding_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_right',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
			'breadcrumb_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '20'
			),
			'breadcrumb_margin_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
            'breadcrumb_text_color' => array(
                'label'       => __( 'Breadcrumb Text', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_text_color',
                'size'        => '10',
                'max'         => '7',
                'min'         => '7',
                'type'        => EPKB_Input_Filter::COLOR_HEX,
                'default'     => '#f7941d'
            ),
			'breadcrumb_description_text' => array(
				'label'       => __( 'Breadcrumb Description', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_description_text',
				'size'        => '50',
				'max'         => '70',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'You are here:', 'echo-knowledge-base' )
			),
			'breadcrumb_home_text' => array(
				'label'       => __( 'Breadcrumb Home Text', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_home_text',
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Main', 'echo-knowledge-base' )
			),
			'breadcrumb_font_size' => array(
				'label'       => __( 'Relative Text Size', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_font_size',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'10' => _x( 'Extra Small', 'font size', 'echo-knowledge-base' ),
					'12' => _x( 'Small', 'font size', 'echo-knowledge-base' ),
					'14' => _x( 'Medium', 'font size', 'echo-knowledge-base' ),
					'16' => _x( 'Large', 'font size', 'echo-knowledge-base' ) ),
				'default'     => '16'
			),


			/******   BACK NAVIGATION   ******/
            'back_navigation_toggle' => array(
                'label'       => __( 'Show Back Button', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_toggle',
                'type'        => EPKB_Input_Filter::CHECKBOX,
                'default'     => 'on'
            ),
            'back_navigation_mode' => array(
                'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_mode',
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
                'size'        => '30',
                'max'         => '50',
                'min'         => '1',
                'mandatory'   => false,
                'type'        => EPKB_Input_Filter::TEXT,
                'default'     => '< ' . __( 'All Topics', 'echo-knowledge-base' )
            ),
            'back_navigation_text_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'back_navigation_bg_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_bg_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
            'back_navigation_border_color' => array(
                'label'       => __( 'Border', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_border_color',
                'size'        => '10',
                'max'         => '7',
                'min'         => '7',
                'type'        => EPKB_Input_Filter::COLOR_HEX,
                'default'     => '#ffffff'
            ),
			'back_navigation_font_size' => array(
				'label'       => __( 'Text Size', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_font_size',
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '16'
			),
            'back_navigation_border' => array(
                'label'       => __( 'Button Border', 'echo-knowledge-base' ),
                'name'        => 'back_navigation_border',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'none'    => __( '-- No Border --', 'echo-knowledge-base' ),
                    'solid'   => __( 'Solid', 'echo-knowledge-base' ),
                ),
                'default'     => 'none'
            ),
			'back_navigation_border_radius' => array(
				'label'       => __( 'Border Radius', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_radius',
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '3'
			),
			'back_navigation_border_width' => array(
				'label'       => __( 'Border Width', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_width',
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '1'
			),
			'back_navigation_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),

			/******   OTHER   ******/
			'last_udpated_on' => array(
				'label'       => __( 'Last Updated Display', 'echo-knowledge-base' ),
				'name'        => 'last_udpated_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'           => __( 'Not Displayed', 'echo-knowledge-base' ),
					'article_top'    => __( 'Show Above Article', 'echo-knowledge-base' ),
					'article_bottom' => __( 'Show Below Article', 'echo-knowledge-base' )
				),
				'default'     => 'article_top'
			),
			'last_udpated_on_text' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'last_udpated_on_text',
				'size'        => '30',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Last Updated On', 'echo-knowledge-base' )
			),
			'created_on' => array(
				'label'       => __( 'Created On Display', 'echo-knowledge-base' ),
				'name'        => 'created_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'           => __( 'Not Displayed', 'echo-knowledge-base' ),
					'article_top'    => __( 'Show Above Article', 'echo-knowledge-base' ),
					'article_bottom' => __( 'Show Below Article', 'echo-knowledge-base' )
				),
				'default'     => 'article_top'
			),
			'created_on_text' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'created_on_text',
				'size'        => '30',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Created On', 'echo-knowledge-base' )
			),
			'author_mode' => array(
				'label'       => __( 'Author Display', 'echo-knowledge-base' ),
				'name'        => 'author_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'           => __( 'Not Displayed', 'echo-knowledge-base' ),
					'article_top'    => __( 'Show Above Article', 'echo-knowledge-base' ),
					'article_bottom' => __( 'Show Below Article', 'echo-knowledge-base' )
				),
				'default'     => 'article_top'
			),
			'author_text' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'author_text',
				'size'        => '30',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'by', 'echo-knowledge-base' )
			),

            /******   TAGS   ******/
            /* do we need this? 'tags_toggle' => array(
                'label'       => __( 'Show Tags', 'echo-knowledge-base' ),
                'name'        => 'tags_toggle',
                'type'        => EPKB_Input_Filter::CHECKBOX,
                'default'     => 'on'
            ), */
			'article_meta_icon_on' => array(
				'label'       => __( 'Article Meta Icon', 'echo-knowledge-base' ),
				'name'        => 'article_meta_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => __( 'Show icon', 'echo-knowledge-base' ),
					'off'    => __( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			), /* option postponed
            'date_format' => array(
                'label'       => __( 'Date Format', 'echo-knowledge-base' ),
                'name'        => 'date_format',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'F j, Y'    => __( 'January 1, 2020', 'echo-knowledge-base' ),
                    'M j, Y'    => __( 'Jan 1, 2020', 'echo-knowledge-base' ),
                    'j F Y'    => __( '1 January 2020', 'echo-knowledge-base' ),
                    'j M Y'    => __( '1 Jan 2020', 'echo-knowledge-base' ),
                    'm/d/Y'    => __( '01/30/2020', 'echo-knowledge-base' ),
                    'Y/m/d'    => __( '2020/01/30', 'echo-knowledge-base' ),
                ),
                'default'     => 'M j, Y'
            ), */
		);

		// add CORE LAYOUTS SHARED configuration
		$config_specification = array_merge( $config_specification, self::shared_configuration() );

		// add CORE LAYOUTS non-shared configuration
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Basic::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Tabs::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Categories::get_fields_specification() );

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

		$default_style = EPKB_KB_Config_Layout_Basic::demo_1_set();
		$default_color = EPKB_KB_Config_Layout_Basic::demo_1_colors();

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
				'label'       => __( 'Search Box Width', 'echo-knowledge-base' ),
				'name'        => 'width',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-boxed' => __( 'Boxed Width', 'echo-knowledge-base' ),
					'epkb-full' => __( 'Full Width', 'echo-knowledge-base' ) ),
				'default'     => $default_style['width']
			),
			'section_font_size' => array(
				'label'       => __( 'Relative Text Size', 'echo-knowledge-base' ),
				'name'        => 'section_font_size',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'section_xsmall_font' => _x( 'Extra Small', 'font size', 'echo-knowledge-base' ),
					'section_small_font' => _x( 'Small', 'font size', 'echo-knowledge-base' ),
					'section_medium_font' => _x( 'Medium', 'font size', 'echo-knowledge-base' ),
					'section_large_font' => _x( 'Large', 'font size', 'echo-knowledge-base' ) ),
				'default'     => $default_style['section_font_size']
			),
			'show_articles_before_categories' => array(
				'label'       => __( 'Show Articles', 'echo-knowledge-base' ),
				'name'        => 'show_articles_before_categories',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => __( 'Before Categories', 'echo-knowledge-base' ),
					'off' => __( 'After Categories', 'echo-knowledge-base' ),
					),
				'default'     => 'off'  /* TODO off for existing KBs, on for new KBs */
			),
			'categories_layout_list_mode' => array(
				'label'       => __( 'Categories List Mode (Article and Archive Page)', 'echo-knowledge-base' ),
				'name'        => 'categories_layout_list_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'list_top_categories' => __( 'Top Categories', 'echo-knowledge-base' ),
					'list_sibling_categories' => __( 'Sibling Categories', 'echo-knowledge-base' ),
					),
				'default'     => $default_style['categories_layout_list_mode']
			),
			'nof_columns' => array(
				'label'       => __( 'Number of Columns', 'echo-knowledge-base' ),
				'name'        => 'nof_columns',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'one-col' => '1', 'two-col' => '2', 'three-col' => '3', 'four-col' => '4' ),
				'default'     => $default_style['nof_columns']
			),
			'nof_articles_displayed' => array(
				'label'       => __( 'Number of Articles Listed', 'echo-knowledge-base' ),
				'name'        => 'nof_articles_displayed',
				'max'         => '2000',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['nof_articles_displayed'],
			),
			'expand_articles_icon' => array(
				'label'       => __( 'Icon to Expand/Collapse Articles', 'echo-knowledge-base' ),
				'name'        => 'expand_articles_icon',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'ep_font_icon_plus_box' => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_plus' => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_right_arrow' => _x( 'Arrow Triangle', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right' => _x( 'Arrow Caret', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right_circle' => _x( 'Arrow Caret 2', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_folder_add' => _x( 'Folder', 'icon type', 'echo-knowledge-base' ) ),
				'default'     => $default_style['expand_articles_icon']
			),


			/***  KB Main Page -> Search Box ***/

			'search_layout' => array(
				'label'       => __( 'Layout', 'echo-knowledge-base' ),
				'name'        => 'search_layout',
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
				'label'       => __( 'Border (px)', 'echo-knowledge-base' ),
				'name'        => 'search_input_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_input_border_width']
			),
			'search_box_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_top',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_top']
			),
			'search_box_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_bottom',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_bottom']
			),
			'search_box_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_left',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_left']
			),
			'search_box_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_right',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_padding_right']
			),
			'search_box_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_top',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_margin_top']
			),
			'search_box_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_bottom',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_box_margin_bottom']
			),
			'search_box_input_width' => array(
				'label'       => __( 'Width (%)', 'echo-knowledge-base' ),
				'name'        => 'search_box_input_width',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 40
			),
			'search_box_results_style' => array(
				'label'       => __( 'Search Results: Match Article Colors', 'echo-knowledge-base' ),
				'name'        => 'search_box_results_style',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['search_box_results_style']
			),
			'search_title_html_tag' => array(
				'label'       => __( 'Search Title Html Tag', 'echo-knowledge-base' ),
				'name'        => 'search_title_html_tag',
				'size'        => '10',
				'max'         => '10',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => $default_style['search_title_html_tag']
			),
			'search_title_font_size' => array(
				'label'       => __( 'Search Title Font Size (px)', 'echo-knowledge-base' ),
				'name'        => 'search_title_font_size',
				'max'         => '50',
				'min'         => '12',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['search_title_font_size']
			),

			/***   KB Main Page -> Tuning -> Categories ***/

			// Style
			'section_head_alignment' => array(
				'label'       => __( 'Text Alignment', 'echo-knowledge-base' ),
				'name'        => 'section_head_alignment',
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
				'label'       => __( 'Icons Location/Turn Off', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_location',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_icons' => __( 'No Icons', 'echo-knowledge-base' ),
					'top'   => __( 'Top',   'echo-knowledge-base' ),
					'left'  => __( 'Left',  'echo-knowledge-base' ),
					'right' => __( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),
			'section_head_category_icon_size' => array(
				'label'       => __( 'Icon Size ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_size',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '21'
			),

			'section_divider' => array(
				'label'       => __( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['section_divider']
			),
			'section_divider_thickness' => array(
				'label'       => __( 'Divider Thickness ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_divider_thickness',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_divider_thickness']
			),
			'section_desc_text_on' => array(
				'label'       => __( 'Description', 'echo-knowledge-base' ),
				'name'        => 'section_desc_text_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['section_desc_text_on']
			),
			'section_hyperlink_text_on' => array(
				'label'       => __( 'Categories Linked to Archive Page', 'echo-knowledge-base' ),
				'name'        => 'section_hyperlink_text_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => __( 'Category Archive Page', 'echo-knowledge-base' ),
					'off' => __( 'No navigation', 'echo-knowledge-base' ),
				),
				'default'     => 'off'
			),

			//Advanced
			'section_box_shadow' => array(
				'label'       => __( 'Article List Shadow', 'echo-knowledge-base' ),
				'name'        => 'section_box_shadow',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_shadow' => __( 'No Shadow', 'echo-knowledge-base' ),
					'section_light_shadow' => __( 'Light Shadow', 'echo-knowledge-base' ),
					'section_medium_shadow' => __( 'Medium Shadow', 'echo-knowledge-base' ),
					'section_bottom_shadow' => __( 'Bottom Shadow', 'echo-knowledge-base' )
				),
				'default'     => $default_style['section_box_shadow']
			),
			'section_head_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_top']
			),
			'section_head_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_bottom']
			),
			'section_head_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_left']
			),
			'section_head_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_head_padding_right']
			),
            'section_border_radius' => array(
				'label'       => __( 'Radius', 'echo-knowledge-base' ),
				'name'        => 'section_border_radius',
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_border_radius']
			),
			'section_border_width' => array(
				'label'       => __( 'Width', 'echo-knowledge-base' ),
				'name'        => 'section_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_border_width']
			),

			/***   KB Main Page -> Articles Listed in Sub-Category ***/
			'section_box_height_mode' => array(
				'label'       => __( 'Height Mode', 'echo-knowledge-base' ),
				'name'        => 'section_box_height_mode',
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
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_height']
			),
			'section_body_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_top',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_top']
			),
			'section_body_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_bottom',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_bottom']
			),
			'section_body_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_left',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_left']
			),
			'section_body_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_right',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['section_body_padding_right']
			),
			'section_article_underline' => array(
				'label'       => __( 'Article Underline Hover', 'echo-knowledge-base' ),
				'name'        => 'section_article_underline',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => $default_style['section_article_underline']
			),
			'article_list_margin' => array(
				'label'       => __( 'Margin', 'echo-knowledge-base' ),
				'name'        => 'article_list_margin',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => $default_style['article_list_margin']
			),
			'article_list_spacing' => array(
				'label'       => __( 'Between', 'echo-knowledge-base' ),
				'name'        => 'article_list_spacing',
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
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_title_font_color']
			),
			'search_background_color' => array(
				'label'       => __( 'Search Background', 'echo-knowledge-base' ),
				'name'        => 'search_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_background_color']
			),
			'search_text_input_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_text_input_background_color']
			),
			'search_text_input_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_text_input_border_color']
			),
			'search_btn_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'search_btn_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['search_btn_background_color']
			),
			'search_btn_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_btn_border_color',
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
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['article_font_color']
			),
			'article_icon_color' => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'article_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['article_icon_color']
			),
			'section_body_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'section_body_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_body_background_color']
			),
			'section_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'section_border_color',
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
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_head_font_color']
			),
			'section_head_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'section_head_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_head_background_color']
			),
			'section_head_description_font_color' => array(
				'label'       => __( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'section_head_description_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_head_description_font_color']
			),
			'section_divider_color' => array(
				'label'       => __( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_divider_color']
			),
			'section_category_font_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'section_category_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_category_font_color']
			),
			'section_category_icon_color' => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'section_category_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => $default_color['section_category_icon_color']
			),
			'section_head_category_icon_color' => array(
				'label'       => __( 'Top Level Category Icon', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_color',
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
				'size'        => '60',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'How Can We Help?', 'echo-knowledge-base' )
			),
			'search_box_hint' => array(
				'label'       => __( 'Search Hint', 'echo-knowledge-base' ),
				'name'        => 'search_box_hint',
				'size'        => '60',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search the documentation...', 'echo-knowledge-base' )
			),
			'search_button_name' => array(
				'label'       => __( 'Search Button Name', 'echo-knowledge-base' ),
				'name'        => 'search_button_name',
				'size'        => '25',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search', 'echo-knowledge-base' )
			),
			'search_results_msg' => array(
				'label'       => __( 'Search Results Message', 'echo-knowledge-base' ),
				'name'        => 'search_results_msg',
				'size'        => '60',
				'max'         => '80',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search Results for', 'echo-knowledge-base' )
			),
			'no_results_found' => array(
				'label'       => __( 'No Matches Found Text', 'echo-knowledge-base' ),
				'name'        => 'no_results_found',
				'size'        => '80',
				'max'         => '80',
				'min'         => '1',
				'allowed_tags' => array('a' => array(
													'href'  => true,
													'title' => true,
												)),
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'No matches found', 'echo-knowledge-base' )
			),
			'min_search_word_size_msg' => array(
				'label'       => __( 'Minimum Search Word Size Message', 'echo-knowledge-base' ),
				'name'        => 'min_search_word_size_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Enter a word with at least one character.', 'echo-knowledge-base' )
			),


            /***   Categories and Articles ***/

			'category_empty_msg' => array(
				'label'       => __( 'Empty Category Notice', 'echo-knowledge-base' ),
				'name'        => 'category_empty_msg',
				'size'        => '60',
				'max'         => '150',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Articles coming soon', 'echo-knowledge-base' )
			),
			'collapse_articles_msg' => array(
				'label'       => __( 'Collapse Articles Text', 'echo-knowledge-base' ),
				'name'        => 'collapse_articles_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Collapse Articles', 'echo-knowledge-base' )
			),
			'show_all_articles_msg' => array(
				'label'       => __( 'Show All Articles Text', 'echo-knowledge-base' ),
				'name'        => 'show_all_articles_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Show all articles', 'echo-knowledge-base' )
			),
			'choose_main_topic' => array(
				'label'       => __( 'Choose Main Topic', 'echo-knowledge-base' ),
				'name'        => 'choose_main_topic',
				'size'        => '60',
				'max'         => '150',
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
	const BLANK = 'blank';
	const ARCHIVED = 'archived';
	const PUBLISHED = 'published';
}
