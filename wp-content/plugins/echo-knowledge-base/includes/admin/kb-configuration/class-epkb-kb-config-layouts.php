<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class EPKB_KB_Config_Layouts {

	const KB_ARTICLE_PAGE_NO_LAYOUT = 'Article';
	const SIDEBAR_LAYOUT = 'Sidebar';
	const GRID_LAYOUT = 'Grid';
	const CATEGORIES_LAYOUT = 'Categories';
	const KB_DEFAULT_LAYOUT_STYLE = 'Demo1';
	const KB_DEFAULT_COLORS_STYLE = 'demo_1';

	/**
	 * Get all known layouts including add-ons
	 * @return array all defined layout names
	 */
	public static function get_main_page_layout_name_value() {
		$core_layouts = array (
			EPKB_KB_Config_Layout_Basic::LAYOUT_NAME => __( 'Basic', 'echo-knowledge-base' ),
			EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME  => __( 'Tabs', 'echo-knowledge-base' ),
			EPKB_KB_Config_Layout_Categories::LAYOUT_NAME  => __( 'Category Focused', 'echo-knowledge-base' )
		);
		return apply_filters( 'epkb_layout_names', $core_layouts );
	}

	/**
	 * Get all known layouts including add-ons
	 * @return array all defined layout names
	 */
	public static function get_main_page_layout_names() {
		$layout_name_values = self::get_main_page_layout_name_value();
		return array_keys($layout_name_values);
	}

	/**
	 * Return current layout or default layout if not found.
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_kb_main_page_layout_name( $kb_config ) {
		$chosen_main_page_layout = EPKB_Utilities::post('epkb_chosen_main_page_layout');
		$layout = empty($kb_config['kb_main_page_layout']) || ! in_array($kb_config['kb_main_page_layout'], self::get_main_page_layout_names() )
						? EPKB_KB_Config_Layout_Basic::LAYOUT_NAME
						: (  empty($chosen_main_page_layout) ? $kb_config['kb_main_page_layout'] : $chosen_main_page_layout );
		return $layout;
	}

	/**
	 * Return current article page layout if any
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_article_page_layout_name( $kb_config ) {
		$layout = empty($kb_config['kb_article_page_layout']) || ! in_array($kb_config['kb_article_page_layout'], array_keys(self::get_article_page_layout_names()) )
						? EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT : $kb_config['kb_article_page_layout'];
		return $layout;
	}

	/**
	 * Get Article Page layouts
	 * @return array all Page 2 layouts
	 */
	public static function get_article_page_layout_names() {
		$core_layouts = array (
			self::KB_ARTICLE_PAGE_NO_LAYOUT => 'Article'
		);
		return apply_filters( 'epkb_article_page_layout_names', $core_layouts );
	}

	/**
	 * Given Main Page layout, get possible Article Page layouts
	 *
	 * @param $main_page_layout
	 * @return array
	 */
	public static function get_article_page_layouts( $main_page_layout ) {
		$layout_mapping = self::get_layout_mapping();
		$found_article_page_layouts = array();
		$article_page_layouts = self::get_article_page_layout_names();
		unset($article_page_layouts[self::KB_ARTICLE_PAGE_NO_LAYOUT]);
		foreach( $layout_mapping as $index => $mapping ) {
			$article_layout = empty($mapping[$main_page_layout]) ? '' : $mapping[$main_page_layout];
			if ( ! empty($article_layout) ) {
				$found_article_page_layouts[ $article_layout ] = isset($article_page_layouts[$article_layout]) ? $article_page_layouts[$article_layout] : 'Article';
			}
		}

		return $found_article_page_layouts;
	}

	/**
	 * Mapping between Page 1 and Page 2
	 *
	 * @return array all defined layout mapping
	 */
	public static function get_layout_mapping() {
		$core_layouts = array (
			array( EPKB_KB_Config_Layout_Basic::LAYOUT_NAME => self::KB_ARTICLE_PAGE_NO_LAYOUT ),
			array( EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME => self::KB_ARTICLE_PAGE_NO_LAYOUT ),
			array( EPKB_KB_Config_Layout_Categories::LAYOUT_NAME => self::KB_ARTICLE_PAGE_NO_LAYOUT )
		);
		return apply_filters( 'epkb_layout_mapping', $core_layouts );
	}

	/**
	 * Does given layout show articles as well?
	 *
	 * @param $layout
	 * @return true if this Main Page layout displayes articles as well
	 */
	public static function is_main_page_displaying_sidebar( $layout ) {
		return in_array( $layout, array(EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT) );
	}

	/**
	 * Does given layout shows article links?
	 *
	 * @param $layout
	 * @return true if this Main Page layout displayes articles as well
	 */
	public static function is_main_layout_displaying_article_links( $layout ) {
		return ! in_array( $layout, array(self::GRID_LAYOUT) );
	}

	/**
	 * Get all layouts that shows article on the KB Main Page
	 *
	 * @param $layout
	 * @return true if this Article Page layout displayes some kind of layout
	 */
	public static function is_article_page_displaying_sidebar( $layout ) {
		return  in_array( $layout, array(EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT) );
	}

	/**
	 * Main Page: Get all known styles (based on layout) including add-ons
	 *
	 * @param $kb_config
	 * @return array all defined color names
	 */
	public static function get_main_page_style_names( $kb_config ) {
		$kb_main_page_layout = self::get_kb_main_page_layout_name( $kb_config );

		$add_on_style_names = apply_filters( 'epkb_style_names', array() );
		if ( isset($add_on_style_names[$kb_main_page_layout]) ) {
			return $add_on_style_names[$kb_main_page_layout];
		}

		switch( $kb_main_page_layout ) {
			case EPKB_KB_Config_Layout_Basic::LAYOUT_NAME:
			default:
				return array( EPKB_KB_Config_Layout_Basic::LAYOUT_STYLE_1 => EPKB_KB_Config_Layout_Basic::LAYOUT_STYLE_1,
							  EPKB_KB_Config_Layout_Basic::LAYOUT_STYLE_2 => EPKB_KB_Config_Layout_Basic::LAYOUT_STYLE_2 );
							  //EPKB_KB_Config_Layout_Basic::BASIC_LAYOUT_STYLE_3 => EPKB_KB_Config_Layout_Basic::BASIC_LAYOUT_STYLE_3 );
				break;
			case EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME:
				return array( EPKB_KB_Config_Layout_Tabs::LAYOUT_STYLE_1 => EPKB_KB_Config_Layout_Tabs::LAYOUT_STYLE_1,
							  EPKB_KB_Config_Layout_Tabs::LAYOUT_STYLE_2 => EPKB_KB_Config_Layout_Tabs::LAYOUT_STYLE_2 );
							  //EPKB_KB_Config_Layout_Tabs::TABS_LAYOUT_STYLE_3 => EPKB_KB_Config_Layout_Tabs::TABS_LAYOUT_STYLE_3 );
				break;
			case EPKB_KB_Config_Layout_Categories::LAYOUT_NAME:
				return array( EPKB_KB_Config_Layout_Categories::LAYOUT_STYLE_1 => EPKB_KB_Config_Layout_Categories::LAYOUT_STYLE_1,
				              EPKB_KB_Config_Layout_Categories::LAYOUT_STYLE_2 => EPKB_KB_Config_Layout_Categories::LAYOUT_STYLE_2 );
				//EPKB_KB_Config_Layout_Categories::TABS_LAYOUT_STYLE_3 => EPKB_KB_Config_Layout_Categories::TABS_LAYOUT_STYLE_3 );
				break;
		}
	}

	/**
	 * Article Page: Get all known styles (based on layout) including add-ons
	 *
	 * @param $kb_config
	 * @return array all defined color names
	 */
	public static function get_article_page_style_names( $kb_config ) {
		$kb_article_page_layout = self::get_article_page_layout_name( $kb_config );

		$add_on_style_names = apply_filters( 'epkb_style_names', array() );
		if ( isset($add_on_style_names[$kb_article_page_layout]) ) {
			return $add_on_style_names[$kb_article_page_layout];
		}

		switch( $kb_article_page_layout ) {
			case EPKB_KB_Config_Layout_Basic::LAYOUT_NAME:
			case EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME:
			case EPKB_KB_Config_Layout_Categories::LAYOUT_NAME:
			default:
				return array( EPKB_KB_Config_Layout_Basic::LAYOUT_STYLE_1 => EPKB_KB_Config_Layout_Basic::LAYOUT_STYLE_1);
				break;
		}
	}

	/**
	 * Get all known search box styles (based on layout) including add-ons
	 *
	 * @param $kb_config
	 * @return array all defined color names
	 */
	public static function get_search_box_style_names( $kb_config ) {
		$kb_main_page_layout = self::get_kb_main_page_layout_name( $kb_config );

		switch( $kb_main_page_layout ) {
			case EPKB_KB_Config_Layout_Basic::LAYOUT_NAME:
			default:
				return array( EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_1 => EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_1,
						EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_2       => EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_2,
						EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_3       => EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_3,
						EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_4       => EPKB_KB_Config_Layout_Basic::SEARCH_BOX_LAYOUT_STYLE_4
				);
				break;
			case EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME:
				return array( EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_1 => EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_1,
				              EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_2 => EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_2,
				              EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_3 => EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_3,
				              EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_4 => EPKB_KB_Config_Layout_Tabs::SEARCH_BOX_LAYOUT_STYLE_4
				);
				break;
			case EPKB_KB_Config_Layout_Categories::LAYOUT_NAME:
				return array( EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_1 => EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_1,
				              EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_2 => EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_2,
				              EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_3 => EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_3,
				              EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_4 => EPKB_KB_Config_Layout_Categories::SEARCH_BOX_LAYOUT_STYLE_4
				);
				break;
		}
	}

	/**
	 * Get all known colors including add-ons
	 * @return array all defined color names
	 */
	public static function get_colors_names() {
		$core_colors = array (
				'black-white1'  => 'black-white1',
				'black-white2'  => 'black-white2',
				'black-white3'  => 'black-white3',
				'black-white4'  => 'black-white4',
				'blue1'         => 'blue1',
				'blue2'         => 'blue2',
				'blue3'         => 'blue3',
				'blue4'         => 'blue4',
				'green1'        => 'green1',
				'green2'        => 'green2',
				'green3'        => 'green3',
				'green4'        => 'green4',
				'red1'          => 'red1',
				'red2'          => 'red2',
				'red3'          => 'red3',
				'red4'          => 'red4',
				'demo_1'        => 'demo_1',

		);
		return apply_filters( 'epkb_colors_names', $core_colors );
	}

	/**
	 * Register filters for layouts that are part of the plugin or add-ons
	 */
	public static function register_kb_config_hooks() {
		
		// register layouts and colors and text
		add_filter( 'epkb_kb_main_page_style_settings', array( 'EPKB_KB_Config_Layout_Basic', 'get_kb_config_style' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_style_settings', array( 'EPKB_KB_Config_Layout_Tabs', 'get_kb_config_style' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_style_settings', array( 'EPKB_KB_Config_Layout_Categories', 'get_kb_config_style' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_colors_settings', array( 'EPKB_KB_Config_Layout_Basic', 'get_kb_config_colors' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_colors_settings', array( 'EPKB_KB_Config_Layout_Tabs', 'get_kb_config_colors' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_colors_settings', array( 'EPKB_KB_Config_Layout_Categories', 'get_kb_config_colors' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_text_settings', array( 'EPKB_KB_Config_Layout_Basic', 'get_kb_config_text' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_text_settings', array( 'EPKB_KB_Config_Layout_Tabs', 'get_kb_config_text' ), 10, 2 );
		add_filter( 'epkb_kb_main_page_text_settings', array( 'EPKB_KB_Config_Layout_Categories', 'get_kb_config_text' ), 10, 2 );

		// register style, search box style and color sets
		add_filter( 'epkb_kb_main_page_style_set', array( 'EPKB_KB_Config_Layout_Basic', 'get_style_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_style_set', array( 'EPKB_KB_Config_Layout_Tabs', 'get_style_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_style_set', array( 'EPKB_KB_Config_Layout_Categories', 'get_style_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_search_box_style_set', array( 'EPKB_KB_Config_Layout_Basic', 'get_search_box_style_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_search_box_style_set', array( 'EPKB_KB_Config_Layout_Tabs', 'get_search_box_style_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_search_box_style_set', array( 'EPKB_KB_Config_Layout_Categories', 'get_search_box_style_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_colors_set', array( 'EPKB_KB_Config_Layout_Basic', 'get_colors_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_colors_set', array( 'EPKB_KB_Config_Layout_Tabs', 'get_colors_set' ), 10, 3 );
		add_filter( 'epkb_kb_main_page_colors_set', array( 'EPKB_KB_Config_Layout_Categories', 'get_colors_set' ), 10, 3 );

		add_filter( 'epkb_max_layout_level', array( 'EPKB_KB_Config_Layouts', 'get_max_layout_level') );

		// register add-on hooks
		do_action( 'epkb_register_kb_config_hooks' );
	}

	public static function get_main_page_style_set( $layout_name, $set_name ) {
		return apply_filters( 'epkb_kb_main_page_style_set', array(), $layout_name, $set_name );
	}

	public static function get_article_page_style_set( $layout_name, $set_name ) {
		return apply_filters( 'epkb_article_page_style_set', array(), $layout_name, $set_name );
	}

	public static function get_main_page_colors_set( $layout_name, $set_name ) {
		return apply_filters( 'epkb_kb_main_page_colors_set', array(), $layout_name, $set_name );
	}

	public static function get_article_page_colors_set( $layout_name, $set_name ) {
		return apply_filters( 'epkb_article_page_colors_set', array(), $layout_name, $set_name );
	}

	public static function get_advanced_search_style_set( $pix, $set_name ) {
		return apply_filters( 'epkb_kb_advanced_search_style_set', array(), $pix, $set_name );
	}

	public static function get_max_layout_level( $layout ) {
		if ( $layout === EPKB_KB_Config_Layout_Basic::LAYOUT_NAME ) {
			return EPKB_KB_Config_Layout_Basic::CATEGORY_LEVELS;
		}
		if ( $layout === EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME ) {
			return EPKB_KB_Config_Layout_Tabs::CATEGORY_LEVELS;
		}
		if ( $layout === EPKB_KB_Config_Layout_Categories::LAYOUT_NAME ) {
			return EPKB_KB_Config_Layout_Categories::CATEGORY_LEVELS;
		}
		return $layout;
	}

}
