<?php

/**
 * Setup WordPress menu for this plugin
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */

/**
 *  Register plugin menus
 */
function epkb_add_plugin_menus() {

	// Add KB menu that belongs to the post type that is listed in the URL or use default one if none specified
	$post_type_name = EPKB_KB_Handler::get_current_kb_post_type();
	if ( empty($post_type_name) ) {
		$post_type_name = EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID );
	}

	add_submenu_page( 'edit.php?post_type=' . $post_type_name, __( 'Configuration - Echo Knowledge Base', 'echo-knowledge-base' ), __( 'Configuration', 'echo-knowledge-base' ),
                        'manage_options', 'epkb-kb-configuration', array(new EPKB_KB_Menu_Configuration, 'display_kb_config_page') );

	do_action( 'eckb_add_kb_menu_item', $post_type_name );

	add_submenu_page( 'edit.php?post_type=' . $post_type_name, __( 'Analytics - Echo Knowledge Base', 'echo-knowledge-base' ), __( 'Analytics', 'echo-knowledge-base' ),
						'manage_options', 'epkb-plugin-analytics', array( new EPKB_Analytics_Page(), 'display_plugin_analytics_page' ) );

	add_submenu_page( 'edit.php?post_type=' . $post_type_name, __( 'Add-ons - Echo Knowledge Base', 'echo-knowledge-base' ), __( 'Add-ons', 'echo-knowledge-base' ),
                        'manage_options', 'epkb-add-ons', array( new EPKB_Add_Ons_Page(), 'display_add_ons_page') );
	
	add_submenu_page( 'edit.php?post_type=' . $post_type_name, __( 'New Features - Echo Knowledge Base', 'echo-knowledge-base' ), EPKB_New_Features_Page::get_menu_item_title(),
                        'manage_options', 'epkb-new-features', array( new EPKB_New_Features_Page(), 'display_new_features_page') );
	
	if ( EPKB_Manage_KB_Page::is_show_core_kbs_page() ) {
		add_submenu_page( 'edit.php?post_type=' . $post_type_name, __( 'Manage KBs - Echo Knowledge Base', 'echo-knowledge-base' ), __( 'Manage KBs', 'echo-knowledge-base' ),
							'manage_options', 'epkb-manage-kb', array( new EPKB_Manage_KB_Page(), 'display_manage_kb_page') );
	}
}
add_action( 'admin_menu', 'epkb_add_plugin_menus', 10 );

/**
 * Display tabs representing existing knowledge bases at the top of each KB admin page
 */
function epkb_add_page_tabs() {

	global $current_screen;

	// first determine if this page belongs to Knowledge Base and return if it does not
	$current_kb_id = EPKB_KB_Handler::get_current_kb_id();
	if ( empty($current_kb_id) ) {
		return;
	}

	// determine tab label e.g. 'Templates For:'
	$screen_id = isset( $current_screen->id ) ? $current_screen->id : '';
	$screen_id = str_replace( EPKB_KB_Handler::get_post_type( $current_kb_id ), 'EKB_SCREEN', $screen_id );

	// if add-on is not using tabs then exit
	$no_kb_tabs = apply_filters( 'eckb_hide_kb_tabs', $screen_id );
	if ( isset($no_kb_tabs) && $no_kb_tabs == 'no_kb_tabs' ) {
		return;
	}

	// if KB is blank then forward to the Wizard
	if ( in_array($screen_id, array('edit-EKB_SCREEN','EKB_SCREEN','edit-EKB_SCREEN_category','edit-EKB_SCREEN_tag','EKB_SCREEN_page_epkb-plugin-analytics')) ) {
		$is_blank_kb = EPKB_KB_Wizard::is_blank_KB( $current_kb_id );
		if ( is_wp_error($is_blank_kb) || $is_blank_kb ) {   ?>
			<div class="wrap">
				<h1></h1>
			</div>
			<div class="epkb-wizard-error-note epkb-wizard-back-to-wizard">
				<?php _e( 'Your Knowledge Base is not set up yet. Use the Wizard to finish the setup.', 'echo-knowledge-base' ); ?>   <?php
				echo ' <a class="button button-primary" href="' . esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $current_kb_id )) ) . '&page=epkb-kb-configuration&wizard-on"> ' . __( 'Start Wizard', 'echo-knowledge-base' ) . '</a>';
				echo is_wp_error($is_blank_kb) ? __( 'Error: ', 'echo-knowledge-base' ) . $is_blank_kb->get_error_message() : '';      ?>
			</div>			<?php
		}
	}

	$disable_kb_buttons = false;

	switch ( $screen_id ) {

		// All Articles page
		case 'edit-EKB_SCREEN':
			$tab_url_base = 'edit.php?post_type=*';
			break;

		// Add New Article page
		case 'EKB_SCREEN':
			$tab_url_base = 'post-new.php?post_type=*';
			break;

		// Categories page
		case 'edit-EKB_SCREEN_category':
			$tab_url_base = 'edit-tags.php?taxonomy=*_category&post_type=*';
			break;

		// Tags page
		case 'edit-EKB_SCREEN_tag':
			$tab_url_base = 'edit-tags.php?taxonomy=*_tag&post_type=*';
			break;

		// KB Configuration page
		case 'EKB_SCREEN_page_epkb-kb-configuration':
			return;

		// Add-ons page
		case 'EKB_SCREEN_page_epkb-add-ons':
			return;

		// New Features page
		case 'EKB_SCREEN_page_epkb-new-features':
			return;
		
		// Manage KBs
		case 'EKB_SCREEN_page_epkb-manage-kb':
			return;

		// Analytics page
		case 'EKB_SCREEN_page_epkb-plugin-analytics':
			return;

		default:
			$tab_url_base = 'edit.php?post_type=*';
	}

	epkb_display_kb_navigation_tabs( $current_kb_id, $tab_url_base, $disable_kb_buttons, $screen_id );
}
add_action( 'all_admin_notices', 'epkb_add_page_tabs', 99999 );

/**
 * Generate navigation bars that show available and selected knowledge bases
 *
 * @param $current_kb_id
 * @param $tab_url_base
 * @param $disable_kb_buttons
 * @param $screen_id
 *
 * @return int
 */
function epkb_display_kb_navigation_tabs( $current_kb_id, $tab_url_base, $disable_kb_buttons, $screen_id ) {	?>

	<div class="wrap">
		<h1></h1>
	</div>

	<div id="ekb_core_top_heading">
		<ul class="tab_navigation">  			<?php

			$ix = 1;
			$nof_tabs_visible = 3;
			$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();

			$nof_kbs = 0;
			$active_kb_configs = array();
			foreach ( $all_kb_configs as $one_kb_config ) {

			    // skip archived KBs
				if ($one_kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
					continue;
				}

				if ( $current_kb_id == $one_kb_config['id'] ) {
					array_unshift($active_kb_configs , $one_kb_config);
				} else {
					$active_kb_configs[] = $one_kb_config;
				}

				$nof_kbs++;
			}

			// add button-link to the multiple kb plugin 
			if ( ! defined( 'EM'.'KB_PLUGIN_NAME' ) && count($all_kb_configs) == 1 ) {
				$active_kb_configs[] = array(
					'id' => 0,
					'kb_name' => __( 'Get Additional Knowledge Bases', 'echo-knowledge-base' ),
					'link' => 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/'
				);
			}

			// display KB tabs
			foreach ( $active_kb_configs as $one_kb_config ) {

				// if is more than $nof_tabs_visible KB then start putting them into a drop down list
				if ( $ix == ( $nof_tabs_visible + 1 ) &&  ( $nof_kbs > $nof_tabs_visible + 1 ) ) {	?>
					<li class="drop_down_tabs">
						<span class="more_tabs"><?php echo esc_html__( 'More Tabs', 'echo-knowledge-base' ) . ' (' . ( $nof_kbs - $nof_tabs_visible ) . ')';	?></span>
						<ul><?php
				}

				// output KB tab
				$kb_name = isset($one_kb_config['kb_name']) ? $one_kb_config['kb_name'] : __( 'Knowledge Base', 'echo-knowledge-base' );
				$target = isset($one_kb_config['link']) ? 'target="_blank"' : '';
				$tab_url = isset($one_kb_config['link']) ? $one_kb_config['link'] : str_replace( '*', EPKB_KB_Handler::get_post_type( $one_kb_config['id'] ), $tab_url_base );
				$active  = ( $current_kb_id == $one_kb_config['id'] ? 'active' : '' );
				echo '<li>';
				echo    '<a ' . ( $disable_kb_buttons ? '' : 'href="' . esc_url( $tab_url ) . '"' ) . ' title="' . esc_attr( $kb_name ) . '" class="nav_tab' . ' ' . $active . '" ' . $target . '>';
				echo       '<span>' . esc_html( $kb_name ) . '</span>';
				echo    '</a>';
				echo '</li>';

				$ix++;

			} //foreach

			//If the last list item add the closing ul li tags
			if ( $nof_kbs > $nof_tabs_visible ) {	?>
					</ul>
				</li>	<?php
			}	?>

		</ul><!-- Tab Navigation -->		<?php

		// display KB status in top right corner of most screens
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $current_kb_id );	// for each KB type create a tab
		if ( ! is_wp_error( $kb_config ) ) {
			echo wp_kses_post( EPKB_KB_Config_Overview::get_kb_status_line( $kb_config ) );
		}		 ?>

	</div><!-- ekb_core_top_heading -->     	<?php
}
