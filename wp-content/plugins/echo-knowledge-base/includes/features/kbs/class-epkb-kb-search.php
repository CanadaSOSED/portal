<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Search Knowledge Base
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Search {

	public function __construct() {
		add_action( 'wp_ajax_epkb-search-kb', array($this, 'search_kb') );
		add_action( 'wp_ajax_nopriv_epkb-search-kb', array($this, 'search_kb') );
	}

	/**
	 * Process AJAX search request
	 */
	public function search_kb() {

		// we don't need nonce and permission check here

		$kb_id = EPKB_Utilities::sanitize_get_id( $_GET['epkb_kb_id'] );
		if ( is_wp_error( $kb_id ) ) {
			wp_die( json_encode( array( 'status' => 'success', 'search_result' => esc_html__( 'Error occurred. Please try again later.', 'echo-knowledge-base' ) ) ) );
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		// remove question marks
		$search_terms = EPKB_Utilities::get( 'search_words' );
		$search_terms = stripslashes( $search_terms );
		$search_terms = str_replace('?', '', $search_terms);
		$search_terms = str_replace( array( "\r", "\n" ), '', $search_terms );

		// require minimum size of search word(s)
		if ( empty($search_terms) ) {
			wp_die( json_encode( array( 'status' => 'success', 'search_result' => esc_html( $kb_config['min_search_word_size_msg'] ) ) ) );
		}

		// search for given keyword(s)
		$result = $this->execute_search( $kb_id, $search_terms  );

		if ( empty($result) ) {
			$search_result = $kb_config['no_results_found'];

		} else {
			// ensure that links have https if the current schema is https
			set_current_screen('front');

			$search_result = '<h3>' . esc_html( $kb_config['search_results_msg'] ) . ' ' . $search_terms . '</h3>';
			$search_result .= '<ul>';

			$title_style = '';
			$icon_style  = '';
			if ( $kb_config['search_box_results_style'] == 'on' ) {
				$title_style = EPKB_Utilities::get_inline_style( 'color:: article_font_color' , $kb_config);
				$icon_style = EPKB_Utilities::get_inline_style( 'color:: article_icon_color' , $kb_config);
			}

			// display one line for each search result
			foreach( $result as $post ) {

				$article_url = get_permalink( $post->ID );
				if ( empty($article_url) || is_wp_error( $article_url )) {
					continue;
				}

				// linked articles have their own icon
				$article_title_icon = 'ep_font_icon_document';
				if ( has_filter( 'eckb_single_article_filter' ) ) {
					$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $post->ID );
					$article_title_icon = empty( $article_title_icon ) ? 'epkbfa-file-text-o' : $article_title_icon;
				}

				// linked articles have open in new tab option
				$new_tab = '';
				if ( class_exists( 'KBLK_Utilities' ) ) {
					$link_editor_config = KBLK_Utilities::get_postmeta( $post->ID, 'kblk-link-editor-data', [], true );
					$new_tab            = empty( $link_editor_config['open-new-tab'] ) ? '' : 'target="_blank"';
				}

				$search_result .=
					'<li>' .
						'<a href="' .  esc_url( $article_url ) . '" ' . $new_tab . ' class="epkb-ajax-search" data-kb-article-id="' . $post->ID . '">' .
							'<span class="eckb-article-title" ' . $title_style . '>' .
	                            '<i class="eckb-article-title-icon epkbfa ' . esc_attr($article_title_icon) . ' ' . $icon_style . '"></i>' .
								'<span>' . esc_html($post->post_title) . '</span>' .
							'</span>' .
						'</a>' .
					'</li>';
			}
			$search_result .= '</ul>';
		}

		// we are done here
		wp_die( json_encode( array( 'status' => 'success', 'search_result' => $search_result ) ) );
	}

	/**
	 * Call WP query to get matching terms (any term OR match)
	 *
	 * @param $kb_id
	 * @param $search_terms
	 * @return array
	 */
	private function execute_search( $kb_id, $search_terms ) {

		// add-ons can adjust the search
		if ( has_filter( 'eckb_execute_search_filter' ) ) {
			$result = apply_filters('eckb_execute_search_filter', '', $kb_id, $search_terms );
			if ( is_array($result) ) {
				return $result;
			}
		}

		$result = array();
		$search_params = array(
				's' => $search_terms,
				'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
				'post_status' => 'publish',      /** only PUBLISHED results */
				'ignore_sticky_posts' => true,  // sticky posts will not show at the top
				'posts_per_page' => 20,         // limit search results
				'no_found_rows' => true,        // query only posts_per_page rather than finding total nof posts for pagination etc.
				'cache_results' => false,       // don't need that for mostly unique searches
				'orderby' => 'relevance'
		);

		$found_posts_obj = new WP_Query( $search_params );
		if ( ! empty($found_posts_obj->posts) ) {
			$result = $found_posts_obj->posts;
			wp_reset_postdata();
		}

		return $result;
	}
}
