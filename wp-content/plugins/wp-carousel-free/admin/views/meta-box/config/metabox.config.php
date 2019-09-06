<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
}
// Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// METABOX OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();

/**
 * Image, Video and Content Metabox.
 */
$options[] = array(
	'id'        => 'sp_wpcp_upload_options',
	'title'     => __( 'Upload Options', 'wp-carousel-free' ),
	'post_type' => 'sp_wp_carousel',
	'context'   => 'normal',
	'priority'  => 'default',
	'sections'  => array(
		// Begin: a section.
		array(
			'name'   => 'sp_wpcp_upload_options_1',
			// 'title'  => __( 'Carousel Content', 'wp-carousel-free' ),
			'icon'   => 'fa fa-file',
			// Begin fields.
			'fields' => array(
				array(
					'id'         => 'wpcp_carousel_type',
					'type'       => 'carousel_type',
					'title'      => __( 'Carousel Type', 'wp-carousel-free' ),
					// 'desc'    => __( 'Select which carousel type you want to display.', 'wp-carousel-free' ),
					'options'    => array(
						'image-carousel'   => array(
							'icon' => 'fa fa-image',
							'text' => __( 'Image', 'wp-carousel-free' ),
							'pro_only' => false,
						),
						'post-carousel'    => array(
							'icon' => 'dashicons dashicons-admin-post',
							'text' => __( 'Post', 'wp-carousel-free' ),
							'pro_only' => false,
						),
						'product-carousel' => array(
							'icon' => 'fa fa-cart-plus',
							'text' => __( 'Product', 'wp-carousel-free' ),
							'pro_only' => false,
						),
						'content-carousel' => array(
							'icon'     => 'fa fa-file-text-o',
							'text'     => __( 'Content', 'wp-carousel-free' ),
							'pro_only' => true,
						),
						'video-carousel'   => array(
							'icon'     => 'fa fa-play-circle-o',
							'text'     => __( 'Video', 'wp-carousel-free' ),
							'pro_only' => true,
						),
					),
					'radio'      => true,
					'default'    => 'image-carousel',
					'attributes' => array(
						'data-depend-id' => 'wpcp_carousel_type',
					),
				),
				array(
					'id'          => 'wpcp_gallery',
					'type'        => 'gallery',
					'title'       => 'Gallery Images',
					'wrap_class'  => 'wpcp-gallery-filed-wrapper',
					'add_title'   => __( 'ADD IMAGE', 'wp-carousel-free' ),
					'edit_title'  => __( 'EDIT IMAGE', 'wp-carousel-free' ),
					'clear_title' => __( 'REMOVE ALL', 'wp-carousel-free' ),
					'dependency'  => array( 'wpcp_carousel_type', '==', 'image-carousel' ),
				),
				array(
					'id'         => 'wpcp_display_posts_from',
					'type'       => 'select',
					'title'      => __( 'Display Posts From', 'wp-carousel-free' ),
					'desc'       => __( 'Select an option to display the posts.', 'wp-carousel-free' ),
					'options'    => array(
						'latest'        => array(
							'text' => __( 'Latest', 'wp-carousel-free' ),
						),
						'taxonomy'      => array(
							'text'     => __( 'Taxonomy (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
						'specific_post' => array(
							'text'     => __( 'Specific Posts (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
					),
					'default'    => 'latest',
					'class'      => 'chosen',
					'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
				),
				array(
					'id'         => 'number_of_total_posts',
					'type'       => 'number',
					'title'      => __( 'Total Posts', 'wp-carousel-free' ),
					'desc'       => __( 'Number of total posts to show. Default value is 10.', 'wp-carousel-free' ),
					'default'    => '10',
					'attributes' => array(
						'min' => 0,
					),
					'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
				),
				// Product Carousel.
				array(
					'id'         => 'wpcp_display_product_from',
					'type'       => 'select',
					'title'      => __( 'Display Product From', 'wp-carousel-free' ),
					'desc'       => __( 'Select an option to display the products.', 'wp-carousel-free' ),
					'options'    => array(
						'latest'        => array(
							'text' => __( 'Latest', 'wp-carousel-free' ),
						),
						'taxonomy'      => array(
							'text'     => __( 'Taxonomy (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
						'specific_post' => array(
							'text'     => __( 'Specific Products (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
					),
					'default'    => 'latest',
					'class'      => 'chosen',
					'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
				),
				array(
					'id'         => 'wpcp_total_products',
					'type'       => 'number',
					'title'      => __( 'Total Products', 'wp-carousel-free' ),
					'desc'       => __( 'Number of total products to display. Default value is 10.', 'wp-carousel-free' ),
					'default'    => '10',
					'attributes' => array(
						'min' => 1,
					),
					'dependency' => array( 'wpcp_display_product_from|wpcp_carousel_type', '!=|==', 'specific_products|product-carousel' ),
				),
			), // End: fields.
		), // End: Upload section.
	),
);
// -----------------------------------------
// Shortcode Generator Options
// -----------------------------------------
$options[] = array(
	'id'        => 'sp_wpcp_shortcode_options',
	'title'     => __( 'Shortcode Options', 'wp-carousel-free' ),
	'post_type' => 'sp_wp_carousel',
	'context'   => 'normal',
	'priority'  => 'default',
	'sections'  => array(
		// Begin: a section.
		array(
			'name'   => 'sp_wcpcp_shortcode_option_1',
			'title'  => __( 'General Settings', 'wp-carousel-free' ),
			'icon'   => 'fa fa-wrench',
			// Begin fields.
			'fields' => array(
				array(
					'id'      => 'section_title',
					'type'    => 'switcher',
					'title'   => __( 'Carousel Section Title', 'wp-carousel-free' ),
					'desc'    => __( 'Show/Hide the carousel section title.', 'wp-carousel-free' ),
					'default' => false,
				),
				array(
					'id'         => 'section_title_margin_bottom',
					'type'       => 'number',
					'title'      => __( 'Carousel Title Margin Bottom', 'wp-carousel-free' ),
					'desc'       => __( 'Set margin bottom for the carousel section title. Default value is 30px.', 'wp-carousel-free' ),
					'after'      => __( '(px)', 'wp-carousel-free' ),
					'default'    => 30,
					'attributes' => array(
						'min' => 0,
					),
					'dependency' => array(
						'section_title',
						'==',
						'true',
					),
				),
				array(
					'id'      => 'wpcp_number_of_columns',
					'type'    => 'column',
					'title'   => __( 'Carousel Column(s)', 'wp-carousel-free' ),
					'desc'    => __( 'Set number of column on devices.', 'wp-carousel-free' ),
					'default' => array(
						'column1' => '5',
						'title1'  => __( 'Large Desktop', 'wp-carousel-free' ),
						'help1'   => __( 'Set number of column(s) for the screen larger than 1200px. Default value is 5.', 'wp-carousel-free' ),
						'column2' => '4',
						'title2'  => __( 'Desktop', 'wp-carousel-free' ),
						'help2'   => __( 'Set number of column on desktop for the screen smaller than 1200px. Default value is 4.', 'wp-carousel-free' ),
						'column3' => '3',
						'title3'  => __( 'Small Desktop', 'wp-carousel-free' ),
						'help3'   => __( 'Set number of column on small desktop for the screen smaller than 980px. Default value is 3.', 'wp-carousel-free' ),
						'column4' => '2',
						'title4'  => __( 'Tablet', 'wp-carousel-free' ),
						'help4'   => __( 'Set number of column on tablet for the screen smaller than 736px. Default value is 2.', 'wp-carousel-free' ),
						'column5' => '1',
						'title5'  => __( 'Mobile', 'wp-carousel-free' ),
						'help5'   => __( 'Set number of column on mobile for the screen smaller than 480px. Default value is 1.', 'wp-carousel-free' ),
						'min1'    => 1,
						'max1'    => 40,
						'min2'    => 1,
						'max2'    => 40,
						'min3'    => 1,
						'max3'    => 40,
						'min4'    => 1,
						'max4'    => 20,
						'min5'    => 0,
						'max5'    => 20,
					),
					'column1' => true,
					'column2' => true,
					'column3' => true,
					'column4' => true,
					'column5' => true,
					// 'dependency' => array( 'wpcp_carousel_mode', '==', 'standard' ),
				),
				array(
					'id'         => 'wpcp_image_order_by',
					'type'       => 'select',
					'title'      => __( 'Order by', 'wp-carousel-free' ),
					'desc'       => __( 'Set an order by option.', 'wp-carousel-free' ),
					'options'    => array(
						'menu_order' => array(
							'text' => __( 'Drag & Drop', 'wp-carousel-free' ),
						),
						'rand'       => array(
							'text' => __( 'Random', 'wp-carousel-free' )
						),
					),
					'default'    => 'menu_order',
					'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel' ),
				),
				array(
					'id'         => 'wpcp_post_order_by',
					'type'       => 'select',
					'title'      => __( 'Order by', 'wp-carousel-free' ),
					'desc'       => __( 'Select an order by option.', 'wp-carousel-free' ),
					'options'    => array(
						'ID'         => array(
							'text' => __( 'ID', 'wp-carousel-free' ),
						),
						'date'       => array(
							'text' => __( 'Date', 'wp-carousel-free' ),
						),
						'rand'       => array(
							'text' => __( 'Random', 'wp-carousel-free' ),
						),
						'title'      => array(
							'text' => __( 'Title', 'wp-carousel-free' ),
						),
						'modified'   => array(
							'text' => __( 'Modified', 'wp-carousel-free' ),
						),
						'menu_order' => array(
							'text' => __( 'Menu Order', 'wp-carousel-free' ),
						),
					),
					'default'    => 'menu_order',
					'dependency' => array( 'wpcp_carousel_type', 'any', 'post-carousel,product-carousel' ),
				),
				array(
					'id'         => 'wpcp_post_order',
					'type'       => 'select',
					'title'      => __( 'Order', 'wp-carousel-free' ),
					'desc'       => __( 'Select an order option.', 'wp-carousel-free' ),
					'options'    => array(
						'ASC'  => array(
							'text' => __( 'Ascending', 'wp-carousel-free' )
						),
						'DESC' => array(
							'text' => __( 'Descending', 'wp-carousel-free' )
						),
					),
					'default'    => 'rand',
					'dependency' => array( 'wpcp_carousel_type', 'any', 'post-carousel,product-carousel' ),
				),
			), // End: fields.
		), // End: General section.

		// Begin Carousel Settings.
		array(
			'name'   => 'sp_wcpcp_shortcode_option_2',
			'title'  => __( 'Carousel Settings', 'wp-carousel-free' ),
			'icon'   => 'fa fa-sliders',
			'fields' => array(
				array(
					'id'         => 'wpcp_carousel_auto_play',
					'type'       => 'switcher',
					'title'      => __( 'AutoPlay', 'wp-carousel-free' ),
					'desc'       => __( 'On/Off auto play.', 'wp-carousel-free' ),
					'default'    => true,
					//'dependency' => array( 'wpcp_carousel_mode', '==', 'standard' ),
				),
				array(
					'id'         => 'carousel_auto_play_speed',
					'type'       => 'number',
					'title'      => __( 'AutoPlay Speed', 'wp-carousel-free' ),
					'desc'       => __( 'Set auto play speed. Default value is 3000 ms.', 'wp-carousel-free' ),
					'after'      => __( '(millisecond)', 'wp-carousel-free' ),
					'default'    => '3000',
					'attributes' => array(
						'min' => 0,
					),
					'dependency' => array( 'wpcp_carousel_auto_play', '==', 'true'),
				),
				array(
					'id'         => 'standard_carousel_scroll_speed',
					'type'       => 'number',
					'title'      => __( 'Pagination Speed', 'wp-carousel-free' ),
					'desc'       => __( 'Set pagination/slide scroll speed. Default value is 600 ms.', 'wp-carousel-free' ),
					'after'      => __( '(millisecond)', 'wp-carousel-free' ),
					'default'    => '600',
					'attributes' => array(
						'min' => 0,
					),
				),
				array(
					'id'      => 'carousel_pause_on_hover',
					'type'    => 'switcher',
					'title'   => __( 'Pause on Hover', 'wp-carousel-free' ),
					'desc'    => __( 'On/Off carousel pause on hover.', 'wp-carousel-free' ),
					'default' => true,
				),
				array(
					'id'      => 'carousel_infinite',
					'type'    => 'switcher',
					'title'   => __( 'Infinite Loop', 'wp-carousel-free' ),
					'desc'    => __( 'On/Off infinite loop mode.', 'wp-carousel-free' ),
					'default' => true,
				),
				array(
					'type'       => 'subheading',
					'content'    => __( 'Navigation Settings', 'wp-carousel-free' ),
				),
				// Navigation Settings.
				array(
					'id'         => 'wpcp_navigation',
					'type'       => 'button_set',
					'title'      => __( 'Navigation', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide carousel navigation.', 'wp-carousel-free' ),
					'options'    => array(
						'show'        => __( 'Show', 'wp-carousel-free' ),
						'hide'        => __( 'Hide', 'wp-carousel-free' ),
						'hide_mobile' => __( 'Hide on Mobile', 'wp-carousel-free' ),
					),
					'radio'      => true,
					'default'    => 'show',
					'attributes' => array(
						'data-depend-id' => 'wpcp_navigation',
					),
				),
				array(
					'id'         => 'wpcp_nav_colors',
					'type'       => 'color_set',
					'title'      => __( 'Navigation Color', 'wp-carousel-free' ),
					'desc'       => __( 'Set color for the carousel navigation.', 'wp-carousel-free' ),
					'color1'     => true,
					'color2'     => true,
					'default'    => array(
						'title1' => __( 'Color', 'wp-carousel-free' ),
						'color1' => '#aaa',
						'title2' => __( 'Hover Color', 'wp-carousel-free' ),
						'color2' => '#18AFB9',
					),
					'dependency' => array( 'wpcp_navigation', '!=', 'hide' ),
				),
				// Pagination Settings.
				array(
					'type'    => 'subheading',
					'content' => __( 'Pagination Settings', 'wp-carousel-free' ),
				),
				array(
					'id'         => 'wpcp_pagination',
					'type'       => 'button_set',
					'title'      => __( 'Pagination', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide carousel pagination.', 'wp-carousel-free' ),
					'options'    => array(
						'show'        => __( 'Show', 'wp-carousel-free' ),
						'hide'        => __( 'Hide', 'wp-carousel-free' ),
						'hide_mobile' => __( 'Hide on Mobile', 'wp-carousel-free' ),
					),
					'radio'      => true,
					'default'    => 'hide_mobile',
					'attributes' => array(
						'data-depend-id' => 'wpcp_pagination',
					),
				),
				array(
					'id'         => 'wpcp_pagination_color',
					'type'       => 'color_set',
					'title'      => __( 'Pagination Color', 'wp-carousel-free' ),
					'desc'       => __( 'Set color for the carousel pagination dots.', 'wp-carousel-free' ),
					'color1'     => true,
					'color2'     => true,
					'default'    => array(
						'title1' => __( 'Color', 'wp-carousel-free' ),
						'color1' => '#cccccc',
						'title2' => __( 'Active Color', 'wp-carousel-free' ),
						'color2' => '#18AFB9',
					),
					'dependency' => array( 'wpcp_pagination', '!=', 'hide' ),
				),

				// Miscellaneous Settings.
				array(
					'type'    => 'subheading',
					'content' => __( 'Misc. Settings', 'wp-carousel-free' ),
				),
				array(
					'id'         => 'wpcp_accessibility',
					'type'       => 'switcher',
					'title'      => __( 'Tab and Key Navigation', 'wp-carousel-free' ),
					'desc'       => __( 'Enable/Disable carousel scroll with tab and keyboard.', 'wp-carousel-free' ),
					'default'    => true,
				),
				array(
					'id'         => 'slider_swipe',
					'type'       => 'switcher',
					'title'      => __( 'Swipe', 'wp-carousel-free' ),
					'desc'       => __( 'On/Off swipe mode.', 'wp-carousel-free' ),
					'default'    => true,
				),
				array(
					'id'         => 'slider_draggable',
					'type'       => 'switcher',
					'title'      => __( 'Mouse Draggable', 'wp-carousel-free' ),
					'desc'       => __( 'On/Off mouse draggable mode.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'slider_swipe', '==', 'true'),
				),
				array(
					'id'      => 'rtl_mode',
					'type'    => 'switcher',
					'title'   => __( 'RTL', 'wp-carousel-free' ),
					'desc'    => __( 'On/Off right to left mode.', 'wp-carousel-free' ),
					'default' => false,
				),
				array(
					'type'       => 'notice',
					'class'      => 'danger',
					'content'    => __( 'To make the RTL Mode work, please select an rtl language in the dashboard e.g. Arabic, Hebrew.', 'wp-carousel-free' ),
					'dependency' => array( 'rtl_mode', '==', 'true' ),
				),
			), // End Fields.
		), // End Carousel section.

		// Begin Style Settings.
		array(
			'name'   => 'sp_wcpcp_shortcode_option_3',
			'title'  => __( 'Style Settings', 'wp-carousel-free' ),
			'icon'   => 'fa fa-paint-brush',
			'fields' => array(
				array(
					'id'         => 'wpcp_post_detail_position',
					'type'       => 'select',
					'title'      => __( 'Content Position', 'wp-carousel-free' ),
					'desc'       => __( 'Select a position for the title, content, meta etc.', 'wp-carousel-free' ),
					'options'    => array(
						'bottom'       => array(
							'text' => __( 'Bottom', 'wp-carousel-free' ),
						),
						'on_right'     => array(
							'text'     => __( 'Right (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
						'with_overlay' => array(
							'text'     => __( 'Overlay (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
					),
					'default'    => 'bottom',
					'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,post-carousel,product-carousel' ),
				),
				array(
					'id'      => 'wpcp_slide_border',
					'type'    => 'border',
					'title'   => __( 'Slide Border', 'wp-carousel-free' ),
					'desc'    => __( 'Set border for the slide.', 'wp-carousel-free' ),
					'default' => array(
						'width' => '1',
						'style' => 'solid',
						'color' => '#dddddd',
						// 'hover_color'  => '#dddddd',
					),
					// 'hover_color'  => true,
				),
				// Post Settings.
				array(
					'id'         => 'wpcp_post_title',
					'type'       => 'switcher',
					'title'      => __( 'Post Title', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide post title.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
				),
				array(
					'id'         => 'wpcp_post_content_show',
					'type'       => 'switcher',
					'title'      => __( 'Post Content', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide post content.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
				),
				array(
					'id'         => 'wpcp_post_content_type',
					'type'       => 'select',
					'title'      => __( 'Content Display Type', 'wp-carousel-free' ),
					'desc'       => __( 'Select a content display type.', 'wp-carousel-free' ),
					'options'    => array(
						'excerpt'            => array(
							'text' => __( 'Excerpt', 'wp-carousel-free' ),
						),
						'content'            => array(
							'text'     => __( 'Full Content (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
						'content_with_limit' => array(
							'text' => __( 'Content with Limit (Pro)', 'wp-carousel-free' ),
							'pro_only' => true,
						),
					),
					'default'    => 'excerpt',
					'class'      => 'chosen',
					'dependency' => array( 'wpcp_carousel_type|wpcp_post_content_show', '==|==', 'post-carousel|true' ),
				),
				array(
					'type'       => 'subheading',
					'content'    => __( 'Post Meta', 'wp-carousel-free' ),
					'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
				),
				array(
					'id'         => 'wpcp_post_author_show',
					'type'       => 'switcher',
					'title'      => __( 'Post Author', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide post author name.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
				),
				array(
					'id'         => 'wpcp_post_date_show',
					'type'       => 'switcher',
					'title'      => __( 'Post Date', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide post date.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
				),

				// Product Settings.
				array(
					'type'       => 'subheading',
					'content'    => __( 'Product Settings', 'wp-carousel-free' ),
					'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
				),
				array(
					'id'         => 'wpcp_product_name',
					'type'       => 'switcher',
					'title'      => __( 'Product Name', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide product name.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
				),
				array(
					'id'         => 'wpcp_product_price',
					'type'       => 'switcher',
					'title'      => __( 'Product Price', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide product price.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
				),
				array(
					'id'         => 'wpcp_product_rating',
					'type'       => 'switcher',
					'title'      => __( 'Product Rating', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide product rating.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
				),
				array(
					'id'         => 'wpcp_product_cart',
					'type'       => 'switcher',
					'title'      => __( 'Add to Cart Button', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide add to cart button.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
				),
				// Image Settings.
				array(
					'type'       => 'subheading',
					'content'    => __( 'Image Settings', 'wp-carousel-free' ),
					'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,post-carousel,product-carousel' ),
				),
				array(
					'id'         => 'show_image',
					'type'       => 'switcher',
					'title'      => __( 'Image', 'wp-carousel-free' ),
					'desc'       => __( 'Show/Hide slide image.', 'wp-carousel-free' ),
					'default'    => true,
					'dependency' => array( 'wpcp_carousel_type', 'any', 'post-carousel,product-carousel' ),
				),
				array(
					'id'         => 'wpcp_image_sizes',
					'type'       => 'image_sizes',
					'class'      => 'chosen',
					'title'      => __( 'Image Sizes', 'wp-carousel-free' ),
					'default'    => 'full',
					'desc'       => __( 'Select a image size.', 'wp-carousel-free' ),
					'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,post-carousel,product-carousel' ),
				),
				array(
					'id'         => '_image_title_attr',
					'type'       => 'checkbox',
					'title'      => __( 'Image Title Attribute', 'wp-carousel-free' ),
					'desc'       => __( 'Check to add image title attribute.', 'wp-carousel-free' ),
					'default'    => false,
					'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,post-carousel,product-carousel' ),
				),
			), // End Fields.
		), // End a section.

		// Begin Typography the section.
		array(
			'name'   => 'sp_wpcp_shortcode_option_4',
			'title'  => __( 'Typography', 'wp-carousel-free' ),
			'icon'   => 'fa fa-font',
			// begin fields.
			'fields' => array(
				array(
					'type'    => 'pronotice',
					'content' => __( 'These Typography (840+ Google Fonts) options are available in the <a href="https://shapedplugin.com/plugin/wordpress-carousel-pro/" target="_blank">Pro Version</a> only.', 'wp-carousel-free' ),
				),
				array(
					'id'      => 'section_title_font_load',
					'type'    => 'switcher',
					'title'   => __( 'Load Carousel Section Title Font', 'wp-carousel-free' ),
					'desc'    => __( 'On/Off google font for the carousel section title.', 'wp-carousel-free' ),
					'default' => true,
				),
				array(
					'id'           => 'wpcp_section_title_typography',
					'type'         => 'typography_advanced',
					'title'        => __( 'Carousel Section Title Font', 'wp-carousel-free' ),
					'desc'         => __( 'Set Carousel section title font properties.', 'wp-carousel-free' ),
					'default'      => array(
						'family'    => 'Open Sans',
						'variant'   => '600',
						'font'      => 'google',
						'size'      => '24',
						'height'    => '28',
						'alignment' => 'center',
						'transform' => 'none',
						'spacing'   => 'normal',
						'color'     => '#444444',
					),
					'color'        => true,
					'preview'      => true,
					'preview_text' => 'The Carousel Section Title', // Replace preview text with any text you like.
				),
				array(
					'id'         => 'wpcp_image_caption_font_load',
					'type'       => 'switcher',
					'title'      => __( 'Load Caption Font', 'wp-carousel-free' ),
					'desc'       => __( 'On/Off google font for the image caption.', 'wp-carousel-free' ),
					'default'    => true,
				),
				array(
					'id'           => 'wpcp_image_caption_typography',
					'type'         => 'typography_advanced',
					'title'        => __( 'Caption Font', 'wp-carousel-free' ),
					'desc'         => __( 'Set caption font properties.', 'wp-carousel-free' ),
					'default'      => array(
						'family'    => 'Open Sans',
						'variant'   => '600',
						'font'      => 'google',
						'size'      => '15',
						'height'    => '23',
						'alignment' => 'center',
						'transform' => 'capitalize',
						'spacing'   => 'normal',
						'color'     => '#333',
					),
					'color'        => true,
					'preview'      => true,
					'preview_text' => 'The Caption', // Replace preview text with any text you like.
				),
				array(
					'id'         => 'wpcp_image_desc_font_load',
					'type'       => 'switcher',
					'title'      => __( 'Load Description Font', 'wp-carousel-free' ),
					'desc'       => __( 'On/Off google font for the image description.', 'wp-carousel-free' ),
					'default'    => true,
				),
				array(
					'id'           => 'wpcp_image_desc_typography',
					'type'         => 'typography_advanced',
					'title'        => __( 'Description Font', 'wp-carousel-free' ),
					'desc'         => __( 'Set description font properties.', 'wp-carousel-free' ),
					'default'      => array(
						'family'    => 'Open Sans',
						'variant'   => '400',
						'font'      => 'google',
						'size'      => '14',
						'height'    => '21',
						'alignment' => 'center',
						'transform' => 'none',
						'spacing'   => 'normal',
						'color'     => '#333',
					),
					'color'        => true,
					'preview'      => true,
					'preview_text' => 'The image description', // Replace preview text with any text you like.
				),
			), // End fields.
		), // End Typography section.
	),
);

SP_WPCP_Framework_Metabox::instance( $options );
