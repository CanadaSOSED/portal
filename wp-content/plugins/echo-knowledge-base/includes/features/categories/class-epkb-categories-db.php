<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Query categories data in the database
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Categories_DB {
	
	/**
	 * Get all categories
	 *
	 * @param $kb_id
	 *
	 * @return array or empty array on error
	 *
	 */
	/* static function get_all_categories( $kb_id ) {

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		$args = array(
			'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), // Support from wp 4.5
			'get' => 'all'
		);
		
		$terms = get_terms( $args );
		if ( is_wp_error( $terms ) ) {
			EPKB_Logging::add_log( 'cannot get terms for kb_id', $kb_id, $terms );
			return array();
		} else if ( empty($terms) || ! is_array($terms) ) {
			return array();
		}

		return array_values($terms);   // rearrange array keys
	} */

	/**
	 * Get all top-level categories
	 *
	 * @param $kb_id
	 * @param bool $hide_empty
	 * @return array or empty array on error
	 */
	static function get_top_level_categories( $kb_id, $hide_empty=false ) {

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		$args = array(
				'parent'        => '0',
				'hide_empty'    => $hide_empty // whether to return categories without articles
		);
		// Deprecated arguments from wp 4.5
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
	 * @param bool $hide_empty
	 * @return array or empty array on error
	 */
	static function get_child_categories( $kb_id, $parent_id, $hide_empty=false ) {

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
			'hide_empty'    => $hide_empty
		);
		// Deprecated arguments from wp 4.5
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

	/**
	 * Show list of top or sibling KB Categories, each with link to Category Archive page and total article count
	 *
	 * @param $kb_id
	 * @param $kb_config
	 * @param $parent_id
	 * @param $active_id
	 * @return string
	 */
	static function get_layout_categories_list( $kb_id, $kb_config, $parent_id = 0, $active_id = 0 ) {

		$is_demo_data = ! empty($_POST['epkb-wizard-demo-data']);

		// determine what categories will be displayed in the Category Focused Layout list
		if ( $is_demo_data ) {
			$top_categories = EPKB_KB_Demo_Data::get_demo_categories_list();
		} else if ( empty($parent_id) || ( isset( $kb_config['categories_layout_list_mode'] ) && ( $kb_config['categories_layout_list_mode'] == 'list_top_categories' ) ) ) {
			$top_categories = self::get_top_level_categories( $kb_id );
		} else {
			$top_categories = self::get_child_categories( $kb_id, $parent_id );
		}
		
		if ( empty($top_categories) ) {
			return '';
		}

		$article_db = new EPKB_Articles_DB();
		ob_start();                         		?>

		<style>
			.eckb-acll__title {
				color:<?php echo $kb_config['category_box_title_text_color']; ?>;
			}
			.eckb-article-cat-layout-list {
				background-color:<?php echo $kb_config['category_box_container_background_color']; ?>;
				font-size:<?php echo $kb_config['categories_box_font_size']; ?>px;
			}
			.eckb-article-cat-layout-list a {
				font-size:<?php echo $kb_config['categories_box_font_size']; ?>px;
			}
			.eckb-acll__cat-item__name {
				color:<?php echo $kb_config['category_box_category_text_color']; ?>;
			}
			.eckb-acll__cat-item__count {
				color:<?php echo $kb_config['category_box_count_text_color']; ?>;
				background-color:<?php echo $kb_config['category_box_count_background_color']; ?>;
				border:solid 1px <?php echo $kb_config['category_box_count_border_color']; ?>;
			}
		</style>

		<div class="eckb-article-cat-layout-list eckb-article-cat-layout-list-reset">
			<div class="eckb-article-cat-layout-list__inner">
				<div class="eckb-acll__title"><?php echo $kb_config['category_focused_menu_heading_text']; ?></div>
				<ul>						<?php

					// display each category in a list
					foreach( $top_categories as $top_category ) {
						
						if ( $is_demo_data ) {
							$top_category = (object) $top_category;
							$term_link = '#';
							$active = $top_category->active;
							$count = $top_category->count;
						} else {
							$term_link = get_term_link( $top_category, $top_category->taxonomy );
							if ( empty($term_link) || is_wp_error( $term_link ) ) {
								$term_link = '';
							}

							$active = ! empty($active_id) && $active_id == $top_category->term_id;
							
							$count = count($article_db->get_published_articles_by_sub_or_category( $kb_id, $top_category->term_id, 'date', -1, true ) );
						}	?>

						<li class="eckb--acll__cat-item <?php echo $active ? 'eckb--acll__cat-item--active' : ''; ?>">
							<a href="<?php echo $term_link; ?>">
								<div>
									<span class="eckb-acll__cat-item__name">
										<?php echo $top_category->name; ?>
									</span>
								</div>
								<div>
									<span class="eckb-acll__cat-item__count">
										<?php echo $count; ?>
									</span>
								</div>
							</a>
						</li>						<?php
					}	?>

				</ul>
			</div>
		</div>			<?php

		return ob_get_clean();
	}
}