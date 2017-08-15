<?php
/**
 * Create recent posts by category widget
 *
 * @package sos-chapter
 */

// Register widget

add_action( 'widgets_init', 'register_widget_cat_recent_posts' );

function register_widget_cat_recent_posts() {

	register_widget( 'widget_cat_recent_posts' );

}

class widget_cat_recent_posts extends WP_Widget {

	public function __construct() {
	
		parent::__construct(
	
			'widget_cat_recent_posts',
			__( 'Recent Posts by Category', 'recent-posts-by-category-widget' ),
			array(
				'classname'   => 'widget_cat_recent_posts widget_recent_entries',
				'description' => __( 'Display recent blog posts from a specific category', 'recent-posts-by-category-widget' )
			)
	
		);
	
	}

	// Build the widget settings form

	function form( $instance ) {
	
		$defaults  = array( 'title' => '', 'category' => '', 'number' => 5, 'show_date' => '' );
		$instance  = wp_parse_args( ( array ) $instance, $defaults );
		$title     = $instance['title'];
		$category  = $instance['category'];
		$number    = $instance['number'];
		$show_date = $instance['show_date'];
		
		?>
		
		<p>
			<label for="widget_cat_recent_posts_title"><?php _e( 'Title' ); ?>:</label>
			<input type="text" class="widefat" id="widget_cat_recent_posts_title" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="widget_cat_recent_posts_category"><?php _e( 'Category' ); ?>:</label>				
			
			<?php

			wp_dropdown_categories( array(

				'orderby'    => 'title',
				'hide_empty' => false,
				'name'       => $this->get_field_name( 'category' ),
				'id'         => 'widget_cat_recent_posts_category',
				'class'      => 'widefat',
				'selected'   => $category

			) );

			?>

		</p>
		
		<p>
			<label for="widget_cat_recent_posts_number"><?php _e( 'Number of posts to show' ); ?>: </label>
			<input type="text" id="widget_cat_recent_posts_number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $number ); ?>" size="3" />
		</p>

		<p>
			<input type="checkbox" id="widget_cat_recent_posts_show_date" class="checkbox" name="<?php echo $this->get_field_name( 'show_date' ); ?>" <?php checked( $show_date, 1 ); ?> />
			<label for="widget_cat_recent_posts_show_date"><?php _e( 'Display post date?' ); ?></label>
		</p>
		
		<?php
	
	}

	// Save widget settings

	function update( $new_instance, $old_instance ) {

		$instance              = $old_instance;
		$instance['title']     = wp_strip_all_tags( $new_instance['title'] );
		$instance['category']  = wp_strip_all_tags( $new_instance['category'] );
		$instance['number']    = is_numeric( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 5;
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? 1 : 0;

		return $instance;

	}

	// Display widget

	function widget( $args, $instance ) {

		extract( $args );

		echo $before_widget;

		// Setup them vars
		$title     		= apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$category_id  	= get_cat_id( $instance['title'] );
		$category_link 	= get_category_link( $category_id );
		$number    		= $instance['number'];
		$show_date 		= ( $instance['show_date'] === 1 ) ? true : false;

		// Display category title with a link to category archive page
		if ( !empty( $title ) ) echo $before_title . '<a href="' . $category_link  . '">' . $title . '</a>' . $after_title;

		// Create custom Query
		$cat_recent_posts = new WP_Query( array( 

			'post_type'      => 'post',
			'posts_per_page' => $number,
			'cat'            => $category

		) );

		if ( $cat_recent_posts->have_posts() ) {

			// Display posts in list
			echo '<ul class="recent-posts-list">';

			while ( $cat_recent_posts->have_posts() ) {

				$cat_recent_posts->the_post();

				echo '<li>';
				
				echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
				
					if ( $show_date ) echo '<span class="post-date">' . get_the_time( get_option( 'date_format' ) ) . '</span>';
				
				echo '</li>';

			}

			echo '</ul>';

		} else {

			_e( 'No posts yet.', 'recent-posts-by-category-widget' );

		}

		wp_reset_postdata();

		echo $after_widget;

	}

}

?>