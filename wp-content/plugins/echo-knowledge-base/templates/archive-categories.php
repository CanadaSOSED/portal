 <?php
/**
 * The template for displaying KB Categories Archive page.
 *
 */

global $eckb_kb_id, $epkb_password_checked;

$kb_id = $eckb_kb_id;
$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );


/**
 * Display ARTICLE PAGE content
 */
get_header();

if ( epkb_is_archive_structure_v2( $kb_config ) ) {
	epkb_category_archive_v2( $kb_config );
} else {
	epkb_category_archive_v1( $kb_config );
}

get_footer();

 /**
  * V2 MAIN - Structure for Category Archive page
  *
  * @param $kb_config
  */
function epkb_category_archive_v2( $kb_config ) {

	// setup hoooks for the new layout
	add_action( 'eckb-categories-archive__body__left-sidebar', 'epkb_category_lists', 10, 3 );
	add_action( 'eckb-categories-archive__body__content__body', 'epkb_main_content', 10, 3 );
	add_action( 'eckb-categories-archive__body__content__header', 'epkb_archive_header', 10, 3 );

	generate_archive_structure_css_v2( $kb_config );	?>

	<!--- Category Archive Version 2 --->

	<!-- Categories Archive Container -->
	<div id="eckb-categories-archive-container-v2" class="eckb-category-archive-reset eckb-categories-archive-container-v2">

		<!-- Categories Archive Header -->
		<div id="eckb-categories-archive__header"><?php epkb_category_archive_section( 'eckb-categories-archive__header', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

		<!-- Categories Archive Body -->
		<div id="eckb-categories-archive__body">

			<!-- Categories Archive Body - Left Sidebar -->
			<div id="eckb-categories-archive__body__left-sidebar"><?php epkb_category_archive_section( 'eckb-categories-archive__body__left-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

			<!-- Categories Archive Body - Content -->
			<div id="eckb-categories-archive__body__content">

				<!-- Categories Archive Body - Content - Header -->
				<div id="eckb-categories-archive__body__content__header"><?php epkb_category_archive_section( 'eckb-categories-archive__body__content__header', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

				<!-- Categories Archive Body - Content - Body -->
				<div id="eckb-categories-archive__body__content__body"><?php epkb_category_archive_section( 'eckb-categories-archive__body__content__body', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

				<!-- Categories Archive Body - Content - Footer -->
				<div id="eckb-categories-archive__body__content__footer"><?php epkb_category_archive_section( 'eckb-categories-archive__body__content__footer', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

			</div>

			<!-- Categories Archive Body - Right Sidebar -->
			<div id="eckb-categories-archive__body__right-sidebar"><?php epkb_category_archive_section( 'eckb-categories-archive__body__right-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

		</div>

		<!-- Categories Archive Header -->
		<div id="eckb-categories-archive__footer"><?php epkb_category_archive_section( 'eckb-categories-archive__footer', array( 'id' => $kb_config['id'], 'config' => $kb_config ) ); ?></div>

	</div>      <?php
}

function generate_archive_structure_css_v2( $kb_config ){
	// NEW ARTICLE VERSION 2

	//This controls the whole width of the Archive page ( Left Sidebar / Content / Right Sidebar )
	$archive_container_width        = $kb_config['archive-container-width-v2'];
	$archive_container_width_units  = $kb_config['archive-container-width-units-v2'];

	// Left Sidebar Settings
	$archive_left_sidebar_width     = $kb_config['archive-left-sidebar-width-v2'];
	$archive_left_sidebar_padding   = $kb_config['archive-left-sidebar-padding-v2'];
	$archive_left_sidebar_bgColor   = $kb_config['archive-left-sidebar-background-color-v2'];

	// Content Settings
	$archive_content_width          = $kb_config['archive-content-width-v2'];
	$archive_content_padding        = $kb_config['archive-content-padding-v2'];
	$archive_content_bgColor        = $kb_config['archive-content-background-color-v2'];

	// Categories Archive Body - Content
	$archive_body_content_title_fontSize            = '35'; //TODO Future to convert to KB Settings ( Title of the Categoy page )
	$archive_body_content_article_fontSize    = '15'; //TODO Future to convert to KB Settings ( Title of the Each Article )

	// Right Sidebar Settings
	//$archive_right_sidebar_width     = $kb_config['archive-right-sidebar-width-v2'];
	$archive_right_sidebar_width     = 20; //TODO Remove this and add back to config one once we add right sidebar into a release.
	//$archive_right_sidebar_padding   = $kb_config['archive-right-sidebar-padding-v2'];
	//$archive_right_sidebar_bgColor   = $kb_config['archive-right-sidebar-background-color-v2'];

	// Advanced
	$mobile_width                    = $kb_config['archive-mobile-break-point-v2'];

	$is_left_sidebar_on =  // TODO in future if needed: $kb_config['archive-left-sidebar-on-v2'] == 'on'  ||
	                      $kb_config['kb_sidebar_location'] == 'left-sidebar' ||
						  $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME;

	$is_right_sidebar_on = // TODO in future if needed: $kb_config['archive-right-sidebar-on-v2'] == 'on' ||
						   $kb_config['kb_sidebar_location'] == 'right-sidebar';


	$archive_length = '';

	// Deal with Sidebar options.
	/**
	 *  Grid Columns start at lines.
	 *
	 *  Left Sidebar Grid Start:    1 - 2;
	 *  Content Grid Start:         2 - 3;
	 *  Left Sidebar Grid Start:    3 - 4;
	 */
	// If No Left Sidebar Expend the Article Content 1 - 3
	if ( ! $is_left_sidebar_on ) {
		$archive_length = '
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns:  0 '.$archive_content_width.'% '.$archive_right_sidebar_width.'%;
					}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
					grid-column-start: 1;
					grid-column-end: 3;
				}
			';
	}

	/**
	 * If No Right Sidebar
	 *  - Expend the Article Content 2 - 4
	 *  - Make Layout 2 Columns only and use the Two remaining values
	 */
	if ( ! $is_right_sidebar_on ) {
		$archive_length = '
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns: '.$archive_left_sidebar_width.'% '.$archive_content_width.'% 0 ;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
					grid-column-start: 2;
					grid-column-end: 4;
				}
			';
	}
	// If No Sidebars Expand the Article Content 1 - 4
	if ( ! $is_left_sidebar_on && ! $is_right_sidebar_on ) {
		$archive_length = '
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns: 0 '.$archive_content_width.'% 0;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
					display:none;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
					grid-column-start: 1;
					grid-column-end: 4;
				}
			';
	}
	/**
	 * If Both Sidebars are active
	 *  - Make Layout 3 Columns and divide their sizes according to the user settings
	 */
	if ( $is_left_sidebar_on && $is_right_sidebar_on ) {
		$archive_length = '
					#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
					      grid-template-columns: '.$archive_left_sidebar_width.'% '.$archive_content_width.'% '.$archive_right_sidebar_width.'%;
					}
					';
	}	?>

	<!-- archive Version 2 Style -->
	<style>
		<?php echo $archive_length; ?>
		#eckb-categories-archive-container-v2 {
			width:<?php echo $archive_container_width.$archive_container_width_units; ?>;
		}
		#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
			padding: <?php echo $archive_left_sidebar_padding.'px'; ?>;
			background-color: <?php echo $archive_left_sidebar_bgColor; ?>
		}
		#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
			padding: <?php echo $archive_content_padding.'px'; ?>;
			background-color: <?php echo $archive_content_bgColor; ?>
		}
		/* Right Sidebar */
		/*#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
			padding: <?php //echo $archive_right_sidebar_padding.'px'; ?>;
			background-color: <?php //echo $archive_right_sidebar_bgColor; ?>
		}*/
		/* Categories Archive Body - Content ----------------------------------------*/
		#eckb-categories-archive-container-v2 .eckb-category-archive-title h1 {
			font-size: <?php echo $archive_body_content_title_fontSize.'px'; ?>;
		}
		#eckb-categories-archive-container-v2 .eckb-article-container {
			font-size: <?php echo $archive_body_content_article_fontSize.'px'; ?>;
		}


		/* Media Queries ------------------------------------------------------------*/
		/* Grid Adjust Column sizes for smaller screen */
		/*@media only screen and ( max-width: <?php //echo $tablet_width; ?>px ) {
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
				grid-template-columns: 20% 60% 20%;
			}
		}*/

		/* Grid becomes 1 Column Layout */
		@media only screen and ( max-width: <?php echo $mobile_width; ?>px ) {

			#eckb-categories-archive-container-v2 {
				width:100%;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body {
				grid-template-columns: 0 100% 0;
			}

			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__content {
				grid-column-start: 1;
				grid-column-end: 4;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__left-sidebar {
				grid-column-start: 1;
				grid-column-end: 4;
			}
			#eckb-categories-archive-container-v2 #eckb-categories-archive__body__right-sidebar {
				grid-column-start: 1;
				grid-column-end: 4;
			}
		}
	</style>	<?php
}

 /**
  * V2 - Call all hooks for given Category section.
  *
  * @param $hook - both hook name and div id
  * @param $args
  */
 function epkb_category_archive_section( $hook, $args ) {
	 do_action( $hook, $args );
 }

 /**
  * V2 - FIRST display archive header
  *
  * @param $args
  * @noinspection PhpUnused*/
 function epkb_archive_header( $args ) {
	 $category_archive_title_icon    = 'epkbfa epkbfa-folder-open';
	 $category_title                 = single_cat_title( '', false );
	 $category_title                 = empty($category_title) ? '' : $category_title;    ?>

	 <header class="eckb-category-archive-header">
		 <div class="eckb-category-archive-title">
			 <h1>
				 <span class="eckb-category-archive-title-icon <?php esc_attr_e($category_archive_title_icon); ?>"></span>
				 <span class="eckb-category-archive-title-desc"><?php echo esc_html($args['config']['templates_for_kb_category_archive_page_heading_description']); ?></span>
				 <span class="eckb-category-archive-title-name"><?php echo esc_html($category_title); ?></span>
			 </h1>
		 </div>            <?php

		 epkb_archive_category_description();
		 epkb_archive_category_breadcrumbs( $args['config'] );     ?>

	 </header>   <?php
 }

 /**
  * V2 - SECOND display main content
  *
  * @param $args
  * @noinspection PhpUnused*/
function epkb_main_content( $args ) {

	$kb_config                   = $args['config'];
	$kb_id                       = $kb_config['id'];
	$read_more                   = $kb_config['templates_for_kb_category_archive_read_more'];
	$read_more_icon              = 'epkbfa epkbfa-long-arrow-right';
	$preset_style                = $kb_config['templates_for_kb_category_archive_page_style'];

	// if category has no article then show proper message
	if ( ! have_posts() ) {
		echo '<main class="eckb-category-archive-main ' . esc_attr($preset_style) . '"><p>' . $args['config']['category_empty_msg'] . '</p></main>';
		return;
	}	?>

	<main class="eckb-category-archive-main <?php esc_attr_e($preset_style); ?>">   <?php

		$term = get_queried_object();
		$category_id = $term->term_id;
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

		$articles_sequence = $kb_config['articles_display_sequence'];
		if ( $articles_sequence == 'alphabetical-title' ) {

			$query_args = array(
				'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
				'post_status' => 'publish',  // we want only published articles
				'orderby' => 'title',
				'order' => 'ASC',
				'paged'         => $paged, 
				'tax_query' => array(
					array(
						'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
						'terms' => $category_id,
					)
				)
			);

		} else if ( $articles_sequence == 'user-sequenced' ) {

			// category and article sequence
			$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
			$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

			// for WPML filter categories and articles given active language
			if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) && ! isset($_POST['epkb-wizard-demo-data']) ) {
				$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
				$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
			}

			// articles with no categories - temporary add one
			if ( isset($articles_seq_data[0]) ) {
				$category_seq_data[0] = array();
			}

			// get category and sub-category ids
			$category_array = isset($category_seq_data[$category_id]) ? $category_seq_data[$category_id] : array();
			$category_ids = epkb_get_array_keys_multi( $category_array );
			$category_ids[] = $category_id;

			// retrieve articles belonging to given (sub) category if any
			$category_article_ids = array();
			foreach($category_ids as $cat_id) {

				if ( ! empty($articles_seq_data[$cat_id]) ) {
					foreach( $articles_seq_data[$cat_id] as $key => $value ) {
						if ( $key > 1 ) {
							$category_article_ids[] = $key;
						}
					}
				}
			}

			$query_args = array(
				'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
				'post_status' => 'publish',  // we want only published articles
				'orderby' => 'post__in',
				'post__in' => $category_article_ids,
				'paged'         => $paged, 
				'tax_query' => array(
					array(
						'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
						'terms' => $category_id,
						
					)
				)
			);

		// ordered by date
		} else {

			$query_args = array(
				'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
				'post_status' => 'publish',  // we want only published articles
				'orderby' => 'date',
				'order' => 'DESC',
				'paged'         => $paged, 
				'tax_query' => array(
					array(
						'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
						'terms' => $category_id,
					)
				)
			);
		}

		$query = new WP_Query( $query_args );
		while ( $query->have_posts() ) {

			$query->the_post();

			// Future Options
			$post = get_post( get_the_ID() );
			$post_date = sprintf( '<time class="entry-date" datetime="%1$s">%2$s</time>', esc_attr( get_the_date( DATE_W3C, $post ) ), esc_html(get_the_date( '', $post )) );
			
			$published_date_esc   = '<span class="eckb-article-meta-name">' . __( 'Date:', 'echo-knowledge-base' ) . '</span> ' . $post_date;
			$author_esc           = '<span class="eckb-article-meta-name">' . __( 'By:', 'echo-knowledge-base' ) .  '</span> ' . get_the_author();
			$categories_esc       = '<span class="eckb-article-meta-name">' . __( 'Categories:', 'echo-knowledge-base' ) .  '</span> ' . get_the_category_list(', ');

			// linked articles have their own icon
			$article_title_icon = 'epkbfa-file-text-o';
			if ( has_filter('eckb_single_article_filter' ) ) {
				$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $post->ID );
				$article_title_icon = empty( $article_title_icon ) ? 'epkbfa-file-text-o' : $article_title_icon;
			}       ?>

			<article class="eckb-article-container" id="post-<?php the_ID(); ?>">
				<div class="eckb-article-image">
					<?php the_post_thumbnail(); ?>
				</div>
				<div class="eckb-article-header">
					<div class="eckb-article-title">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<span class="eckb-article-title-icon epkbfa <?php esc_attr_e($article_title_icon); ?>"></span>
					</div>
					<div class="eckb-article-metadata">
						<ul>
							<li class="eckb-article-posted-on"><?php echo $published_date_esc ?></li>
							<li class="eckb-article-byline"><?php echo $author_esc; ?></li>
							<li class="eckb-article-categories"><?php echo $categories_esc; ?></li>
						</ul>
					</div>
				</div>
				<div class="eckb-article-body">					    <?php

					if ( post_password_required() ) {
						echo get_the_password_form();
					} else {
						$epkb_password_checked = true;

						if ( has_excerpt( get_the_ID() ) ) {
							echo get_the_excerpt( get_the_ID() );
						} ?>

						<a href="<?php the_permalink(); ?>" class="eckb-article-read-more">
							<div class="eckb-article-read-more-text"><?php echo esc_html($read_more); ?></div>
							<div class="eckb-article-read-more-icon <?php echo esc_html($read_more_icon); ?>"></div>
						</a>    <?php
					}	    ?>

				</div>
				<div class="eckb-article-footer"></div>
			</article>			    <?php

		}

		the_posts_pagination(
			array(
				'prev_text'          => __( 'Previous', 'echo-knowledge-base' ),
				'next_text'          => __( 'Next', 'echo-knowledge-base' ),
				'before_page_number' => '<span>' . __( 'Page', 'echo-knowledge-base' ) . ' </span>',
			)
		);
			
		wp_reset_postdata();			?>

	</main> <?php
}


 /**
  * Function to flatten array
  * @param array $category_array
  * @return array
  */
 function epkb_get_array_keys_multi( array $category_array ) {
	 $keys = array();

	 foreach ($category_array as $key => $value) {
		 $keys[] = $key;

		 if ( is_array($category_array[$key]) ) {
			 $keys = array_merge($keys, epkb_get_array_keys_multi( $category_array[$key] ));
		 }
	 }

	 return $keys;
 }

 /**
  * V2 -THIRD display category list
  *
  * @param $args
  *
  * @noinspection PhpUnused*/
 function epkb_category_lists( $args ) {

	 // for Category Focused Layout show sidebar with list of top-level categories
	 if ( $args['config']['kb_main_page_layout'] != EPKB_KB_Config_Layout_Categories::LAYOUT_NAME ) {
	 	return;
	 }

	// find parent ID
	$category_id = 0;
	$active_id = 0;
	$breadcrumb_tree = EPKB_Templates_Various::get_term_breadcrumb( $args['config'], get_queried_object()->term_id );
	
	$breadcrumb_tree[] = get_queried_object()->term_id;
	
	if ( $args['config']['categories_layout_list_mode'] == 'list_top_categories' ) {
		$active_id = $breadcrumb_tree[0];
	} else {		
		$tree_count = count( $breadcrumb_tree );
		
		if ( $tree_count > 1 ) {
			$category_id = $breadcrumb_tree[$tree_count - 2];
			$active_id = $breadcrumb_tree[$tree_count - 1];
		}
		
		if ( $tree_count == 1 ) {
			$active_id = get_queried_object()->term_id;
		}
	}

	echo EPKB_Categories_DB::get_layout_categories_list( $args['id'], $args['config'], $category_id, $active_id );
 }

 /**
  * V1 + V2 - Output breadcrumbs
  * @param $kb_config
  */
 function epkb_archive_category_breadcrumbs( $kb_config ) {

	 if ( $kb_config['breadcrumb_toggle'] == 'on' ) {

		 $term = get_queried_object();
		 if ( empty($term) || ! $term instanceof WP_Term ) {
			 return;
		 }	?>

		 <div class="eckb-category-archive-breadcrumbs">	<?php
			 EPKB_Templates::get_template_part( 'feature', 'breadcrumb', $kb_config, $term ); ?>
		 </div>	<?php
	 }
 }

 /**
  * V1 + V2 - Output term description
  */
 function epkb_archive_category_description() {

	 $term = get_queried_object();
	 if ( empty($term) || ! $term instanceof WP_Term ) {
		 return;
	 }

	 $term_description = get_term_field( 'description', $term );
	 if ( empty($term_description) || is_wp_error($term_description) ) {
		 return;
	 }

	 echo '<div class="eckb-category-archive-description">' . $term_description . '</div>';
 }

 /**
  * V1 page structure
  *
  * @param $kb_config
  */
function epkb_category_archive_v1( $kb_config ) {

	$preset_style                = $kb_config['templates_for_kb_category_archive_page_style'];
	$category_archive_title_icon = 'epkbfa epkbfa-folder-open';
	$read_more                   = $kb_config['templates_for_kb_category_archive_read_more'];
	$read_more_icon              = 'epkbfa epkbfa-long-arrow-right';

	$category_title = single_cat_title( '', false );
	$category_title = empty($category_title) ? '' : $category_title;	?>

	<section id="eckb-categories-archive-container">
		<div class="eckb-category-archive-reset eckb-category-archive-defaults <?php esc_attr_e($preset_style); ?>">
			<header class="eckb-category-archive-header">
				<div class="eckb-category-archive-title">
					<h1>
						<span class="eckb-category-archive-title-icon <?php esc_attr_e($category_archive_title_icon); ?>"></span>
						<span class="eckb-category-archive-title-desc"><?php echo esc_html($kb_config['templates_for_kb_category_archive_page_heading_description']); ?></span>
						<span class="eckb-category-archive-title-name"><?php echo esc_html($category_title); ?></span>
					</h1>
				</div>            <?php

				epkb_archive_category_description();
				epkb_archive_category_breadcrumbs( $kb_config );     ?>

			</header>	    <?php

			// for Category Focused layout show sidebar with list of top-level categories
			if ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME ) {
				// find parent ID
				$category_id = 0;
				$active_id = 0;
				$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $kb_config, get_queried_object()->term_id );

				if ( $breadcrumb_tree ) {
					$breadcrumb_tree = array_keys( $breadcrumb_tree );
					$tree_count = count( $breadcrumb_tree );

					if ( $tree_count > 1 ) {
						$category_id = $breadcrumb_tree[$tree_count - 1];
						$active_id = get_queried_object()->term_id;
					}
				}

				echo EPKB_Categories_DB::get_layout_categories_list( $kb_config['id'], $kb_config, $category_id, $active_id );
			}		    	                ?>

			<main class="eckb-category-archive-main">   <?php

				while ( have_posts() ) {

					the_post();

					// Future Options
					$post = get_post();
					$post_date = sprintf( '<time class="entry-date" datetime="%1$s">%2$s</time>', esc_attr( get_the_date( DATE_W3C, $post ) ), esc_html(get_the_date( '', $post )) );
					 
					$published_date_esc   = '<span class="eckb-article-meta-name">' . __( 'Date:', 'echo-knowledge-base' ) . '</span> ' . $post_date;
					$author_esc           = '<span class="eckb-article-meta-name">' . __( 'By:', 'echo-knowledge-base' ) . '</span> ' . get_the_author();
					$categories_esc       = '<span class="eckb-article-meta-name">' . __( 'Categories:', 'echo-knowledge-base' ) . '</span> ' . get_the_category_list(', ');

					// linked articles have their own icon
					$article_title_icon = 'epkbfa-file-text-o';
					if ( has_filter('eckb_single_article_filter' ) ) {
						$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $post->ID );
						$article_title_icon = empty( $article_title_icon ) ? 'epkbfa-file-text-o' : $article_title_icon;
					}       ?>

					<article class="eckb-article-container" id="post-<?php the_ID(); ?>">
						<div class="eckb-article-image">
							<?php the_post_thumbnail(); ?>
						</div>
						<div class="eckb-article-header">
							<div class="eckb-article-title">
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<span class="eckb-article-title-icon epkbfa <?php esc_attr_e($article_title_icon); ?>"></span>
							</div>
							<div class="eckb-article-metadata">
								<ul>
									<li class="eckb-article-posted-on"><?php echo $published_date_esc ?></li>
									<li class="eckb-article-byline"><?php echo $author_esc; ?></li>
									<li class="eckb-article-categories"><?php echo $categories_esc; ?></li>
								</ul>
							</div>
						</div>
						<div class="eckb-article-body">					    <?php

							if ( post_password_required() ) {
								echo get_the_password_form();
							} else {
								$epkb_password_checked = true;

								if ( has_excerpt( get_the_ID() ) ) {
									echo get_the_excerpt( get_the_ID() );
								} ?>

								<a href="<?php the_permalink(); ?>" class="eckb-article-read-more">
									<div class="eckb-article-read-more-text"><?php echo esc_html($read_more); ?></div>
									<div class="eckb-article-read-more-icon <?php echo esc_html($read_more_icon); ?>"></div>
								</a>    <?php
							}	    ?>

						</div>
						<div class="eckb-article-footer"></div>
					</article>			    <?php
				}

				the_posts_pagination(
						array(
								'prev_text'          => __( 'Previous', 'echo-knowledge-base' ),
								'next_text'          => __( 'Next', 'echo-knowledge-base' ),
								'before_page_number' => '<span>' . __( 'Page', 'echo-knowledge-base' ) . ' </span>',
						)
				);      ?>

			</main>
		</div>
	</section>      <?php
}

function epkb_is_archive_structure_v2( $kb_config ) {
   	$plugin_first_version = get_option( 'epkb_version_first' );
   	// TODO 'category-archive-structure-version'
	$category_archive_version = $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME || ! empty($plugin_first_version) ? 'version-2' : 'version-1';
	return $category_archive_version === 'version-2';
}
