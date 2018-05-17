<?php
/**
 * Plugin updater
 * 
 * @since 2.1.0
 *
 * @package LearnDash\Updater
 */



class nss_plugin_updater_sfwd_lms {

	/**
	 * The plugin current version
	 * @var string
	 */
	public $current_version;

	/**
	 * The plugin remote update path
	 * @var string
	 */
	public $update_path;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	public $slug;

	/**
	 * Initialized as $slug, this is used as a substring to create dynamic hooks and actions
	 * @var string
	 */
	public $code;

	private $ld_updater;

	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 *
	 * @since 2.1.0
	 *
	 * @param string $update_path
	 * @param string $plugin_slug
	 */
	function __construct( $update_path, $plugin_slug ) {

		// Set the class public variables
		//$this->update_path = $update_path;
		$this->plugin_slug = $plugin_slug;
		$this->current_version = $this->get_plugin_data()->Version;

		list ( $t1, $t2 ) = explode( '/', $plugin_slug );
		$this->slug = str_replace( '.php', '', $t2 );
		$code = $this->code = $this->slug;

		$license = get_option( 'nss_plugin_license_'.$code );
		$licenseemail = get_option( 'nss_plugin_license_email_'.$code );
		$this->update_path = $update_path.'?pluginupdate='.$code.'&licensekey='.urlencode( $license ).'&licenseemail='.urlencode( $licenseemail ).'&nsspu_wpurl='.urlencode( get_bloginfo( 'wpurl' ) ).'&nsspu_admin='.urlencode( get_bloginfo( 'admin_email' ) ).'&current_version='.$this->current_version;

		$this->time_to_recheck();

		//Add Menu
		add_action( 'admin_menu', array( &$this, 'nss_plugin_license_menu' ), 1 );

		// define the alternative API for updating checking
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );

		// Define the alternative response for information checking
		add_filter( 'plugins_api', array( &$this, 'check_info' ), 50, 3 );
		//    add_action( 'admin_notices', array( &$this, 'admin_notice' ));
		add_action( 'in_admin_header', array( &$this, 'check_notice' ) );
	}

	/**
	 * Checks to see if a license administrative notice needs to be displayed, and if so, displays it.
	 *
	 * @since 2.1.0
	 *
	 */
	function check_notice() {
		//add_action( 'admin_notices', array( &$this, 'admin_notice' ));
		if ( @$_REQUEST['page'] == 'nss_plugin_license-'.$this->code.'-settings' ) {
			$this->check_update( array( ) );
		}

		$license = get_option( 'nss_plugin_remote_license_'.$this->slug );

		if ( isset( $license['value'] ) && empty( $license['value'] ) ) {
			add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
		}
	}



	/**
	 * Determines if the plugin should check for updates
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	function time_to_recheck() {
		$nss_plugin_check = get_option( 'nss_plugin_check_'.$this->slug );

		if ( empty( $nss_plugin_check) || ( ! empty( $_REQUEST['pluginupdate'] ) && $_REQUEST['pluginupdate'] == $this->code ) || ! empty( $_GET['force-check'] ) || $nss_plugin_check <= time() - 12 * 60 * 60  || @$_REQUEST['page'] == 'nss_plugin_license-'.$this->code.'-settings' ) {
			$this->reset();
			return true;
		} else {
			return false;
		}
	}



	/**
	 * Resets the time the plugin was checked last, and removes previous license, version, and plugin info data
	 *
	 * @since 2.1.0
	 *
	 */
	function reset() {
		delete_option( 'nss_plugin_remote_version_'.$this->slug );
		delete_option( 'nss_plugin_remote_license_'.$this->slug );
		delete_option( 'nss_plugin_info_'.$this->slug );
		update_option( 'nss_plugin_check_'.$this->slug, time() );
	}



	/**
	 * Echos the administrative notice if the plugin license is incorrect
	 *
	 * @since 2.1.0
	 *
	 */
	function admin_notice() {
		/*
		echo '<style>
				#nss_plugin_updater_admin_notice {
					width: 90%;
					margin: auto;
					margin-top: 30px;
					border: 2px solid black;
					padding: 5px 10px;
					background-color: yellow;
				}
				</style>';
		$licensepage = get_admin_url( null,'admin.php?page=nss_plugin_license-'.$this->code.'-settings' );
		
		echo "<p id='nss_plugin_updater_admin_notice'>License of your plugin <b>".$this->get_plugin_data()->Name."</b> is invalid or incomplete. Please click <a href='".$licensepage."'>here</a> and update your license.</p>";
		*/

		$current_screen = get_current_screen();
		if ( ( 'admin_page_nss_plugin_license-sfwd_lms-settings' != $current_screen->id ) && ( 'dashboard' != $current_screen->id ) ) {
			$licensepage = get_admin_url( null, 'admin.php?page=nss_plugin_license-sfwd_lms-settings' );
			?>
			<div class="notice notice-error below-h2 is-dismissible">
				<p><?php echo wp_kses_post( __('License of your plugin <strong>'. $this->get_plugin_data()->Name .'</strong> is invalid or incomplete. Please click <a href="'. $licensepage .'">here</a> and update your license.', 'learndash' ) ) ?></p>
			</div>
			<?php
		}
	}



	/**
	 * Adds admin notices, and deactivates the plugin.
	 *
	 * @since 2.1.0
	 *
	 */
	function invalid_current_license() {
		add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
		deactivate_plugins( $this->plugin_slug );
	}



	/**
	 * Returns the metadata of the LearnDash plugin
	 *
	 * @since 2.1.0
	 *
	 * @return object Metadata of the LearnDash plugin
	 */
	function get_plugin_data() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php' );
		}

		return (object) get_plugin_data( dirname( dirname( dirname( __FILE__ ) ) ).DIRECTORY_SEPARATOR.$this->plugin_slug );
	}



	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @since 2.1.0
	 *
	 * @param $transient
	 *
	 * @return object $transient
	 */
	public function check_update( $transient ) {
		if ( empty( $transient->checked) ) {
			// return $transient;
		}

		if ( ! $this->time_to_recheck() ) {
			$remote_version = get_option( 'nss_plugin_remote_version_'.$this->slug );
			$license = get_option( 'nss_plugin_remote_license_'.$this->slug );
		}

		// Get the remote version
		if ( empty( $remote_version) ) {
			$info = $this->getRemote_information();
			if ( property_exists( $info, 'new_version' ) ) {
				$remote_version = $info->new_version;
				update_option( 'nss_plugin_remote_version_'.$this->slug, $remote_version );
				update_option( 'nss_plugin_info_'.$this->slug, $info );
			}
		}

		if ( empty( $license) ) {
			$value = $this->getRemote_license();
			$license = array( 'value' => $value);
			update_option( 'nss_plugin_remote_license_'.$this->slug, $license );
		}

		if ( empty( $license) ) {
			$this->getRemote_current_license(); }

		if ( empty( $license['value'] ) ) {
			add_action( 'admin_notices', array( &$this, 'admin_notice' ) ); }

		// If a newer version is available, add the update
		if ( version_compare( $this->current_version, $remote_version, '<' ) ) {			
			$obj = new stdClass();
			$obj->slug = $this->slug;
			$obj->new_version = $remote_version;
			$obj->plugin = 'sfwd-lms/'. $this->slug;
			$obj->url = $this->update_path;
			$obj->package = $this->update_path;
			
			if ( is_null( $this->ld_updater ) ) {
				$this->ld_updater = new LearnDash_Addon_Updater();
			}
			//$this->ld_updater->load_repositories_options();
			$this->ld_updater->get_addon_plugins();
			
			$plugin_readme = $this->ld_updater->update_plugin_readme( 'learndash-core-readme' );
			
			if ( !empty( $plugin_readme ) ) {
				// First we remove the properties we DON'T want from the support site
				foreach( array( 'sections', 'requires', 'tested', 'last_updated' ) as $property_key ) {
					if ( property_exists ( $obj, $property_key ) ) {
						unset( $obj->$property_key );
					}
				}
				
				foreach( $plugin_readme as $key => $val ) {
					if ( !property_exists ( $obj, $key ) ) {
						$obj->$key = $val;
					}
				}
			}
						
			if ( !property_exists ( $obj, 'icons' ) ) {
				// Add an image for the WP 4.9.x plugins update screen.
				$obj->icons = array(
					'default' => LEARNDASH_LMS_PLUGIN_URL .'/assets/images/ld-plugin-image.jpg'
				);
			}
			
			$transient->response[ $this->plugin_slug ] = $obj;
		}

		return $transient;
	}



	/**
	 * Add our self-hosted description to the filter, or returns false
	 *
	 * @since 2.1.0
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 *
	 * @return bool|object
	 */
	public function check_info( $false, $action, $arg ) {
		if ( empty( $arg ) || empty( $arg->slug ) || empty( $this->slug ) ) {
			return $false;
		}

		if ( $arg->slug === $this->slug ) {

			if ( ! $this->time_to_recheck() ) {
				$info = get_option( 'nss_plugin_info_'.$this->slug );
				if ( ! empty( $info ) ) {
					return $info;
				}
			}

			if ( 'plugin_information' == $action ) {
				$information = $this->getRemote_information();
				
				update_option( 'nss_plugin_info_'.$this->slug, $information );
				$false = $information;
			}
		}

		return $false;
	}



	/**
	 * Return the remote version, or returns false
	 *
	 * @return bool|string $remote_version
	 */
	public function getRemote_version() {
		$request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => 'version' ) ) );
		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return $request['body'];
		}

		return false;
	}



	/**
	 * Get information about the remote version, or returns false
	 *
	 * @return bool|object
	 */
	public function getRemote_information()	{
		$request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => 'info' ) ) );

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			$information = @unserialize( $request['body'] );
			if ( empty( $information ) ) {
				$information = new stdClass();
			}
			
			if ( is_null( $this->ld_updater ) ) {
				$this->ld_updater = new LearnDash_Addon_Updater();
			}
			$plugin_readme = $this->ld_updater->update_plugin_readme( 'learndash-core-readme' );
			
			if ( !empty( $plugin_readme ) ) {
				// First we remove the properties we DON'T want from the support site
				foreach( array( 'sections', 'requires', 'tested', 'last_updated' ) as $property_key ) {
					if ( property_exists ( $information, $property_key ) ) {
						unset( $information->$property_key );
					}
				}
				
				foreach( $plugin_readme as $key => $val ) {
					if ( !property_exists ( $information, $key ) ) {
						$information->$key = $val;
					}
				}
							
				//$information_array = $this->ld_updater->convert_readme( (array)$information );
				//$information = (object)$information_array;
			}
			
			return $information;
		}

		return false;
	}



	/**
	 * Return the status of the plugin licensing, or returns true
	 *
	 * @since 2.1.0
	 *
	 * @return bool|string $remote_license
	 */
	public function getRemote_license()	{
		$request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => 'license' ) ) );

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			if ( $request['body'] == 'false' || $request['body'] == 'not_found' || empty( $request['body'] ) ) {
				add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
			}

			return $request['body'];
		}

		return true;
	}


	/**
	 * Retrieves the current license from remote server, or returns true
	 *
	 * @since 2.1.0
	 *
	 * @return bool|string $current_license
	 */
	public function getRemote_current_license()	{
		$request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => 'current_license' ) ) );

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			if ( $request['body'] == 'not_found' ) {
				$this->invalid_current_license();
			}

			return $request['body'];
		}

		//$this->invalid_current_license();
		return true;
	}


	/**
	 * Adds the license submenu to the administrative settings page
	 *
	 * @since 2.1.0
	 *
	 */
	function nss_plugin_license_menu() {
		$hello = '';
		add_submenu_page(
			'admin.php?page=learndash_lms_settings', //'learndash-lms-non-existant',
			$this->get_plugin_data()->Name.' License',
			$this->get_plugin_data()->Name.' License',
			LEARNDASH_ADMIN_CAPABILITY_CHECK,
			'nss_plugin_license-'.$this->code.'-settings',
			array( $this, 'nss_plugin_license_menupage' )
		);
	}

	/**
	 * Outputs the license settings page
	 *
	 * @since 2.1.0
	 *
	 */
	function nss_plugin_license_menupage() {
		$code = $this->code;

		//must check that the user has the required capability
		if ( !learndash_is_admin_user( ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Read in existing option value from database
		$license = get_option( 'nss_plugin_license_'.$code );
		$email = get_option( 'nss_plugin_license_email_'.$code );

		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if ( isset( $_POST[ 'update_nss_plugin_license_'.$code ] ) ) {
			// Read their posted value
			$license = $_POST['nss_plugin_license_'.$code];
			$email = $_POST['nss_plugin_license_email_'.$code];

			// Save the posted value in the database
			update_option( 'nss_plugin_license_'.$code, $license );
			update_option( 'nss_plugin_license_email_'.$code, $email );
			$this->reset();
			$this->check_update( array( ) );

			?>
			<script> window.location = window.location; </script>
			<?php
			// Put a settings updated message on the screen
		}

		$domain = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
		$license = get_option( 'nss_plugin_license_'.$code );
		$email = get_option( 'nss_plugin_license_email_'.$code );

		if ( ! empty( $license) && ! empty( $email) ) {
			$license_status = get_option( 'nss_plugin_remote_license_'.$this->slug );
			if ( isset( $license_status['value'] ) ) {
				$license_status = $license_status['value'];
			} else {
				$license_status = $this->getRemote_license();
			}
		}

		?>
		<style>
		.grayblock {
			border: solid 1px #ccc;
			background: #eee;
			padding: 1px 8px;
			width: 30%;
		}
		</style>
		<div class=wrap>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<h1><?php esc_html_e( 'License Settings', 'learndash' ); ?></h1>
				<br>
				<?php
				if ( ! isset( $_POST[ 'update_nss_plugin_license_'.$code ] ) ):

					if ( empty( $license_status) || $license_status == 'false' || $license_status == 'not_found' ): ?>
						
						<div class="notice notice-error">
							<p><?php echo sprintf( esc_html_x( 'Please enter a valid license or %s one now.', 'placeholder: link to purchase LearnDash', 'learndash' ), "<a href='http://www.learndash.com/' target='_blank'>". esc_html__( 'buy', 'learndash' ).'</a>' ); ?></p>
						</div>
						
				<?php else: ?>
						
						<div class="notice notice-success">
							<p><?php esc_html_e( 'Your license is valid.', 'learndash' ); ?></p>
						</div>
						
				<?php
					endif;
				endif;
				?>
				<p><label for="nss_plugin_license_email_<?php echo $code; ?>"><?php esc_html_e( 'Email:', 'learndash' ); ?></label><br />

				<?php
				/**
				 * Returns the Learndash license email
				 *
				 * @since 2.1.0
				 *
				 * @param  string 'format_to_edit'
				 * @param  string $email 'nss_plugin_license_email_' appended with this object property $code
				 */
				?>
				<input id="nss_plugin_license_email_<?php echo $code; ?>" name="nss_plugin_license_email_<?php echo $code; ?>" style="min-width:30%" value="<?php esc_html_e( apply_filters( 'format_to_edit', $email ), 'learndash' ) ?>" /></p>

				<p><label ><?php esc_html_e( 'License Key:', 'learndash' ); ?></label><br />
				<input id="nss_plugin_license_<?php echo $code; ?>" name="nss_plugin_license_<?php echo $code; ?>" style="min-width:30%" value="<?php esc_html_e( apply_filters( 'format_to_edit', $license ), 'learndash' ) ?>" /></p>

				<div class="submit">
					<input type="submit" name="update_nss_plugin_license_<?php echo $code; ?>" value="<?php esc_html_e( 'Update License', 'learndash' ) ?>" class="button button-primary"/>
				</div>
			</form>

			<br><br><br><br>
			<div id="nss_license_footer">

			<?php
			/**
				 * Outputs the NSS License footer HTML
				 *
				 * @since 2.1.0
				 *
				 * @param  string This object's property "$code" appended with '-nss_license_footer'
				 */
				do_action( $code.'-nss_license_footer' );

				?>
			</div>
		</div>
		<?php
	}

}
