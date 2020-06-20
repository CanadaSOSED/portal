<?php
/**
 * The image carousel template.
 *
 * @package WP_Carousel_Free
 * @subpackage WP_Carousel_Free/public/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$number_of_total_posts = ( isset( $upload_data['number_of_total_posts'] ) ? $upload_data['number_of_total_posts'] : '' );

$show_post_content = $shortcode_data['wpcp_post_content_show'];
$show_post_date    = $shortcode_data['wpcp_post_date_show'];
$show_post_author  = $shortcode_data['wpcp_post_author_show'];
	$args          = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'order'               => $post_order,
		'orderby'             => $post_order_by,
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $number_of_total_posts,
	);

	// Carousel Wrapper Start.
	echo '<div class="wpcp-carousel-wrapper wpcp-wrapper-' . $post_id . '">';
	if ( $section_title ) {
		echo '<h2 class="sp-wpcpro-section-title">' . get_the_title( $post_id ) . '</h2>';
	}
	if ( $preloader ) {
		require WPCAROUSELF_PATH . '/public/templates/preloader.php';
	}
	echo '<div id="sp-wp-carousel-free-id-' . $post_id . '" class="' . $carousel_classes . '" ' . $wpcp_slick_options . ' dir="ltr">';
	$post_query = new WP_Query( $args );
	if ( $post_query->have_posts() ) {
		while ( $post_query->have_posts() ) :
			$post_query->the_post();
			$image = '';
			if ( has_post_thumbnail( $post_query->post->ID ) && $show_slide_image ) {
				$image_id             = get_post_thumbnail_id();
				$image_url            = wp_get_attachment_image_src( $image_id, $image_sizes );
				$image_alt_text       = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				$the_image_title_attr = ' title="' . the_title_attribute( 'echo=0' ) . '"';
				$image_title_attr     = ( 'true' === $show_image_title_attr ) ? $the_image_title_attr : '';

				$post_thumb = sprintf( '<img class="wpcp-post-thumb" src="%1$s"%2$s alt="%3$s" width="%4$s" height="%5$s">', $image_url[0], $image_title_attr, $image_alt_text, $image_url[1], $image_url[2] );

				$image = sprintf( '<div class="wpcp-slide-image"><a href="%2$s">%1$s</a></div>', $post_thumb, get_the_permalink() );

			} // End of Has post thumbnail.

			// Post Title.
			$wpcp_title           = sprintf( '<h2 class="wpcp-post-title"><a href="%1$s">%2$s</a></h2>', get_the_permalink(), get_the_title() );
			$wpcp_post_title = ( $show_img_title && ! empty( get_the_title() ) ) ? $wpcp_title : '';

			// The Post Author.
			$the_post_author_name = sprintf( '<li><a href="%1$s">%2$s%3$s</a></li>', get_author_posts_url( get_the_author_meta( 'ID' ) ), __( ' By ', 'wp-carousel-free' ), get_the_author() );
			$post_author_name     = $show_post_author ? $the_post_author_name : '';

			// The Post Date.
			$post_update_date = sprintf( '<time class="updated wpcp-hidden" datetime="%1$s">%2$s</time>', get_the_modified_date( 'c' ), get_the_modified_date() );
			$wpcp_post_date   = sprintf( '<li><time class="entry-date published updated" datetime="%1$s">%2$s%3$s</time></li>', get_the_date( 'c' ), __( 'On ', 'wp-carousel-free' ), get_the_date() );
			$post_date        = $show_post_date ? $wpcp_post_date : '';

			// The Post Meta.
			$wpcp_post_meta = '';
			if ( $show_post_date || $show_post_author ) {
				$wpcp_post_meta = sprintf( '<ul class="wpcp-post-meta">%1$s%2$s</ul>', $post_author_name, $post_date );
			}

			// Post Content.
			$wpcp_post_content = sprintf( '<p>%1$s</p>', get_the_excerpt() );

				$all_captions = '';
			if ( $show_img_title || $show_post_content || ! empty( $wpcp_post_meta ) ) {
				$all_captions = '<div class="wpcp-all-captions">' . $wpcp_post_title . ( $show_post_content ? $wpcp_post_content : '' ) . $wpcp_post_meta . '</div>';
			}

			if ( $image || $all_captions ) {
				echo '<div class="wpcp-single-item">';
				echo $image . $all_captions;
				echo '</div>';
			}
		endwhile;
		wp_reset_postdata();
	} else {
		echo '<h2 class="wpcp-no-post-found" >' . esc_html__( 'No posts found', 'wp-carousel-free' ) . '</h2>';
	}
	echo '</div>';
	echo '</div>'; // Carousel Wrapper.
