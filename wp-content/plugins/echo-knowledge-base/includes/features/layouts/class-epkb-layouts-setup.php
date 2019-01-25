<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class EPKB_Layouts_Setup {

	static $demo_mode = false;

	public function __construct() {
		add_filter( 'the_content', array( $this, 'get_kb_page_output_hook' ), 99999 ); // must be high priority
		add_shortcode( EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME, array( 'EPKB_Layouts_Setup', 'output_kb_page_shortcode' ) );
	}

	/**
	 * Current Theme / KB template  ==>  the_content()  ==> get article (this method)
	 *
	 * @param $content
	 * @return string
	 */
	public function get_kb_page_output_hook( $content ) {

		// ignore if not post, is archive or current theme with any layout
		// KEEP performance optimized
		$post = empty($GLOBALS['post']) ? '' : $GLOBALS['post'];
		if ( empty($post) || ! $post instanceof WP_Post || empty($post->post_type) || is_archive() || ! is_main_query() ) {
			return $content;
		}

		// continue if NOT KB Article URL; KEEP performance optimized
		if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return $content;
		}

		// we have KB Article
		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( is_wp_error($kb_id) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// initialize KB config to be accessible to templates
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		// retrieve article content and features
		$content = EPKB_Articles_Setup::get_article_content_and_features( $post, $content, $kb_config );

		// if this is ARTICLE PAGE with SBL then add Sidebar
		if ( $kb_config['kb_article_page_layout'] == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT ) {
			$content = EPKB_Articles_Setup:: output_article_page_with_layout( $content, $kb_config );
		}

		return $content;
	}

	/**
	 * Output layout based on KB Shortcode.
	 *
	 * @param array $shortcode_attributes are shortcode attributes that the user added with the shortcode
	 * @return string of HTML output replacing the shortcode itself
	 */
	public static function output_kb_page_shortcode( $shortcode_attributes ) {
        $kb_config = self::get_kb_config( $shortcode_attributes );

		return self:: output_main_page( $kb_config );
	}

	/**
	 * Show KB Main page i.e. knowledge-base/ url or KB Article Page in case of SBL.
	 *
	 * @param bool $is_builder_on
	 * @param null $kb_config
	 * @param array $article_seq
	 * @param array $categories_seq
	 *
	 * @return string
	 */
	public static function output_main_page( $kb_config, $is_builder_on=false, $article_seq=array(), $categories_seq=array() ) {

		// do not display Main Page of Archived KB
		if ( $kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Utilities::is_kb_archived( $kb_config['status'] ) ) {
			return __( 'This knowledge base was archived.', 'echo-knowledge-base' );
		}

		// let layout class display the KB main page
		$layout = empty($kb_config['kb_main_page_layout']) ? '' : $kb_config['kb_main_page_layout'];

		$layout_output = '';
		if ( ! self::is_core_layout( $layout ) ) {
			ob_start();
			apply_filters( 'epkb_' . strtolower($layout) . '_layout_output', $kb_config, $is_builder_on, $article_seq, $categories_seq );
			$layout_output = ob_get_clean();

			// use Basic Layout if the current layout is missing
			$layout = empty($layout_output) ? EPKB_KB_Config_Layout_Basic::LAYOUT_NAME : $layout;
		}

		// if this is core layout then generate it; if this is add-on layout but it is missing then use Basic Layout
		if ( empty($layout_output) ) {
			$layout_class_name = 'EPKB_Layout_' . ucfirst($layout);
			$layout_class = class_exists($layout_class_name) ? new $layout_class_name() : new EPKB_Layout_Basic();
			ob_start();
			$layout_class->display_kb_main_page( $kb_config, $is_builder_on, $article_seq, $categories_seq );
			$layout_output = ob_get_clean();
		}

		return $layout_output;
	}

	private static function is_core_layout( $layout ) {
		return $layout == EPKB_KB_Config_Layout_Basic::LAYOUT_NAME || $layout == EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME;
	}

	/**
	 * Check that the layout exists and is properly configured
	 *
	 * @param array $shortcode_attributes
	 *
	 * @return array return the KB configuration
	 */
	private static function get_kb_config( $shortcode_attributes ) {

		$kb_id = empty($shortcode_attributes['id']) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $shortcode_attributes['id'] ;
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( "KB ID in shortcode is invalid. Using KB ID 1 instead of: ", $kb_id );
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		if ( count( $shortcode_attributes ) > 1 ) {
			EPKB_Logging::add_log( "KB with ID " . $kb_id . ' has too many shortcode attributes', $shortcode_attributes );
		}

		//retrieve KB config
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		return $kb_config;
	}
}
