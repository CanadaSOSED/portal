<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display wizard information that is displayed with KB Configuration page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Advanced {

	/**
	 * Display wizard Page
	 * @param $kb_config
	 */
	public static function display_page( $kb_config ) { ?>
		<div class="epkb-advanced-config" id="epkb-config-advanced-config-content" >

			<section class="epkb-wizards__section-intro">
				<h1><?php _e( 'Advanced Configuration', 'echo-knowledge-base' ); ?></h1>
				<p><?php _e( 'You typically will not need to change this configuration. If you need a very customized look you can fine tune the KB here.', 'echo-knowledge-base' ); ?></p>
			</section>  <?php

			// ensure users have latest add-on
			if ( EPKB_KB_Wizard::is_wizard_disabled() ) {
				echo '<div class="epkb-wizard-error-note">' . __('Elegant Layouts, Advanced Search and Article Rating plugins need to be up to date. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support();
				return;
			} ?>

			<form class="epkb-wizards__two_columns" id="epkb-config-config4"> <?php
				self::display_main_page_column( $kb_config );
				self::display_article_page_column( $kb_config );   ?>
			</form>

		</div>	<?php
	}

	/**
	 * Displays advanced configuration for Main Page
	 * @param $kb_config
	 */
	private static function display_main_page_column( $kb_config ) {      ?>

		<div class="epkb-config-sidebar" id="epkb-config-main-setup-sidebar">
			<h2><?php _e( 'Main page', 'echo-knowledge-base' ); ?></h2>
			<div class="epkb-config-sidebar-options eckb-wizard-accordion">                        <?php

				$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
				$form = new EPKB_KB_Config_Elements();

				// ARTICLE PAGE
				$arg_bn_padding_top = $feature_specs['templates_for_kb_padding_top'] + array( 
					'value' => $kb_config['templates_for_kb_padding_top'], 
					'current' => $kb_config['templates_for_kb_padding_top'], 
					'text_class' => 'config-col-6'
				);
				
				$arg_bn_padding_bottom = $feature_specs['templates_for_kb_padding_bottom'] + array(
					'value' => $kb_config['templates_for_kb_padding_bottom'], 
					'current' => $kb_config['templates_for_kb_padding_bottom'], 
					'text_class' => 'config-col-6' 
				);
				
				$arg_bn_padding_left   = $feature_specs['templates_for_kb_padding_left'] + array( 
					'value' => $kb_config['templates_for_kb_padding_left'], 
					'current' => $kb_config['templates_for_kb_padding_left'], 
					'text_class' => 'config-col-6'
				);
				
				$arg_bn_padding_right  = $feature_specs['templates_for_kb_padding_right'] + array( 
					'value' => $kb_config['templates_for_kb_padding_right'],
					'current' => $kb_config['templates_for_kb_padding_right'], 
					'text_class' => 'config-col-6' 
				);

				$arg_bn_margin_top    = $feature_specs['templates_for_kb_margin_top'] + array( 
					'value' => $kb_config['templates_for_kb_margin_top'], 
					'current' => $kb_config['templates_for_kb_margin_top'], 
					'text_class' => 'config-col-6' 
				);
				
				$arg_bn_margin_bottom = $feature_specs['templates_for_kb_margin_bottom'] + array( 
					'value' => $kb_config['templates_for_kb_margin_bottom'], 
					'current' => $kb_config['templates_for_kb_margin_bottom'], 
					'text_class' => 'config-col-6' 
				);
				
				$arg_bn_margin_left   = $feature_specs['templates_for_kb_margin_left'] + array( 
					'value' => $kb_config['templates_for_kb_margin_left'], 
					'current' => $kb_config['templates_for_kb_margin_left'], 
					'text_class' => 'config-col-6' 
				);
				
				$arg_bn_margin_right  = $feature_specs['templates_for_kb_margin_right'] + array( 
					'value' => $kb_config['templates_for_kb_margin_right'], 
					'current' => $kb_config['templates_for_kb_margin_right'], 
					'text_class' => 'config-col-6'
				);
				
				if ( $kb_config['templates_for_kb'] == 'kb_templates' ) {
					// KB TEMPLATE
					$form->option_group_wizard( $feature_specs, array(
						'option-heading'    => __( 'KB Template', 'echo-knowledge-base' ),
						'class'             => 'eckb-mm-mp-links-setup-main-template eckb-wizard-accordion__body',
						'inputs'            => array(
							'0' => $form->multiple_number_inputs(
								array(
									'id'                => 'templates_for_kb_padding_group',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Template Padding( px )', 'echo-knowledge-base' ),
								),
								array( $arg_bn_padding_top, $arg_bn_padding_bottom, $arg_bn_padding_left, $arg_bn_padding_right )
							),
							'1' => $form->multiple_number_inputs(
								array(
									'id'                => 'templates_for_kb_margin_group',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Template Margin( px )', 'echo-knowledge-base' ),
								),
								array( $arg_bn_margin_top, $arg_bn_margin_bottom , $arg_bn_margin_left, $arg_bn_margin_right )
							),
						)
					));
				}
				
				if ( ! EPKB_Utilities::is_advanced_search_enabled( $kb_config ) && ! EPKB_Utilities::is_elegant_layouts_enabled() ) {

					// SEARCH BOX
					$arg1_search_box_padding_vertical   = $feature_specs['search_box_padding_top'] + array( 'value' => $kb_config['search_box_padding_top'], 'current' => $kb_config['search_box_padding_top'], 'text_class' => 'config-col-6' );
					$arg2_search_box_padding_vertical   = $feature_specs['search_box_padding_bottom'] + array( 'value' => $kb_config['search_box_padding_bottom'], 'current' => $kb_config['search_box_padding_bottom'], 'text_class' => 'config-col-6' );
					$arg1_search_box_padding_horizontal = $feature_specs['search_box_padding_left'] + array( 'value' => $kb_config['search_box_padding_left'], 'current' => $kb_config['search_box_padding_left'], 'text_class' => 'config-col-6' );
					$arg2_search_box_padding_horizontal = $feature_specs['search_box_padding_right'] + array( 'value' => $kb_config['search_box_padding_right'], 'current' => $kb_config['search_box_padding_right'], 'text_class' => 'config-col-6' );
					$arg1_search_box_margin_vertical = $feature_specs['search_box_margin_top'] + array( 'value' => $kb_config['search_box_margin_top'], 'current' => $kb_config['search_box_margin_top'], 'text_class' => 'config-col-6' );
					$arg2_search_box_margin_vertical = $feature_specs['search_box_margin_bottom'] + array( 'value' => $kb_config['search_box_margin_bottom'], 'current' => $kb_config['search_box_margin_bottom'], 'text_class' => 'config-col-6' );

					$form->option_group_wizard( $feature_specs, array(
						'option-heading' => __( 'Search Box', 'echo-knowledge-base' ),
						'class'        => 'eckb-mm-mp-links-tuning-searchbox-advanced eckb-wizard-accordion__body',
						'inputs' => array(
							'0' => $form->multiple_number_inputs(
								array(
									'id'                => 'search_box_padding',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Padding( px )', 'echo-knowledge-base' ),
								),
								array( $arg1_search_box_padding_vertical, $arg2_search_box_padding_vertical ,$arg1_search_box_padding_horizontal, $arg2_search_box_padding_horizontal )
							),
							'1' => $form->multiple_number_inputs(
								array(
									'id'                => 'search_box_margin',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Margin( px )', 'echo-knowledge-base' ),
								),
								array( $arg1_search_box_margin_vertical, $arg2_search_box_margin_vertical )
							),
							'2' => $form->text( $feature_specs['search_input_border_width'] + array(
									'value' => $kb_config['search_input_border_width'],
									'input_group_class' => 'config-col-12',
									'label_class' => 'config-col-5',
									'input_class' => 'config-col-2'
								) ),
							'3' => $form->checkbox( $feature_specs['search_box_results_style'] + array(
									'value'             => $kb_config['search_box_results_style'],
									'id'                => 'search_box_results_style',
									'input_group_class' => 'config-col-12',
									'label_class'       => 'config-col-5',
									'input_class'       => 'config-col-2'
								) ),
						)));
				}

				// CATEGORIES - Style
				$arg1_section_head_padding_vertical = $feature_specs['section_head_padding_top'] + array( 'value' => $kb_config['section_head_padding_top'], 'current' => $kb_config['section_head_padding_top'], 'text_class' => 'config-col-6' );
				$arg2_section_head_padding_vertical = $feature_specs['section_head_padding_bottom'] + array( 'value' => $kb_config['section_head_padding_bottom'], 'current' => $kb_config['section_head_padding_bottom'], 'text_class' => 'config-col-6' );
				$arg1_section_head_padding_horizontal = $feature_specs['section_head_padding_left'] + array( 'value' => $kb_config['section_head_padding_left'], 'current' => $kb_config['section_head_padding_left'], 'text_class' => 'config-col-6' );
				$arg2_section_head_padding_horizontal = $feature_specs['section_head_padding_right'] + array( 'value' => $kb_config['section_head_padding_right'], 'current' => $kb_config['section_head_padding_right'], 'text_class' => 'config-col-6' );
				$arg1_box_border = $feature_specs['section_border_radius'] + array( 'value' => $kb_config['section_border_radius'], 'current' => $kb_config['section_border_radius'], 'text_class' => 'config-col-6' );
				$arg2_box_border = $feature_specs['section_border_width'] + array( 'value' => $kb_config['section_border_width'], 'current' => $kb_config['section_border_width'], 'text_class' => 'config-col-6' );

				if ( in_array( $kb_config['kb_main_page_layout'], array( 'Basic', 'Tabs', 'Categories' ) ) ) {
					$form->option_group_wizard( $feature_specs, array(
						'option-heading' => __( 'Categories', 'echo-knowledge-base' ),
						'class'        => 'eckb-mm-mp-links-tuning-categories-style eckb-wizard-accordion__body',
						'inputs' => array(
							'1' => $form->checkbox( $feature_specs['section_divider'] + array(
									'value'             => $kb_config['section_divider'],
									'input_group_class' => 'config-col-12',
									'label_class'       => 'config-col-5',
									'input_class'       => 'config-col-2'
								) ),
							'2' => $form->text( $feature_specs['section_divider_thickness'] + array(
									'value'             => $kb_config['section_divider_thickness'],
									'input_group_class' => 'config-col-12',
									'label_class'       => 'config-col-5',
									'input_class'       => 'config-col-2'
								) ),
							'3' => $form->dropdown( $feature_specs['section_box_shadow'] + array(
									'value'             => $kb_config['section_box_shadow'],
									'current'           => $kb_config['section_box_shadow'],
									'input_group_class' => 'config-col-12',
									'label_class'       => 'config-col-5',
									'input_class'       => 'config-col-6'
								) ),
							'4' => $form->multiple_number_inputs(
								array(
									'id'                => 'section_head_padding',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Section Head Padding( px )', 'echo-knowledge-base' ),
								),
								array( $arg1_section_head_padding_vertical, $arg2_section_head_padding_vertical, $arg1_section_head_padding_horizontal, $arg2_section_head_padding_horizontal  )
							),
							'5' => $form->multiple_number_inputs(
								array(
									'id'                => 'box_border',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Box Border ( px )', 'echo-knowledge-base' ),
								),
								array( $arg1_box_border, $arg2_box_border )
							)
						)
					));
				}
				
				if ( $kb_config['kb_main_page_layout'] == 'Tabs' ) {
					// TABS TOP CATEGORIES - Style
					$form->option_group_wizard( $feature_specs, array(
						'option-heading' => __( 'Categories - Tabs', 'echo-knowledge-base' ),
						'class'        => 'eckb-mm-mp-links-tuning-categories-style eckb-wizard-accordion__body',
						'inputs' => array(
							'0' => $form->dropdown( $feature_specs['tab_font_size'] + array(
									'value' => $kb_config['tab_font_size'], 'current' => $kb_config['tab_font_size'],
									'input_group_class' => 'config-col-12',
									'label_class'       => 'config-col-3',
									'input_class'       => 'config-col-4'
								) ),
							'1' => $form->checkbox( $feature_specs['tab_down_pointer'] + array(
									'value'             => $kb_config['tab_down_pointer'],
									'input_group_class' => 'config-col-12',
									'label_class'       => 'config-col-3',
									'input_class'       => 'config-col-4'
								) ),
						)
					));
				}
				
				// LIST OF ARTICLES - Advanced Style
				$arg1_section_body_padding_vertical = $feature_specs['section_body_padding_top'] + array( 'value' => $kb_config['section_body_padding_top'], 'current' => $kb_config['section_body_padding_top'], 'text_class' => 'config-col-6' );
				$arg2_section_body_padding_vertical = $feature_specs['section_body_padding_bottom'] + array( 'value' => $kb_config['section_body_padding_bottom'], 'current' => $kb_config['section_body_padding_bottom'], 'text_class' => 'config-col-6' );
				$arg1_section_body_padding_horizontal = $feature_specs['section_body_padding_left'] + array( 'value' => $kb_config['section_body_padding_left'], 'current' => $kb_config['section_body_padding_left'], 'text_class' => 'config-col-6' );
				$arg2_section_body_padding_horizontal = $feature_specs['section_body_padding_right'] + array( 'value' => $kb_config['section_body_padding_right'], 'current' => $kb_config['section_body_padding_right'], 'text_class' => 'config-col-6' );

				$article_spacing_arg1 = $feature_specs['article_list_margin'] +  array(
						'value'             => $kb_config['article_list_margin'],
						'id'                => 'article_list_margin',
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-3'
					);
				$article_spacing_arg2 = $feature_specs['article_list_spacing'] +  array(
						'value'             => $kb_config['article_list_spacing'],
						'id'                => 'article_list_spacing',
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-3'
					);
				
				if ( in_array( $kb_config['kb_main_page_layout'], array( 'Basic', 'Tabs', 'Categories' ) ) ) {
					$form->option_group_wizard( $feature_specs, array(
						'option-heading' => __( 'List of Articles', 'echo-knowledge-base' ),
						'class'        => 'eckb-mm-mp-links-tuning-listofarticles-advanced eckb-wizard-accordion__body',
						'inputs' => array(
							'0' => $form->checkbox( $feature_specs['section_article_underline'] + array(
									'value'             => $kb_config['section_article_underline'],
									'id'                => 'section_article_underline',
									'input_group_class' => 'config-col-12',
									'label_class'       => 'config-col-5',
									'input_class'       => 'config-col-2'
								) ),
							'1' => $form->multiple_number_inputs(
								array(
									'id'                => 'article_list_group',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Article Spacing ( px )', 'echo-knowledge-base' )
								),
								array( $article_spacing_arg1, $article_spacing_arg2 )
							),
							'2' => $form->multiple_number_inputs(
								array(
									'id'                => 'section_body_padding',
									'input_group_class' => '',
									'main_label_class'  => '',
									'input_class'       => '',
									'label'             => __( 'Section Body Padding( px )', 'echo-knowledge-base' )
								),
								array( $arg1_section_body_padding_vertical, $arg2_section_body_padding_vertical, $arg1_section_body_padding_horizontal, $arg2_section_body_padding_horizontal)
							),
						)
					));
				}
				
				do_action( 'epkb_advanced_config_after_main_page', $kb_config['id'], $kb_config );		?>
			</div>
		</div>		<?php
	}

	/**
	 * Displays advanced configuration for Article Page
	 * @param $kb_config
	 */
	private static function display_article_page_column( $kb_config ) {      ?>

		<div class="epkb-config-sidebar" id="epkb-config-main-setup-sidebar">
			<h2><?php _e( 'Article Page', 'echo-knowledge-base' ); ?></h2>
			<div class="epkb-config-sidebar-options  eckb-wizard-accordion">                        <?php


		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
		$form = new EPKB_KB_Config_Elements();
		
		// ARTICLE PAGE
		$arg_bn_padding_top = $feature_specs['templates_for_kb_article_padding_top'] + array(
			'value' => $kb_config['templates_for_kb_article_padding_top'],
			'current' => $kb_config['templates_for_kb_article_padding_top'],
			'text_class' => 'config-col-6'
		);

		$arg_bn_padding_bottom = $feature_specs['templates_for_kb_article_padding_bottom'] + array(
			'value' => $kb_config['templates_for_kb_article_padding_bottom'],
			'current' => $kb_config['templates_for_kb_article_padding_bottom'],
			'text_class' => 'config-col-6'
		);

		$arg_bn_padding_left   = $feature_specs['templates_for_kb_article_padding_left'] + array(
			'value' => $kb_config['templates_for_kb_article_padding_left'],
			'current' => $kb_config['templates_for_kb_article_padding_left'],
			'text_class' => 'config-col-6'
		);

		$arg_bn_padding_right  = $feature_specs['templates_for_kb_article_padding_right'] + array(
			'value' => $kb_config['templates_for_kb_article_padding_right'],
			'current' => $kb_config['templates_for_kb_article_padding_right'],
			'text_class' => 'config-col-6'
		);

		$arg_bn_margin_top    = $feature_specs['templates_for_kb_article_margin_top'] + array(
			'value' => $kb_config['templates_for_kb_article_margin_top'],
			'current' => $kb_config['templates_for_kb_article_margin_top'],
			'text_class' => 'config-col-6'
		);

		$arg_bn_margin_bottom = $feature_specs['templates_for_kb_article_margin_bottom'] + array(
			'value' => $kb_config['templates_for_kb_article_margin_bottom'],
			'current' => $kb_config['templates_for_kb_article_margin_bottom'],
			'text_class' => 'config-col-6'
		);

		$arg_bn_margin_left   = $feature_specs['templates_for_kb_article_margin_left'] + array(
			'value' => $kb_config['templates_for_kb_article_margin_left'],
			'current' => $kb_config['templates_for_kb_article_margin_left'],
			'text_class' => 'config-col-6'
		);

		$arg_bn_margin_right  = $feature_specs['templates_for_kb_article_margin_right'] + array(
			'value' => $kb_config['templates_for_kb_article_margin_right'],
			'current' => $kb_config['templates_for_kb_article_margin_right'],
			'text_class' => 'config-col-6'
		);
		
		if ( $kb_config['templates_for_kb'] == 'kb_templates' ) {
			// KB TEMPLATE
			$form->option_group_wizard( $feature_specs, array(
				'option-heading'    => __( 'KB Template', 'echo-knowledge-base' ),
				'class'             => 'eckb-mm-mp-links-setup-article-template eckb-wizard-accordion__body',
				'inputs'            => array(
					'0' => $form->multiple_number_inputs(
						array(
							'id'                => 'templates_for_kb_article_padding_group',
							'input_group_class' => '',
							'main_label_class'  => '',
							'input_class'       => '',
							'label'             => __( 'Template Padding( px )', 'echo-knowledge-base' ),
						),
						array( $arg_bn_padding_top, $arg_bn_padding_bottom, $arg_bn_padding_left, $arg_bn_padding_right )
					),
					'1' => $form->multiple_number_inputs(
						array(
							'id'                => 'templates_for_kb_article_margin_group',
							'input_group_class' => '',
							'main_label_class'  => '',
							'input_class'       => '',
							'label'             => __( 'Template Margin( px )', 'echo-knowledge-base' ),
						),
						array( $arg_bn_margin_top, $arg_bn_margin_bottom , $arg_bn_margin_left, $arg_bn_margin_right )
					),
				)
			));
		}
		$plugin_first_version = get_option( 'epkb_version_first' );
		if ( version_compare( $plugin_first_version, '6.4.0', '<' ) ) {
			$article_structure_version = $form->dropdown( $feature_specs['article-structure-version'] + array(
							'value'             =>$kb_config['article-structure-version'],
							'current'           =>$kb_config['article-structure-version'],
							'input_group_class' => 'config-col-12',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-5',
						) );
		} else {
			$article_structure_version = '';
		}
		
		if ( $kb_config['article-structure-version'] == 'version-1' ) {
			$kb_article_page_layout = $form->dropdown( $feature_specs['kb_article_page_layout'] + array(
								'value' => $kb_config['kb_article_page_layout'],
								'current' => $kb_config['kb_article_page_layout'],
								'input_group_class' => 'config-col-12',
								'label_class' => 'config-col-5',
								'input_class' => 'config-col-4'
							) );
			$padding1 = '';
			$padding2 = '';
			$padding3 = '';
		} else {
			$kb_article_page_layout = '';
			$padding1 = $form->text( $feature_specs['article-left-sidebar-padding-v2'] +
							array( $kb_config['article-left-sidebar-padding-v2'],
								'value'             => $kb_config['article-left-sidebar-padding-v2'],
								'input_group_class' => 'config-col-12',
								'label_class'       => 'config-col-5',
								'input_class'       => 'config-col-5'
							) );
			$padding2 =  $form->text( $feature_specs['article-content-padding-v2'] +
							array( $kb_config['article-content-padding-v2'],
								'value'             => $kb_config['article-content-padding-v2'],
								'input_group_class' => 'config-col-12',
								'label_class'       => 'config-col-5',
								'input_class'       => 'config-col-5'
							) );
			$padding3 = $form->text( $feature_specs['article-right-sidebar-padding-v2'] +
							array( $kb_config['article-right-sidebar-padding-v2'],
								'value'             => $kb_config['article-right-sidebar-padding-v2'],
								'input_group_class' => 'config-col-12',
								'label_class'       => 'config-col-5',
								'input_class'       => 'config-col-5'
							) );
		}
		
		$form->option_group_wizard( $feature_specs, array(
					'option-heading'    => __( 'Layout Mode', 'echo-knowledge-base' ),
					'class'             => 'eckb-mm-ap-links-features-features-layout-mode eckb-wizard-accordion__body',
					'inputs'            => array(
						'0' => $kb_article_page_layout,
						'1' => $article_structure_version,
						'6' => $padding1,
						'7' => $padding2,
						'8' => $padding3,
					)
				));
		
		if ( $kb_config['templates_for_kb'] == 'kb_templates' ) {
			// RESETS
			$form->option_group_wizard( $feature_specs, array(
				'option-heading'    => __( 'CSS', 'echo-knowledge-base' ),
				'class'             => 'eckb-mm-mp-links-setup-main-template eckb-wizard-accordion__body',
				'inputs'            => array(
					'0' => $form->checkbox( $feature_specs['templates_for_kb_article_reset'] + array(
							'value'             => $kb_config['templates_for_kb_article_reset'],
							'id'                => 'templates_for_kb_article_reset',
							'input_group_class' => 'config-col-12',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-2'
						) ),
					'1' => $form->checkbox( $feature_specs['templates_for_kb_article_defaults'] + array(
							'value'             => $kb_config['templates_for_kb_article_defaults'],
							'id'                => 'templates_for_kb_article_defaults',
							'input_group_class' => 'config-col-12',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-2'
						) ),
					'2' => $form->checkbox( $feature_specs['templates_for_kb_widget_sidebar_defaults'] + array(
							'value'             => $kb_config['templates_for_kb_widget_sidebar_defaults'],
							'id'                => 'templates_for_kb_widget_sidebar_defaults',
							'input_group_class' => 'config-col-12',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-2'
						) )    
				)
			));
		}
		// FEATURES - Breadcrumb
				
		// FEATURES - Breadcrumb - Advanced
		$arg_bc_top1 = $feature_specs['breadcrumb_padding_top'] + array( 'value' => $kb_config['breadcrumb_padding_top'], 'current' => $kb_config['breadcrumb_padding_top'], 'text_class' => 'config-col-6' );
		$arg_bc_btm2 = $feature_specs['breadcrumb_padding_bottom'] + array( 'value' => $kb_config['breadcrumb_padding_bottom'], 'current' => $kb_config['breadcrumb_padding_bottom'], 'text_class' => 'config-col-6' );
		$arg_bc_left3 = $feature_specs['breadcrumb_padding_left'] + array( 'value' => $kb_config['breadcrumb_padding_left'], 'current' => $kb_config['breadcrumb_padding_left'], 'text_class' => 'config-col-6' );
		$arg_bc_right4 = $feature_specs['breadcrumb_padding_right'] + array( 'value' => $kb_config['breadcrumb_padding_right'], 'current' => $kb_config['breadcrumb_padding_right'], 'text_class' => 'config-col-6' );

		//Breadcrumb: Margin
		$arg_bc_margin_top      = $feature_specs['breadcrumb_margin_top'] + array( 'value' => $kb_config['breadcrumb_margin_top'], 'current' => $kb_config['breadcrumb_margin_top'], 'text_class' => 'config-col-6' );
		$arg_bc_margin_bottom   = $feature_specs['breadcrumb_margin_bottom'] + array( 'value' => $kb_config['breadcrumb_margin_bottom'], 'current' => $kb_config['breadcrumb_margin_bottom'], 'text_class' => 'config-col-6' );
		$arg_bc_margin_left     = $feature_specs['breadcrumb_margin_left'] + array( 'value' => $kb_config['breadcrumb_margin_left'], 'current' => $kb_config['breadcrumb_margin_left'], 'text_class' => 'config-col-6' );
		$arg_bc_margin_right    = $feature_specs['breadcrumb_margin_right'] + array( 'value' => $kb_config['breadcrumb_margin_right'], 'current' => $kb_config['breadcrumb_margin_right'], 'text_class' => 'config-col-6' );
				
		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Breadcrumb', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-ap-links-features-features-breadcrumb eckb-wizard-accordion__body',
			'inputs'            => array(
				'1' => $form->dropdown( $feature_specs['breadcrumb_font_size'] + array(
						'value' => $kb_config['breadcrumb_font_size'],
						'current' => $kb_config['breadcrumb_font_size'],
						'input_group_class' => 'config-col-12',
						'label_class' => 'config-col-5',
						'input_class' => 'config-col-4'
					) ),
				'2' => $form->multiple_number_inputs(
					array(
						'id'                => 'breadcrumb_padding_group',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Padding( px )', 'echo-knowledge-base' )
					),
					array( $arg_bc_top1, $arg_bc_btm2, $arg_bc_left3, $arg_bc_right4 )
				),
				'3' => $form->multiple_number_inputs(
					array(
						'id'                => 'breadcrumb_margin_group',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Margin( px )', 'echo-knowledge-base' )
					),
					array( $arg_bc_margin_top, $arg_bc_margin_bottom, $arg_bc_margin_left, $arg_bc_margin_right )
				),
			)
		));
		
		if ( ! EPKB_Articles_Setup::is_article_structure_v2( $kb_config ) ) {
			// FEATURES - TOC - ADVANCED
			$form->option_group_wizard( $feature_specs, array(
				'option-heading'    => __( 'Table of Contents', 'echo-knowledge-base' ),
				'class'             => 'eckb-mm-ap-links-features-features-articletoc eckb-wizard-accordion__body',
				'inputs'            => array(
					'1' => $form->text( $feature_specs['article_toc_width_1'] + array(
							'value'             => $kb_config['article_toc_width_1'],
							'input_group_class' => 'config-col-12',
							'class'             => 'ekb-color-picker',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-5'
						) ),
					'2' => $form->text( $feature_specs['article_toc_media_1'] + array(
							'value'             => $kb_config['article_toc_media_1'],
							'input_group_class' => 'config-col-12',
							'class'             => 'ekb-color-picker',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-5'
						) ),
					'3' => $form->text( $feature_specs['article_toc_width_2'] + array(
							'value'             => $kb_config['article_toc_width_2'],
							'input_group_class' => 'config-col-12',
							'class'             => 'ekb-color-picker',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-5'
						) ),
					'4' => $form->text( $feature_specs['article_toc_media_2'] + array(
							'value'             => $kb_config['article_toc_media_2'],
							'input_group_class' => 'config-col-12',
							'class'             => 'ekb-color-picker',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-5'
						) ),
					'5' => $form->text( $feature_specs['article_toc_media_3'] + array(
							'value'             => $kb_config['article_toc_media_3'],
							'input_group_class' => 'config-col-12',
							'class'             => 'ekb-color-picker',
							'label_class'       => 'config-col-5',
							'input_class'       => 'config-col-5'
						) ),
				)
			));
		}
		
		// FEATURES - Back Navigation
		$arg_bn_padding_top    = $feature_specs['back_navigation_padding_top'] + array( 'value' => $kb_config['back_navigation_padding_top'], 'current' => $kb_config['back_navigation_padding_top'], 'text_class' => 'config-col-6' );
		$arg_bn_padding_bottom = $feature_specs['back_navigation_padding_bottom'] + array( 'value' => $kb_config['back_navigation_padding_bottom'], 'current' => $kb_config['back_navigation_padding_bottom'], 'text_class' => 'config-col-6' );
		$arg_bn_padding_left   = $feature_specs['back_navigation_padding_left'] + array( 'value' => $kb_config['back_navigation_padding_left'], 'current' => $kb_config['back_navigation_padding_left'], 'text_class' => 'config-col-6' );
		$arg_bn_padding_right  = $feature_specs['back_navigation_padding_right'] + array( 'value' => $kb_config['back_navigation_padding_right'], 'current' => $kb_config['back_navigation_padding_right'], 'text_class' => 'config-col-6' );

		$arg_bn_margin_top    = $feature_specs['back_navigation_margin_top'] + array( 'value' => $kb_config['back_navigation_margin_top'], 'current' => $kb_config['back_navigation_margin_top'], 'text_class' => 'config-col-6' );
		$arg_bn_margin_bottom = $feature_specs['back_navigation_margin_bottom'] + array( 'value' => $kb_config['back_navigation_margin_bottom'], 'current' => $kb_config['back_navigation_margin_bottom'], 'text_class' => 'config-col-6' );
		$arg_bn_margin_left   = $feature_specs['back_navigation_margin_left'] + array( 'value' => $kb_config['back_navigation_margin_left'], 'current' => $kb_config['back_navigation_margin_left'], 'text_class' => 'config-col-6' );
		$arg_bn_margin_right  = $feature_specs['back_navigation_margin_right'] + array( 'value' => $kb_config['back_navigation_margin_right'], 'current' => $kb_config['back_navigation_margin_right'], 'text_class' => 'config-col-6' );

		$form->option_group_wizard( $feature_specs, array(
			'option-heading'    => __( 'Back Navigation', 'echo-knowledge-base' ),
			'class'             => 'eckb-mm-ap-links-features-features-backnavigation eckb-wizard-accordion__body',
			'inputs'            => array(
				'0' => $form->radio_buttons_vertical( $feature_specs['back_navigation_border'] + array(
						'value'             => $kb_config['back_navigation_border'],
						'current'           => $kb_config['back_navigation_border'],
						'input_group_class' => 'config-col-12',
						'main_label_class'  => 'config-col-4',
						'input_class'       => 'config-col-8',
						'radio_class'       => 'config-col-12'
					) ),
				'1' => $form->text( $feature_specs['back_navigation_border_radius'] + array(
						'value' => $kb_config['back_navigation_border_radius'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5'
					) ),
				'2' => $form->text( $feature_specs['back_navigation_border_width'] + array(
						'value' => $kb_config['back_navigation_border_width'],
						'input_group_class' => 'config-col-12',
						'label_class'       => 'config-col-5',
						'input_class'       => 'config-col-5'
					) ),
				'3' => $form->multiple_number_inputs(
					array(
						'id'                => 'back_navigation_padding_group',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Padding( px )', 'echo-knowledge-base' )
					),
					array( $arg_bn_padding_top, $arg_bn_padding_bottom, $arg_bn_padding_left, $arg_bn_padding_right )
				),
				'4' => $form->multiple_number_inputs(
					array(
						'id'                => 'back_navigation_margin_group',
						'input_group_class' => '',
						'main_label_class'  => '',
						'input_class'       => '',
						'label'             => __( 'Margin( px )', 'echo-knowledge-base' )
					),
					array( $arg_bn_margin_top, $arg_bn_margin_bottom, $arg_bn_margin_left, $arg_bn_margin_right )
				)
			)
		));
		
		do_action( 'epkb_advanced_config_after_article_page', $kb_config['id'], $kb_config );	

		if ( $kb_config['article-structure-version'] == 'version-2' ) { ?>
			<a href="#" id="show-old-config"><?php _e('Use old configurator (not recommended)', 'echo-knowledge-base'); ?></a><?php
		}		?>
			</div>
		</div>		<?php
	}

	/**
	 * This configuration defines fields that are part of this wizard configuration related to text.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	public static $advanced_fields = array(
		
		// ARTICLE PAGE
		'kb_article_page_layout',
		'article-structure-version',
		'article-left-sidebar-padding-v2',
		'article-content-padding-v2',
		'article-right-sidebar-padding-v2',
		'templates_for_kb_padding_top',
		'templates_for_kb_padding_bottom',
		'templates_for_kb_padding_left',
		'templates_for_kb_padding_right',
		'templates_for_kb_margin_top',
		'templates_for_kb_margin_bottom',
		'templates_for_kb_margin_left',
		'templates_for_kb_margin_right',
		'templates_for_kb_article_reset',
		'templates_for_kb_article_defaults',
		'templates_for_kb_padding_group',
		'templates_for_kb_margin_group',
		'templates_for_kb_widget_sidebar_defaults',
		'templates_for_kb_article_padding_top',
		'templates_for_kb_article_padding_bottom',
		'templates_for_kb_article_padding_left',
		'templates_for_kb_article_padding_right',
		'templates_for_kb_article_margin_top',
		'templates_for_kb_article_margin_bottom',
		'templates_for_kb_article_margin_left',
		'templates_for_kb_article_margin_right',
		
		// SEARCH BOX
		'search_box_padding_top',
		'search_box_padding_bottom',
		'search_box_padding_left',
		'search_box_padding_right',
		'search_box_margin_top',
		'search_box_margin_bottom',
		'search_input_border_width',
		'search_box_results_style',
		
		// CATEGORIES - Style
		'section_head_padding_top',
		'section_head_padding_bottom',
		'section_head_padding_left',
		'section_head_padding_right',
		'section_border_radius',
		'section_border_width',
		'section_divider',
		'section_divider_thickness',
		'section_box_shadow',
		
		// TABS TOP CATEGORIES - Style
		'tab_font_size',
		'tab_down_pointer',
		
		// LIST OF ARTICLES - Advanced Style
		'section_body_padding_top',
		'section_body_padding_bottom',
		'section_body_padding_left',
		'section_body_padding_right',
		'article_list_margin',
		'article_list_spacing',
		'section_article_underline',
		
		// FEATURES - Breadcrumb - Advanced
		'breadcrumb_padding_top',
		'breadcrumb_padding_bottom',
		'breadcrumb_padding_left',
		'breadcrumb_padding_right',
		
		//Breadcrumb: Margin
		'breadcrumb_margin_top',
		'breadcrumb_margin_bottom',
		'breadcrumb_margin_left',
		'breadcrumb_margin_right',
		'breadcrumb_font_size',
		
		// FEATURES - TOC - ADVANCED
		'article_toc_width_1',
		'article_toc_media_1',
		'article_toc_width_2',
		'article_toc_media_2',
		'article_toc_media_3',
		
		// FEATURES - Back Navigation
		'back_navigation_padding_top',
		'back_navigation_padding_bottom',
		'back_navigation_padding_left',
		'back_navigation_padding_right',
		'back_navigation_margin_top',
		'back_navigation_margin_bottom',
		'back_navigation_margin_left',
		'back_navigation_margin_right',
		'back_navigation_border',
		'back_navigation_border_radius',
		'back_navigation_border_width',
		
		// Elegant Layouts - SIDEBAR
		'sidebar_search_box_padding_top',
		'sidebar_search_box_padding_bottom',
		'sidebar_search_box_padding_left',
		'sidebar_search_box_padding_right',
		'sidebar_search_box_margin_top',
		'sidebar_search_box_margin_bottom',
		'sidebar_search_box_results_style',
		'sidebar_section_body_padding_top',
		'sidebar_section_body_padding_bottom',
		'sidebar_section_body_padding_left',
		'sidebar_section_body_padding_right',
		'sidebar_article_list_margin',
		'sidebar_article_list_spacing',
		'sidebar_article_underline',
		'sidebar_article_active_bold',
		'sidebar_section_head_padding_top',
		'sidebar_section_head_padding_bottom',
		'sidebar_section_head_padding_left',
		'sidebar_section_head_padding_right',
		'sidebar_section_border_radius',
		'sidebar_section_border_width',
		'sidebar_section_box_shadow',

		// Elegant Layouts - GRID
		'grid_search_box_padding_top',
		'grid_search_box_padding_bottom',
		'grid_search_box_padding_left',
		'grid_search_box_padding_right',
		'grid_search_box_margin_top',
		'grid_search_box_margin_bottom',
		'grid_search_box_results_style',
		'grid_section_border_radius',
		'grid_section_border_width',
		'grid_section_head_padding_top',
		'grid_section_head_padding_bottom',
		'grid_section_head_padding_left',
		'grid_section_head_padding_right',
		'grid_section_body_padding_top',
		'grid_section_body_padding_bottom',
		'grid_section_body_padding_left',
		'grid_section_body_padding_right',
		'grid_section_cat_name_padding_top',
		'grid_section_cat_name_padding_bottom',
		'grid_section_cat_name_padding_left',
		'grid_section_cat_name_padding_right',
		'grid_section_icon_padding_top',
		'grid_section_icon_padding_bottom',
		'grid_section_icon_padding_left',
		'grid_section_icon_padding_right',
		'grid_section_desc_padding_top',
		'grid_section_desc_padding_bottom',
		'grid_section_desc_padding_left',
		'grid_section_desc_padding_right',
		'grid_section_box_hover',
		'grid_section_box_shadow',
		'grid_section_divider',
		'grid_section_divider_thickness',
		
		// Rating and Feedback 
		'rating_element_size',
		'rating_text_font_size',
	);
}
