<?php 
/**
 * Create Breadcrumb Navigation
 *
 * @package sos-knowledge-base
 */


add_action( 'widgets_init', 'register_widget_cat_related_posts' );

function register_widget_cat_related_posts() {

	register_widget( 'widget_cat_related_posts' );

}

class widget_cat_related_posts extends WP_Widget {

	public function __construct() {
	
		parent::__construct(
	
			'widget_cat_related_posts',
			__( 'Related Posts by Category', 'related-posts-by-category-widget' ),
			array(
				'classname'   => 'widget_cat_related_posts widget_related_entries',
				'description' => __( 'Display related blog posts from a specific category', 'related-posts-by-category-widget' )
			)
	
		);
	
	}


	// Build the widget settings form

	function form( $instance ) {
	
		$defaults  = array( 'title' => '', 'number' => 5, );
		$instance  = wp_parse_args( ( array ) $instance, $defaults );
		$title     = $instance['title'];
		$number    = $instance['number'];

		
		?>
			
<!-- 			<p>
				<label for="widget_cat_related_posts_title"><?php _e( 'Title' ); ?>:</label>
				<input type="text" class="widefat" id="widget_cat_related_posts_title" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p> -->
			
			<p>
				<label for="widget_cat_related_posts_number"><?php _e( 'Number of posts to show' ); ?>: </label>
				<input type="text" id="widget_cat_related_posts_number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $number ); ?>" size="3" />
			</p>
			
			<?php
		
		}		


		// Save widget settings

		function update( $new_instance, $old_instance ) {

			$instance              = $old_instance;
			$instance['title']     = wp_strip_all_tags( $new_instance['title'] );
			$instance['number']    = is_numeric( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 5;

			return $instance;

		}


		// Display widget

		function widget( $instance, $post_id ) {

			echo $before_widget;

			// Setup them vars
			$title     		= $instance['title'];
			$number    		= $instance['number'];
			$post 			= get_post( $post_id );
			$taxonomies 	= get_object_taxonomies( $post );

			foreach( $taxonomies as $taxonomy ) {
				
				$terms = get_the_terms( $post_id, $taxonomy );
				
				if ( empty( $terms ) ) continue;
				
				$term_list = wp_list_pluck( $terms, 'slug' );
				
				$related_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $term_list
				);
			}

			if( count( $related_args['tax_query'] ) > 1 ) {
				$related_args['tax_query']['relation'] = 'OR';
			}

			// Create custom Query
			$cat_related_posts = new WP_Query( array( 
				
				'post_type' => get_post_type( $post_id ),
				'posts_per_page' => $related_count,
				'post_status' => 'publish',
				'post__not_in' => array( $post_id ),
				'orderby' => 'rand',
				'tax_query' => array()
				
			));

			if ( $cat_related_posts->have_posts() ) {

				// Display the widget title
				echo '<div id="related_posts">';

				echo '<h3 class="widget-title">Related Posts</h3>';

				// Display posts in list
				echo '<ul class="related-posts-list">';

					while ( $cat_related_posts->have_posts() ) {

						$cat_related_posts->the_post();
					
						echo '<li>';
						
						echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';

						echo '</li>';

					}

				echo '</ul>';

				echo '</div>';
			}

			// Reset the query so it dosen't conflict with any other querries
			wp_reset_query(); 
		}
	}
?>