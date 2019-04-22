<?php
/**
 * LearnDash Posts Listing Abstract Class.
 *
 * @package LearnDash
 * @subpackage admin
 */

if ( ! class_exists( 'Learndash_Admin_Posts_Listing' ) ) {
	/**
	 * Absract for LearnDash Posts Listing Pages.
	 */
	abstract class Learndash_Admin_Posts_Listing {
		/**
		 * Variable to hold the listing post type. This will be set in the sub-classes instances.
		 *
		 * @var array $post_type
		 */
		protected $post_type;

		/**
		 * Array of custom columns to add to the listing.
		 *
		 * @var array $post_type
		 */
		protected $columns = array();

		/**
		 * Array of filter selectors shown at the top of the table listing.
		 *
		 * @var array $filter_selectors Array of filter selectors.
		 */
		protected $post_type_selectors = array();


		/**
		 * Public constructor for class
		 */
		public function __construct() {
			// Hook into the on-load action for our post_type editor.
			add_action( 'load-edit.php', array( $this, 'on_load' ) );
		}

		/**
		 * Call via the WordPress load sequence for admin pages.
		 */
		public function on_load() {
			global $typenow, $post;

			if ( ( empty( $typenow ) ) || ( $typenow !== $this->post_type ) ) {
				return;
			}

			add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'manage_column_headers' ), 50, 1 );
			add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'manage_column_rows' ), 50, 3 );
			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts_action' ), 50, 2 );
			add_filter( 'parse_query', array( $this, 'parse_query_table_filter' ), 50, 1 );
		}

		/**
		 * Display options above table listing to allow filtering.
		 *
		 * @since 2.6.0
		 * @param string $post_type Post Type being displayed.
		 * @param string $location Location of filter displayed. Will normally be 'top'.
		 */
		public function restrict_manage_posts_action( $post_type = '', $location = '' ) {
			global $pagenow, $sfwd_lms;

			if ( ( empty( $post_type ) ) || ( $post_type !== $this->post_type ) ) {
				return;
			}

			if ( ! is_admin() ) {
				return;
			}

			if ( 'edit.php' != $pagenow ) {
				return;
			}

			if ( 'top' != $location ) {
				return;
			}

			if ( ( isset( $_GET['post_status'] ) ) && ( 'trash' === $_GET['post_status'] ) ) {
				return;
			}
			$this->show_early_selectors();
			$this->show_post_type_selectors();
			$this->show_taxonomy_selectors();
			$this->show_late_selectors();
		}

		/**
		 * Add post type column headers.
		 *
		 * @since 2.6.0
		 * @param array $columns Columns array passed from WordPress.
		 * @return array $colums modified array with new columns.
		 */
		public function manage_column_headers( $columns = array() ) {
			if ( ! empty( $this->columns ) ) {
				$columns = array_merge(
					array_slice( $columns, 0, 2 ),
					$this->columns,
					array_slice( $columns, 2 )
				);
			}

			return $columns;
		}

		/**
		 * Function to show selectors before the post_type selectors.
		 */
		protected function show_early_selectors() {}

		/**
		 * Function to show selectors after the taxonomy selectors.
		 */
		protected function show_late_selectors() {}

		/**
		 * Display post type selectors for post type.
		 *
		 * @since 2.6.0
		 */
		protected function show_post_type_selectors() {
			if ( ! empty( $this->post_type_selectors ) ) {
				foreach( $this->post_type_selectors as $selector_slug => $selector_args ) {
					if ( isset( $_GET[ $selector_slug ] ) ) {
						$selector_args['selected'] = esc_attr( $_GET[ $selector_slug ] );
					} else {
						$selector_args['selected'] = 0;
					}

					$this->show_post_type_selector( $selector_args );
				}
			}
		}

		/**
		 * Display taxonomy selectors for post type.
		 *
		 * @since 2.6.0
		 */
		protected function show_taxonomy_selectors() {
			$object_taxonomies = get_object_taxonomies( $this->post_type );
			$object_taxonomies = apply_filters( 'learndash-admin-taxonomy-filters-display', $object_taxonomies, $this->post_type );
			if ( ( ! empty( $object_taxonomies ) ) && ( is_array( $object_taxonomies ) ) ) {
				foreach ( $object_taxonomies as $taxonomy_slug ) {
					if ( isset( $_GET[ $taxonomy_slug ] ) ) {
						$selected = esc_attr( $_GET[ $taxonomy_slug ] );
					} else {
						$selected = false;
					}
					$taxonomy_slug_name = $taxonomy_slug;

					$dropdown_options = array(
						'taxonomy'          => $taxonomy_slug,
						'name'              => $taxonomy_slug_name,
						'show_option_none'  => get_taxonomy( $taxonomy_slug )->labels->all_items,
						'option_none_value' => '',
						'hide_empty'        => 0,
						'hierarchical'      => get_taxonomy( $taxonomy_slug )->hierarchical,
						'show_count'        => 0,
						'orderby'           => 'name',
						'value_field'       => 'slug',
						'selected'          => $selected,
					);

					echo '<label class="screen-reader-text" for="' . esc_attr( $taxonomy_slug ) . '">' . sprintf(
						// translators: placeholder: Taxonomy singular name.
						esc_html_x( 'Filter by %s', 'placeholder: Taxonomy singular name.', 'learndash' ),
						get_taxonomy( $taxonomy_slug )->labels->singular_name
					) . '</label>';
					wp_dropdown_categories( $dropdown_options );
				}
			}
		}

		/**
		 * Utility display function to show a post title with row actions.
		 *
		 * @since 2.6.0
		 * @param integer $post_id Post ID of post to show title of.
		 */
		protected function show_post_link( $post_id = 0 ) {
			$post_link = '';
			if ( ! empty( $post_id ) ) {
				$post_link = '<a href="' . get_edit_post_link( $post_id ) . '">' . get_the_title( $post_id  ) . '</a>';
			}

			echo $post_link;
		}

		/**
		 * Utility function to show the row actions hover links on certain rows.
		 *
		 * @since 2.6.0
		 * @param array   $actions Row actions to display. Must be key => label pairs.
		 * @param boolean $always_visible Flag to have actions always show (true) or show on hover (false).
		 */
		protected function show_row_actions( $actions, $always_visible = false ) {
			$actions_out = '';

			if ( ! empty( $actions ) ) {
				$action_count = count( $actions );
				$i = 0;

				$actions_out .= '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
				foreach ( $actions as $action => $link ) {
					++$i;
					( $i == $action_count ) ? $sep = '' : $sep = ' | ';
					$actions_out .= '<span class="' . $action . '">' . $link . $sep . '</span>';
				}
				$actions_out .= '</div>';

				$actions_out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . esc_html__( 'Show more details' ) . '</span></button>';
			}

			echo $actions_out;
		}

		/**
		 * Shows post type filters above the table listing.
		 * 
		 * @since 2.6.0
		 * @param array $selector_args Array of attributes used to display the filter selector.
		 */
		protected function show_post_type_selector( $selector_args = array() ) {

			$query_args_default = array(
				'post_type'      => '',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			);

			if ( ( ! isset( $selector_args['query_args'] ) ) || ( ! is_array( $selector_args['query_args'] ) ) ) {
				$selector_args['query_args'] = array();
			}

			$selector_args['query_args'] = wp_parse_args( $selector_args['query_args'], $query_args_default );
			$selector_args['query_args'] = apply_filters( 'learndash_show_post_type_selector_filter', $selector_args['query_args'], $this->post_type );
			$query_results = new WP_Query( $selector_args['query_args'] );

			if ( ! empty( $query_results->posts ) ) {
				if ( count( $query_results->posts ) >= $query_results->found_posts ) {
					// If the number of returned posts is equal or greater then found_posts then no need to run lazy load.
					$lazy_load = false;
				}

				if ( true === $lazy_load ) {
					$lazy_load_data = array();
					$lazy_load_data['query_vars'] = $selector_args['query_args'];
					$lazy_load_data['query_type'] = 'WP_Query';
					$lazy_load_data['value']      = $selector_args['selected'];
					$lazy_load_data = ' learndash_lazy_load_data="' . htmlspecialchars( json_encode( $lazy_load_data ) ) . '" ';
				} else {
					$lazy_load_data = '';
				}

				echo '<select ' . $lazy_load_data . ' name="' . $selector_args['field_name'] . '" id="' . $selector_args['field_id'] . '" class="postform">';
				if ( ( isset( $selector_args['show_all_value'] ) ) && ( isset( $selector_args['show_all_label'] ) ) ) {
					$all_selected = '';
					if ( ( isset( $_GET[ $selector_args['field_name'] ] ) ) && ( $selector_args['show_all_value'] === $_GET[ $selector_args['field_name'] ] ) ) {
						$all_selected = ' selected="selected" ';
					}
					echo '<option value="' . $selector_args['show_all_value'] . '" ' . $all_selected . '>' . $selector_args['show_all_label'] . '</option>';
				}
				if ( ( isset( $selector_args['show_empty_value'] ) ) && ( isset( $selector_args['show_empty_label'] ) ) ) {
					$empty_selected = '';
					$field_name = $selector_args['field_name'];
					if ( ( isset( $_GET[ $selector_args['field_name'] ] ) ) && ( $selector_args['show_empty_value'] === $_GET[ $selector_args['field_name'] ] ) ) {
						$empty_selected = ' selected="selected" ';
					} 
					echo '<option value="'. $selector_args['show_empty_value'] .'" ' . $empty_selected . '>' . $selector_args['show_empty_label'] . '</option>';
				}
				foreach ( $query_results->posts as $p ) {
					echo '<option value="' . $p->ID . '" ' . selected( $p->ID, $selector_args['selected'], false ) . '>' . $p->post_title . '</option>';
				}

				echo '</select>';
			} else {
				echo '<select name="' . esc_attr( $selector_args['field_name'] ) . '" id="' . esc_attr( $selector_args['field_id'] ) . '" class="postform">';
				echo '<option value="">' . $selector_args['show_all_label'] . '</option>';
				echo '</select>';			
			}
		}

		/**
		 * This function fill filter the table listing items based on filters selected.
		 * Called via 'parse_query' filter from WP.
		 *
		 * @since 2.6.0
		 * @param object $query WP_Query instance.
		 */
		protected function parse_query_table_filter( $query ) {

		}
		// End of functions.
	}
}
// Incldue the LearnDash table listing files here.
require_once LEARNDASH_LMS_PLUGIN_DIR . 'includes/admin/classes-posts-listings/class-learndash-admin-questions-listing.php';
