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


		/***  Errors  ***/

		// LICENSE / ADD-ON issues
		$add_on_messages = apply_filters( 'epkb_add_on_license_message', array() );
		if ( ( ! empty($add_on_messages) && is_array($add_on_messages) ) || did_action('kb_overview_add_on_errors' ) ) {        ?>

		    <section class="overview-info-section">
	            <div class="overview-error">
	                <div class="overview-header">
	                    <div class="overview-title"><?php _e( 'Errors', 'echo-knowledge-base' ); ?></div>
	                    <div class="overview-brief"><span class="overview-icon ep_font_icon_error_circle"></span><?php _e( 'Critical Errors Detected', 'echo-knowledge-base' ); ?></div>
	                </div>
	                <div class="overview-content">	<?php
			                if ( ! empty($add_on_messages) && is_array($add_on_messages) ) {
				                foreach( $add_on_messages as $add_on_name => $add_on_message ) {
					                $add_on_name = str_replace( array('2', '3', '4'), '', $add_on_name );
					                echo
						                '<div class="callout callout_error">' .
						                '<h4>' . esc_html($add_on_name) . ': ' . __( 'License issue', 'echo-knowledge-base' ) . '</h4>' .
						                '<p>' . wp_kses_post( $add_on_message ) . '</p>' .
						                '</div>';
				                }
			                }
			                do_action('kb_overview_add_on_errors' );				?>
	                </div>
	            </div>
	        </section>      <?php
		}

		/***  Warnings  ***/

		$message = self::get_kb_status( $kb_config );
		$is_status_error = ! empty($message);

		if ( $is_status_error ) {
			$class_status = 'overview-warning';
			$brief = '<div class="overview-brief"><span class="overview-icon ep_font_icon_question"></span>' . __( 'Potential issues.', 'echo-knowledge-base' ) . '</div>
	                  <div class="overview-toggle">' . __( 'View Details', 'echo-knowledge-base' ) . '</div>';
		} else {
			$class_status = 'overview-success';
			$brief = '<div class="overview-brief"><span class="overview-icon ep_font_icon_checkmark"></span>' . __( 'All Good, No Issues.', 'echo-knowledge-base' ) . '</div>';
        }        ?>

        <section class="overview-info-section">
            <div class="<?php echo $class_status; ?>">
                <div class="overview-header">
                <div class="overview-title"><?php _e( 'Warnings', 'echo-knowledge-base' ); ?></div>
                    <?php echo $brief; ?>
            </div>
                <div class="overview-content">				<?php
					if ( $is_status_error ) {
						echo wp_kses_post( self::get_kb_status_msg( $message ) );
					}       ?>
                </div>
            </div>
        </section>

		<!-- News -->
        <!-- <section class="overview-info-section">
            <div class="overview-news">
                <div class="overview-header">
                    <div class="overview-title">News</div>
                    <div class="overview-brief"><span class="overview-icon ep_font_icon_light_bulb"></span>News and Updates</div>
                    <div class="overview-toggle">View Details</div>
                </div>
                <div class="overview-content">	-->	            <?php

		            // CHECK OUT NEW FEATURES - after plugin / add-ons were upgraded
		            /* $upgrade_message = apply_filters( 'eckb_plugin_upgrade_message', '' );
		            if ( ! empty($upgrade_message) ) {      ?>
                        <div class="callout callout_attention epkb_upgrade_message">
                            <h4>What's New</h4>     <?php
	                        echo '<p>Read about our new features, improvements and changes on our Blog <a href="https://www.echoknowledgebase.com/blog/" target="_blank">here.</a>';

	                        echo wp_kses_post( $upgrade_message ); ?>
                        </div>        <?php
		            }  */		            ?>

              <!--  </div>
            </div>
        </section> -->


        <!-- Configuration -->
        <section class="overview-info-section">
            <div class="overview-config">
                <div class="overview-header">
                    <div class="overview-title"><?php _e( 'Configuration', 'echo-knowledge-base' ); ?></div>
                    <div class="overview-brief"><span class="overview-icon ep_font_icon_gear"></span><?php _e( 'Additional Global Settings', 'echo-knowledge-base' ); ?></div>
                    <div class="overview-toggle"><?php _e( 'View Details', 'echo-knowledge-base' ); ?></div>
                </div>
                <div class="overview-content">

                    <form id="epkb-config-config3">

                        <!--  KB NAME and other global settings -->
                        <div class="callout callout_default">
                            <h4>KB Name</h4>
                            <div class="row">
                                <div class="config-col-4">		            <?php
                                    echo $form->text(  array(
                                            'value' => $kb_config[ 'kb_name' ],
                                            'input_group_class' => 'config-col-12',
                                            'label_class' => 'config-col-3',
                                            'input_class' => 'config-col-9'
                                        ) + $feature_specs['kb_name'] );		            ?>
                                </div>
                                <div class="config-col-6">

                                    <div class="config-col-3">			            <?php
                                        $form->submit_button( array(
                                            'label'             => 'Update',
                                            'id'                => 'epkb_save_dashboard',
                                            'main_class'        => 'epkb_save_dashboard',
                                            'action'            => 'epkb_save_dashboard',
                                            'input_class'       => 'epkb-info-settings-button'
                                        ) );			            ?>
                                    </div>
                                    <div class="config-col-3">			            <?php
                                        $form->submit_button( array(
                                            'label'             => 'Cancel',
                                            'id'                => 'epkb_cancel_dashboard',
                                            'main_class'        => 'epkb_cancel_dashboard',
                                            'action'            => 'epkb_cancel_dashboard',
                                            'input_class'       => 'epkb-info-settings-button',
                                        ) );			            ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </form>

	                <form id="epkb-wpml-enabled-config">

		                <!--  KB NAME and other global settings -->
		                <div class="callout callout_default">
			                <h4>WPML</h4>
			                <div class="row">
				                <div class="config-col-2">

					                <?php

					                $wpml_value = EPKB_Utilities::get_wp_option( EPKB_Settings_Controller::EPKB_WPML_ON, false ) ? 'on' : '';
					                $form = new EPKB_HTML_Elements();
					                $form->checkbox( array(
						                'label'       => __( 'WPML Enabled', 'echo-knowledge-base' ),
						                'name'        => 'epkb_wpml_enabled',

						                'type'        => EPKB_Input_Filter::CHECKBOX,
						                'label_class' => 'col-6',
						                'input_class' => 'col-6',
						                'value'       => $wpml_value
					                ) );
					                ?>
				                </div>
				                <div class="config-col-2">
					                <section class="save-settings">    <?php
						                $form->submit_button( __( 'Save', 'echo-knowledge-base' ), 'epkb_save_wpml_settings' ); ?>
					                </section>
				                </div>
			                </div>
		                </div>

	                </form>

	                <div class="callout callout_default">
		                <h4><?php _e( 'KB Main Page', 'echo-knowledge-base' ); ?></h4>
		                <p><?php _e( 'To display a <strong>Knowledge Base Main page</strong>, add the following KB shortcode to any page: &nbsp;&nbsp;<strong>', 'echo-knowledge-base' ); ?>
				                [<?php echo EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME . ' id=' . $kb_id; ?>]</strong></p>
		                <p><strong><?php _e( 'Existing KB Main Page(s)', 'echo-knowledge-base' ); ?>:</strong></p>
		                <ul>			                <?php
			                echo wp_kses_post( $kb_main_pages_url );    ?>
		                </ul>
	                </div>

                </div>
            </div>
        </section>        <?php
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
	 * Return HTML to display status message
	 *
	 * @param $message
	 * @return string
	 */
	private static function get_kb_status_msg( $message ) {

		$callout_class = 'callout_warning';
		$msg = __( 'Potential problems found.', 'echo-knowledge-base' );
		$details = '<p class="note_type_1">' .
		           __( "NOTE: It is OK to see warnings if you are creating and updating your knowledge base. After you're done, you should see " .
		                       "no warnings. Warnings indicate that your users might come accross empty categories, that they might not see certain " .
		                       "articles or they might otherwise have less-than-optimal experience", 'echo-knowledge-base' ) . ' ' .
		           '.</p>';
		$details .= $message;

		return
			"<div id='kb_status' class='callout $callout_class'>
				<h4><strong>" . __( 'Status', 'echo-knowledge-base' ) . ":</strong>&nbsp;&nbsp;" . esc_html($msg) . '</h4>' .
				$details . '
			</div>';
	}

	/**
	 * Show status of current Knowledge Base
	 *
	 * @param $kb_config
	 * @param string $chosen_layout - layout user just switched to or empty
	 * @param array $articles_seq_data
	 * @param array $category_seq_data
	 * @return string
	 */
	private static function get_kb_status( $kb_config, $chosen_layout='', $articles_seq_data=array(), $category_seq_data=array() ) {

		$message = '';
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
			$message .= '<div class="status_group"><p>' .
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
			$message .= '<div class="status_group">';
			/* translators: the %s will be replaced with the word 'articles' (in bold) */
			$message .= '<p>' . sprintf( esc_html__( 'The following %s have no categories assigned:', 'echo-knowledge-base' ), $i18_articles ) . '</p>';
			$message .= '<ul>';
			foreach( $orphan_articles as $orphan_article ) {
				$message .= '<li>' . $orphan_article->post_title . ' &nbsp;&nbsp;' . '<a href="' .  get_edit_post_link( $orphan_article->ID ) . '" target="_blank">' . $i18_edit_word . '</a></li>';
			}
			$message .= '</ul>';
			$message .= '</div>';
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

				$message .= '<div class="status_group">'.
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
		$max_category_level = EPKB_Utilities::is_positive_or_zero_int( $max_category_level ) ? $max_category_level : 3;
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
			$message .= '<div class="status_group"><p>' . $msg3 . '</p><ul>' . $invisible_cat_msg . '</ul><p>' .
			            esc_html__( 'You can move the categories and/or switch layout.', 'echo-knowledge-base' ) . '</p></div>';
		}
		if ( ! empty($invisible_articles_msg) ) {

			/* translators: the first %s will be replaced with the word 'articles' (in bold) and the second %s will replaced with 'basic' or 'tabs' word (in bold) */
			$msg4 = sprintf( esc_html__( 'The following %s are assigned to categories not visible so they will not be visible with the selected %s layout:', 'echo-knowledge-base' ),
					$i18_articles, $current_layout );
			$message .= '<div class="status_group"><p>' . $msg4 . '</p><ul>' . $invisible_articles_msg . '</ul>' .
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
			$message .= '<div class="status_group"><p>' . $msg5 . '</p><ul>' . $empty_cat_msg . '</ul></div>';
		}

		return $message;
	}
}
