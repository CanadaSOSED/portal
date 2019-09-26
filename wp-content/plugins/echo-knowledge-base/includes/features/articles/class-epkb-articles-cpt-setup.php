<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a new CUSTOM POST TYPE + category + tag for a given instance of KNOWLEDGE BASE.
 *
 * This KB articles will have their post_type set to this newly registered custom post type.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Articles_CPT_Setup {

	public function __construct() {

		add_action( 'init', array( $this, 'register_knowledge_base_post_types'), 10 );

		// only for front-end page display when categories are listed
		if ( ! defined('WP_ADMIN') ) {
			add_filter( 'the_category', array( $this, 'output_article_categories' ), 99, 3 );
		}
	}

	/**
	 * Read configuration and create configured custom post types, each representing a knowledge base
	 */
	public function register_knowledge_base_post_types() {

		$current_id = EPKB_KB_Handler::get_current_kb_id();
		foreach ( epkb_get_instance()->kb_config_obj->get_kb_configs() as $kb_config ) {

			$result = self::register_custom_post_type( $kb_config, $current_id );
			if ( is_wp_error( $result ) ) {
				EPKB_Logging::add_log("Could not register custom post type.", $kb_config['id'], $result);
			}
		}

		// flush rules on plugin activation after CPTs were registered
		$is_flush_rewrite_rules = get_option( 'epkb_flush_rewrite_rules' );
		if ( ! empty($is_flush_rewrite_rules) && ! is_wp_error( $is_flush_rewrite_rules ) ) {
			delete_option( 'epkb_flush_rewrite_rules' );
			flush_rewrite_rules( false );
		}
	}

	/**
	 * Register custom post type, including taxonomies (category, tag) and other constructs.
	 *
	 * @param array $kb_config
	 * @param int|string $current_id
	 * @return bool|WP_Error
	 */
	public static function register_custom_post_type( $kb_config, $current_id ) {

		$kb_id = $kb_config['id'];
		
		// do not register Archived KB
		if ( $kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Utilities::is_kb_archived( $kb_config['status'] ) ) {
			return true;
		}
		
		$kb_post_type = EPKB_KB_Handler::get_post_type( $kb_id );
		$kb_articles_common_path = empty( $kb_config['kb_articles_common_path'] ) ?
									EPKB_KB_Handler::get_default_slug( $kb_id ) : $kb_config['kb_articles_common_path'];

		// determine if this custom post type will be registered for user selected KB; if yes make it visible in admin UI
		$current_id = empty($current_id) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $current_id;
		$show_post_in_ui = ( $kb_id == $current_id ) || ! is_admin();  // true if front-end (like admin bar)

		// first we need to setup CATEGORY taxonomy so that its rules are above 'attachments' links from its post type

		/** setup Category taxonomy */

		$taxonomy_name = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$labels = array(
				'name'              => _x( 'Categories', 'taxonomy general name', 'echo-knowledge-base' ),
				'singular_name'     => _x( 'Category', 'taxonomy singular name', 'echo-knowledge-base' ),
				'search_items'      => __( 'Search Categories', 'echo-knowledge-base' ),
				'all_items'         => __( 'All Categories', 'echo-knowledge-base' ),
				'parent_item'       => __( 'Parent Category', 'echo-knowledge-base' ),
				'parent_item_colon' => __( 'Parent Category:', 'echo-knowledge-base' ),
				'edit_item'         => __( 'Edit Category', 'echo-knowledge-base' ),
				'update_item'       => __( 'Update Category', 'echo-knowledge-base' ),
				'add_new_item'      => __( 'Add New Category', 'echo-knowledge-base' ),
				'new_item_name'     => __( 'New Category Name', 'echo-knowledge-base' ),
				'menu_name'         => __( 'Categories', 'echo-knowledge-base' ),
		);
		$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => $show_post_in_ui,
				'show_admin_column' => $show_post_in_ui,
				'show_in_nav_menus' => $show_post_in_ui,
				'query_var'         => $taxonomy_name,
				'show_in_rest'      => true,
				'rewrite'           => array(
											/* translators: do NOT change this translation again. It will break links !!! */
											'slug'         => $kb_articles_common_path . '/' . _x( 'category', 'taxonomy singular name', 'echo-knowledge-base' ),  // TODO FUTURE overwrite with config
											'with_front'   => false,
											'hierarchical' => true
										),
		);
		$result = register_taxonomy( $taxonomy_name, array( $kb_post_type ), $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** setup Tag taxonomy */
		$tag_name = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );
		$labels = array(
				'name'                       => _x( 'Tags', 'taxonomy general name', 'echo-knowledge-base' ),
				'singular_name'              => _x( 'Tag', 'taxonomy singular name', 'echo-knowledge-base' ),
				'search_items'               => __( 'Search Tags', 'echo-knowledge-base' ),
				'all_items'                  => __( 'All Tags', 'echo-knowledge-base' ),
				'parent_item'                => __( 'Parent Tag', 'echo-knowledge-base' ),
				'parent_item_colon'          => __( 'Parent Tag:', 'echo-knowledge-base' ),
				'edit_item'                  => __( 'Edit Tag', 'echo-knowledge-base' ),
				'update_item'                => __( 'Update Tag', 'echo-knowledge-base' ),
				'view_item'                  => __( 'View Tag', 'echo-knowledge-base' ),
				'separate_items_with_commas' => __( 'Separate Tags with commas', 'echo-knowledge-base' ),
				'add_or_remove_items'        => __( 'Add or remove Tags', 'echo-knowledge-base' ),
				'add_new_item'               => __( 'Add New Tag', 'echo-knowledge-base' ),
				'new_item_name'              => __( 'New Tag Name', 'echo-knowledge-base' ),
				'menu_name'                  => __( 'Tags', 'echo-knowledge-base' )
		);
		$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => $show_post_in_ui,
				'show_admin_column'     => $show_post_in_ui,
				'show_in_nav_menus'     => $show_post_in_ui,
				'show_tagcloud'         => true,
				'query_var'             => $tag_name,
				'update_count_callback' => '_update_post_term_count',
				'show_in_rest'          => true,
				'rewrite'               => array(
												/* translators: do NOT change this translation again. It will break links !!! */
												'slug'         => $kb_articles_common_path . '/' . _x( 'tag', 'taxonomy singular name', 'echo-knowledge-base' ),  // TODO FUTURE override with config
												'with_front'   => false,
												'hierarchical' => false
											),
		);
		$result = register_taxonomy( $tag_name, array( $kb_post_type ), $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** setup Custom Post Type */
		$post_type_name = _x( $kb_config['kb_name'], 'post type general name', 'echo-knowledge-base' );
		$post_type_name = empty($post_type_name) ? 'Knowledge Base' : $post_type_name;
		$labels = array(
				'name'               => $post_type_name,
				'singular_name'      => $post_type_name . ' - ' . _x( 'Article', 'post type singular name', 'echo-knowledge-base' ),
				'add_new'            => _x( 'Add New Article', 'Articles', 'echo-knowledge-base' ),
				'add_new_item'       => __( 'Add New Article', 'echo-knowledge-base' ),
				'edit_item'          => __( 'Edit Article', 'echo-knowledge-base' ),
				'new_item'           => __( 'New Article', 'echo-knowledge-base' ),
				'all_items'          => __( 'All Articles', 'echo-knowledge-base' ),
				'view_item'          => __( 'View Article', 'echo-knowledge-base' ),
				'search_items'       => __( 'Search in Articles', 'echo-knowledge-base' ),
				'not_found'          => __( 'No Articles found', 'echo-knowledge-base' ),
				'not_found_in_trash' => __( 'No Articles found in Trash', 'echo-knowledge-base' ),
				'parent_item_colon'  => '',
				'menu_name'          => _x( 'Knowledge Base', 'admin menu', 'echo-knowledge-base' )
		);
		$args = array(
				'labels'             => $labels,
				'public'             => true,
				'show_ui'            => true,
				'show_in_menu'       => $show_post_in_ui,
				'publicly_queryable' => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $kb_articles_common_path, 'with_front' => false ),
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'has_archive'        => false,
				'hierarchical'       => false,
				'show_in_rest'       => true,
				'menu_position'      => 5,    // below Posts menu
				'menu_icon'          => 'dashicons-welcome-learn-more',
				'supports'           => array(
											'title',
											'editor',
											'thumbnail',
											'excerpt',
											'revisions',
											'author',
											'comments'
				),
		);
		$result = register_post_type( $kb_post_type, $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** tie taxonomies to the post type */

		$result = register_taxonomy_for_object_type( $taxonomy_name, $kb_post_type );
		if ( ! $result ) {
			return new WP_Error( 'register_object_for_tax_failed', "Failed to register taxonomy '$taxonomy_name' for post type '$kb_post_type' for KB ID: $kb_id" );
		}

		$result = register_taxonomy_for_object_type( $tag_name, $kb_post_type );
		if ( ! $result ) {
			return new WP_Error( 'register_object_for_tax_failed', "Failed to register taxonomy '$tag_name' for post type '$kb_post_type' for KB ID: $kb_id" );
		}

		return true;
	}

	/**
	 *  Filters the category or list of categories.
	 *
	 * @param array  $thelist   List of categories for the current post.
	 * @param string $separator Separator used between the categories.
	 * @param string $parents   How to display the category parents. Accepts 'multiple',
	 *                          'single', or empty.
	 *
	 * @return mixed
	 */
	public function output_article_categories( $thelist, $separator=', ', $parents='' ) {
		/** @var $wp_rewrite WP_Rewrite */
		global $wp_rewrite;

		// for some strange reason the same hook has only 1 parameter in wp-admin
		$post = isset($GLOBALS['post']) ? $GLOBALS['post'] : '';
		if ( defined('WP_ADMIN') || empty($post) || ! $post instanceof WP_Post || ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return $thelist;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( is_wp_error($kb_id) ) {
			return $thelist;  // shouldn't happen because of is_kb_post_type() above
		}

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';
		$separator = ! empty($separator) && is_string($separator) ? $separator : ', ';

		// find all categories of this article including subcategories
		$ix = 0;
		foreach ( $articles_seq_data as $category_id => $sub_category_article_list ) {
			if ( isset($articles_seq_data[$category_id][$post->ID]) && isset($articles_seq_data[$category_id][0]) ) {
				$thelist .= ( $ix++ == 0 ? '' : $separator ) . '<a href="' . esc_url( get_category_link( $category_id ) ) . '" ' . $rel . '>' . $articles_seq_data[$category_id][0] . '</a>';
			}
		}

		return $thelist;
	}
}

