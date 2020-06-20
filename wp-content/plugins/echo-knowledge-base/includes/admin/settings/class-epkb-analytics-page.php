<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display analytics
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Analytics_Page {

	var $kb_config = array();

	public function __construct( $kb_config=array() ) {
		$this->kb_config = empty($kb_config) ? epkb_get_instance()->kb_config_obj->get_current_kb_configuration() : $kb_config;
		if ( is_wp_error( $this->kb_config ) ) {
			$this->kb_config = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		}
	}

	/**
	 * Display analytics page with toolbar and content.
	 */
	public function display_plugin_analytics_page() { ?>

		<div class="wrap">
			<h1></h1><!-- This is a honeypot for WP JS injected garbage -->
		</div>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container epkb-analytics-container <?php do_action( 'eckb_add_container_classes'); ?>">
			<div class="epkb-config-wrapper">
				<div class="wrap" id="ekb_core_top_heading"></div>

					<div id="epkb-config-main-info"> <?php
						$this->display_top_panel(); ?>
					</div>				    <?php

					$this->display_page_details();   ?>

			</div>
		</div>

		<div id="epkb-dialog-info-icon" title="" style="display: none;">
			<p id="epkb-dialog-info-icon-msg"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span></p>
		</div>      <?php
	}

	/**
	 * Display top panel with buttons
	 */
	private function display_top_panel() { ?>

		<div class="eckb-nav-section epkb-kb-name-section">			<?php
			$this->display_list_of_kbs(); 			?>
		</div>

		<!--  CORE STATISTICS PAGE BUTTON -->
		<div class="eckb-nav-section epkb-active-nav">
			<div class="page-icon-container">
				<p><?php _e( 'KB Stats', 'echo-knowledge-base' ); ?></p>
				<div class="page-icon ep_font_icon_data_report" id="epkb-statistics-data"></div>
			</div>
		</div>

		<!-- DISPLAY BUTTONS FOR OTHER ANALYTICS PAGES -->  <?php
		do_action( 'eckb_analytics_navigation_bar');
	}

	/**
	 * Display all configuration fields
	 */
	private function display_page_details() {

		$kb_id = EPKB_KB_Handler::get_current_kb_id();  ?>

		<div class="eckb-config-content epkb-active-content" id="epkb-statistics-data-content">
			<?php $this->display_core_analytics( $kb_id ); ?>
		</div>		<?php

		// display add-on analytics pages
		do_action( 'eckb_analytics_content', $kb_id );
	}

	/**
	 * Show KB core statistics
	 *
	 * @param $kb_id
	 */
	private function display_core_analytics( $kb_id ) {

		$all_kb_terms      = EPKB_Utilities::get_kb_categories_unfiltered( $kb_id );
		$nof_kb_categories = $all_kb_terms === null ? 'unknown' : count( $all_kb_terms );
		$nof_kb_articles   = EPKB_Articles_DB::get_count_of_all_kb_articles( $kb_id );  ?>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php _e( 'Categories', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo EPKB_Utilities::sanitize_int( $nof_kb_categories ); ?></div>
				<div class="widget-desc"><?php _e( 'Categories help you to organize articles into groups and hierarchies.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="widget-toggle"><?php
				$url = admin_url('edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ));  ?>
				<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php _e( 'View Categories', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php _e( 'Articles', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo EPKB_Utilities::sanitize_int( $nof_kb_articles ); ?></div>
				<div class="widget-desc"><?php _e( 'Article belongs to one or more categories or sub-categories.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="widget-toggle">
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id )) ); ?>" target="_blank"><?php _e( 'View Articles', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>	<?php
	}

	/**
	 * Display a list of KBs if Multiple KB is available.
	 */
	private function display_list_of_kbs() {

		if ( ! defined('EM' . 'KB_PLUGIN_NAME') ) {
			$kb_name = $this->kb_config[ 'kb_name' ];
			echo '<h1 class="epkb-kb-name">' . esc_html( $kb_name ) . '</h1>';
			return;
		}

		// output the list
		$list_output = '<select class="epkb-kb-name" id="epkb-list-of-kbs">';
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $one_kb_config ) {

			if ( $one_kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
				continue;
			}

			$kb_name = $one_kb_config[ 'kb_name' ];
			$active = ( $this->kb_config['id'] == $one_kb_config['id'] ? 'selected' : '' );
			$tab_url = 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . $one_kb_config['id'] . '&page=epkb-plugin-analytics';

			$list_output .= '<option value="' . $one_kb_config['id'] . '" ' . $active . ' data-kb-admin-url=' . esc_url($tab_url) . '>' . esc_html( $kb_name ) . '</option>';
			$list_output .= '</a>';
		}

		$list_output .= '</select>';

		echo $list_output;
	}
}