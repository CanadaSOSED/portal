<?php

/**
 *  Outputs the Category Focused Layout for the knowledge base main page.
 *
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Layout_Categories extends EPKB_Layout {

	/**
	 * Generate content of the KB main page
	 */
	public function generate_kb_main_page() {

		$class2 = $this->get_css_class( '::width' );		     ?>

		<div id="epkb-main-page-container" class="epkb-css-full-reset epkb-categories-template">
			<div <?php echo $class2; ?>>  <?php

				//  KB Search form
				$this->get_search_form();

				//  Knowledge Base Layout
				$style1 = $this->get_inline_style( 'background-color:: background_color' );				?>
				<div id="epkb-content-container" <?php echo $style1; ?> >

					<!--  Main Page Content -->
					<div class="epkb-section-container">
						<?php $this->display_main_page_content(); ?>
					</div>

				</div>
			</div>
		</div>   <?php
	}

	/**
	 * Display KB Main Page content
	 */
	private function display_main_page_content() {

		$class0 = $this->get_css_class('::section_box_shadow, epkb-top-category-box');
		$style0 = $this->get_inline_style( 
				'border-radius:: section_border_radius,
				 border-width:: section_border_width,
				 border-color:: section_border_color, ' .
				'background-color:: section_body_background_color, border-style: solid' );

		$class_section_head = $this->get_css_class( 'section-head' . ($this->kb_config[ 'section_divider' ] == 'on' ? ', section_divider' : '' ) );
		$style_section_head = $this->get_inline_style(
					'border-bottom-width:: section_divider_thickness,
					background-color:: section_head_background_color, ' .
					'border-top-left-radius:: section_border_radius,
					border-top-right-radius:: section_border_radius,
					border-bottom-color:: section_divider_color,
					padding-top:: section_head_padding_top,
					padding-bottom:: section_head_padding_bottom,
					padding-left:: section_head_padding_left,
					padding-right:: section_head_padding_right'
		);
		$style3 = $this->get_inline_style(
					'color:: section_head_font_color,
					 text-align::section_head_alignment,
					justify-content::section_head_alignment'
		);
		if ( $this->kb_config['section_head_alignment'] == 'right' ) {
			$style3 = $this->get_inline_style(
				'color:: section_head_font_color,
					 text-align::section_head_alignment,
					justify-content:flex-end'
			);
		}
		$style31 = $this->get_inline_style(
			'color:: section_head_font_color'
		);
		$style4 = $this->get_inline_style(
					'color:: section_head_description_font_color,
					 text-align::section_head_alignment'
		);
		$style5 = 'border-bottom-width:: section_border_width,
					padding-top::    section_body_padding_top,
					padding-bottom:: section_body_padding_bottom,
					padding-left::   section_body_padding_left,
					padding-right::  section_body_padding_right,';

		if ( $this->kb_config['section_box_height_mode'] == 'section_min_height' ) {
			$style5 .= 'min-height:: section_body_height';
		} else if ( $this->kb_config['section_box_height_mode'] == 'section_fixed_height' ) {
			$style5 .= 'overflow: auto, height:: section_body_height';
		}

		// for each CATEGORY display: a) its articles and b) top-level SUB-CATEGORIES with its articles

		$class1 = $this->get_css_class( ' ::nof_columns, ::section_font_size, eckb-categories-list' );
		
		$categories_icons = $this->is_builder_on && ! empty($this->kb_config['wizard-icons']) ? $this->kb_config['wizard-icons'] : EPKB_Utilities::get_kb_option( $this->kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, array(), true );
		
		$header_icon_style = $this->get_inline_style( 'color:: section_head_category_icon_color, font-size:: section_head_category_icon_size' );
		$header_image_style = $this->get_inline_style( 'max-height:: section_head_category_icon_size' );

		$icon_location = empty($this->kb_config['section_head_category_icon_location']) ? '' : $this->kb_config['section_head_category_icon_location'];
		$top_icon_class = $icon_location == 'top' ? 'epkb-top-cat-icon' : '';

		//Count Styling
		$count_background_color     = '#FFFFFF ';    //TODO Get KB setting
		$count_text_color           = '#000000';     //TODO Get KB setting
		$count_border_color         = '#CCCCCC';     //TODO Get KB setting      ?>

		<style>
			.epkb-cat-count {
				color:<?php echo $count_text_color; ?> !important;
				background-color:<?php echo $count_background_color; ?> !important;
				border:solid 1px <?php echo $count_border_color; ?> !important;
			}
		</style>
		<div <?php echo $class1; //Classes that are controlled by config settings ?> >   <?php

			/** DISPLAY BOXED CATEGORIES */
			foreach ( $this->category_seq_data as $box_category_id => $box_sub_categories ) {

				$category_name = isset($this->articles_seq_data[$box_category_id][0]) ?	$this->articles_seq_data[$box_category_id][0] : '';

				// Get the URL of this category
				$category_link = get_category_link( $box_category_id );

				if ( empty($category_name) ) {
					continue;
				}

				$category_icon = EPKB_KB_Config_Category::get_category_icon( $box_category_id, $categories_icons );

				$category_desc = isset($this->articles_seq_data[$box_category_id][1]) && $this->kb_config['section_desc_text_on'] == 'on' ? $this->articles_seq_data[$box_category_id][1] : '';
				$box_sub_categories = is_array($box_sub_categories) ? $box_sub_categories : array();
				$box_category_data = $this->is_builder_on ? 'data-kb-category-id=' . $box_category_id . ' data-kb-type=category ' : '';

				$category_count = $this->get_category_count( $box_category_id ); 	?>

				<!-- Section Container ( Category Box ) -->
				<section <?php echo $class0 . ' ' . $style0; ?> >

					<!-- Section Head -->
					<div <?php echo $class_section_head . ' ' . $style_section_head; ?> >

						<!-- Category Name + Icon -->
						<div class="epkb-category-level-1 <?php echo $top_icon_class; ?>" <?php echo $box_category_data . ' ' . $style3; ?> >

							<!-- Icon Top / Left -->	                            <?php
							if ( in_array( $icon_location, array('left', 'top') ) ) {

								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image <?php echo $top_icon_class; ?> "
									     src="<?php echo $category_icon['image_thumbnail_url']; ?>"
										<?php echo $header_image_style; ?>
									>								<?php
								} else { ?>
									<span class="epkb-cat-icon epkbfa <?php echo $top_icon_class . ' ' . $category_icon['name']; ?>" data-kb-category-icon="<?php echo $category_icon['name']; ?>" <?php echo $header_icon_style; ?>></span>	<?php
								}

							}

							if ( isset($this->kb_config['section_hyperlink_text_on']) ) { // TODO FUTURE should be option?   ?>
								<span class="epkb-cat-name">
									<a href="<?php echo esc_url( $category_link ); ?>" <?php echo $style31; ?>>
										<?php echo $category_name; ?>
										<span class="epkb-cat-count"><?php echo $category_count; ?></span>
									</a>
								</span>		<?php

							} else {        ?>
								<span class="epkb-cat-name"
									<?php echo $style31; ?>><?php echo $category_name; ?>
									<span class="epkb-cat-count"><?php echo $category_count; ?></span>
								</span>							<?php
							}	?>

							<!-- Icon Right -->     <?php
							if ( $icon_location == 'right' ) {

								if ( $category_icon['type'] == 'image' ) { ?>
									<img class="epkb-cat-icon epkb-cat-icon--image <?php echo $top_icon_class; ?> "
									     src="<?php echo $category_icon['image_thumbnail_url']; ?>"
										<?php echo $header_image_style; ?>
									>								<?php
								} else { ?>
									<span class="epkb-cat-icon epkbfa <?php echo $top_icon_class . ' ' . $category_icon['name']; ?>" data-kb-category-icon="<?php echo $category_icon['name']; ?>" <?php echo $header_icon_style; ?>></span>	<?php
								}

							}       ?>

						</div>

						<!-- Category Description -->						<?php
						if ( $category_desc ) {   ?>
						    <p <?php echo $style4; ?> >
						        <?php echo $category_desc; ?>
						    </p>						<?php
						}       ?>

					</div>

					<!-- Section Body -->
					<div class="epkb-section-body" <?php echo $this->get_inline_style( $style5 ); ?> >   			<?php
						
						/** DISPLAY TOP-CATEGORY ARTICLES LIST */
						if (  $this->kb_config['show_articles_before_categories'] != 'off' ) {
							$this->display_articles_list( 1, $box_category_id, ! empty($box_sub_categories) );
						}
						
						if ( ! empty($box_sub_categories) ) {
							$this->display_box_sub_categories( $box_sub_categories );
						}
						
						/** DISPLAY TOP-CATEGORY ARTICLES LIST */
						if (  $this->kb_config['show_articles_before_categories'] == 'off' ) {
							$this->display_articles_list( 1, $box_category_id, ! empty($box_sub_categories) );
						}                      ?>

					</div><!-- Section Body End -->

				</section><!-- Section End -->  <?php

			}  ?>

			</div>       <?php
	}
	
	/**
	 * Display categories within the Box i.e. sub-sub-categories
	 *
	 * @param $box_sub_category_list
	 */
	private function display_box_sub_categories( $box_sub_category_list ) {     	?>

		<ul class="epkb-sub-category eckb-sub-category-ordering"> <?php

			/** DISPLAY SUB-CATEGORIES */
			foreach ( $box_sub_category_list as $box_sub_category_id => $box_sub_sub_category_list ) {

				$category_count = $this->get_category_count( $box_sub_category_id );

				$category_name = isset($this->articles_seq_data[$box_sub_category_id][0]) ?
											$this->articles_seq_data[$box_sub_category_id][0] : __( 'Category.', 'echo-knowledge-base' );

				$style2         = $this->get_inline_style( 'color:: section_category_font_color' );
				$sub_cat_style  = $this->get_inline_style( 'color:: section_category_icon_color' );

				$icon_name = 'epkbfa-folder';

				$box_sub_category_data = $this->is_builder_on ? 'data-kb-category-id=' . $box_sub_category_id  . ' data-kb-type=sub-category ' : '';  	?>

				<li <?php echo $this->get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing' ); ?>>
					<div class="epkb-category-level-2-3" <?php echo $box_sub_category_data; ?>> <?php

						// Get the URL of this category
						$sub_category_link = get_category_link( $box_sub_category_id );	?>

						<i class="epkb-cat-icon epkbfa <?php echo $icon_name; ?>" data-kb-category-icon="<?php echo $icon_name; ?>" <?php echo $sub_cat_style; ?>></i>					<?php

						if ( isset($this->kb_config['section_hyperlink_text_on']) ) { // TODO FUTURE should be option?  ?>

							<span class="epkb-category-level-2-3__cat-name" tabindex="0">
								<a href="<?php echo esc_url( $sub_category_link ); ?>" <?php echo $style2; ?>><?php echo $category_name; ?>
									<span class="epkb-cat-count"><?php echo $category_count; ?></span>
								</a>
							</span>   <?php

						} else {        ?>
							<span class="epkb-category-level-2-3__cat-name" tabindex="0" <?php echo $style2; ?> ><?php echo $category_name; ?>
								<span class="epkb-cat-count"><?php echo $category_count; ?></span>
							</span>   <?php
						}   ?>

					</div>

				</li>  <?php
			}           ?>

		</ul> <?php
	}

	/**
	 * Display list of articles that belong to given subcategory
	 *
	 * @param $level
	 * @param $category_id
	 * @param bool $sub_category_exists - if true then we don't want to show "Articles coming soon" if there are no articles because
	 *                                   we have at least categories listed. But sub-category should always have that message if no article present
	 * @param string $sub_sub_string
	 */
	private function display_articles_list( $level, $category_id, $sub_category_exists=false, $sub_sub_string = '' ) {

		// retrieve articles belonging to given (sub) category if any
		$articles_list = array();
		if ( isset($this->articles_seq_data[$category_id]) ) {
			$articles_list = $this->articles_seq_data[$category_id];
			unset($articles_list[0]);
			unset($articles_list[1]);
		}

		// return if we have no articles and will not show 'Articles coming soon' message
		$articles_coming_soon_msg = $this->kb_config['category_empty_msg'];
		if ( empty($articles_list) && ( $sub_category_exists || empty($articles_coming_soon_msg) ) ) {
			return;
		}

		$sub_category_styles = 'padding-left:: article_list_margin';
		if ( $level == 1 ) {
			$data_kb_type = 'article';
			$sub_category_styles .= 'padding-left:: sidebar_article_list_margin,';
		} else if ( $level == 2 ) {
			$data_kb_type = 'sub-article';
		} else {
			$data_kb_type = empty($sub_sub_string) ? 'sub-sub-article' : $sub_sub_string . 'article';
		}

		$style = 'class="' . ( $level == 1 ? 'epkb-main-category ' : '' ) .  'epkb-articles eckb-articles-ordering"';		?>

		<ul <?php echo $style . ' ' . $this->get_inline_style( $sub_category_styles ); ?>> <?php

			if ( empty($articles_list) ) {
				echo '<li class="epkb-articles-coming-soon">' . esc_html( $articles_coming_soon_msg ) . '</li>';
			}

			$article_num = 0;
			$article_data = '';
			$nof_articles_displayed = $this->kb_config['nof_articles_displayed'];
			foreach ( $articles_list as $article_id => $article_title ) {
				$article_num++;
				$hide_class = $article_num > $nof_articles_displayed ? 'epkb-hide-elem' : '';
				if ( $this->is_builder_on ) {
					$article_data = $this->is_builder_on ? 'data-kb-article-id=' . $article_id . ' data-kb-type=' . $data_kb_type : '';
				}

				/** DISPLAY ARTICLE LINK */         ?>
				<li class="epkb-article-level-<?php echo $level . ' ' . $hide_class; ?>" <?php echo $article_data; ?> <?php echo $this->get_inline_style( 'padding-bottom:: article_list_spacing,padding-top::article_list_spacing' ); ?> >   <?php
					$this->single_article_link( $article_title, $article_id ); ?>
				</li> <?php
			}

			// if article list is longer than initial article list size then show expand/collapse message
			if ( count($articles_list) > $nof_articles_displayed ) { ?>
				<span class="epkb-show-all-articles">
					<span class="epkb-show-text">
						<?php echo esc_html( $this->kb_config['show_all_articles_msg'] ) . ' ( ' . ( count($articles_list) - $nof_articles_displayed ); ?> )
					</span>
				<span class="epkb-hide-text epkb-hide-elem"><?php echo esc_html( $this->kb_config['collapse_articles_msg'] ); ?></span> <?php
			}  ?>

		</ul> <?php
	}

	/**
	 * Count articles in category and sub-category
	 *
	 * @param $category_id
	 * @return int|mixed
	 */
	private function get_category_count( $category_id ) {
		$article_db = new EPKB_Articles_DB();
		$counter = 0;

		// demo data counter
		if ( isset($_POST['epkb-wizard-demo-data']) && $_POST['epkb-wizard-demo-data'] == true ) {
			$demo_articles_count = EPKB_KB_Demo_Data::get_demo_categories_list();
			foreach($demo_articles_count as $demo_article_count ) {
				if ( $demo_article_count['category_id'] == $category_id ) {
					$counter = $demo_article_count['count'];
				}
			}
		} else {
			$counter = count($article_db->get_published_articles_by_sub_or_category( $this->kb_config['id'], $category_id, 'date', -1, true ));
		}

		return $counter;
	}
}