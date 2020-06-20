<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display wizard information that is displayed with KB Configuration page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Wizards {

	/**
	 * Display wizard Page
	 * @param $kb_id
	 * @param $is_active
	 */
	public static function display_page( $kb_id, $is_active ) { ?>
		<div class="epkb-wizards" id="epkb-config-wizards-content" <?php echo $is_active ? 'style="display: block;' : ''; ?>>

			<section class="epkb-wizards__section-intro">
				<h1><?php _e( 'Easy Configuration with Wizard', 'echo-knowledge-base' ); ?></h1>
				<p><?php _e( 'Each Wizard will help you setup a different aspect of the Knowledge Base.', 'echo-knowledge-base' ); ?></p>
			</section>  <?php

			// ensure users have latest add-on
			if ( EPKB_KB_Wizard::is_wizard_disabled() ) {
				echo '<div class="epkb-wizard-error-note">' . __('Elegant Layouts, Advanced Search and Article Rating plugins need to be up to date. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support() . '</div>';
				return;
			}

			$page_url = 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id );
			$is_blank_kb = EPKB_KB_Wizard::is_blank_KB( $kb_id );
			if ( is_wp_error($is_blank_kb) || $is_blank_kb ) {
				self::display_initial_wizard( $page_url );
			} else {
				self::display_full_wizard( $page_url );
			}	?>

		</div>	<?php
	}

	private static function display_initial_wizard( $page_url ) {         ?>
		<section class="epkb-wizards__row-3-col">				<?php
			self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-paint-brush',
					'title'         => __( 'Theme Wizard', 'echo-knowledge-base' ),
					'content'       => __( 'Configure KB look, style and colors.', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Run Wizard', 'echo-knowledge-base' ),
					'btn_url'       =>  admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-on',
			));     ?>
		</section>      <?php
	}

	private static function display_full_wizard( $page_url ) {         ?>

		<section class="epkb-wizards__row-3-col">				<?php
				self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-paint-brush',
					'title'         => __( 'Theme Wizard', 'echo-knowledge-base' ),
					'content'       => __( 'Configure the KB look, style, and colors.', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Run Wizard', 'echo-knowledge-base' ),
					'btn_url'       =>  admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-on',
				));

				self::display_wizard_box_1( array(
						'icon_class'    => 'epkbfa-font',
						'title'         => __( 'Text Wizard', 'echo-knowledge-base' ),
						'content'       => __( 'Change predefined text strings or set them to your native language.', 'echo-knowledge-base' ),
						'btn_text'      => __( 'Run Wizard', 'echo-knowledge-base' ),
						'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-text-on',
				));

				self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-cog',
					'title'         => __( 'Features and Sidebars Wizard', 'echo-knowledge-base' ),
					'content'       => __( 'Configure sidebars, TOC, breadcrumbs, and more.', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Run Wizard', 'echo-knowledge-base' ),
					'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-features',
				));				?>
			</section>

		<section class="epkb-wizards__row-3-col">				<?php
				self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-search',
					'title'         => __( 'Search Wizard', 'echo-knowledge-base' ),
					'content'       => __( 'Configure advanced search.', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Run Wizard', 'echo-knowledge-base' ),
					'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-search'
				));

				self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-sort-alpha-asc',
					'title'         => __( 'Ordering Wizard', 'echo-knowledge-base' ),
					'content'       => __( 'Ordering articles and categories alphabetically, chronologically, or in any order.', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Run Wizard', 'echo-knowledge-base' ),
					'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-ordering'
				));		
				
				self::display_wizard_box_1( array(
					'icon_class'    => 'epkbfa-globe',
					'title'         => __( 'Global Wizard', 'echo-knowledge-base' ),
					'content'       => __( 'KB URL, Template, Category Navigation, WPML.', 'echo-knowledge-base' ),
					'btn_text'      => __( 'Run Wizard', 'echo-knowledge-base' ),
					'btn_url'       => admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-global',
				)); 	?>
		</section>      <?php
	}


	/**
	 * Show a box with Icon, Title, Description and Link
	 *
	 * @param $args array

	 * - ['icon_class']     Top Icon to display ( Choose between these available ones: https://fontawesome.com/v4.7.0/icons/ )
	 * - ['title']          H3 title of the box.
	 * - ['content']        Body content of the box.
	 * - ['btn_text']       Show button and the text of the button at the bottom of the box, if no text is defined no button will show up.
	 * - ['btn_url']        Button URL.
	 */
	public static function display_wizard_box_1( $args ) { ?>

		<div class="epkb-wizard-box-container_1">

			<!-- Header -------------------->
			<div class="epkb-wizard-box__header">
				<i class="epkb-wizard-box__header__icon epkbfa <?php echo $args['icon_class']; ?>"></i>
				<h3 class="epkb-wizard-box__header__title"><?php echo $args['title']; ?></h3>
			</div>

			<!-- Body ---------------------->
			<div class="epkb-wizard-box__body">
				<?php echo $args['content']; ?>
			</div>

			<!-- Footer ---------------------->
			<div class="epkb-wizard-box__footer">
					<a class="epkb-wizard-box__footer__button" href="<?php echo esc_url( $args['btn_url'] ); ?>"><?php echo $args['btn_text']; ?></a>
			</div>

		</div>	<?php
	}
}
