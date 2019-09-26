<?php

/**
 * Additional helper functions used by templates
 */
class EPKB_Templates_Various {

	/**
	 * BREADCRUMB: get given article breadccrumb categories
	 *
	 * @param $kb_config
	 * @param $article_id
	 * @return array
	 */
	public static function get_article_breadcrumb( $kb_config, $article_id ) {

		$kb_id = $kb_config['id'];

		if ( isset($kb_config[EPKB_Articles_Admin::KB_ARTICLES_SEQ_META]) && isset($kb_config[EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META]) ) {
			$articles_seq_data = $kb_config[EPKB_Articles_Admin::KB_ARTICLES_SEQ_META];
			$category_seq_data = $kb_config[EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META];
		} else {
			$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
			$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		}

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
			$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
		}

		$seq_no = EPKB_Utilities::get('seq_no', 1, false);
		$seq_no = EPKB_Utilities::sanitize_int( $seq_no );
		$seq_cnt = 0;
		$first_instance = array();

		// find it on the first level
		foreach( $category_seq_data as $category_id => $sub_categories ) {

			if ( empty($articles_seq_data[$category_id][0]) ) {
				continue;
			}

			if ( isset($articles_seq_data[$category_id][$article_id]) ) {
				$result = array($category_id => $articles_seq_data[$category_id][0]);
				if ( ++$seq_cnt >= $seq_no ) {
					return $result;
				}
				$first_instance = empty($first_instance) ? $result : $first_instance;
			}

			// find it on the second level
			foreach( $sub_categories as $sub_category_id => $sub_sub_categories ) {

				if ( empty($articles_seq_data[$sub_category_id][0]) ) {
					continue;
				}

				if ( isset($articles_seq_data[$sub_category_id][$article_id]) ) {
					$result = array($category_id => $articles_seq_data[$category_id][0],
					                $sub_category_id => $articles_seq_data[$sub_category_id][0]);
					if ( ++$seq_cnt >= $seq_no ) {
						return $result;
					}
					$first_instance = empty($first_instance) ? $result : $first_instance;
				}

				// find it on the third level
				foreach( $sub_sub_categories as $sub_sub_category_id => $sub_sub_sub_categories ) {

					if ( empty($articles_seq_data[$sub_sub_category_id][0]) ) {
						continue;
					}

					if ( isset($articles_seq_data[$sub_sub_category_id][$article_id]) ) {
						$result = array($category_id => $articles_seq_data[$category_id][0],
						                $sub_category_id => $articles_seq_data[$sub_category_id][0],
						                $sub_sub_category_id => $articles_seq_data[$sub_sub_category_id][0]);
						if ( ++$seq_cnt >= $seq_no ) {
							return $result;
						}
						$first_instance = empty($first_instance) ? $result : $first_instance;
					}
				}
			}
		}

		return $first_instance;
	}
}