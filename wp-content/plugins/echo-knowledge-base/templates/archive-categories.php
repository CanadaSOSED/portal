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

$template_style1 = EPKB_Utilities::get_inline_style(
    ' padding-top::       templates_for_kb_article_padding_top,
	        padding-bottom::    templates_for_kb_article_padding_bottom,
	        padding-left::      templates_for_kb_article_padding_left,
	        padding-right::     templates_for_kb_article_padding_right,
	        margin-top::        templates_for_kb_article_margin_top,
	        margin-bottom::     templates_for_kb_article_margin_bottom,
	        margin-left::       templates_for_kb_article_margin_left,
	        margin-right::      templates_for_kb_article_margin_right,', $kb_config );

// Future Options
$preset_style                = $kb_config['templates_for_kb_category_archive_page_style'];
$category_archive_title_icon = 'fa fa-folder-open';
$read_more                   = 'Read More';
$read_more_icon              = 'fa fa-long-arrow-right';

$category_title = single_cat_title( '', false );
$category_title = empty($category_title) ? '' : $category_title;     ?>


<section id="eckb-categories-archive-container">
    <div class="eckb-category-archive-reset eckb-category-archive-defaults <?php esc_attr_e($preset_style); ?>">
        <header class="eckb-category-archive-header">
            <div class="eckb-category-archive-title">
                <h1>
	                <span class="eckb-category-archive-title-desc"><?php echo esc_html($kb_config['templates_for_kb_category_archive_page_heading_description']); ?></span>
	                <span class="eckb-category-archive-title-name"><?php echo esc_html($category_title); ?></span>
                </h1>
                <span class="eckb-category-archive-title-icon <?php esc_attr_e($category_archive_title_icon); ?>"></span>
            </div>            <?php

            epkb_archive_category_description();
            // epkb_archive_category_breadcrumbs( $kb_config );     ?>

        </header>

        <main class="eckb-category-archive-main">   <?php

            while ( have_posts() ) {

			    the_post();

	            // Future Options
                $post = get_post();
                $post_date = ! empty($post->post_date) ? $post->post_date : '';
	            $post_date = EPKB_Utilities::get_formatted_datetime_string( $post_date, ' F jS, Y');
	            $published_date_esc   = '<span class="eckb-article-meta-name">Date: </span>' . esc_html($post_date);
	            $author_esc           = '<span class="eckb-article-meta-name">By: </span>' . get_the_author();
                $categories_esc       = '<span class="eckb-article-meta-name">Categories: </span>' . get_the_category_list(', ');

                // linked articles have their own icon
	            $article_title_icon = 'fa-file-text-o';
	            if ( has_filter('eckb_single_article_filter' ) ) {
		            $article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $post->ID );
		            $article_title_icon = empty( $article_title_icon ) ? 'fa-file-text-o' : $article_title_icon;
	            }       ?>

                <article class="eckb-article-container" id="post-<?php the_ID(); ?>">
                    <div class="eckb-article-image">
                        <?php the_post_thumbnail(); ?>
                    </div>
                    <div class="eckb-article-header">
                        <div class="eckb-article-title">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <span class="eckb-article-title-icon fa <?php esc_attr_e($article_title_icon); ?>"></span>
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
</section>        <?php

get_footer();


/**
 * Output term description
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

//TODO Build out Breadcrumbs
function epkb_archive_category_breadcrumbs( $kb_config ) {

	if ( $kb_config['breadcrumb_toggle'] == 'on' ) {?>
		<div class="eckb-category-archive-breadcrumbs">
			test
		</div>	<?php
	}
}