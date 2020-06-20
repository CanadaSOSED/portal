<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Store Wizard presets data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Color_Presets {

	public static function get_preset_defaults() {
		return array(
			// general
			'background_color'                    => '#FFFFFF',

			// search Box
			'search_title_font_color'             => '#FFFFFF',
			'search_background_color'             => '#f7941d',
			'search_text_input_background_color'  => '#FFFFFF',
			'search_text_input_border_color'      => '#CCCCCC',
			'search_btn_background_color'         => '#40474f',
			'search_btn_border_color'             => '#F1F1F1',

			// articles Listed In Category Box
			'section_head_font_color'             => '#40474f',
			'section_head_background_color'       => '#FFFFFF',
			'section_head_description_font_color' => '#b3b3b3',
			'section_body_background_color'       => '#FFFFFF',
			'section_border_color'                => '#F7F7F7',
			'section_divider_color'               => '#edf2f6',
			'section_category_font_color'         => '#40474f',
			'section_category_icon_color'         => '#f7941d',
			'section_head_category_icon_color'    => '#f7941d',
			'article_font_color'                  => '#333232',
			'article_icon_color'                  => '#333232',

			// tabs
			'tab_nav_active_font_color'           => '#686868',
			'tab_nav_active_background_color'     => '#FFFFFF',
			'tab_nav_font_color'                  => '#B3B3B3',
			'tab_nav_background_color'            => '#FFFFFF',
			'tab_nav_border_color'                => '#686868',
		);
	}

	/**
	 * Return specific template.
	 *
	 * @param $preset_id
	 *
	 * @return array
	 */
	public static function get_template( $preset_id ) {

		if ( ! EPKB_Utilities::is_positive_int( $preset_id ) ) {
			return self::get_preset_defaults();
		}

		switch ( $preset_id ) {
			case 1:
				$preset_config = self::$preset_yellow;
				break;
			// BLUE
			case 3:
				$preset_config = self::$preset_light_blue;
				break;
			case 4:
				$preset_config = self::$preset_medium_blue;
				break;
			case 5:
				$preset_config = self::$preset_dark_blue;
				break;
			// GREEN
			case 6:
				$preset_config = self::$template_light_green;
				break;
			case 7:
				$preset_config = self::$preset_medium_green;
				break;
			case 8:
				$preset_config = self::$preset_dark_green;
				break;
			// RED
			case 9:
				$preset_config = self::$preset_light_red;
				break;
			case 10:
				$preset_config = self::$preset_medium_red;
				break;
			case 11:
				$preset_config = self::$preset_dark_red;
				break;
			// GRAY
			case 12:
				$preset_config = self::$preset_light_gray;
				break;
			case 13:
				$preset_config = self::$preset_medium_gray;
				break;
			case 14:
				$preset_config = self::$preset_dark_gray;
				break;
			default:
				$preset_config = self::get_preset_defaults();
		}

		// color presets for add-ons
		$add_on_color_presets = apply_filters( 'epkb_theme_wizard_get_color_presets', $preset_config, $preset_id );

		$preset_config = array_merge( $preset_config, $add_on_color_presets );

		return array_merge( self::get_preset_defaults(), $preset_config );
	}

	/**
	 * Get JSON string with default template data ready to use in html
	 *
	 * @param $preset_id
	 *
	 * @return string
	 */
	public static function get_template_data( $preset_id ) {
		$template = self::get_template( $preset_id );

		return htmlspecialchars( json_encode( $template ), ENT_QUOTES, 'UTF-8' );
	}

	public static $preset_yellow = array(

		// Search Box
		'search_title_font_color'            => '#111111', // Title
		'search_background_color'            => '#eded00', // Search Background
		'search_text_input_background_color' => '#FFFFFF', // Input - Background
		'search_text_input_border_color'     => '#383838', // Input - Border
		'search_btn_background_color'        => '#40474f', // Button - Background
		'search_btn_border_color'            => '#383838', // Button - Border

		// Tabs
		'tab_nav_active_font_color'          => '#111111', // Active Tab - Text
		'tab_nav_active_background_color'    => '#eded00', // Active Tab - Background
		'tab_nav_border_color'               => '#000000', // Active Tab - Border
		'tab_nav_font_color'                 => '#3a3a3a', // InActive Tabs - Text
		'tab_nav_background_color'           => '#FFFFFF', // InActive Tabs - Text

		// Categories
		'section_head_font_color'             => '#3a3a3a', // Category Box Heading - Text
		'section_head_background_color'       => '#eded00', // Category Box Heading - Background
		'section_head_category_icon_color'    => '#424242', // Category Box Heading - Top Level Category Icon
		'section_head_description_font_color' => '#515151', // Category Box Heading - Category Description
		'section_divider_color'               => '#515151', // Category Box Heading - Divider

		'section_category_font_color'   => '#40474f', // Sub Category - Text
		'section_category_icon_color'   => '#efc300', // Sub Category - Icon

		// Articles
		'section_body_background_color' => '#FFFFFF', // Articles Container - Background
		'section_border_color'          => '#eeee22', // Articles Container - Border
		'article_font_color'            => '#3a3a3a', // Articles - Text
		'article_icon_color'            => '#becc00'  // Articles - Icon
	); // Yellow Preset

	// BLUE
	public static $preset_light_blue = array(

		//Search Box
		'search_title_font_color'             => '#FFFFFF',
		'search_background_color'             => '#53ccfb',
		'search_text_input_background_color'  => '#FFFFFF',
		'search_text_input_border_color'      => '#DDDDDD',
		'search_btn_background_color'         => '#3093ba',
		'search_btn_border_color'             => '#DDDDDD',

		//Category Tabs
		'tab_nav_active_font_color'           => '#53ccfb',
		'tab_nav_active_background_color'     => '#FFFFFF',
		'tab_nav_font_color'                  => '#686868',
		'tab_nav_background_color'            => '#FFFFFF',
		'tab_nav_border_color'                => '#686868',

		//Articles Listed In Category Box
		'section_head_font_color'             => '#53ccfb',
		'section_head_background_color'       => '#FFFFFF',
		'section_head_description_font_color' => '#b3b3b3',
		'section_body_background_color'       => '#FFFFFF',
		'section_border_color'                => '#dbdbdb',
		'section_divider_color'               => '#c5c5c5',
		'section_category_font_color'         => '#868686',
		'section_category_icon_color'         => '#868686',
		'section_head_category_icon_color'    => '#53ccfb',
	);

	public static $preset_medium_blue = array(

		// Search Box
		'search_title_font_color'            => '#FFFFFF', // Title
		'search_background_color'            => '#1e73be', // Search Background
		'search_text_input_background_color' => '#FFFFFF', // Input - Background
		'search_text_input_border_color'     => '#CCCCCC', // Input - Border
		'search_btn_background_color'        => '#333333', // Button - Background
		'search_btn_border_color'            => '#CCCCCC', // Button - Border

		// Tabs
		'tab_nav_active_font_color'          => '#FFFFFF', // Active Tab - Text
		'tab_nav_active_background_color'    => '#1e73be', // Active Tab - Background
		'tab_nav_border_color'               => '#000000', // Active Tab - Border
		'tab_nav_font_color'                 => '#80919d', // InActive Tabs - Text
		'tab_nav_background_color'           => '#ffffff', // InActive Tabs - Text

		// Categories
		'section_head_font_color'             => '#333333', // Category Box Heading - Text
		'section_head_background_color'       => '#FFFFFF', // Category Box Heading - Background
		'section_head_category_icon_color'    => '#039be5', // Category Box Heading - Top Level Category Icon
		'section_head_description_font_color' => '#515151', // Category Box Heading - Category Description
		'section_divider_color'               => '#039be5', // Category Box Heading - Divider

		'section_category_font_color'   => '#40474f', // Sub Category - Text
		'section_category_icon_color'   => '#039be5', // Sub Category - Icon

		// Articles
		'section_body_background_color' => '#FFFFFF', // Articles Container - Background
		'section_border_color'          => '#039be5', // Articles Container - Border
		'article_font_color'            => '#333333', // Articles - Text
		'article_icon_color'            => '#039be5'  // Articles - Icon
	); // Blue Preset

	public static $preset_dark_blue = array(

		//Search Box
		'search_title_font_color'             => '#FFFFFF',
		'search_background_color'             => '#4398ba',
		'search_text_input_background_color'  => '#ffffff',
		'search_text_input_border_color'      => '#FFFFFF',
		'search_btn_background_color'         => '#686868',
		'search_btn_border_color'             => '#F1F1F1',

		//Category Tabs
		'tab_nav_active_font_color'           => '#FFFFFF',
		'tab_nav_active_background_color'     => '#4398ba',
		'tab_nav_font_color'                  => '#686868',
		'tab_nav_background_color'            => '#f9f9f9',
		'tab_nav_border_color'                => '#686868',

		//Articles Listed In Category Box
		'section_head_font_color'             => '#FFFFFF',
		'section_head_background_color'       => '#4398ba',
		'section_head_description_font_color' => '#FFFFFF',
		'section_body_background_color'       => '#f9f9f9',
		'section_border_color'                => '#F7F7F7',
		'section_divider_color'               => '#CDCDCD',
		'section_category_font_color'         => '#868686',
		'section_category_icon_color'         => '#868686',
		'section_head_category_icon_color'    => '#FFFFFF',
	);

	// GREEN light
	public static $template_light_green = array(

		// Search Box
		'search_title_font_color'             => '#FFFFFF', // Title
		'search_background_color'             => '#bfdac1', // Search Background
		'search_text_input_background_color'  => '#FFFFFF', // Input - Background
		'search_text_input_border_color'      => '#DDDDDD', // Input - Border
		'search_btn_background_color'         => '#4a714e', // Button - Background
		'search_btn_border_color'             => '#DDDDDD', // Button - Border

		// Tabs
		'tab_nav_active_font_color'           => '#111111', // Active Tab - Text
		'tab_nav_active_background_color'     => '#bfdac1', // Active Tab - Background
		'tab_nav_border_color'                => '#000000', // Active Tab - Border
		'tab_nav_font_color'                  => '#3a3a3a', // InActive Tabs - Text
		'tab_nav_background_color'            => '#FFFFFF', // InActive Tabs - Text

		// Categories
		'section_head_font_color'             => '#4a714e', // Category Box Heading - Text
		'section_head_background_color'       => '#FFFFFF', // Category Box Heading - Background
		'section_head_category_icon_color'    => '#b1d8b4', // Category Box Heading - Top Level Category Icon
		'section_head_description_font_color' => '#bfdac1', // Category Box Heading - Category Description
		'section_divider_color'               => '#c5c5c5', // Category Box Heading - Divider

		'section_category_font_color'   => '#b1d8b4', // Sub Category - Text
		'section_category_icon_color'   => '#868686', // Sub Category - Icon

		// Articles
		'section_body_background_color' => '#FFFFFF', // Articles Container - Background
		'section_border_color'          => '#dbdbdb', // Articles Container - Border
	); // Green Preset

	// GREEN medium
	public static $preset_medium_green = array(

		// Search Box
		'search_title_font_color'            => '#FFFFFF', // Title
		'search_background_color'            => '#81d742', // Search Background
		'search_text_input_background_color' => '#FFFFFF', // Input - Background
		'search_text_input_border_color'     => '#CCCCCC', // Input - Border
		'search_btn_background_color'        => '#333333', // Button - Background
		'search_btn_border_color'            => '#CCCCCC', // Button - Border

		// Tabs
		'tab_nav_active_font_color'          => '#FFFFFF', // Active Tab - Text
		'tab_nav_active_background_color'    => '#81d742', // Active Tab - Background
		'tab_nav_border_color'               => '#000000', // Active Tab - Border
		'tab_nav_font_color'                 => '#80919d', // InActive Tabs - Text
		'tab_nav_background_color'           => '#ffffff', // InActive Tabs - Text

		// Categories
		'section_head_font_color'             => '#81d742', // Category Box Heading - Text
		'section_head_background_color'       => '#fcfcfc', // Category Box Heading - Background
		'section_head_category_icon_color'    => '#333333', // Category Box Heading - Top Level Category Icon
		'section_head_description_font_color' => '#515151', // Category Box Heading - Category Description
		'section_divider_color'               => '#81d742', // Category Box Heading - Divider

		'section_category_font_color'   => '#40474f', // Sub Category - Text
		'section_category_icon_color'   => '#333333', // Sub Category - Icon

		// Articles
		'section_body_background_color' => '#FFFFFF', // Articles Container - Background
		'section_border_color'          => '#dddddd', // Articles Container - Border
		'article_font_color'            => '#333333', // Articles - Text
		'article_icon_color'            => '#81d742'  // Articles - Icon
	); // Green Preset

	// GREEN dark
	public static $preset_dark_green = array(

		// Search Box
		'search_title_font_color'            => '#FFFFFF', // Title
		'search_background_color'            => '#628365', // Search Background
		'search_text_input_background_color' => '#FFFFFF', // Input - Background
		'search_text_input_border_color'     => '#DDDDDD', // Input - Border
		'search_btn_background_color'        => '#686868', // Button - Background
		'search_btn_border_color'            => '#DDDDDD', // Button - Border

		// Tabs
		'tab_nav_active_font_color'          => '#111111', // Active Tab - Text
		'tab_nav_active_background_color'    => '#628365', // Active Tab - Background
		'tab_nav_border_color'               => '#000000', // Active Tab - Border
		'tab_nav_font_color'                 => '#3a3a3a', // InActive Tabs - Text
		'tab_nav_background_color'           => '#ffffff', // InActive Tabs - Text

		// Categories
		'section_head_font_color'             => '#FFFFFF', // Category Box Heading - Text
		'section_head_background_color'       => '#628365', // Category Box Heading - Background
		'section_head_category_icon_color'    => '#FFFFFF', // Category Box Heading - Top Level Category Icon
		'section_head_description_font_color' => '#FFFFFF', // Category Box Heading - Category Description
		'section_divider_color'               => '#c5c5c5', // Category Box Heading - Divider

		'section_category_font_color'   => '#868686', // Sub Category - Text
		'section_category_icon_color'   => '#868686', // Sub Category - Icon

		// Articles
		'section_body_background_color' => '#edf4ee', // Articles Container - Background
		'section_border_color'          => '#dbdbdb', // Articles Container - Border
	); // Green Preset

	// RED
	public static $preset_light_red = array(

		//Search Box
		'search_title_font_color'               =>  '#CC0000',
		'search_background_color'               =>  '#f9e5e5',
		'search_text_input_background_color'    =>  '#FFFFFF',
		'search_text_input_border_color'        =>  '#FFFFFF',
		'search_btn_background_color'           =>  '#686868',
		'search_btn_border_color'               =>  '#F1F1F1',

		//Category Tabs
		'tab_nav_active_font_color'             =>  '#CC0000',
		'tab_nav_active_background_color'       =>  '#f9e5e5',
		'tab_nav_font_color'                    =>  '#686868',
		'tab_nav_background_color'              =>  '#FFFFFF',
		'tab_nav_border_color'                  =>  '#686868',

		//Articles Listed In Category Box
		'section_head_font_color'               =>  '#CC0000',
		'section_head_background_color'         =>  '#f9e5e5',
		'section_head_description_font_color'   =>  '#e57f7f',
		'section_body_background_color'         =>  '#FFFFFF',
		'section_border_color'                  =>  '#F7F7F7',
		'section_divider_color'                 =>  '#CDCDCD',
		'section_category_font_color'           =>  '#868686',
		'section_category_icon_color'           =>  '#868686',
		'section_head_category_icon_color'      =>  '#ffffff',
	);

	public static $preset_medium_red = array(

		//Search Box
		'search_title_font_color'               =>  '#FFFFFF',
		'search_background_color'               =>  '#fb8787',
		'search_text_input_background_color'    =>  '#FFFFFF',
		'search_text_input_border_color'        =>  '#DDDDDD',
		'search_btn_background_color'           =>  '#af1e1e',
		'search_btn_border_color'               =>  '#DDDDDD',

		//Category Tabs
		'tab_nav_active_font_color'             =>  '#fb8787',
		'tab_nav_active_background_color'       =>  '#FFFFFF',
		'tab_nav_font_color'                    =>  '#686868',
		'tab_nav_background_color'              =>  '#FFFFFF',
		'tab_nav_border_color'                  =>  '#686868',

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
	);

	public static $preset_dark_red = array(

		//Search Box
		'search_title_font_color'               =>  '#FFFFFF',
		'search_background_color'               =>  '#fb6262',
		'search_text_input_background_color'    =>  '#FFFFFF',
		'search_text_input_border_color'        =>  '#FFFFFF',
		'search_btn_background_color'           =>  '#686868',
		'search_btn_border_color'               =>  '#F1F1F1',

		//Category Tabs
		'tab_nav_active_font_color'             =>  '#FFFFFF',
		'tab_nav_active_background_color'       =>  '#fb6262',
		'tab_nav_font_color'                    =>  '#686868',
		'tab_nav_background_color'              =>  '#fefcfc',
		'tab_nav_border_color'                  =>  '#686868',

		//Articles Listed In Category Box
		'section_head_font_color'               =>  '#FFFFFF',
		'section_head_background_color'         =>  '#fb6262',
		'section_head_description_font_color'   =>  '#FFFFFF',
		'section_body_background_color'         =>  '#fefcfc',
		'section_border_color'                  =>  '#F7F7F7',
		'section_divider_color'                 =>  '#CDCDCD',
		'section_category_font_color'           =>  '#868686',
		'section_category_icon_color'           =>  '#868686',
		'section_head_category_icon_color'      =>  '#ffffff',
	);

	// GRAY
	public static $preset_light_gray = array(

		//Search Box
		'search_title_font_color'               =>  '#686868',
		'search_background_color'               =>  '#fbfbfb',
		'search_text_input_background_color'    =>  '#FFFFFF',
		'search_text_input_border_color'        =>  '#FFFFFF',
		'search_btn_background_color'           =>  '#686868',
		'search_btn_border_color'               =>  '#F1F1F1',

		//Category Tabs
		'tab_nav_active_font_color'             =>  '#686868',
		'tab_nav_active_background_color'       =>  '#ffffff',
		'tab_nav_font_color'                    =>  '#b3b3b3',
		'tab_nav_background_color'              =>  '#FFFFFF',
		'tab_nav_border_color'                  =>  '#686868',

		//Articles Listed In Category Box
		'section_head_font_color'               =>  '#827a74',
		'section_head_background_color'         =>  '#FFFFFF',
		'section_head_description_font_color'   =>  '#b3b3b3',
		'section_body_background_color'         =>  '#FFFFFF',
		'section_border_color'                  =>  '#dbdbdb',
		'section_divider_color'                 =>  '#dadada',
		'section_category_font_color'           =>  '#868686',
		'section_category_icon_color'           =>  '#868686',
		'section_head_category_icon_color'      =>  '#000000 ',
	);

	public static $preset_medium_gray = array(

		//KB Main Page -> Colors -> Search Box
		'search_title_font_color'               =>  '#686868',
		'search_background_color'               =>  '#f1f1f1',
		'search_text_input_background_color'    =>  '#ffffff',
		'search_text_input_border_color'        =>  '#FFFFFF',
		'search_btn_background_color'           =>  '#686868',
		'search_btn_border_color'               =>  '#F1F1F1',

		//Category Tabs
		'tab_nav_active_font_color'             =>  '#686868',
		'tab_nav_active_background_color'       =>  '#F1F1F1',
		'tab_nav_font_color'                    =>  '#686868',
		'tab_nav_background_color'              =>  '#fdfdfd',
		'tab_nav_border_color'                  =>  '#686868',

		//KB Main Page -> Colors -> Articles Listed in Category Box
		'section_head_font_color'               =>  '#525252',
		'section_head_background_color'         =>  '#f1f1f1',
		'section_head_description_font_color'   =>  '#b3b3b3',
		'section_body_background_color'         =>  '#fdfdfd',
		'section_border_color'                  =>  '#F7F7F7',
		'section_divider_color'                 =>  '#CDCDCD',
		'section_category_font_color'           =>  '#868686',
		'section_category_icon_color'           =>  '#000000',
		'section_head_category_icon_color'      =>  '#000000',
	);

	public static $preset_dark_gray = array(

		// Search Box
		'search_title_font_color'               =>  '#FFFFFF', // Title
		'search_background_color'               =>  '#34424c', // Search Background
		'search_text_input_background_color'    =>  '#FFFFFF', // Input - Background
		'search_text_input_border_color'        =>  '#CCCCCC', // Input - Border
		'search_btn_background_color'           =>  '#17aacf', // Button - Background
		'search_btn_border_color'               =>  '#CCCCCC', // Button - Border

		// Tabs
		'tab_nav_active_font_color'             => '#FFFFFF', // Active Tab - Text
		'tab_nav_active_background_color'       => '#34424c', // Active Tab - Background
		'tab_nav_border_color'                  => '#000000', // Active Tab - Border
		'tab_nav_font_color'                    => '#80919d', // InActive Tabs - Text
		'tab_nav_background_color'              => '#ffffff', // InActive Tabs - Text

		'section_head_font_color'               => '#333333', // Category Box Heading - Text
		'section_head_background_color'         => '#7d7d7d', // Category Box Heading - Background
		'section_head_category_icon_color'      => '#36444f', // Category Box Heading - Top Level Category Icon
		'section_head_description_font_color'   => '#515151', // Category Box Heading - Category Description
		'section_divider_color'                 => '#dddddd', // Category Box Heading - Divider

		'section_category_font_color'           => '#40474f', // Sub Category - Text
		'section_category_icon_color'           => '#17aacf', // Sub Category - Icon

		// Articles
		'section_body_background_color'         => '#FFFFFF', // Articles Container - Background
		'section_border_color'                  => '#dddddd', // Articles Container - Border
	); // Grey Preset

}