<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Overview information that is displayed with KB Configuration page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Overview {

	/**
	 * Display Overview Page
	 *
	 * @param $kb_config
	 * @param $feature_specs
	 * @param EPKB_KB_Config_Elements $form
	 */
	public static function display_overview( $kb_config, $feature_specs, $form ) {

		$kb_id = $kb_config['id'];

		$config_elements = new EPKB_KB_Config_Elements();

		$kb_main_pages_url = '';
		$kb_main_pages_info = EPKB_KB_Handler::get_kb_main_pages( $kb_config );
		foreach( $kb_main_pages_info as $post_id => $post_info ) {
			$post_status = $post_info['post_status'] == EPKB_Utilities::get_post_status_text( 'publish' ) ? '' : ' (' . $post_info['post_status'] . ')';
			$kb_main_pages_url .= '  <li>' .	$post_info['post_title'] . $post_status . ' &nbsp;&nbsp;';
			$main_page_view_url = get_permalink( $post_id );
			$kb_main_pages_url .= '<a href="' . ( empty($main_page_view_url) || is_wp_error( $main_page_view_url ) ? '' : $main_page_view_url ) . '" target="_blank">' . __( 'View', 'echo-knowledge-base' ) . '</a> ';
			$post_link = get_edit_post_link( $post_id );
			$kb_main_pages_url .= ' &nbsp;&nbsp;<a href="' . ( empty($post_link) ? '' : $post_link ) . '" target="_blank">' . __( 'Edit', 'echo-knowledge-base' ) . '</a></li>';
		}

		$kb_main_pages_url = empty($kb_main_pages_url) ? ' ' . __( 'None found', 'echo-knowledge-base' ) : $kb_main_pages_url;

		$wpml_value = EPKB_Utilities::is_wpml_enabled( epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id ) ) ? 'on' : '';
		$form = new EPKB_HTML_Elements();


		/***  Errors  ***/

		// LICENSE / ADD-ON issues
		$add_on_messages = apply_filters( 'epkb_add_on_license_message', array() );
		if ( ( ! empty($add_on_messages) && is_array($add_on_messages) ) || did_action('kb_overview_add_on_errors' ) ) {        ?>

		    <section id="epkb-overview-section-errors" class="overview-info-section">
	            <div class="overview-error">
	                <div class="overview-header">
	                    <div class="overview-title"><?php _e( 'Errors', 'echo-knowledge-base' ); ?></div>
	                </div>
	                <div class="overview-content">
		                <div class="epkb-overview-row epkb-flex-space-between epkb-flex-align-items-stretch">			                <?php
			                if ( ! empty($add_on_messages) && is_array($add_on_messages) ) {
				                foreach( $add_on_messages as $add_on_name => $add_on_message ) {
					                $add_on_name = str_replace( array('2', '3', '4'), '', $add_on_name );

					                self::display_overview_box( array (
						                'option_classes' => array(
							                'epkb-overview-box-icon-location-top',
							                'epkb-overview-box-border-all',
							                'epkb-overview-box-margin',
							                'epkb-overview-box-red'
						                ),
						                'icon_class'    => 'epkbfa-exclamation-circle',
						                'title'         => esc_html($add_on_name) . ': ' . __( 'License issue', 'echo-knowledge-base' ),
						                'content'       => wp_kses_post( $add_on_message )
					                ) );
				                }
			                }
			                do_action('kb_overview_add_on_errors' );		                ?>
		                </div>
	                </div>
	            </div>
	        </section>      <?php
		}

		/***  Warnings  ***/

		$messages = self::get_kb_status( $kb_config );
		$is_status_error = ! empty($messages);
		$class_status = $is_status_error ? 'overview-warning' : 'overview-success';

		if ( $is_status_error ) {   ?>
			<!-- Warnings -------------------------------------------------->
	        <section id="epkb-overview-section-warnings" class="overview-info-section">
	            <div class="<?php echo $class_status; ?>">
	                <div class="overview-header">
	                <div class="overview-title"><?php _e( 'Warnings', 'echo-knowledge-base' ); ?></div>
	            </div>
	                <div class="overview-content">											
						<p class="note_type_1">     <?php
							/*	echo __( "NOTE: It is OK to see warnings if you are creating and updating your knowledge base. After you're done, you should see " .
									"no warnings. Warnings indicate that your users might come accross empty categories, that they might not see certain " .
									"articles or they might otherwise have less-than-optimal experience.", 'echo-knowledge-base' );    */ ?>
							</p>
							<div class="epkb-overview-row epkb-flex-stretch epkb-flex-align-items-stretch">							<?php
								foreach( $messages as $message ) {
									self::display_overview_box( array(
										'option_classes' => array(
											'epkb-overview-box-icon-location-top',
											'epkb-overview-box-border-all',
											'epkb-overview-box-orange',
											'epkb-overview-box-margin',
											'epkb-overview-box-width-one-third'
										),
										'icon_class'     => 'epkbfa-exclamation-triangle',
										'title'          => __( 'Potential Content Issue', 'echo-knowledge-base' ),
										'content'        => $message,
									) );
								}               ?>
							</div>
	                </div>
	            </div>
	        </section>		<?php
		}   ?>

        <!-- Configuration --------------------------------------------->
        <section id="epkb-overview-section-config" class="overview-info-section">
            <div class="overview-config">
                <div class="overview-header">
                    <div class="overview-title"><?php _e( 'General', 'echo-knowledge-base' ); ?></div>
                </div>
                <div class="overview-content">

	                <div class="epkb-overview-row epkb-flex-stretch epkb-flex-align-items-stretch">		                <?php
		                self::display_overview_box( array(
			                'option_classes' => array(
				                'epkb-overview-box-icon-location-left',
				                'epkb-overview-box-center-align',
				                'epkb-overview-box-green',
				                'epkb-overview-box-margin',
				                'epkb-overview-box-width-one-third'
			                ),
			                'icon_class'    => 'epkbfa-cogs',
			                'title'         => __( 'KB Main Page', 'echo-knowledge-base' ),
			                'content'       =>
				                '<p>' . __( 'To display a <strong>Knowledge Base Main page</strong>, add the following KB shortcode to any page:', 'echo-knowledge-base' ) . '</p><br />'.
				                '<p style="color:#5cb85c;"><strong> ['.EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME . ' id=' . $kb_id.']</strong></p><br />'.
				                '<p><strong>' . __( 'Existing KB Main Page(s):', 'echo-knowledge-base' ) . '</strong></p>'.
				                '<ul class="epkb-overview-box-form-list">'.wp_kses_post( $kb_main_pages_url ).'</ul>',
		                ) );

		                if ( EPKB_KB_Wizard::is_wizard_disabled() ) {

			                self::display_overview_box( array(
				                'option_classes' => array(
					                'epkb-overview-box-icon-location-left',
					                'epkb-overview-box-center-align',
					                'epkb-overview-box-border-right',
					                'epkb-overview-box-border-left',
					                'epkb-overview-box-green',
					                'epkb-overview-box-margin',
					                'epkb-overview-box-width-one-third'
				                ),
				                'form_id'        => 'epkb-config-config3',
				                'icon_class'     => 'epkbfa-cogs',
				                'title'          => __( 'KB Name', 'echo-knowledge-base' ),
				                'content'        =>
					                '<ul class="epkb-overview-box-form-list">' .
					                $form->text( array(
						                             'value'             => $kb_config['kb_name'],
						                             'input_group_class' => '',//
						                             'label_class'       => '',//config-col-3
						                             'input_class'       => ''//config-col-9
					                             ) + $feature_specs['kb_name'], true ) .
					                '<li>' . $config_elements->submit_button( array(
						                'label'       => __( 'Update', 'echo-knowledge-base' ),
						                'id'          => 'epkb_save_dashboard',
						                'main_class'  => 'epkb_save_dashboard',
						                'action'      => 'epkb_save_dashboard',
						                'input_class' => 'epkb-info-settings-button primary-btn'
					                ), true ) .
					                $config_elements->submit_button( array(
						                'label'       => __( 'Cancel', 'echo-knowledge-base' ),
						                'id'          => 'epkb_cancel_dashboard',
						                'main_class'  => 'epkb_cancel_dashboard',
						                'action'      => 'epkb_cancel_dashboard',
						                'input_class' => 'epkb-info-settings-button error-btn',
					                ), true ) . '</li>' .
					                '</ul>'

			                ) );

			                self::display_overview_box( array(
				                'option_classes' => array(
					                'epkb-overview-box-icon-location-left',
					                'epkb-overview-box-center-align',
					                'epkb-overview-box-green',
					                'epkb-overview-box-margin',
					                'epkb-overview-box-width-one-third'
				                ),
				                'form_id'       => 'epkb-wpml-enabled-config',
				                'icon_class'    => 'epkbfa-cogs',
				                'title'         => 'WPML',
				                'content'       =>
					                $form->checkbox( array(
						                'label'       => __( 'WPML Enabled', 'echo-knowledge-base' ),
						                'name'        => 'epkb_wpml_is_enabled',
						                'type'        => EPKB_Input_Filter::CHECKBOX,
						                'label_class' => '',//col-4
						                'input_class' => '',//col-4
						                'value'       => $wpml_value
					                ), true ) .
					                $config_elements->submit_button( array(
						                'label'             => __( 'Save', 'echo-knowledge-base' ),
						                'id'                => 'epkb_save_wpml_settings',
						                'main_class'        => 'epkb_save_wpml_settings',
						                'action'            => 'epkb_save_wpml_settings',
						                'input_class'       => 'epkb-info-settings-button primary-btn'
					                ) , true ) .
					               // $config_elements->submit_button( __( 'Save', 'echo-knowledge-base' ), 'epkb_save_wpml_settings','','','',true ).
			                '<br /><a href="https://www.echoknowledgebase.com/documentation/setup-wpml-for-knowledge-base/" target="_blank">' . __( 'WPML Setup for Knowledge Base documentation', 'echo-knowledge-base' ) . '</a> ',
			                ) );
		                }		                ?>
	                </div>
                </div>
            </div>
        </section>

		<!-- Resources ------------------------------------------------->
		<section id="epkb-overview-section-resources" class="overview-info-section">
			<div class="overview-config">
				<div class="overview-header">
					<div class="overview-title"><?php _e( 'Resources', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="overview-content">
					<div class="epkb-overview-row epkb-flex-stretch epkb-flex-align-items-stretch">						<?php
						self::display_overview_box( array(
							'option_classes' => array(
								'epkb-overview-box-icon-location-left',
								'epkb-overview-box-border-right',
								'epkb-overview-box-purple',
								'epkb-overview-box-width-one-third'
							),
							'icon_class'        => 'epkbfa-book',
							'title'             => __( 'KB Documentation', 'echo-knowledge-base' ),
							'content'           => '<p>' . __( 'Comprehensive documentation for configuring and using Echo Knowledge Base plugin.', 'echo-knowledge-base' ) . '</p>',
							'btn_text_1'          => __( 'Read Documentation', 'echo-knowledge-base' ),
							'btn_url_1'           => 'https://www.echoknowledgebase.com/documentation/',
							'show_box_spacer'   => true,
							'box_spacer_loc'    => 'show-box-spacer-top-right'
						) );
						self::display_overview_box( array (
							'option_classes' => array(
								'epkb-overview-box-icon-location-left',
								'epkb-overview-box-border-right',
								'epkb-overview-box-purple',
								'epkb-overview-box-width-one-third'
							),
							'icon_class'    => 'epkbfa-life-ring',
							'title'         => __( 'Need Some Help?', 'echo-knowledge-base' ),
							'content'       => '<p>' . __( 'If you encounter an issue or have a question, please submit your request below.', 'echo-knowledge-base' ) . '</p>',
							'btn_text_1'      => __( 'Contact Us', 'echo-knowledge-base' ),
							'btn_url_1'       => 'https://www.echoknowledgebase.com/contact-us/?inquiry-type=technical&plugin_type=knowledge-base',
							'show_box_spacer'   => true,
							'box_spacer_loc'    => 'show-box-spacer-top-right'
						) );
						self::display_overview_box( array(
							'option_classes' => array(
								'epkb-overview-box-icon-location-left',
								'epkb-overview-box-purple',
								'epkb-overview-box-width-one-third'
							),
							'icon_class'    => 'epkbfa-newspaper-o',
							'title'         => __( 'Newsletter', 'echo-knowledge-base' ),
							'content'       => '<p>' . sprintf( __( 'Subscribe to our %s Newsletter%s to learn about new features, receive special offers and more.', 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/subscribe-to-our-newsletter/" target="_blank" rel="noopener">', '</a>' ) . '</p>',
							'btn_text_1'      => __( 'Read More', 'echo-knowledge-base' ),
							'btn_url_1'       => 'https://www.echoknowledgebase.com/subscribe-to-our-newsletter/',
						) );    ?>
					</div>

					<div class="epkb-overview-row epkb-flex-stretch epkb-flex-align-items-stretch">     <?php
						self::display_overview_box( array(
							'option_classes' => array(
								'epkb-overview-box-icon-location-left',
								'epkb-overview-box-border-right',
								'epkb-overview-box-border-top',
								'epkb-overview-box-purple',
								'epkb-overview-box-width-one-third'
							),
							'icon_class'        => 'epkbfa-star',
							'title'             => __( 'Feedback', 'echo-knowledge-base' ),
							'content'           => __( 'Let us know if you are missing a feature that you would like to see.', 'echo-knowledge-base' ),
							'btn_text_1'          => __( 'Feature Request', 'echo-knowledge-base' ),
							'btn_url_1'           => 'https://www.echoknowledgebase.com/feature-request/',
							'show_box_spacer'   => true,
							'box_spacer_loc'    => 'show-box-spacer-top-right'
						) );
						self::display_overview_box( array (
							'option_classes' => array(
								'epkb-overview-box-icon-location-left',
								'epkb-overview-box-border-right',
								'epkb-overview-box-border-top',
								'epkb-overview-box-purple',
								'epkb-overview-box-width-one-third'
							),
							'icon_class'    => 'epkbfa-map-signs',
							'title'         => __( 'Getting Started', 'echo-knowledge-base' ),
							'content'       => '<p>' . __( 'Learn the basic structure of KB and how to get started.', 'echo-knowledge-base' ) . '</p>',
							'btn_text_1'      => __( 'Getting Started', 'echo-knowledge-base' ),
							'btn_url_1'       => 'https://www.echoknowledgebase.com/documentation/getting-started/',
							'show_box_spacer'   => true,
							'box_spacer_loc'    => 'show-box-spacer-top-right'
						) );
						self::display_overview_box( array(
							'option_classes' => array(
								'epkb-overview-box-icon-location-left',
								'epkb-overview-box-border-top',
								'epkb-overview-box-purple',
								'epkb-overview-box-width-one-third'
							),
							'icon_class'    => 'epkbfa-globe',
							'title'         => __( 'Visit Our Website', 'echo-knowledge-base' ),
							'content'       => __( 'Check out the Echo Knowledge Base website to find KB documentation, demo layouts, blog updates, and more.', 'echo-knowledge-base' ),
							'btn_text_1'      => __( 'Visit', 'echo-knowledge-base' ),
							'btn_url_1'       => 'https://www.echoknowledgebase.com/',
						) );					?>

					</div>
				</div>
			</div>
		</section>		<?php
	}

	/**
	 * Return KB status line
	 *
	 * @param $kb_config
	 * @param $chosen_layout - layout user just switched to or empty
	 * @return string
	 */
	public static function get_kb_status_line( $kb_config, $chosen_layout='' ) {

		$status = self::get_kb_status_code( $kb_config, $chosen_layout );
		$status_tab_url = 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-kb-configuration';
		$status_class = $status == 'OK' ? 'kb_status_success' : ( $status == 'Warning' ? 'kb_status_warning' : 'kb_status_error error_pulse' );

		$output = '<div id="status_line" class="kb_status ' . $status_class . '">';

		$status_msg = '';
		switch( $status ) {
			case 'OK':
				$status_msg = __( 'KB Status: OK', 'echo-knowledge-base' );
				break;
			case 'Warning':
				$status_msg = __( 'KB Status: OK', 'echo-knowledge-base' );
				break;
			case 'ERROR':
				$status_msg = __( 'KB Status: Error', 'echo-knowledge-base' );
				break;
		}

		$output .= '<strong>' . esc_html( $status_msg ) . '</strong>';

		if ( $status != 'OK' ) {
			$output .= " - <strong><a href='$status_tab_url'>" . esc_html__( 'Learn More', 'echo-knowledge-base' ) . "</a></strong>";
		}

		$output .= '</div>';

		return $output;
	}

	private static function get_kb_status_code( $kb_config, $chosen_layout ) {
		$add_on_messages = apply_filters( 'epkb_add_on_license_message', array() );
		if ( ! empty($add_on_messages) ) {
			return 'ERROR';
		}

		$warning_msg = self::get_kb_status( $kb_config, $chosen_layout );
		if ( ! empty($warning_msg) ) {
			return 'Warning';
		}

		return 'OK';
	}

	/**
	 * Show status of current Knowledge Base
	 *
	 * @param $kb_config
	 * @param string $chosen_layout - layout user just switched to or empty
	 * @param array $articles_seq_data
	 * @param array $category_seq_data
	 * @return array
	 */
	private static function get_kb_status( $kb_config, $chosen_layout='', $articles_seq_data=array(), $category_seq_data=array() ) {

		$message = array();
		$kb_id = $kb_config['id'];
		$current_layout =  empty($chosen_layout) ? EPKB_KB_Config_Layouts::get_kb_main_page_layout_name( $kb_config ) : $chosen_layout;

		// 1. ensure we have KB pages with KB shortcode
		$kb_main_pages = $kb_config['kb_main_pages'];
		$kb_main_page_found = false;
		foreach( $kb_main_pages as $post_id => $post_title ) {
			$post_status = get_post_status( $post_id );
			if ( ! empty($post_status) && in_array( $post_status, array( 'publish', 'future', 'private' ) ) ) {
				$kb_main_page_found = true;
				break;
			}
		}

		if ( ! $kb_main_page_found ) {
			/* translators: refers to Knowledge Base main page that shows all links to articles */
			$i18_KB_Main = '<strong>' . esc_html__( 'Knowledge Base Main', 'echo-knowledge-base' ) . '</strong>';
			$i18_KB_shortcode = '<strong>' . esc_html__( 'KB shortcode', 'echo-knowledge-base' ) . '</strong>';
			/* translators: first %s will be replaced with the word 'Knowledge Base Main' (in bold) and the second %s will be replaced with 'KB shortcode' (also in bold). */
			$message[] = '<div class="status_group"><p>' .
			            sprintf( __( 'Did not find active %s page. Only page with %s will display KB Main page. If you do have a KB shortcode on a page, ' .
			                         'save that page and this message should disappear.', 'echo-knowledge-base' ), $i18_KB_Main, $i18_KB_shortcode ) . '</p></div>';
		}

		$i18_articles = '<strong>' . esc_html__( 'articles', 'echo-knowledge-base' ) . '</strong>';
		$i18_edit_word = esc_html__( 'Edit', 'echo-knowledge-base' );
		$i18_category = '<strong>' . esc_html__(  _x( 'category', 'taxonomy singular name', 'echo-knowledge-base' ), 'echo-knowledge-base' ) . '</strong>';

		// 2. check orphan articles
		$article_db = new EPKB_Articles_DB();
		$orphan_articles = $article_db->get_orphan_published_articles( $kb_id );
		if ( ! empty($orphan_articles) ) {
			$temp = '';
			foreach( $orphan_articles as $orphan_article ) {
				$temp = '<li>' . $orphan_article->post_title . ' &nbsp;&nbsp;' . '<a href="' .  get_edit_post_link( $orphan_article->ID ) . '" target="_blank">' . $i18_edit_word . '</a></li>';
			}

			$message[] = '<div class="status_group">' .
							/* translators: the %s will be replaced with the word 'articles' (in bold) */
							'<p>' . sprintf( esc_html__( 'The following %s have no categories assigned:', 'echo-knowledge-base' ), $i18_articles ) . '</p>' .
							'<ul>' . $temp . '</ul>' .
						 '</div>';
		}

		if ( empty($articles_seq_data) || empty($category_seq_data) ) {
			// ensure category hierarchy is up to date
			$category_admin = new EPKB_Categories_Admin();
			$category_admin->update_categories_sequence();

			// ensure articles assignment to categories is up to date
			$article_admin = new EPKB_Articles_Admin();
			$article_admin->update_articles_sequence( $kb_id );

			// category and article sequence
			$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
			$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		}

		// 3. check if this is Tabs layout and there are articles attached to the top level category
		//    AND do not have any other non-top category, report them
		if ( $current_layout == EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME ) {

			// 3.1 retrieve top-level categories and attached articles
			$top_level_categories = array();
			$top_level_category_articles = array();
			foreach ( $category_seq_data as $category_id => $subcategories ) {
				$top_level_categories[] = $category_id;

				// ignore empty category
				if ( $category_id == 0 || empty($articles_seq_data[$category_id]) || count($articles_seq_data[$category_id]) < 3 ) {
					continue;
				}

				$top_level_category_articles += $articles_seq_data[$category_id];
				unset($top_level_category_articles[0]);
				unset($top_level_category_articles[1]);
			}

			// 3.2 remove top-level articles that are also attached sub-catagories
			foreach ( $articles_seq_data as $category_id => $sub_category_article_list ) {
				// skip top level categories
				if ( in_array($category_id, $top_level_categories) || $category_id == 0 ) {
					continue;
				}
				// does sub-category have top-level article as well?
				unset($sub_category_article_list[0]);
				unset($sub_category_article_list[1]);
				foreach ( $top_level_category_articles as $top_level_article_id => $top_level_article_title ) {
					if ( in_array($top_level_article_id, array_keys($sub_category_article_list) ) ) {
						unset($top_level_category_articles[$top_level_article_id]);
					}
				}
			}

			// 3.3 output articles that are only on top-level
			$top_level_msg = '';
			$ix = 0;
			$top_level_category_articles = array_unique( $top_level_category_articles );
			foreach( $top_level_category_articles as $top_level_article_id => $top_level_article_title ) {
				$ix++;
				$top_level_msg .= '<li>' . $top_level_article_title . ' &nbsp;&nbsp;' . '<a href="' .  get_edit_post_link( $top_level_article_id ) . '" target="_blank">' . $i18_edit_word . '</a></li>';
			}

			if (  !empty($top_level_msg) ) {
				$i18_layout = '<strong>' . esc_html__( 'Layout', 'echo-knowledge-base' ) . '</strong>';
				$i18_tabs = '<strong>' . esc_html__( 'Tabs', 'echo-knowledge-base' ) . '</strong>';

				/* translators: the first %s will be replaced with the word 'Layout' (in bold) and the second %s will replaced with 'Tabs' word (in bold) */
				$msg1 = sprintf( esc_html__( 'Current %s is set to %s.', 'echo-knowledge-base' ), $i18_layout, $i18_tabs );
				/* translators: the %s will be replaced with the word 'category' (in bold) */
				$msg2 = sprintf( esc_html(_n( 'The following article has only top-level %s and will not be displayed' .
				                              ' on KB Main page. In the Tab layout, this article needs to be assigned to a sub-category.',
						'The following articles have only top-level %s and will not be displayed' .
						' on KB Main page. In the Tab layout, these articles need to be assigned to a sub-category.', $ix, 'echo-knowledge-base')), $i18_category );

				$message[] = '<div class="status_group">'.
				                '<p>'. $msg1 .'</p>'.
				                '<p>' . $msg2 . '</p>
			                    <ul>'. $top_level_msg . '</ul>
			                </div>';
			}
		}

		$stored_ids_obj = new EPKB_Categories_Array( $category_seq_data ); // normalizes the array as well
		$category_ids_levels = $stored_ids_obj->get_all_keys();


		// 4. check if user does not have too many levels of categories; these categories and articles within them
		//    will not show; ignore empty categories
		add_filter( 'epkb_max_layout_level', array( 'EPKB_KB_Config_Layouts', 'get_max_layout_level') );
		$max_category_level = apply_filters( 'epkb_max_layout_level', $current_layout );
		$max_category_level = EPKB_Utilities::is_positive_or_zero_int( $max_category_level ) ? $max_category_level : 6;
		if ( $max_category_level > 0 ) {

			// 4.1 get all visible articles
			$visible_articles = array();
			foreach ( $category_ids_levels as $category_id => $level ) {
				if ( $level <= $max_category_level && ! empty( $articles_seq_data[ $category_id ] ) && count( $articles_seq_data[ $category_id ] ) > 2 ) {
					$visible_articles += $articles_seq_data[ $category_id ];
					unset( $visible_articles[0] );
					unset( $visible_articles[1] );
				}
			}

			// 4.2 get invisible subcategories (these categories are too deep)
			$invisible_articles = array();
			$invisible_cat_msg  = '';
			foreach ( $category_ids_levels as $category_id => $level ) {
				if ( $level > $max_category_level && ! empty( $articles_seq_data[ $category_id ] ) ) {
					$invisible_cat_msg .= '<li>' . $articles_seq_data[ $category_id ][0] . ' &nbsp;&nbsp;' . '<a href="' .
					                      get_edit_term_link( $category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), EPKB_KB_Handler::get_post_type( $kb_id ) ) .
					                      '" target="_blank">' . $i18_edit_word . '</a></li>';
					$invisible_articles += $articles_seq_data[ $category_id ];
					unset( $invisible_articles[0] );
					unset( $invisible_articles[1] );
				}
			}

			// 4.3 list any articles that are NOT in other visible categories
			$invisible_articles_msg = '';
			foreach( $invisible_articles as $article_id => $article_title ) {
				if ( in_array( $article_id, $visible_articles) ) {
					continue;
				}
				$invisible_articles_msg .= '<li>' . $article_title . ' &nbsp;&nbsp;' . '<a href="' .  get_edit_post_link( $article_id ) . '" target="_blank">' . $i18_edit_word . '</a></li>';
			}
		}

		$i18_categories = '<strong>' . esc_html__( 'categories', 'echo-knowledge-base' ) . '</strong>';

		if ( ! empty($invisible_cat_msg) ) {
			/* translators: the first %s will be replaced with the word 'categories' (in bold) and the second %s will replaced with 'basic' or 'tabs' word (in bold) */
			$msg3 = sprintf( esc_html__( 'The following %s are nested too deeply to be visible with the selected %s layout:', 'echo-knowledge-base' ), $i18_categories, $current_layout );
			$message[] = '<div class="status_group"><p>' . $msg3 . '</p><ul>' . $invisible_cat_msg . '</ul><p>' .
			                 esc_html__( 'You can move the categories and/or switch layout.', 'echo-knowledge-base' ) . '</p></div>';
		}
		if ( ! empty($invisible_articles_msg) ) {

			/* translators: the first %s will be replaced with the word 'articles' (in bold) and the second %s will replaced with 'basic' or 'tabs' word (in bold) */
			$msg4 = sprintf( esc_html__( 'The following %s are assigned to categories not visible so they will not be visible with the selected %s layout:', 'echo-knowledge-base' ),
					$i18_articles, $current_layout );
			$message[] = '<div class="status_group"><p>' . $msg4 . '</p><ul>' . $invisible_articles_msg . '</ul>' .
			              '<p>' . esc_html__( 'You can either assign the article(s) to different categories and/or move categories.', 'echo-knowledge-base' ) . '</p></div>';
		}

		// 5. show empty categories; do not count categories containing other categories
		$empty_cat_msg = '';
		foreach( $stored_ids_obj->get_all_leafs() as $category_id ) {
			if ( isset($articles_seq_data[$category_id]) && count($articles_seq_data[$category_id]) < 3 ) {
				$empty_cat_msg .= '<li>' . $articles_seq_data[$category_id][0] . ' &nbsp;&nbsp;' . '<a href="' .
				                  get_edit_term_link( $category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), EPKB_KB_Handler::get_post_type( $kb_id) ) .
				                  '" target="_blank">' . $i18_edit_word. '</a></li>';
			}
		}
		if ( ! empty($empty_cat_msg) ) {
			/* translators: the first %s will be replaced with the word 'articles' (in bold) and the second %s will replaced with 'basic' or 'tabs' word (in bold) */
			$msg5 = sprintf( esc_html__( 'The following %s have no articles:', 'echo-knowledge-base' ), $i18_categories );
			$message[] = '<div class="status_group"><p>' . $msg5 . '</p><ul>' . $empty_cat_msg . '</ul></div>';
		}

		return $message;
	}

	/**
	 * Show a box with Icon , Title , Description and Link
	 *
	 * @param $args array
	 * $args
	 * - ['option_classes']
	 *      'epkb-overview-box-icon-location-top'   Icon will be above Title.
	 *      'epkb-overview-box-icon-location-left'  Icon will be on the left of the Title.
			'epkb-overview-box-center-align'        Body, footer text will be center aligned.
			'epkb-overview-box-border-all'          Show Grey Border on all sides of container.
			'epkb-overview-box-border-top'          Show Grey Border at top of container.
			'epkb-overview-box-border-right'        Show Grey Border on the right of container.
			'epkb-overview-box-border-bottom'       Show Grey Border on the bottom of container.
			'epkb-overview-box-border-left'         Show Grey Border on the left of container.
			'epkb-overview-box-margin'              Adds 20px margin on all sides of container.
			'epkb-overview-box-width-one-half'      Make box container one half of the row.
			'epkb-overview-box-width-one-third'     Make box container one third of the row.
			'epkb-overview-box-width-one-fourth'    Make box container one fourth of the row.
	 		'epkb-overview-box-purple'              Add Purple color to Icon, bottom link button.
	 		'epkb-overview-box-red'                 Add Red color to Icon, bottom link button.
	 		'epkb-overview-box-orange'              Add Orange color to Icon, bottom link button.
	 		'epkb-overview-box-grey'                Add Grey color to Icon, bottom link button.
	 		'epkb-overview-box-green'               Add Green color to Icon, bottom link button.
	 		'epkb-overview-box-spacer'              Add a white spacer for the borders to show a gap between them.
	 		'show-box-spacer-top-right'             Placement of the white spacer.

	 * - ['form_id']        If the id is set. Outputs the Opening and closing form tags with the id.
	 * - ['icon_class']     Top Icon to display ( Choose between these available ones: https://fontawesome.com/v4.7.0/icons/ )
	 * - ['title']          H3 title of the box.
	 * - ['content']        Body content of the box.
	 * - ['btn_text_1']       if the text is set show button and the text of the button at the bottom of the box, if no text is defined no button will show up.
	 * - ['btn_url_1']        Button URL.
	 * - ['btn_text_2']       if the text is set show button and the text of the button at the bottom of the box, if no text is defined no button will show up.
	 * - ['btn_url_2']        Button URL.
	 */
	public static function display_overview_box( $args ) {
		$option_classes = '';
		if( isset($args['option_classes']) ) {
			foreach($args['option_classes'] as $class ) {
				$option_classes .= $class.' ';
			}
		}		?>

		<div class="epkb-overview-box-container <?php echo $option_classes; ?>">			<?php

			if ( isset( $args['form_id'] ) ) { 	?>
				<form id="<?php echo $args['form_id']; ?>">			<?php
			}			?>

			<!-- Header -------------------->
			<div class="epkb-overview-box-header">
				<i class="epkb-overview-box-icon epkbfa <?php echo $args['icon_class']; ?>"></i>
				<h3 class="epkb-overview-box-title"><?php echo $args['title']; ?></h3>
			</div>

			<!-- Body ---------------------->
			<div class="epkb-overview-box-body">				<?php
				echo $args['content'];                  ?>
			</div>

			<!-- Footer ---------------------->
			<div class="epkb-overview-box-footer">				<?php
				if ( isset($args['btn_text_1']) ) {       ?>
					<a class="epkb-overview-box-button" href="<?php echo $args['btn_url_1']; ?>" <?php echo empty($args['btn_url_1'][0]) || $args['btn_url_1'][0] == '#' ? '' : 'target="_blank"'; ?>><?php echo $args['btn_text_1']; ?></a>				<?php
				}
				if ( isset($args['btn_text_2']) ) {       ?>
					<a class="epkb-overview-box-button" href="<?php echo $args['btn_url_2']; ?>" <?php echo empty($args['btn_url_2'][0]) || $args['btn_url_2'][0] == '#' ? '' : 'target="_blank"'; ?>><?php echo $args['btn_text_2']; ?></a>				<?php
				}       ?>
			</div>			<?php
			if ( isset($args['show_box_spacer']) ) {    ?>
				<span class="epkb-overview-box-spacer <?php echo $args['box_spacer_loc']; ?>"></span>			<?php
			}

			if ( isset($args['form_id']) ) { 	?>
				</form>			<?php
			}			?>

		</div>	<?php
	}
}
