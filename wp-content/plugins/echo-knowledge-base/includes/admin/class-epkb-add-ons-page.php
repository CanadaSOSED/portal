<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Add-ons page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Add_Ons_Page {

	public function display_add_ons_page() {

		ob_start(); ?>

		<div class="wrap">
			<h1></h1>
		</div>
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-add-ons-container">
			<div class="epkb-config-wrapper">
				<div class="wrap" id="ekb_core_top_heading"></div>
				<div class="eckb-top-notice-message"></div>

				<div class="welcome_header">
					<div class="container-fluid">
						<div class="row">
							<div class="col-5">
								<h1><?php esc_html_e( 'Extend Your Knowledge Base', 'echo-knowledge-base' ); ?></h1>
							</div>
						</div>
					</div>
				</div>          <?php

				self::display_add_ons_details();  ?>
			</div>

		</div>      <?php

		echo ob_get_clean();
	}

	/**
	 * Display all add-ons
	 */
	private static function display_add_ons_details() {

		$output = '';

		// only administrator can see licenses
		$license_content = '<h3>You can access your license account <a href="https://www.echoknowledgebase.com/your-account/" target="_blank" rel="noopener">here</a></h3>' .
		                   '<h3>Please refer to the ' .
		                   '<a href="https://www.echoknowledgebase.com/documentation/my-account-and-license-faqs/" target="_blank" rel="noopener">documentation</a>' .
		                   ' for help with your license account and any other issues.</h3> ';
		if ( current_user_can('manage_options') ) {
			$license_content = apply_filters( 'epkb_license_fields', $license_content );
		}

		$tab = empty($_REQUEST['epkb-tab']) || empty($license_content) ? 'add-ons' : 'licenses';    ?>

		<div id="epkb-tabs" class="add_on_container">
			<section class="epkb-main-nav">
				<ul class="epkb-admin-pages-nav-tabs">
					<li class="nav_tab <?php echo ($tab == 'add-ons' ? 'active' : ''); ?>">
						<h2><?php _e( 'Add-ons', 'echo-knowledge-base' ); ?></h2>
						<p><?php _e( 'More Possibilities', 'echo-knowledge-base' ); ?></p>
					</li>
					<?php if ( ! empty($license_content) ) { ?>
						<li id="eckb_license_tab" class="nav_tab <?php echo ($tab == 'licenses' ? 'active' : ''); ?>">
							<h2><?php _e( 'Licenses', 'echo-knowledge-base' ); ?></h2>
							<p><?php _e( 'Licenses for add-ons', 'echo-knowledge-base' ); ?></p>
						</li>
					<?php }  ?>
				</ul>
			</section>
			<div id="add_on_panels" class="ekb-admin-pages-panel-container">
				<div class="ekb-admin-page-tab-panel container-fluid <?php echo ($tab == 'add-ons' ? 'active' : ''); ?>">
					<div class="row">   <?php

						// http://www.echoknowledgebase.com/wp-content/uploads/2017/09/product_preview_coming_soon.png

						self::add_on_product( array(
							'id'                => 'epkb-add-on-bundle',
							'title'             => __( 'Add-on Bundle', 'echo-knowledge-base' ),
							'special_note'      => __( 'Save money with bundle discount', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/04/Bundle-Box1.jpg',
							'desc'              => __( 'Save up to 50% when buying multiple add-ons together.', 'echo-knowledge-base' ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/bundle-pricing/',
						) );

						$i18_grid = '<strong>' . __( 'Grid Layout', 'echo-knowledge-base' ) . '</strong>';
						$i18_sidebar = '<strong>' . __( 'Sidebar Layout', 'echo-knowledge-base' ) . '</strong>';
						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Elegant Layouts', 'echo-knowledge-base' ),
							'special_note'      => __( 'More ways to design your KB', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2017/08/EL'.'AY-Featured-image.jpg',
							'desc'              => sprintf( _x( 'Use %s or %s for KB Main page or combine Basic, ' .
							                       'Tabs, Grid and Sidebar layouts in many cool ways.', 'echo-knowledge-base' ), $i18_grid, $i18_sidebar ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
						) );

						$i18_list = '<strong>' . __( 'product, service or team', 'echo-knowledge-base' ) . '</strong>';
						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Multiple Knowledge Bases', 'echo-knowledge-base' ),
							'special_note'      => __( 'Expand your documentation', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2017/08/MKB-Featured-image-2.jpg',
							'desc'              => sprintf( _x( 'Create a separate Knowledge Base for each %s.',
                                                    'product, service and team.', 'echo-knowledge-base' ), $i18_list ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/'
						) );

						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Advanced Search', 'echo-knowledge-base' ),
							'special_note'      => __( 'Enhance and analyze user searches', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2018/09/KB-Advanced-Search-Featured-Images-1.jpg',
							'desc'              => _x( 'Enhance users search experience and view search analytics including popular searches and no results searches.',
								'echo-knowledge-base' ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/'
						) );

						$i18_what = '<strong>' . __( 'Widgets and shortcodes', 'echo-knowledge-base' ) . '</strong>';
						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Widgets', 'echo-knowledge-base' ),
							'special_note'      => __( 'Shortcodes, Widgets, Sidebars', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2015/08/Widgets-Featured-image.jpg',
							'desc'              => sprintf( _x( 'Add KB Search, Most Recent Articles and other %s to your articles, sidebars and pages.',
                                                'echo-knowledge-base' ), $i18_what ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/widgets/'
						) );

						$i18_groups = '<strong>' . __( 'Groups', 'echo-knowledge-base' ) . '</strong>';
						$i18_roles = '<strong>' . __( 'KB Roles', 'echo-knowledge-base' ) . '</strong>';
                        self::add_on_product( array(
	                        'id'                => '',
                            'title'             => __( 'Access Manager', 'echo-knowledge-base' ),
                            'special_note'      => __( 'Protect your KB content', 'echo-knowledge-base' ),
                            'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2018/02/AM'.'GR-Featured-image.jpg',
                            'desc'              => sprintf( _x( 'Restrict your Articles to certain %s using KB Categories. Assign users to specific %s within Groups.', 'echo-knowledge-base' ), $i18_groups, $i18_roles ),
	                        'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/'
                        ) );

						$i18_objects = '<strong>' . __( 'PDFs, pages, posts and websites', 'echo-knowledge-base' ) . '</strong>';
						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Links Editor for PDFs and More', 'echo-knowledge-base' ),
							'special_note'      => __( 'Link to PDFs, posts and pages', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2018/02/LINK-Featured-image.jpg',
							'desc'              => sprintf( _x( 'Set Articles to links to %s. On KB Main Page, choose icons for your articles.', 'echo-knowledge-base' ), $i18_objects ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/'
						) );

						/* self::add_on_product( array(
							'title' => __( 'Article Shortcodes', 'echo-knowledge-base' ),
							'special_note' => __( 'Supercharge Your Articles', 'echo-knowledge-base' ),
							'img' => 'http://www.echoknowledgebase.com/wp-content/uploads/2017/09/product_preview_coming_soon.png',
							'desc' => __( 'Use a set of shortcodes to make it easier and faster to create professional-looking articles. ' .
							              'Additionally, your users will find these articles easier to read.'),
							'coming_when' =>  __( 'Coming in Sept', 'echo-knowledge-base' ),
							'#' //'learn_more_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/sidebar-layout/',
						) ); */	?>

					</div>
				</div>

				<!--   LICENSES ONLY -->		<?php
				if ( ! empty($license_content) ) { ?>
					<div class="ekb-admin-page-tab-panel container-fluid <?php echo ($tab == 'licenses' ? 'active' : ''); ?>">
						<section id="ekcb-licenses" class="form_options">
							<ul>  	<!-- Add-on name / License input / status  -->   <?php
								echo $license_content;      ?>
							</ul>
						</section>
					</div>
				<?php }  ?>

			</div>
		</div>   <?php
	}

	private static function add_on_product( $values = array () ) {    ?>

		<div id="<?php echo $values['id']; ?>" class="add_on_product">
			<div class="top_heading">
				<h3><?php esc_html_e($values['title']); ?></h3>
				<p><i><?php esc_html_e($values['special_note']); ?></i></p>
			</div>
			<div class="featured_img">
				<img src="<?php echo $values['img']; ?>">
			</div>
			<div class="description">
				<p>
					<?php echo wp_kses_post($values['desc']); ?>
				</p>
			</div>
			<div class="button_container">
				<?php if ( ! empty($values['coming_when']) ) { ?>
					<div class="coming_soon"><?php esc_html_e( $values['coming_when'] ); ?></div>
				<?php } else { ?>
					<a class="button primary-btn" href="<?php echo $values['learn_more_url']; ?>" target="_blank"><?php _e( 'Learn More', 'echo-knowledge-base' ); ?></a>
				<?php } ?>
			</div>

		</div>    <?php
	}
}

