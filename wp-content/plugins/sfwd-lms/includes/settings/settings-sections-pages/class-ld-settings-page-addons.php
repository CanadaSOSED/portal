<?php
if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( !class_exists( 'LearnDash_Settings_Page_Addons' ) ) ) {
	class LearnDash_Settings_Page_Addons extends LearnDash_Settings_Page {

		function __construct() {
			//$this->settings_screen_id		= 	'admin_page_learndash_lms_addons';
			//$this->parent_menu_page_url		=	'learndash-lms';
			$this->parent_menu_page_url		=	'admin.php?page=learndash_lms_addons';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'learndash_lms_addons';
			$this->settings_page_title 		= 	esc_html__( 'LearnDash Add-ons', 'learndash' );
			$this->settings_tab_title		=	esc_html__( 'Add-ons', 'learndash' );
			$this->settings_tab_priority	=	0;

			// Override action with custom plugins function for add-ons.
			add_action( 'install_plugins_pre_plugin-information', array( $this, 'shows_addon_plugin_information' ) );
			add_filter( 'learndash_submenu_last', array( $this, 'submenu_item' ), 200 );

			add_filter( 'learndash_admin_tab_sets', array( $this, 'learndash_admin_tab_sets' ), 10, 3 );
			
			parent::__construct(); 
		}
		
		public function submenu_item( $submenu ) {
			if ( !isset( $submenu[$this->settings_page_id] ) ) {
				$license_status = get_option( 'nss_plugin_remote_license_sfwd_lms' );
				if ( isset( $license_status['value'] ) ) {
					$license_status = $license_status['value'];
					if ( !empty( $license_status ) && ( $license_status != 'false') && ( $license_status != 'not_found' ) ) {
						$submenu[$this->settings_page_id] = array(
				        	'name' => $this->settings_tab_title,
				        	'cap'  => $this->menu_page_capability, 
				        	'link' => $this->parent_menu_page_url,
				    	);
					}
				}
			}

		    return $submenu;
		}
				
		function get_admin_page_title() {
			return apply_filters( 'learndash_admin_page_title', '<h1>'. $this->settings_page_title . '</h1>' );
		}
		
		function load_settings_page() {
			require_once LEARNDASH_LMS_PLUGIN_DIR . '/includes/admin/class-learndash-admin-addons-list-table.php';
			
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'updates' );
			
			add_thickbox();			
		}
		
		function learndash_admin_tab_sets( $tab_set = array(), $tab_key = '', $current_page_id = '' ) {
			if ( ( !empty( $tab_set ) ) && ( !empty( $tab_key ) ) && ( !empty( $current_page_id ) ) ) {
				if ( 'admin_page_learndash_lms_addons' == $current_page_id ) {
					//$tab_set = array();
					//add_action( 'admin_footer', 'learndash_select_menu' );
					?><style> h1.nav-tab-wrapper { display: none; }</style><?php
				}
			} 
			return $tab_set;
		}

		function show_settings_page() {
			
			?>
			<div class="wrap learndash-settings-page-wrap">

				<?php settings_errors(); ?>

				<?php do_action('learndash_settings_page_before_title', $this->settings_screen_id ); ?>
				<?php echo $this->get_admin_page_title() ?>
				<?php do_action('learndash_settings_page_after_title', $this->settings_screen_id ); ?>
				
				<?php do_action('learndash_settings_page_before_form', $this->settings_screen_id ); ?>
				<div id="plugin-filter-xxx">
				<?php echo $this->get_admin_page_form( true ); ?>
				<?php do_action('learndash_settings_page_inside_form_top', $this->settings_screen_id ); ?>

					<?php //settings_fields( $this->settings_page_id );  ?>
					<?php //wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
					<?php //wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

					<?php
						$wp_list_table = new Learndash_Admin_Addons_List_Table();
						$wp_list_table->prepare_items();

						$wp_list_table->views();
						$wp_list_table->display();
					?>
				<?php do_action('learndash_settings_page_inside_form_bottom', $this->settings_screen_id ); ?>
				<?php echo $this->get_admin_page_form( false ); ?>
				</div>
				<?php do_action('learndash_settings_page_after_form', $this->settings_screen_id ); ?>
			</div>
			<?php
			/* The following is needed to trigger the wp-admin/js/updates.js logic in 
			 * wp.updates.updatePlugin() where is checks for specific pagenow values
			 * but doesn't leave any option for externals.
			 */
			?>
			<script type="text/javascript">
				//pagenow = 'plugin-install';
			</script>
			<?php
		}
		
		/**
		 * Display plugin information in dialog box form.
		 *
		 * @since 2.5.5
		 *
		 * @global string $tab
		 */
		function shows_addon_plugin_information() {
			if ( empty( $_REQUEST['plugin'] ) ) {
				return;
			}

			$addon_updater = new LearnDash_Addon_Updater();
			$plugin_readme_information = $addon_updater->get_plugin_information( esc_attr( $_REQUEST['plugin'] ) );
			//error_log('plugin_information<pre>'. print_r($plugin_readme_information, true) .'</pre>');

			if ( empty( $plugin_readme_information ) ) return;
			
			$api = new StdClass();
			foreach( $plugin_readme_information as $_k => $_s ) {
				$api->$_k = $_s;	
			}

			$plugins_allowedtags = array(
				'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ),
				'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ),
				'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
				'div' => array( 'class' => array() ), 'span' => array( 'class' => array() ),
				'p' => array(), 'br' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
				'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
				'img' => array( 'src' => array(), 'class' => array(), 'alt' => array() ),
				'blockquote' => array( 'cite' => true ),
			);

			$plugins_section_titles = array(
				'description'  => _x( 'Description',  'Plugin installer section title' ),
				'installation' => _x( 'Installation', 'Plugin installer section title' ),
				'faq'          => _x( 'FAQ',          'Plugin installer section title' ),
				'screenshots'  => _x( 'Screenshots',  'Plugin installer section title' ),
				'changelog'    => _x( 'Changelog',    'Plugin installer section title' ),
				'reviews'      => _x( 'Reviews',      'Plugin installer section title' ),
				'other_notes'  => _x( 'Other Notes',  'Plugin installer section title' )
			);

			// Sanitize HTML
			foreach ( (array) $api->sections as $section_name => $content ) {
				$api->sections[$section_name] = wp_kses( $content, $plugins_allowedtags );
			}
			
			foreach ( array( 'version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug' ) as $key ) {
				if ( isset( $api->$key ) ) {
					$api->$key = wp_kses( $api->$key, $plugins_allowedtags );
				}
			}
			//error_log('api<pre>'. print_r($api, true) .'</pre>');
			
			$section = isset( $_REQUEST['section'] ) ? wp_unslash( $_REQUEST['section'] ) : 'description'; // Default to the Description tab, Do not translate, API returns English.
			if ( empty( $section ) || ! isset( $api->sections[ $section ] ) ) {
				$section_titles = array_keys( (array) $api->sections );
				$section = reset( $section_titles );
			}
			
			if ( ( isset( $_GET['tab'] ) ) && ( !empty( $_GET['tab'] ) ) ) {
				$tab = esc_attr( $_GET['tab'] );
			} else {
				$tab = 'plugin-information';
			}
			$_tab = $tab;
			
			$section = isset( $_REQUEST['section'] ) ? wp_unslash( $_REQUEST['section'] ) : 'description'; // Default to the Description tab, Do not translate, API returns English.
			if ( empty( $section ) || ! isset( $api->sections[ $section ] ) ) {
				$section_titles = array_keys( (array) $api->sections );
				$section = reset( $section_titles );
			}

			iframe_header( __( 'Plugin Installation' ) );

			$_with_banner = '';

			if ( ! empty( $api->banners ) && ( ! empty( $api->banners['low'] ) || ! empty( $api->banners['high'] ) ) ) {
				$_with_banner = 'with-banner';
				$low  = empty( $api->banners['low'] ) ? $api->banners['high'] : $api->banners['low'];
				$high = empty( $api->banners['high'] ) ? $api->banners['low'] : $api->banners['high'];
				?>
				<style type="text/css">
					#plugin-information-title.with-banner {
						background-image: url( <?php echo esc_url( $low ); ?> );
					}
					@media only screen and ( -webkit-min-device-pixel-ratio: 1.5 ) {
						#plugin-information-title.with-banner {
							background-image: url( <?php echo esc_url( $high ); ?> );
						}
					}
				</style>
				<?php
			}

			echo '<div id="plugin-information-scrollable">';
			echo "<div id='{$_tab}-title' class='{$_with_banner}'><div class='vignette'></div><h2>{$api->name}</h2></div>";
			echo "<div id='{$_tab}-tabs' class='{$_with_banner}'>\n";

			foreach ( (array) $api->sections as $section_name => $content ) {
				if ( 'reviews' === $section_name && ( empty( $api->ratings ) || 0 === array_sum( (array) $api->ratings ) ) ) {
					continue;
				}

				if ( isset( $plugins_section_titles[ $section_name ] ) ) {
					$title = $plugins_section_titles[ $section_name ];
				} else {
					$title = ucwords( str_replace( '_', ' ', $section_name ) );
				}

				$class = ( $section_name === $section ) ? ' class="current"' : '';
				$href = add_query_arg( array('tab' => $tab, 'section' => $section_name) );
				$href = esc_url( $href );
				$san_section = esc_attr( $section_name );
				echo "\t<a name='$san_section' href='$href' $class>$title</a>\n";
			}

			echo "</div>\n";

			?>
		<div id="<?php echo $_tab; ?>-content" class='<?php echo $_with_banner; ?>'>
			<div class="fyi">
				<ul>
					<?php if ( ! empty( $api->version ) ) { ?>
						<li><strong><?php _e( 'Version:' ); ?></strong> <?php echo $api->version; ?></li>
					<?php } if ( ! empty( $api->author ) ) { ?>
						<li><strong><?php _e( 'Author:' ); ?></strong> <?php echo links_add_target( $api->author, '_blank' ); ?></li>
					<?php } if ( ! empty( $api->last_updated ) ) { ?>
						<li><strong><?php _e( 'Last Updated:' ); ?></strong>
							<?php
							/* translators: %s: Time since the last update */
							printf( __( '%s ago' ), human_time_diff( strtotime( $api->last_updated ) ) );
							?>
						</li>
					<?php } if ( ! empty( $api->requires ) ) { ?>
						<li>
							<strong><?php _e( 'Requires WordPress Version:' ); ?></strong>
							<?php
							/* translators: %s: WordPress version */
							printf( __( '%s or higher' ), $api->requires );
							?>
						</li>
					<?php } if ( ! empty( $api->tested ) ) { ?>
						<li><strong><?php _e( 'Compatible up to:' ); ?></strong> <?php echo $api->tested; ?></li>
					<?php } if ( isset( $api->active_installs ) ) { ?>
						<li><strong><?php _e( 'Active Installations:' ); ?></strong> <?php
							if ( $api->active_installs >= 1000000 ) {
								_ex( '1+ Million', 'Active plugin installations' );
							} elseif ( 0 == $api->active_installs ) {
								_ex( 'Less Than 10', 'Active plugin installations' );
							} else {
								echo number_format_i18n( $api->active_installs ) . '+';
							}
							?></li>
					<?php } if ( ! empty( $api->slug ) && empty( $api->external ) ) { ?>
						<li><a target="_blank" href="<?php echo __( 'https://wordpress.org/plugins/' ) . $api->slug; ?>/"><?php _e( 'WordPress.org Plugin Page &#187;' ); ?></a></li>
					<?php } if ( ! empty( $api->homepage ) ) { ?>
						<li><a target="_blank" href="<?php echo esc_url( $api->homepage ); ?>"><?php _e( 'Plugin Homepage &#187;' ); ?></a></li>
					<?php } if ( ! empty( $api->donate_link ) && empty( $api->contributors ) ) { ?>
						<li><a target="_blank" href="<?php echo esc_url( $api->donate_link ); ?>"><?php _e( 'Donate to this plugin &#187;' ); ?></a></li>
					<?php } ?>
				</ul>
				<?php if ( ! empty( $api->rating ) ) { ?>
					<h3><?php _e( 'Average Rating' ); ?></h3>
					<?php wp_star_rating( array( 'rating' => $api->rating, 'type' => 'percent', 'number' => $api->num_ratings ) ); ?>
					<p aria-hidden="true" class="fyi-description"><?php printf( _n( '(based on %s rating)', '(based on %s ratings)', $api->num_ratings ), number_format_i18n( $api->num_ratings ) ); ?></p>
				<?php }

				if ( ! empty( $api->ratings ) && array_sum( (array) $api->ratings ) > 0 ) { ?>
					<h3><?php _e( 'Reviews' ); ?></h3>
					<p class="fyi-description"><?php _e( 'Read all reviews on WordPress.org or write your own!' ); ?></p>
					<?php
					foreach ( $api->ratings as $key => $ratecount ) {
						// Avoid div-by-zero.
						$_rating = $api->num_ratings ? ( $ratecount / $api->num_ratings ) : 0;
						/* translators: 1: number of stars (used to determine singular/plural), 2: number of reviews */
						$aria_label = esc_attr( sprintf( _n( 'Reviews with %1$d star: %2$s. Opens in a new window.', 'Reviews with %1$d stars: %2$s. Opens in a new window.', $key ),
							$key,
							number_format_i18n( $ratecount )
						) );
						?>
						<div class="counter-container">
								<span class="counter-label"><a href="https://wordpress.org/support/view/plugin-reviews/<?php echo $api->slug; ?>?filter=<?php echo $key; ?>"
								                               target="_blank" aria-label="<?php echo $aria_label; ?>"><?php printf( _n( '%d star', '%d stars', $key ), $key ); ?></a></span>
								<span class="counter-back">
									<span class="counter-bar" style="width: <?php echo 92 * $_rating; ?>px;"></span>
								</span>
							<span class="counter-count" aria-hidden="true"><?php echo number_format_i18n( $ratecount ); ?></span>
						</div>
						<?php
					}
				}
				if ( ! empty( $api->contributors ) ) { ?>
					<h3><?php _e( 'Contributors' ); ?></h3>
					<ul class="contributors">
						<?php
						foreach ( (array) $api->contributors as $contrib_username => $contrib_profile ) {
							if ( empty( $contrib_username ) && empty( $contrib_profile ) ) {
								continue;
							}
							if ( empty( $contrib_username ) ) {
								$contrib_username = preg_replace( '/^.+\/(.+)\/?$/', '\1', $contrib_profile );
							}
							$contrib_username = sanitize_user( $contrib_username );
							if ( empty( $contrib_profile ) ) {
								echo "<li><img src='https://wordpress.org/grav-redirect.php?user={$contrib_username}&amp;s=36' width='18' height='18' alt='' />{$contrib_username}</li>";
							} else {
								echo "<li><a href='{$contrib_profile}' target='_blank'><img src='https://wordpress.org/grav-redirect.php?user={$contrib_username}&amp;s=36' width='18' height='18' alt='' />{$contrib_username}</a></li>";
							}
						}
						?>
					</ul>
					<?php if ( ! empty( $api->donate_link ) ) { ?>
						<a target="_blank" href="<?php echo esc_url( $api->donate_link ); ?>"><?php _e( 'Donate to this plugin &#187;' ); ?></a>
					<?php } ?>
				<?php } ?>
			</div>
			<div id="section-holder" class="wrap">
			<?php
			$wp_version = get_bloginfo( 'version' );

			if ( ! empty( $api->tested ) && version_compare( substr( $wp_version, 0, strlen( $api->tested ) ), $api->tested, '>' ) ) {
				echo '<div class="notice notice-warning notice-alt"><p>' . __( '<strong>Warning:</strong> This plugin has <strong>not been tested</strong> with your current version of WordPress.' ) . '</p></div>';
			} elseif ( ! empty( $api->requires ) && version_compare( substr( $wp_version, 0, strlen( $api->requires ) ), $api->requires, '<' ) ) {
				echo '<div class="notice notice-warning notice-alt"><p>' . __( '<strong>Warning:</strong> This plugin has <strong>not been marked as compatible</strong> with your version of WordPress.' ) . '</p></div>';
			}

			foreach ( (array) $api->sections as $section_name => $content ) {
				$content = links_add_base_url( $content, 'https://wordpress.org/plugins/' . $api->slug . '/' );
				$content = links_add_target( $content, '_blank' );

				$san_section = esc_attr( $section_name );

				$display = ( $section_name === $section ) ? 'block' : 'none';

				echo "\t<div id='section-{$san_section}' class='section' style='display: {$display};'>\n";
				echo $content;
				echo "\t</div>\n";
			}
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n"; // #plugin-information-scrollable
			echo "<div id='$tab-footer'>\n";
			if (  empty( $api->download_link ) && ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) ) {
				if ( isset( $api->plugin_status ) ) {
					$status = $api->plugin_status;
					switch ( $status['status'] ) {
						case 'install':
							if ( $status['url'] ) {
								echo '<a data-slug="' . esc_attr( $api->slug ) . '" id="plugin_install_from_iframe" class="button button-primary right" href="' . $status['url'] . '" target="_parent">' . __( 'Install Now' ) . '</a>';
							}
							break;
						case 'update_available':
							if ( $status['url'] ) {
								echo '<a data-slug="' . esc_attr( $api->slug ) . '" data-plugin="' . esc_attr( $status['file'] ) . '" id="plugin_update_from_iframe" class="button button-primary right" href="' . $status['url'] . '" target="_parent">' . __( 'Install Update Now' ) .'</a>';
							}
							break;
						case 'newer_installed':
							/* translators: %s: Plugin version */
							echo '<a class="button button-primary right disabled">' . sprintf( __( 'Newer Version (%s) Installed'), $status['version'] ) . '</a>';
							break;
						case 'latest_installed':
							echo '<a class="button button-primary right disabled">' . __( 'Latest Version Installed' ) . '</a>';
							break;
					}
				}
			}
			echo "</div>\n";

			iframe_footer();
			exit;
		}
	}
}
add_action( 'learndash_settings_pages_init', function() {
	LearnDash_Settings_Page_Addons::add_page_instance();
} );
