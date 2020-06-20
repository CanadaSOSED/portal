<?php

/**
 * Handle loading EP templates
 
 * @copyright   Copyright (C) 2018, Echo Plugins 
 * Some code adapted from code in EDD/WooCommmerce (Copyright (c) 2017, Pippin Williamson) and WP.
 */
class EPKB_Templates {

	public function __construct() {
   		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
	}

	/**
	 * Load article templates. Templates are in the 'templates' folder.
	 *
	 * Templates can be overriden in /theme/knowledgebase/ folder.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public static function template_loader( $template ) {
		/** @var WP_Query $wp_query */
        global $wp_query, $eckb_kb_id, $eckb_is_kb_main_page, $epkb_kb_templates_on;

        // KEEP performance optimized

		// handle Category archive page
		$is_kb_taxonomy = ! empty($GLOBALS['taxonomy']) && EPKB_KB_Handler::is_kb_taxonomy($GLOBALS['taxonomy']);
		if ( $is_kb_taxonomy && self::is_kb_template_active() ) {
			$located_template = self::locate_template( 'archive-categories.php' );
			$kb_id = EPKB_KB_Handler::get_kb_id_from_any_taxonomy($GLOBALS['taxonomy']);
			if ( ! empty( $located_template ) && ! is_wp_error($kb_id) ) {
				$eckb_kb_id = $kb_id;
				return $located_template;
			}
		}

		// ignore non-page/post conditions
        if ( ! self::is_post_page() ) {
            return $template;
        }

		// get current post
		$post = isset($GLOBALS['post']) ? $GLOBALS['post'] : '';
		if ( empty($post) || ! $post instanceof WP_Post ) {
			return $template;
		}

        // ignore posts that are not KB Articles; KB Main Page should not be in a post
        if ( $post->post_type == 'post' ) {
            return $template;
        }

		// ignore WordPress search results page
		if ( $wp_query->is_search() ) {
			return $template;
		}

        // is this KB Main Page ?
        $kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		$eckb_is_kb_main_page = false;
        $all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs( true );
        foreach ( $all_kb_configs as $one_kb_config ) {
            if ( ! empty($one_kb_config['kb_main_pages']) && is_array($one_kb_config['kb_main_pages']) &&
                 in_array($post->ID, array_keys($one_kb_config['kb_main_pages']) ) ) {
	            $eckb_is_kb_main_page = true;
                $kb_id = $one_kb_config['id'];
                break;  // found matching KB Main Page
            }
        }

        // is this KB Article Page ?
        if ( ! $eckb_is_kb_main_page ) {
            $kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
            if ( is_wp_error( $kb_id ) ) {
                return $template;
            }
        }

		$eckb_kb_id = $kb_id;

		// continue only if we are using KB templates
		$temp_config = empty($all_kb_configs[$kb_id]) ? array() : $all_kb_configs[$kb_id];
        if ( ! self::is_kb_template_active( $temp_config ) ) {
            return $template;
        }

		// get the layout name
		$layout_config_name = $eckb_is_kb_main_page ? 'kb_main_page_layout' : 'kb_article_page_layout';
		$default_layout_name = $eckb_is_kb_main_page ? EPKB_KB_Config_Layout_Basic::LAYOUT_NAME : EPKB_KB_Config_Layouts::KB_ARTICLE_PAGE_NO_LAYOUT;
		$layout_name =  empty($all_kb_configs[$kb_id][$layout_config_name]) ? $default_layout_name : $all_kb_configs[$kb_id][$layout_config_name];

		// locate KB template
		$template_name = self::get_template_name( $layout_name );
		if ( empty($template_name) ) {
			return $template;
		}

		// locate KB template; if none found then return the default WP template
		$located_template = self::locate_template( $template_name );
		if ( empty($located_template) ) {
			return $template;
		}

		//$epkb_kb_templates_on = true;

		return $located_template;
	}

	private static function is_kb_template_active( $kb_config=array() ) {

		if ( empty($kb_config) ) {
			$taxonomy = empty($GLOBALS['taxonomy']) ? '' : $GLOBALS['taxonomy'];
			$kb_id = EPKB_KB_Handler::get_kb_id_from_any_taxonomy( $taxonomy );
			if ( is_wp_error($kb_id) ) {
				return false;
			}

			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
			if ( is_wp_error($kb_config) ) {
				return false;
			}
		}

		return ! empty($kb_config['templates_for_kb']) && ( $kb_config['templates_for_kb'] == 'kb_templates' );
	}

	/**
	 * Get the default filename for a template.
	 *
	 * @param $layout_name
	 * @return string
	 */
	private static function get_template_name( $layout_name ) {

		$layout_name = strtolower( $layout_name );
		if ( $layout_name == 'article' ) {
			return 'single-article.php';
		} else if ( in_array( $layout_name, array('basic', 'tabs', 'categories', 'grid', 'sidebar') ) ) {
            return 'layout-' . $layout_name . '.php';
        }

		return '';
	}

	/**
	 * Retrieve the name of the highest priority template file that exists.
	 *
	 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that CHILD THEME which
	 * inherit from a PARENT THEME can just overload one file. If the template is
	 * not found in either of those, it looks in KB template folder last
	 *
	 * Taken from bbPress
	 *
	 * @param string|array $template_names Template file(s) to search for, in order.
	 * @return false|string The template filename if one is located.
	 */
	public static function locate_template( $template_names ) {

		// No file found yet
		$located = false;

		// loop through hierarchy of template names
		foreach ( (array) $template_names as $template_name ) {

			// Continue if template is empty
			if ( empty( $template_name ) ) {
				continue;
			}

			// Trim off any slashes from the template name
			$template_name = ltrim( $template_name, '/' );

			// loop through hierarchy of template file locations ( child -> parent -> our theme )
			foreach( self::get_theme_template_paths() as $template_path ) {
				if ( file_exists( $template_path . $template_name ) ) {
					$located = $template_path . $template_name;
					break;
				}
			}

			if ( $located ) {
				break;
			}
		}

		return $located;
	}

	/**
	 * Returns a list of paths to check for template locations:
	 * 1. Child Theme
	 * 2. Parent Theme
	 * 3. KB Theme
	 *
	 * @return array
	 */
	private static function get_theme_template_paths() {

		$template_dir = self::get_theme_template_dir_name();

		$file_paths = array(
			1 => trailingslashit( get_stylesheet_directory() ) . $template_dir,
			10 => trailingslashit( get_template_directory() ) . $template_dir,
			100 => self::get_templates_dir()
		);

		$file_paths = apply_filters( 'epkb_template_paths', $file_paths );

		// sort the file paths based on priority
		ksort( $file_paths, SORT_NUMERIC );

		return array_map( 'trailingslashit', $file_paths );
	}

	/**
	 * Retrieves a template part
	 *
	 * Taken from bbPress
	 *
	 * @param string $slug
	 * @param string $name Optional. Default null
	 * @param $kb_config - used in templates
	 * @param $article - used in templates
	 * @param bool $load
	 *
	 * @return string
	 */
	public static function get_template_part( $slug, $name = null, /** @noinspection PhpUnusedParameterInspection */ $kb_config,
		/** @noinspection PhpUnusedParameterInspection */$article, $load = true ) {
		// Execute code for this part
		do_action( 'epkb_get_template_part_' . $slug, $slug, $name );

		$load_template = apply_filters( 'epkb_allow_template_part_' . $slug . '_' . $name, true );
		if ( false === $load_template ) {
			return '';
		}

		// Setup possible parts
		$templates = array();
		if ( isset( $name ) )
			$templates[] = $slug . '-' . $name . '.php';
		$templates[] = $slug . '.php';

		// Allow template parts to be filtered
		$templates = apply_filters( 'epkb_get_template_part', $templates, $slug, $name );

		// Return the part that is found
		$template_path = self::locate_template( $templates );
		if ( ( true == $load ) && ! empty( $template_path ) ) {
			include( $template_path );
		}

		return $template_path;
	}

	/**
	 * Check if current post/page could be KB one
	 *
	 * @return bool
	 */
	public static function is_post_page() {
		global $wp_query;

		if ( ( isset( $wp_query->is_archive ) && $wp_query->is_archive ) ||
		     ( isset( $wp_query->is_embed ) && $wp_query->is_embed ) ||
		     ( isset( $wp_query->is_category ) && $wp_query->is_category ) ||
		     ( isset( $wp_query->is_tag ) && $wp_query->is_tag ) ||
		     ( isset( $wp_query->is_attachment ) && $wp_query->is_attachment ) ) {
			return false;
		}

		$post = isset($GLOBALS['post']) ? $GLOBALS['post'] : '';
		if ( empty($post) || ! $post instanceof WP_Post || empty($post->post_type) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the path to the EP templates directory
	 * @return string
	 */
	private static function get_templates_dir() {
		return Echo_Knowledge_Base::$plugin_dir . 'templates';
	}

	/**
	 * Returns name of directory inside child or parent theme folder where KB templates are located
	 * Themes can filter this by using the epkb_templates_dir filter.
	 *
	 * @return string
	 */
	private static function get_theme_template_dir_name() {
		return trailingslashit( apply_filters( 'epkb_templates_dir', 'kb_templates' ) );
	}
}