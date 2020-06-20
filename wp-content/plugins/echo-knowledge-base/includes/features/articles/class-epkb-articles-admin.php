<?php

/**
 * Setup hookds for KB Articles
 */
class EPKB_Articles_Admin {

	const KB_ARTICLES_SEQ_META =  'epkb_articles_sequence';

	public function __construct() {

		// refresh article order when on All Articles page
		add_action( 'restrict_manage_posts', array( $this, 'update_articles_sequence_all_articles' ), 10, 2 );

		// handle post status change
		add_action( 'trashed_post', array( $this, 'update_articles_sequence_article_state_change' ) );
		add_action( 'untrashed_post', array( $this, 'update_articles_sequence_article_state_change' ) );
		add_action( 'deleted_post', array( $this, 'update_articles_sequence_article_state_change' ) );

		// article gets updated its categories
		add_action( 'set_object_terms', array( $this, 'update_articles_sequence_article_categories_changed' ), 10, 6 );

		// Classic Editor: post saved (cache cleared) so update article sequence
		add_action( 'save_post', array( $this, 'update_articles_sequence_article_post_saved' ), 10, 2 );

		// Post from Pending Review to Publish need to refresh post categories
		add_action( 'pending_to_publish', array( $this, 'update_articles_sequence_article_pending_to_publish' ) );
	}

	/**
	 * Article is saved. Check if article title has changed.
	 * @param $post_id
	 * @param $post
	 */
	public function update_articles_sequence_article_post_saved( $post_id, $post ) {

		if ( empty($post->post_type) || ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) || empty($post->post_status) || $post->post_status == 'auto-draft' ) {
			return;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( is_wp_error( $kb_id) ) {
			return;
		}

		$post_title = empty($post->post_title) ? '' : $post->post_title;

		$article_seq_data = EPKB_Utilities::get_kb_option( $kb_id, self::KB_ARTICLES_SEQ_META, null, true );
		if ( empty($article_seq_data) ) {
			return;
		}

		$article_title_changed = false;
		foreach( $article_seq_data as $category_id => $articles_array ) {

			$ix = 0;
			foreach( $articles_array as $article_id => $article_title ) {
				if ( $ix ++ < 2 ) {
					continue;
				}
				if ( $article_id == $post_id && $article_title != $post_title ) {
					$article_seq_data[$category_id][$article_id] = $post_title;
					$article_title_changed = true;
				}
			}
		}

		if ( $article_title_changed ) {
			EPKB_Utilities::save_kb_option( $kb_id, self::KB_ARTICLES_SEQ_META, $article_seq_data, true );
		}
	}

	/**
	 * When article permanently deleted, update article sequence.
	 * @param $post_id
	 */
	public function update_articles_sequence_article_state_change( $post_id ) {

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty($kb_id) ) {
			return;
		}

		$this->update_articles_sequence( $kb_id );
	}

	/**
	 * Refresh article sequence on All Articles page.
	 *
	 * @param $post_type
	 * @param $which
	 * @return bool
	 */
	public function update_articles_sequence_all_articles($post_type, $which) {

		if ( empty($post_type) || ! EPKB_KB_Handler::is_kb_post_type( $post_type ) ) {
			return false;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post_type );
		if ( is_wp_error( $kb_id ) ) {
			return false;
		}

		return $this->update_articles_sequence( $kb_id );
	}

	/**
	 * When a post is updated and terms recounted then update article sequence. Does not work for Classic editor.
	 *
	 * @param int    $post_id    Post ID.
	 * @param array  $terms      An array of object terms.
	 * @param array  $tt_ids     An array of term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy slug.
	 * @param bool   $append     Whether to append new terms to the old terms.
	 * @param array  $old_tt_ids Old array of term taxonomy IDs.
	 */
	public function update_articles_sequence_article_categories_changed( $post_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {

		if ( ! EPKB_KB_Handler::is_kb_taxonomy( $taxonomy) || empty($post_id) || EPKB_Utilities::post('action', '', false) == 'em'.'kb_add_knowledge_base') {
			return;
		}

		// ignore autosave/revision which is not article submission; same with ajax and bulk edit
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_category_taxonomy_name( $taxonomy );
		if ( is_wp_error( $kb_id ) ) {
			return;
		}

		// Classic editor saving has still draft title in the cache so remove it
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			clean_post_cache( $post_id );
		}

		$this->update_articles_sequence( $kb_id );
	}

	public function update_articles_sequence_article_pending_to_publish( $post ) {

		if ( empty($post->post_type) || ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( is_wp_error( $kb_id) ) {
			return;
		}

		$this->update_articles_sequence( $kb_id );
	}

	/**
	 * Update article sequence based on latest DB data.
	 *
	 * @param $kb_id
	 * @return bool
	 */
	public function update_articles_sequence( $kb_id ) {

		// 1. get stored sequence of articles
		$article_order_method = epkb_get_instance()->kb_config_obj->get_value( 'articles_display_sequence', $kb_id );

		// 2. get all term ids  ( do not use WP function get_terms() to avoid recursions or unhook actions )
		$all_kb_terms = EPKB_Utilities::get_kb_categories_unfiltered( $kb_id );
		if ( $all_kb_terms === null ) {
			return false;
		}

		// 3. FOR EACH CATEGORY:
		$new_stored_ids = array();
		$db = new EPKB_Articles_DB();
		foreach( $all_kb_terms as $term ) {

			// 3. setup sequence of articles within this category
			if ( $article_order_method == 'created-date' ) {
				$articles = $db->get_published_articles_by_sub_or_category( $kb_id, $term->term_id );
			} else {  // for 'user-sequenced' use default for now otherwise default is 'alphabetical-title'
				$articles = $db->get_published_articles_by_sub_or_category( $kb_id, $term->term_id, 'title' );  
			}

			// 4. add article sequence to the configuration
			$new_article_sequence = EPKB_Articles_Array::retrieve_article_sequence( $articles );
			$new_stored_ids[$term->term_id] = array( '0' => $term->name, '1' => $term->description);
			foreach( $new_article_sequence as $article_id => $article_title ) {
				$new_stored_ids[$term->term_id] += array( $article_id => $article_title );
			}
		}

		// 4. stored new configuration
		$new_article_ids_obj = new EPKB_Articles_Array( $new_stored_ids ); // normalizes and sanitizes the array as well

		// for custom sequenced update the custom sequence with changes
		if ( $article_order_method == 'user-sequenced' ) {
			$orig_sequence = $this->get_orig_custom_sequence( $kb_id );
			if ( $orig_sequence === false ) {
				return false;
			}
			$config = new EPKB_KB_Config_Sequence();
			$new_article_ids_obj = $config->update_articles_order( $kb_id, $orig_sequence, $new_article_ids_obj );
			if ( $new_article_ids_obj === false ) {
				return false;
			}
		}

		EPKB_Utilities::save_kb_option( $kb_id, self::KB_ARTICLES_SEQ_META, $new_article_ids_obj->ids_array, true );

		return true;
	}

	/**
	 * Update custom order with changed articles/categories
	 *
	 * @param $kb_id
	 * @return array|false on error
	 */
	public function get_orig_custom_sequence( $kb_id ) {

		$stored_articles_ids = EPKB_Utilities::get_kb_option( $kb_id, self::KB_ARTICLES_SEQ_META, null, true );
		if ( $stored_articles_ids === null ) {
			return false;
		}

		$custom_sequence = array();
		foreach( $stored_articles_ids as $category_id => $articles_array ) {

			$custom_sequence[] = array( $category_id, 'category' );

			$ix = 0;
			foreach( $articles_array as $article_id => $article_title ) {
				if ( $ix ++ < 2 ) {
					continue;
				}
				$custom_sequence[] = array( $article_id, 'article' );
			}
		}

		return $custom_sequence;
	}

	/**
	 * Retrieve non-custom sequence of articles e.g. based on date or title
	 *
	 * @param $kb_id
	 * @param $article_order_method
	 * @return array|false on error
	 */
	public function get_articles_sequence_non_custom( $kb_id, $article_order_method ) {

		// 1. get all term ids  ( do not use WP function get_terms() to avoid recursions or unhook actions )
		$all_kb_terms = EPKB_Utilities::get_kb_categories_unfiltered( $kb_id );
		if ( $all_kb_terms === null ) {
			return false;
		}

		// 3. FOR EACH CATEGORY:
		$new_stored_ids = array();
		$db = new EPKB_Articles_DB();
		foreach( $all_kb_terms as $term ) {

			// 3. setup sequence of articles within this category
			if ( $article_order_method == 'created-date' ) {
				$articles = $db->get_published_articles_by_sub_or_category( $kb_id, $term->term_id );
			} else {  // for 'user-sequenced' use default for now otherwise default is 'alphabetical-title'
				$articles = $db->get_published_articles_by_sub_or_category( $kb_id, $term->term_id, 'title' );
			}

			// 4. add article sequence to the configuration
			$new_article_sequence = EPKB_Articles_Array::retrieve_article_sequence( $articles );
			$new_stored_ids[$term->term_id] = array( '0' => $term->name, '1' => $term->description);
			foreach( $new_article_sequence as $article_id => $article_title ) {
				$new_stored_ids[$term->term_id] += array( $article_id => $article_title );
			}
		}

		// 5. stored new configuration
		$new_article_ids_obj = new EPKB_Articles_Array( $new_stored_ids ); // normalizes and sanitizes the array as well

		return $new_article_ids_obj->ids_array;
	}
}
