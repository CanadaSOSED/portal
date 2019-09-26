<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Query article data in the database
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Articles_DB {

	// TODO NEXT RELEASE wp_cache_get, wp_cache_set etc. and set_transient/get_transient

	/**
	 * Get PUBLISHED articles related to a given category OR sub-category
	 *
	 * @param $kb_id
	 * @param $sub_or_category_id
	 * @param string $order_by
	 * @param int $nof_articles
	 * @param bool $include_children
	 *
	 * @return array of matching articles or empty array
	 */
	function get_published_articles_by_sub_or_category( $kb_id, $sub_or_category_id, $order_by='date', $nof_articles=200, $include_children=false ) {
		/** @var $wpdb Wpdb */
		global $wpdb;
		
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		if ( ! EPKB_Utilities::is_positive_int($sub_or_category_id) ) {
			EPKB_Logging::add_log( 'Invalid category id', $sub_or_category_id );
			return array();
		}

		$order = $order_by == 'title' ? 'ASC' : 'DESC';

		$query_args = array(
			'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status' => 'publish',  // we want only published articles
			'posts_per_page' => $nof_articles,
			'orderby' => $order_by,
			'order'=> $order,
			'tax_query' => array(
				array(
					'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
					'terms' => $sub_or_category_id,
					'include_children' => $include_children
				)
			)
		);

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( ! is_wp_error( $kb_config ) && EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$order_by = $order_by == 'title' ? 'post_title' : 'post_modified';
			$articles = $wpdb->get_results( " SELECT * " .
			                                " FROM $wpdb->posts p " .
			                                " WHERE p.ID in " .
			                                "   (SELECT object_id FROM $wpdb->term_relationships tr INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
			                                "    WHERE tt.term_id = " . $sub_or_category_id . " AND tt.taxonomy = '" . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) . "') " .
			                                "   AND post_type = '" . EPKB_KB_Handler::get_post_type( $kb_id ) . "' AND post_status in ('publish')
		                              ORDER BY " . $order_by . ' ' . $order );  // Get only Published articles
			return $articles;
		}

		return get_posts( $query_args );  /** @secure 02.17 */
	}

	/**
	 * Retrieve all KB articles but do not count articles in Trash
	 *
	 * @param $kb_id
	 *
	 * @return number of all posts
	 */
	static function get_count_of_all_kb_articles( $kb_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$kb_id = EPKB_Utilities::sanitize_int( $kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// parameters sanitized
		$posts = $wpdb->get_results( " SELECT * " .
									 " FROM $wpdb->posts " . /** @secure 02.17 */
		                             " WHERE post_type = '" . EPKB_KB_Handler::get_post_type( $kb_id ) . "' AND post_status in ('publish') ");
		if ( empty( $posts ) || ! is_array( $posts ) ) {
			return 0;
		}

		return empty( $posts ) ? 0 : count( $posts );
	}

	/**
	 * Retrieve all PUBLISHED articles that do not have either category or subcategory
	 *
	 * @param $kb_id
	 *
	 * @return array of posts
	 */
	function get_orphan_published_articles( $kb_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// sanitize KB ID
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		// parameters sanitized
		$posts = $wpdb->get_results( "SELECT * FROM " .
		                             "   $wpdb->posts p LEFT JOIN " .  /** @secure 02.17 */
	                                 "   (SELECT object_id FROM $wpdb->term_relationships tr INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
		                                        " WHERE tt.taxonomy = '" . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) . "') AS ta " .
		                             "ON ta.object_id = p.ID " .
		                             "WHERE post_type = '" . EPKB_KB_Handler::get_post_type( $kb_id ) . "' AND object_id IS NULL AND post_status in ('publish') ");  // Get only Published articles

		if ( empty( $posts ) || ! is_array( $posts ) ) {
			return array();
		}

		return $posts;
	}
}