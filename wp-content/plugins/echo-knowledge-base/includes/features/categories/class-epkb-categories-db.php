<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Query categories data in the database
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Categories_DB {

	/**
	 * Get all top-level categories
	 *
	 * @param $kb_id
	 * @param string $hide_choice - if 'hide_empty' then do not return empty categories
	 *
	 * @return array or empty array on error
	 *
	 */
	function get_top_level_categories( $kb_id, $hide_choice='hide_empty' ) {

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		$args = array(
				'parent'        => '0',
				'hide_empty'    => $hide_choice === 'hide_empty' // whether to return categories without articles
		);

		$terms = get_terms( EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), $args );
		if ( is_wp_error( $terms ) ) {
			EPKB_Logging::add_log( 'cannot get terms for kb_id', $kb_id, $terms );
			return array();
		} else if ( empty($terms) || ! is_array($terms) ) {
			return array();
		}

		return array_values($terms);   // rearrange array keys
	}

	/**
	 * Get all categories that belong to given parent
	 *
	 * @param $kb_id
	 * @param int $parent_id is parent category we use to find children
	 * @param string $hide_choice
	 *
	 * @return array or empty array on error
	 */
	function get_child_categories( $kb_id, $parent_id, $hide_choice='hide_empty' ) {

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		if ( ! EPKB_Utilities::is_positive_int( $parent_id ) ) {
			EPKB_Logging::add_log( 'Invalid parent id', $parent_id );
			return array();
		}

		$args = array(
				'child_of'      => $parent_id,
				'parent'        => $parent_id,
				'hide_empty'    => $hide_choice === 'hide_empty'
		);

		$terms = get_terms( EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), $args );
		if ( is_wp_error( $terms ) ) {
			EPKB_Logging::add_log( 'failed to get terms for kb_id: ' . $kb_id . ', parent_id: ' . $parent_id, $terms );
			return array();
		}

		if ( empty( $terms ) || ! is_array( $terms ) ) {
			return array();
		}

		return array_values($terms);
	}
}