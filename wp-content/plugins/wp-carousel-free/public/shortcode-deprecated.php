<?php
/**
 * Registering shortcode.
 *
 * @package WP Carousel
 */

if ( ! function_exists( 'wp_carousel_free_shortcode' ) ) {

	/**
	 * Shortcode main function.
	 *
	 * @param mixed $attr The attributes of the shortcode.
	 * @return statement
	 */
	function wp_carousel_free_shortcode( $attr ) {
		$post = get_post();

		static $instance = 0;
		$instance ++;

		if ( ! empty( $attr['ids'] ) ) {
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		$output = apply_filters( 'sp_wcfgallery_shortcode', '', $attr );
		if ( '' != $output ) {
			return $output;
		}

		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		extract(
			shortcode_atts(
				array(
					'ids'                 => '',
					'items'               => '5',
					'items_desktop'       => '4',
					'items_desktop_small' => '3',
					'items_tablet'        => '2',
					'items_mobile'        => '1',
					'bullets'             => 'false',
					'bullets_mobile'      => 'false',
					'nav'                 => 'true',
					'nav_mobile'          => 'true',
					'auto_play'           => 'true',
					'autoplay_speed'      => '3000',
					'speed'               => '600',
					'infinite'            => 'true',
					'pause_on_hover'      => 'true',
					'swipe'               => 'true',
					'draggable'           => 'true',
					'size'                => 'medium',
					'include'             => '',
					'exclude'             => '',
				), $attr, 'gallery'
			)
		);

		// helper function to return shortcode regex match on instance occurring on page or post.
		if ( ! function_exists( 'get_match' ) ) {
			/**
			 * Find and match gallery shortcode
			 *
			 * @param mix $regex The regular expression.
			 * @param mix $content The regular expression content.
			 * @param mix $instance The regular expression match.
			 * @return statement
			 */
			function get_match( $regex, $content, $instance ) {
				preg_match_all( $regex, $content, $matches );

				return $matches[1][ $instance ];
			}
		}

		// Extract the shortcode arguments from the $page or $post.
		$shortcode_args = shortcode_parse_atts( get_match( '/\[wcfgallery\s(.*)\]/isU', $post->post_content, $instance - 1 ) );

		// get the ids specified in the shortcode call.
		if ( is_array( $ids ) ) {
			$ids = $shortcode_args['ids'];
		}

		$id      = uniqid();
		$order   = 'DESC';
		$orderby = 'title';

		if ( 'RAND' == $order ) {
			$orderby = 'none';
		}

		if ( ! empty( $ids ) ) {
			$_attachments = get_posts(
				array(
					'include'        => $ids,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $order,
					'orderby'        => $orderby,
				)
			);

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[ $val->ID ] = $_attachments[ $key ];
			}
		} elseif ( ! empty( $exclude ) ) {
			$attachments = get_children(
				array(
					'post_parent'    => $id,
					'exclude'        => $exclude,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $order,
					'orderby'        => $orderby,
				)
			);
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
			}

			return $output;
		}

		$gallery_style = $gallery_div = '';

		// Carousel Configurations.
		wp_enqueue_script( 'wpcf-slick' );
		wp_enqueue_script( 'wpcf-slick-config' );
		$wpcp_slick_options = 'data-slick=\'{ "accessibility": true, "arrows":' . $nav . ', "autoplay":' . $auto_play . ', "autoplaySpeed":' . $autoplay_speed . ', "dots":' . $bullets . ', "infinite":' . $infinite . ', "speed":' . $speed . ', "pauseOnHover":' . $pause_on_hover . ', "slidesToShow":' . $items . ', "responsive":[ { "breakpoint":1200, "settings": { "slidesToShow":' . $items_desktop . ' } }, { "breakpoint":980, "settings":{ "slidesToShow":' . $items_desktop_small . ' } }, { "breakpoint":736, "settings": { "slidesToShow":' . $items_tablet . ' } }, {"breakpoint":480, "settings":{ "slidesToShow":' . $items_mobile . ', "arrows":' . $nav_mobile . ', "dots":' . $bullets_mobile . ' } } ], "rows":1, "swipe":' . $swipe . ', "draggable":' . $draggable . ' }\' ';

		$gallery_div = "	
		<div id='wordpress-carousel-free-$id' class='wpcp-carousel-section wpcp-standard nav-vertical-center' $wpcp_slick_options>";

		$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

		foreach ( $attachments as $attach_id => $attachment ) {
			$wcf_image_url   = wp_get_attachment_image_src( $attach_id, $size, false );
			$wcf_image_title = $attachment->post_title;

			$output .= "
			<div class='wpcp-single-item'>
				<img src='$wcf_image_url[0]' alt='$wcf_image_title' />
			</div>";
		}

		$output .= "
			</div>\n";

		return $output;
	}

	add_shortcode( 'wcfgallery', 'wp_carousel_free_shortcode' );
}
