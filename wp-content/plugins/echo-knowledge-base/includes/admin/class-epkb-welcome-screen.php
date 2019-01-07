<?php

/**
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Welcome_Screen {

	private $configuration_url;
	const PLUGIN_NAME = 'Knowledge Base for Documents and FAQs';

	public function __construct() {

	    $this->configuration_url = 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-kb-configuration';

		// user clicked on link for getting started or what's new page
		if ( EPKB_Utilities::post('page') == 'epkb-welcome-page' ) {
			add_action( 'admin_menu', array( $this, 'register_welcome_page') );
			return;
		}

		// ignore if plugin not activated
		if ( ! get_transient( '_epkb_plugin_activated' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_epkb_plugin_activated' );

		// return if activating from network or doing bulk activation
		if ( is_network_admin() || isset($_GET['activate-multi']) ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'forward_to_welcome_page' ), 20 );
	}

	/**
	 * Trigger display of welcome screen on plugin first activation or upgrade
	 */
	public function forward_to_welcome_page() {
		wp_safe_redirect( admin_url( 'index.php?page=epkb-welcome-page' ) ); exit;
	}

	/**
	 * Register welcome page
	 */
	public function register_welcome_page() {
		// About Page
		$welcome_page = add_dashboard_page( __( 'Welcome to ' . self::PLUGIN_NAME , 'echo-knowledge-base' ),
											__( 'Welcome to ' . self::PLUGIN_NAME , 'echo-knowledge-base' ), 'manage_options',
											'epkb-welcome-page', array( $this, 'show_welcome_page' ) );
		if ( $welcome_page === false ) {
			return;
		}

		// load scripts needed for Features Settings page only on that page
		add_action( 'load-' . $welcome_page, array( $this, 'load_admin_page_resources') );

		// do not show this page in the WP menu itself
		remove_submenu_page( 'index.php', 'epkb-welcome-page' );
	}

	/**
	 * Show the Welcome page
	 */
	public function show_welcome_page() {

		// was plugin just installed?
		$is_plugin_activated_first_time = get_transient( '_epkb_plugin_installed' );
		delete_transient( '_epkb_plugin_installed' );
		$is_plugin_installed = ! empty($is_plugin_activated_first_time);

		$start_tab_active = $is_plugin_installed || ( EPKB_Utilities::post('tab') == 'get-started' ) ? 'active' : '';
		$news_tab_active = $start_tab_active ? '' : 'active';    ?>

		<div class="wrap">
			<h1></h1>
		</div>
		<div class="wrap">
			<div id="ekb-admin-page-wrap">

				<!-- WELCOME HEADER -->
				<div class="welcome_header">
					<div class="container-fluid">
						<div class="row">
							<div class="col-6">
								<h1><?php esc_html_e( 'Welcome to ' . self::PLUGIN_NAME, 'echo-knowledge-base' ); ?> <?php echo Echo_Knowledge_Base::$version; ?></h1>
								<p>            <?php
									echo $this->show_top_section( $is_plugin_installed ); ?>
								</p>
							</div>
							<div class="col-2">
								<div class="logo">
									<img src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png'; ?>">
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- TABS -->
				<h2 id="welcome_tab_nav" class="nav-tab-wrapper">
					<a href="#" class="nav_tab nav-tab <?php echo $news_tab_active; ?>"><?php esc_html_e( "What's New", 'echo-knowledge-base' ); ?></a>
					<a href="#" class="nav_tab nav-tab <?php echo $start_tab_active; ?>"><?php esc_html_e( 'Getting Started', 'echo-knowledge-base' ); ?></a>
				</h2>

				<!-- TAB CONTENT -->
				<div id="welcome_panel_container" class="ekb-admin-pages-panel-container">

					<!-- WHAT IS NEW -->
					<div class="ekb-admin-page-tab-panel <?php echo $news_tab_active; ?>">
						<div class="container-fluid">   <?php
							$this->show_what_is_new();   ?>
						</div>
					</div>

					<!-- GET STARTED -->
					<div class="ekb-admin-page-tab-panel <?php echo $start_tab_active; ?>">
						<div class="container-fluid">   <?php
							$this->show_getting_started();   ?>
						</div>
					</div>

				</div>

			</div>
		</div>    	<?php
	}

	private function show_top_section( $is_plugin_installed ) {

		$i18_config = '<a href="' . admin_url($this->configuration_url . '&ekb-main-page=yes') . '" target="_blank">' .	esc_html__( 'Configuration', 'echo-knowledge-base' ) . '</a>';

		// plugin was installed
		if ( $is_plugin_installed ) {

			return sprintf( esc_html__( 'Thanks for installing ' . self::PLUGIN_NAME . ' plugin. You can setup your Knowledge Base by reading the Getting Started ' .
					            'section below or you can explore %s.', 'echo-knowledge-base' ), $i18_config ) . ' <p class="by_echo">By Echo Plugins</p>';
		// plugin was updated
		} else {
			return sprintf( esc_html__( 'Thanks for using ' . self::PLUGIN_NAME . ' plugin. You can read about our new features ' .
	                            'below or explore the new %s.'), $i18_config ) . ' <p class="by_echo">By Echo Plugins</p>';
		}
	}

	/**
	 * Show What's New tab
	 */
	private function show_what_is_new() {   ?>

       <!-- <div class="eckb-whats-new">
            <div class="eckb-content">
                <h2>Details About the Latest Update</h2>
                <a class="whats-new-button" href="https://www.echoknowledgebase.com/kb-updates-for-access-manager-search/" target="_blank"> <?php esc_html_e( "Read Full Article on Our Blog", 'echo-knowledge-base' ); ?></a>
            </div>
            <div class="bg-color"></div>
        </div> -->

		<?php // TODO move to blog ?>
		<h2>Changelog</h2>
		<div class="row">
			<div class="col-12">
				<h4>4.3.0, Dec 8, 2018</h4>
				<ul>
					<li>New: Make article updates compatible with Gutenberg</li>
					<li>New: Finalize WPML compatibility</li>
					<li>Fix: Add Section Head Category icon for Tabs Layout</li>
					<li>Fix: Minor issues</li>
				</ul>
				<h4>4.2.0, Nov 1, 2018</h4>
				<ul>
					<li>New: Add initial version of analytics page</li>
					<li>New: Prepare for WPML compability</li>
					<li>Fix: Minor issues</li>
				</ul>
				<h4>4.1.0, Oct 17, 2018</h4>
				<ul>
					<li>New: Make KB compatible with Gutenberg</li>
					<li>Fix: Minor issues</li>
				</ul>
			</div>
		</div>      <?php
	}

	/**
	 * Show Getting Started tab
	 */
	private function show_getting_started() {

		$new_kb_url = '';
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		if ( ! is_wp_error( $kb_config ) && ! empty($kb_config['kb_main_pages']) ) {
			reset($kb_config['kb_main_pages']);
			$new_kb_url = get_permalink( key($kb_config['kb_main_pages']) );
		}

		/**  KB COMPOSITION  */
		$content = '<p>' . __('Our Knowledge Base (KB) is composed of:', 'echo-knowledge-base') . '</p>
					<ul>
						<li style="padding-bottom: 10px;"><strong>' . __( 'KB Main page', 'echo-knowledge-base' ) . '</strong> - ' . __( 'lists knowledge base articles', 'echo-knowledge-base' ) . ',</li>
						<li style="padding-bottom: 10px;"><strong>' . __( 'KB Articles', 'echo-knowledge-base' ) . ' - ' . __( 'the actual KB content', 'echo-knowledge-base' ) . ',</li>
						<li><strong>' . __( 'KB Categories', 'echo-knowledge-base' ) . ' - ' . __( 'help you to organize articles into groups and hierarchies', 'echo-knowledge-base' ) . '.</li>
					</ul>';
		$image_url = 'http://www.echoknowledgebase.com/wp-content/uploads/2016/03/KB-Main-Page-Overview-e1495826007554.jpg';
		$this->display_row( __( 'Overview', 'echo-knowledge-base' ), $content, $image_url );

		/**  DEMO KB  */
		$content = '<p>' . __( 'Use Demo KB to play with configuration of layouts, styles and colors', 'echo-knowledge-base' ) . '.</p>
					<p>' . __( 'Switch to the Demo KB at any time to see how populated Knowledge Base will look with a specific configuration', 'echo-knowledge-base' ) . '.</p>
					<p>' . __( 'Note that Demo data is never saved to your KB.', 'echo-knowledge-base' ) . '.</p>';
		$image_url = 'http://www.echoknowledgebase.com/wp-content/uploads/2017/06/Demo-Data.jpg';
		$button1 = array( __( 'View Demo KB', 'echo-knowledge-base' ), admin_url( $this->configuration_url . '&epkb-demo=yes') );
		$this->display_row( __( 'Demo Knowledge Base', 'echo-knowledge-base' ), $content, $image_url, $button1 );

		/**  YOUR KB  */
		$content = '<p>' . __( 'Initial pre-configured KB has been created with one KB Category and one empty KB Article', 'echo-knowledge-base' ) . '.</p>
					<p>' . __( 'To further configure your knowledge base, you need to select KB Layout, Style, Colors and slug', 'echo-knowledge-base' ) . '.</p>';
		$image_url1 = 'http://www.echoknowledgebase.com/wp-content/uploads/2017/06/Configuration-Overview.jpg';
		//$image_url2 = 'http://www.echoknowledgebase.com/wp-content/uploads/2017/05/EPKB-Getting-Started-Reset-Colors.jpg';
		//$image_url3 = 'http://www.echoknowledgebase.com/wp-content/uploads/2017/05/EPKB-Getting-Started-Reset-Styles.jpg';
		$button1 = array( __( 'Configure KB', 'echo-knowledge-base' ), admin_url( $this->configuration_url . '&ekb-main-page=yes') );
		$button2 = array();
		if ( ! empty($new_kb_url) && ! is_wp_error( $new_kb_url ) ) {
			$button2 = array( __( 'View Initial KB', 'echo-knowledge-base' ), $new_kb_url );
		}
		$this->display_row( __( 'Your Initial Knowledge Base', 'echo-knowledge-base' ), $content, $image_url1, $button1, $button2 );

		$this->display_help_row();
	}

	private function display_row( $title, $content, $image_url, $button1=array(), $button2=array() ) {

		$first_col_width = empty($image_url) ? 'col-5' : 'col-3';       ?>

		<div class="row">
			<div class="<?php echo $first_col_width; ?>">
				<h3><?php echo $title; ?></h3>				<?php
				echo $content;
				echo '<div class="row">';
                    if ( ! empty($button1) ) {
                        echo '<div class="col-6">';
                            $this->display_button( $button1[0], $button1[1] );
                        echo '</div>';
                    }
                    if ( ! empty($button2) ) {
                        echo '<div class="col-6">';
                            $this->display_button( $button2[0], $button2[1] );
                        echo '</div>';
                    }
				echo '</div>';				?>
			</div>
			<div class="col-7">
				<p style="color: forestgreen; font-style: italic;"><?php _e( 'Click on any image to Zoom in', 'echo-knowledge-base' ); ?></p>                <?php
				if ( is_array($image_url) ) {       ?>
						<div class="featured_img"> <?php
							$ix = 0;
							foreach ( $image_url as $one_image ) {
								$ix++;
								echo '<img id="epkb-welcome-page-img' . $ix . '" src="' . $one_image . '" ' . ( $ix > 1 ? 'style="display:none;"' : '' ) . '>';
							}							?>
						</div>
						<div>       <?php
							$ix = 0;
							foreach ( $image_url as $one_image ) {
								$ix++;
								echo '<img class="epkb-gallery-thumb" id="epkb-welcome-page-img' . $ix . '-thumb" style="height:90px; width:90px;" src="' . $one_image . '">';
							}					?>
						</div>
					    <?php
				} else if ( ! empty($image_url) ) { ?>
						<div class="featured_img">
							<img src="<?php echo $image_url; ?>">
						</div>						      <?php
				}		?>
			</div>
		</div>      <?php
	}

	private function display_help_row( $plugin_name='knowledge-base' ) {

		$document = $plugin_name == 'knowledge-base' ? '' : '?top-category=' . $plugin_name;     ?>
        <br />
		<h2><?php _e( 'Need Help?', 'echo-knowledge-base' ); ?></h2>
		<div class="row">
			<div class="col-3">
				<h3><span class="welcome-icon ep_font_icon_documents"></span> <?php _e( 'Documentation', 'echo-knowledge-base' ); ?></h3>
				<p><?php _e( 'Reference our Knowledge Base as it covers all the plugin features', 'echo-knowledge-base' ); ?>.</p>
				<a class="button primary-btn" href="https://www.echoknowledgebase.com/documentation/<?php echo $document; ?>" target="_blank"><?php _e( 'Knowledge Base', 'echo-knowledge-base' ); ?></a>
			</div>
			<div class="col-3">
				<h3><span class="welcome-icon ep_font_icon_comment"></span> <?php _e( 'Still Need Some Help?', 'echo-knowledge-base' ); ?></h3>
				<p><?php _e( 'If you encounter an issue or have a question, please submit your request below', 'echo-knowledge-base' ); ?>.</p>
				<a class="button primary-btn" href="https://www.echoknowledgebase.com/contact-us/?inquiry-type=technical/" target="_blank"><?php _e( 'Contact Us', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>      <?php
	}

	private function display_button( $label, $url ) {       ?>
		<!-- <div class="col-1"> -->
			<a class="button primary-btn new_kb_notice_btn" href="<?php echo $url; ?>" target="_blank"><?php echo $label; ?></a>
		<!-- </div> -->      <?php
	}

	public function load_admin_page_resources() {
		add_action( 'admin_enqueue_scripts', 'epkb_load_admin_plugin_pages_resources' );
	}
}
