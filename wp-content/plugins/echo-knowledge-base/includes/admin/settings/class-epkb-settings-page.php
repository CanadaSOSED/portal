<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display plugin settings
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Settings_Page {

	/**
	 * Display feature settings
	 */
	function display_plugin_settings_page() { ?>

		<div class="wrap">
			<h1></h1>
		</div>
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container epkb-plugin-info-container">
			<div class="epkb-config-wrapper">
				<div class="wrap" id="ekb_core_top_heading"></div>
				<div class="eckb-top-notice-message"></div>          <?php

				self::display_welcome_header();
				self::display_page_details(); ?>

			</div>
		</div>

		<div id="epkb-dialog-info-icon" title="" style="display: none;">
			<p id="epkb-dialog-info-icon-msg"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span></p>
		</div>      <?php

	}

	/**
	 * Display Welcome Page if plugin newly installed
	 */
	private static function display_welcome_header() {

		$header_option = get_option( 'epkb_show_welcome_header' );
		if ( empty( $header_option ) ) {
			return;
		}   ?>

		<div class="welcome_header">
			<div class="container-fluid">
				<div class="row">

					<div class="col-5">
						<h1><?php echo esc_html__( 'Welcome to Knowledge Base for Documents and FAQs', 'echo-knowledge-base' ) . ' ' . Echo_Knowledge_Base::$version; ?></h1>
						<p><?php
							$i18_doc_link = '<a href="https://www.echoknowledgebase.com/documentation/" target="_blank">' .
							                esc_html_e( 'documentation', 'echo-knowledge-base' ) . '</a>';
							$i18_rating_link = '<a href="https://wordpress.org/support/plugin/echo-knowledge-base/reviews/?filter=5" ' .
							   'target="_blank">' . esc_html_e( '5-stars', 'echo-knowledge-base' ) . '</a>.';
							printf( esc_html( _x( 'Thanks for using Knowledge Base. To get started, read over the %s ' .
							                 'and play with the settings. If you enjoy this plugin please consider telling a ' .
											 'friend, or rating it %s ', ' document link, rating link', 'echo-knowledge-base') ),
											$i18_doc_link, $i18_rating_link );  ?>
						</p>
					</div>
					<div class="col-2">
						<div class="logo">
							<img src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png'; ?>">
						</div>
						<button id="close_intro"><?php esc_html_e( 'Close', 'echo-knowledge-base' ); ?></button>
					</div>

				</div>
			</div>
		</div>  <?php
	}

	/**
	 * Display all configuration fields
	 */
	private static function display_page_details() {

		$debug_tab_active = EPKB_Utilities::post('info_tab') == 'debug';

		$form = new EPKB_HTML_Elements();     		?>

		<div id="epkb-tabs" class="plugin_settings_container">

			<!--  NAVIGATION TABS  -->

			<section class="epkb-main-nav">

				<ul class="epkb-admin-pages-nav-tabs">
					<li class="nav_tab <?php echo $debug_tab_active ? '' : 'active'; ?>">
						<h2><span class="ep_font_icon_life_saver"></span> <?php esc_html_e( 'Help | Info', 'echo-knowledge-base' ); ?></h2>
						<p><?php esc_html_e( 'Docs / Contact Us / About Us', 'echo-knowledge-base' ); ?></p>
					</li>
					<li class="nav_tab">
						<h2><span class="ep_font_icon_comment"></span> <?php esc_html_e( 'Feedback', 'echo-knowledge-base' ); ?></h2>
						<p><?php esc_html_e( 'Let us know what you think?', 'echo-knowledge-base' ); ?></p>
					</li>
					<li class="nav_tab">
						<h2><span class="ep_font_icon_building"></span> <?php esc_html_e( 'About us', 'echo-knowledge-base' ); ?></h2>
						<p><?php esc_html_e( 'What else do we do?', 'echo-knowledge-base' ); ?></p>
					</li>
					<li class="nav_tab <?php echo $debug_tab_active ? 'active' : ''; ?>">
						<h2><span class="ep_font_icon_tools"></span> <?php esc_html_e( 'Debug', 'echo-knowledge-base' ); ?></h2>
						<p><?php esc_html_e( 'Information required for support.', 'echo-knowledge-base' ); ?></p>
					</li>
				</ul>

			</section>

			<!--  TABS CONTENT  -->

			<div id="main_panels" class="ekb-admin-pages-panel-container">

				<!--   PLUGIN WIDE SETTINGS -->

				<div class="ekb-admin-page-tab-panel container-fluid <?php echo $debug_tab_active ? '' : 'active'; ?>">  <?php
					self::display_help();    ?>
				</div>

				<div class="ekb-admin-page-tab-panel container-fluid">   <?php
					self::display_feedback_form( $form );       ?>
				</div>

				<div class="ekb-admin-page-tab-panel container-fluid">
					<section>   <?php
						self::display_other_plugins();     ?>
					</section>
				</div>

				<div class="ekb-admin-page-tab-panel container-fluid <?php echo $debug_tab_active ? 'active' : ''; ?>">
                    <p>Enable debugging when instructed by the Echo team.</p>
                    <?php
					self::display_debug_info( $form );       ?>
				</div>

			</div>

			<div id='epkb-ajax-in-progress' style="display:none;">
				<?php esc_html_e( 'Saving settings...', 'echo-knowledge-base' ); ?>
				<img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
			</div>

		</div>	 <?php
	}

	private static function display_help() {    ?>
		<div class="row">
			<section class="col-3">
				<h3><?php esc_html_e( 'Getting Started / What\'s New', 'echo-knowledge-base' ); ?></h3>
				<p><?php esc_html_e( 'Read about what\'s new in the latest plugin update or how to get started.', 'echo-knowledge-base' ); ?></p>
				<a class="button primary-btn"
				   href="<?php echo admin_url( 'index.php?page=epkb-welcome-page&tab=get-started' ); ?>"><?php esc_html_e( 'Welcome Page', 'echo-knowledge-base' ); ?>
				</a>
			</section>
			<section class="col-3">
				<h3><?php esc_html_e( 'Full Documentation', 'echo-knowledge-base' ); ?></h3>
				<p><?php esc_html_e( 'Knowledge Base that explains all plugin features.', 'echo-knowledge-base' ); ?></p>
				<a class="button primary-btn" href="https://www.echoknowledgebase.com/documentation/"
				   target="_blank"><?php esc_html_e( 'Knowledge Base', 'echo-knowledge-base' ); ?></a>
			</section>
			<section class="col-3">
				<h3><?php esc_html_e( 'Still Need Some Help?', 'echo-knowledge-base' ); ?></h3>
				<p><?php esc_html_e( 'If you encounter an issue or have a question, please submit your request below.', 'echo-knowledge-base' ); ?></p>
				<a class="button primary-btn"
				   href="https://www.echoknowledgebase.com/contact-us/?inquiry-type=technical&plugin_type=knowledge-base"
				   target="_blank"><?php esc_html_e( 'Contact Us', 'echo-knowledge-base' ); ?></a>
			</section>
		</div>   <?php
	}

	/**
	 * @param EPKB_HTML_Elements $form
	 */
	private static function display_feedback_form( $form ) {   ?>

		<div class="form_options">
            <div class="ekb-feedback-type-container">
                <h4><?php _e('What best describes your inquiry?', 'echo-knowledge-base' ); ?></h4>
                <a href="http://www.echoknowledgebase.com/contact-us/?inquiry-type=technical" class="ekb-feedback-btn"><?php _e('Technical Support', 'echo-knowledge-base' ); ?><span class="icon_tools"></span></a>
                <a id="feature-request" class="ekb-feedback-btn"><?php _e('Feature Request', 'echo-knowledge-base' ); ?><span class="icon_lightbulb_alt"></span></a>
            </div>

			<form id="epkb_feedback_form" method="post">

				<section>
					<h3><?php esc_html_e( 'What features should we add or improve?', 'echo-knowledge-base' ); ?></h3>

					<ul>				<?php

						$form->text( array(
							'label'       => __( 'Email *', 'echo-knowledge-base' ),
							'name'        => 'your_email',
							'info'        => __( 'If you would like to hear back from us please provide us your email.', 'echo-knowledge-base' ),
							'type'        => EPKB_Input_Filter::TEXT,
							'max'         => '50',
							'label_class' => 'col-3',
							'input_class' => 'col-4'
						) );

						$form->text( array(
								'label'       => __( 'Name *', 'echo-knowledge-base' ),
								'name'        => 'your_name',
								'info'        => __( 'First name is sufficient.', 'echo-knowledge-base' ),
								'type'        => EPKB_Input_Filter::TEXT,
								'max'         => '50',
								'label_class' => 'col-3',
								'input_class' => 'col-4'
						) );

						$form->textarea( array(
							'label'       => __( 'Your Ideas and Feedback *', 'echo-knowledge-base' ),
							'name'        => 'your_feedback',
							'info'        => '',
							'type'        => EPKB_Input_Filter::TEXT,
							'max'         => '1000',
							'min'         => '3',
							'label_class' => 'col-3',
							'input_class' => 'col-4',
							'rows'        => 7
						) ); ?>

					</ul>
				</section>

				<section style="padding-top: 20px;" class="save-settings">    <?php
					$form->submit_button( 'Send Feedback', 'epkb_send_feedback', 'send_feedback' ); ?>
				</section>

			</form>

		</div>    <?php
	}

	private static function display_other_plugins() {   ?>

		<h3><?php esc_html_e( 'Our other Plugins', 'echo-knowledge-base' ); ?></h3>

		<div class="preview_product">
			<div class="top_heading">
				<h3><?php esc_html_e( 'Show IDs', 'echo-knowledge-base' ); ?></h3>
			</div>
			<div class="featured_img">
				<img src="<?php echo 'http://www.echoplugins.com/wp-content/uploads/2016/10/show_id_plugin.png'; ?>">
			</div>
			<div class="description">
				<p>
					<span><?php echo wp_kses_post( __( '<strong>Show IDs</strong> of post, pages and taxonomies.', 'echo-knowledge-base' ) ); ?></span>
				</p>
				<p><i><?php esc_html_e( 'Free on WordPress.org', 'echo-knowledge-base' ); ?></i></p>
			</div>
			<a class="button primary-btn" href="https://wordpress.org/plugins/echo-show-ids//"
			   target="_blank"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></a>

		</div>
		<div class="preview_product">
			<div class="top_heading">
				<h3><?php esc_html_e( 'Content Down Arrow', 'echo-knowledge-base' ); ?></h3>
			</div>
			<div class="featured_img">
				<img src="<?php echo 'http://www.echoplugins.com/wp-content/uploads/2016/10/arrow_plugin.png'; ?>">
			</div>
			<div class="description">
				<p>
					<span><?php echo wp_kses_post( __( 'Display <strong>downward-pointing arrow</strong> to indicate more content below.', 'echo-knowledge-base' ) ); ?></span>
				</p>
			</div>
			<a class="button primary-btn"
			   href="https://www.echoplugins.com/wordpress-plugins/content-down-arrow/"
			   target="_blank"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></a>

		</div>    <?php
	}

	/**
	 * @param EPKB_HTML_Elements $form
	 */
	private static function display_debug_info( $form ) {

		$is_debug_on = EPKB_Utilities::get_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, false );
		$heading = $is_debug_on ? esc_html__( 'Debug Information:', 'echo-knowledge-base' ) :
								  esc_html__( 'Enable debug when asked by Echo KB support team.', 'echo-knowledge-base' );     ?>

		<div class="form_options" id="epkb_debug_info_tab_page">

			<section style="padding-top: 20px;" class="save-settings">    <?php
				$button_text = $is_debug_on ? __('Disable Debug', 'echo-knowledge-base') : __( 'Enable Debug', 'echo-knowledge-base' );
				$form->submit_button( $button_text, 'epkb_toggle_debug', 'epkb_toggle_debug' ); ?>
			</section>

			<section>
				<h3><?php echo $heading; ?></h3>
			</section>     <?php

			if ( $is_debug_on ) {
				echo self::display_debug_data();        ?>

				<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-plugin-settings' ) ); ?>" method="post" dir="ltr">
					<section style="padding-top: 20px;" class="save-settings">    <?php
						$form->submit_button( 'Download System Information', 'epkb_download_debug_info', 'epkb_download_debug_info' ); ?>
					</section>
				</form>     <?php
			}    ?>

			<div id='epkb-ajax-in-progress-debug-switch' style="display:none;">
				<?php esc_html_e( 'Switching debug... ', 'echo-knowledge-base' ); ?><img class="epkb-ajax waiting" style="height: 30px;"
			                                                                         src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
			</div>

		</div>      		<?php
	}

	public static function display_debug_data() {

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return 'No access';
		}

		$output = '<textarea rows="30" cols="150" style="overflow:scroll;">';

		// display KB configuration
		$output .= "KB Configurations:\n";
		$output .= "==================\n\n";
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $kb_config ) {
			$output .= 'KB Config ' . $kb_config['id'] . "\n\n";
			$specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );
			foreach( $kb_config as $name => $value ) {
				if ( is_array($value) ) {
					$value = EPKB_Utilities::get_variable_string($value);
				}
				$label = empty($specs[$name]['label']) ? 'unknown' : $specs[$name]['label'];
				$output .= '- ' . $label . ' [' . $name . ']' . ' => ' . $value . "\n";
			}

			// other configuration - not needed yet
			//$output .= "\nArticles Sequence:\n\n";
			//$output .= EPKB_Utilities::get_variable_string( EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true ) );

			$output .= "\n\n";
		}

		// display PHP and WP settings
		$output .= self::get_system_info();

		// display error logs
		$output .= "\n\nERROR LOG:\n";
		$output .= "==========\n";
		$logs = EPKB_Logging::get_logs();
		foreach( $logs as $log ) {
			$output .= empty($log['plugin']) ? '' : $log['plugin'] . " ";
			$output .= empty($log['kb']) ? '' : $log['kb'] . " ";
			$output .= empty($log['date']) ? '' : $log['date'] . "\n";
			$output .= empty($log['message']) ? '' : $log['message'] . "\n";
			$output .= empty($log['trace']) ? '' : $log['trace'] . "\n\n";
		}

		// retrieve add-on data
		$add_on_output = apply_filters( 'eckb_add_on_debug_data', '' );
		$output .= is_string($add_on_output) ? $add_on_output : '';

		$output .= '</textarea>';



		return $output;
	}

	/**
	 * Based on EDD system-info.php file
	 * @return string
	 */
	private static function get_system_info() {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$host = defined( 'WPE_APIKEY' ) ? "Host: WP Engine" : '';
		/** @var $theme_data WP_Theme */
		$theme_data = wp_get_theme();
		/** @noinspection PhpUndefinedFieldInspection */
		$theme = $theme_data->Name . ' ' . $theme_data->Version;

		$echo_versions = 'KB Version: ' . Echo_Knowledge_Base::$version . "\n";
		$echo_versions .= class_exists('Echo_Elegant_Layouts') && isset(Echo_Elegant_Layouts::$version) ? "		EL"."AY version: " . Echo_Elegant_Layouts::$version . "\n" : '';
		$echo_versions .= class_exists('Echo_Multiple_Knowledge_Bases') && isset(Echo_Multiple_Knowledge_Bases::$version) ? "		EM"."KB version: " . Echo_Multiple_Knowledge_Bases::$version : '';

		ob_start();     ?>

		PHP and WordPress Information:
		==============================

		Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

		SITE_URL:                 <?php echo site_url() . "\n"; ?>
		HOME_URL:                 <?php echo home_url() . "\n"; ?>

		Echo Versions:            <?php echo $echo_versions . "\n"; ?>
		WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
		Permalink Structure:      <?php echo get_option( 'permalink_structure' ) . "\n"; ?>
		Active Theme:             <?php echo $theme; ?>

        <?php echo $host; ?>

		PHP Version:              <?php echo PHP_VERSION . "\n"; ?>

		PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
		PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
		PHP Max Input Vars:       <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
		WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

		WP Table Prefix:          <?php echo "Length: ". strlen( $wpdb->prefix );

		/* $params = array(
			'sslverify'		=> false,
			'timeout'		=> 60,
			'user-agent'	=> 'EDD/' . EDD_VERSION,
			'body'			=> '_notify-validate'
		);

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$WP_REMOTE_POST =  'wp_remote_post() works' . "\n";
		} else {
			$WP_REMOTE_POST =  'wp_remote_post() does not work' . "\n";
		}		?>

		WP Remote Post:           <?php echo $WP_REMOTE_POST; ?> */  ?>

		DISPLAY ERRORS:           <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
		FSOCKOPEN:                <?php echo ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
		cURL:                     <?php echo ( function_exists( 'curl_init' ) ) ? 'Your server supports cURL:' : 'Your server does not support cURL.'; ?><?php echo "\n";

									if ( function_exists( 'curl_init' ) ) {
										$curl_values = curl_version();
										echo "\n\t\t\t\tVersion: " . $curl_values["version"];
										echo "\n\t\t\t\tSSL Version: " . $curl_values["ssl_version"];
										echo "\n\t\t\t\tLib Version: " . $curl_values["libz_version"] . "\n";
									}		?>

		SOAP Client:              <?php echo ( class_exists( 'SoapClient' ) ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>

		ACTIVE PLUGINS:	         <?php echo "\n";

		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			echo "		" . $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
		}

		if ( is_multisite() ) {		?>
			NETWORK ACTIVE PLUGINS:		<?php  echo "\n";

			$plugins = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );

				// If the plugin isn't active, don't show it.
				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}

				$plugin = get_plugin_data( $plugin_path );

				echo $plugin['Name'] . ' :' . $plugin['Version'] ."\n";
			}
		}

		return ob_get_clean();
	}
}