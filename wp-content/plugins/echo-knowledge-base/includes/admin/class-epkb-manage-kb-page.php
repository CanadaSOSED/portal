<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Manage KB page 
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Manage_KB_Page {
	
	private $all_kb_configs = array(); // current configs, define after handle form actions
	private $message = array(); // error/warning/success messages 
	private $active_kb_tab = 0; // active KB tab on the left panel, not always current KB, int 
	private $active_action_tab = 'manage'; // active Action tab on the top panel, string
	private $export_link = array(); // link to the file for export 

	function __construct() {

		// Handle manage kb buttons and other, set messages here 
		$this->handle_form_actions();

		// get configs
		$this->all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		
		// Default export/import 
	//	if ( ! EPKB_Utilities::is_export_import_enabled() ) {
			add_action( 'eckb_manage_content_tab_body', array( $this, 'export_import_tabs_body' ), 10, 2 );
	//	}
		
		// Define active tabs
		$this->active_kb_tab = EPKB_Utilities::get('active_kb_tab');
		$this->active_kb_tab = empty($this->active_kb_tab) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $this->active_kb_tab;
		$this->active_kb_tab = isset( $this->all_kb_configs[$this->active_kb_tab] ) ? $this->active_kb_tab : EPKB_KB_Config_DB::DEFAULT_KB_ID;

		$this->active_action_tab = EPKB_Utilities::get('active_action_tab');
		$this->active_action_tab = empty($this->active_action_tab) ? 'manage' : $this->active_action_tab;
	}
	
	/**
	 * Display page body 
	 */
	public function display_manage_kb_page() {
		
		 // only administrators can handle this page
		if ( ! current_user_can('manage_options') ) {
			return;
		}

		// reset cache and get latest KB config
		epkb_get_instance()->kb_config_obj->reset_cache();
		$this->all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();   ?>

		<!-- This is to catch WP JS stuff -->
		<div class="wrap">
			<h1></h1>
		</div>
		<div class=""></div>
		
		<div class="epkb-manage-kb-container">
			<div class="epkb-manage-header"><?php $this->show_header(); ?></div>
				<div class="epkb-manage-tabs-container">
					<div class="epkb-manage-tabs__buttons"><?php $this->show_tabs_buttons(); ?></div>
					<div class="epkb-manage-tabs__content"> <?php
					foreach ( $this->all_kb_configs as $kb_id => $kb_config ) {
						$this->show_tabs_body($kb_id, $kb_config);
					} ?>
				</div>
			</div>
		</div>      <?php

		// show any notifications
		foreach ( $this->message as $class => $message ) {
			echo  EPKB_Utilities::get_bottom_notice_message_box( $message, '', $class );
		}
	}

	function show_header() { ?>
		<h1><?php _e( 'Manage Your KB(s)', 'echo-knowledge-base' ); ?></h1><?php 
		
		// create KB button, hook for MKB
		do_action( 'eckb_manage_show_header' );
	}

	/**
	 * Tabs
	 */
	function show_tabs_buttons() {
		$tabs = array();
		
		foreach ( $this->all_kb_configs as $kb_id => $kb_config ) {
			$tabs[] = array(
				'kb_id' => $kb_id,
				'link' => EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ),
				'title' => $kb_config['kb_name'],
				'target' => '#kb_' . $kb_id,
				'url' => '#'
			);
		}
		
		// add Get more button 
		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() && count($this->all_kb_configs) == 1 ) {
			$tabs[] = array(
				'kb_id' => '0', // 0 for usual link, ID for tabs changing 
				'title' => __( 'Get Additional Knowledge Bases', 'echo-knowledge-base' ),
				'url' => 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/',
				'target' => '',
			);
		}
		
		// show tabs buttons 
		foreach ( $tabs as $tab ) { 
			$active_class = false;
			
			// add active class to current KB tab 
			if ( $this->active_kb_tab == $tab['kb_id'] ) {
				$active_class = 'active';
			} ?>
			
			<div class="epkb-manage-tabs__button <?php echo $active_class; ?>">

				<div class="epkb-manage-tabs__button__id"><?php echo '#: ' . $tab['kb_id']; ?></div>

				<a class="epkb-manage-tabs__button__title" href="<?php echo $tab['url']; ?>" target="_blank" data-kb_id="<?php echo $tab['kb_id']; ?>" data-target="<?php echo $tab['target']; ?>"><?php echo $tab['title']; ?></a><?php 
				
				if ( empty($tab['kb_id']) ) {
					echo '</div>';
					continue;
				} 
				
				if (  empty($tab['link']) ) { ?>
					<span class="epkb-manage-tabs__button__page-link">
						<span class="ep_font_icon_xmark"></span>
					</span><?php 
				} else { ?>
					<a class="epkb-manage-tabs__button__page-link" href="<?php echo $tab['link']; ?>" target="_blank"><span class="ep_font_icon_external_link"></span></a><?php 
				} ?>
				

			</div><?php 
		}
		
	}

	/**
	 * Show selected tab content.
	 * @param $kb_id
	 * @param $kb_config
	 */
	private function show_tabs_body( $kb_id, $kb_config ) {

		$active_class = $this->active_kb_tab == $kb_id ? 'active' : false; ?>

		<div class="epkb-manage-content  <?php echo $active_class; ?>" id="kb_<?php echo $kb_id; ?>">
			<div class="epkb-manage-content__header"><?php
				$manage_active = ( ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'manage' ) || ( $this->active_kb_tab != $kb_id ) ) ? 'active' : ''; ?>

				<div class="epkb-manage-content__tab-button <?php echo $manage_active; ?>" data-target="#kb_<?php echo $kb_id; ?>_manage"><?php esc_html_e( 'Manage', 'echo-knowledge-base' ); ?></div>

				<div class="epkb-manage-content__tab-button <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'export' ) ? 'active' : ''; ?>" data-target="#kb_<?php echo $kb_id; ?>_export"><?php esc_html_e( 'Export', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-manage-content__tab-button <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'import' ) ? 'active' : ''; ?>" data-target="#kb_<?php echo $kb_id; ?>_import"><?php esc_html_e( 'Import', 'echo-knowledge-base' ); ?></div>

				<?php do_action( 'eckb_manage_content_tab_header', $kb_id, $kb_config ); ?>
			</div>
			<div class="epkb-manage-content__tabs"><?php
			
				$active = ( ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'manage' )
							|| ( $this->active_kb_tab != $kb_id ) ) ? 'active' : ''; ?>

				<div id="kb_<?php echo $kb_id; ?>_manage" class="epkb-manage-content__tab <?php echo $active; ?>"><?php

					if ( $kb_config['status'] == 'archived' ) {
						$icon ='ep_font_icon_error_circle';
					}	else if ( $kb_config['status'] == 'published' ) {
						$icon ='ep_font_icon_checkmark';
					} else {
						$icon ='ep_font_icon_error_circle';
					}	 ?>

					<div class="epkb-manage-content__tab__status">
						<i class="<?php echo $icon; ?>"></i>
						<span><?php echo ucfirst($kb_config['status']);?></span>
					</div>
					
					<?php do_action( 'eckb_manage_content_tab_body_manage', $kb_id, $kb_config ); ?>
					
				</div>

				<?php do_action( 'eckb_manage_content_tab_body', $kb_id, $kb_config ); ?>
			</div>
		</div><?php
	}

	/**
	 * Tabs for import and export
	 * @param $kb_id
	 * @param $kb_config
	 */
	function export_import_tabs_body ( $kb_id, $kb_config ) {

		$alert = New EPKB_HTML_Elements();		?>

		<div id="kb_<?php echo $kb_id; ?>_export" class="epkb-manage-content__tab <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'export' ) ? 'active' : ''; ?>  epkb-manage-content__tab--export">			<?php

			$alert->callout( array(
				'id'        => '1',
				'title'     => 'This new feature is in Beta Mode',
				'content'   =>
					'
						<p>A Beta phase generally begins when the software is feature complete but likely to contain a number of known or unknown bugs.</p>
						<p>Please backup your website before importing KB configuration.</p>
					',
				'callout_type'   => 'error',
			));			?>

			<form class="epkb-export-kbs" action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_id, 'active_action_tab' => 'export' ) ) ); ?>" method="post">
				<p><?php _e( 'You can export KB and add-ons configuration. Export of KB articles and categories is not yet supported.', 'echo-knowledge-base'); ?></p>
				<input type="hidden" name="_wpnonce_manage_kbs" value="<?php echo wp_create_nonce( "_wpnonce_manage_kbs" ); ?>"/>
				<input type="hidden" name="action" value="epkb_export_knowledge_base"/>
				<input type="hidden" name="emkb_kb_id" value="<?php echo $kb_id; ?>"/>
				<input type="submit" class="success-btn" value="<?php echo  __( 'Export', 'echo-knowledge-base' ) . ' ' . $kb_config['kb_name']; ?>" /><br/>
				<?php if ( !empty ( $this->export_link[$kb_id] ) ) { ?>
					<a href="<?php echo $this->export_link[$kb_id]; ?>" download class="epkb_download_export_link info-btn"><?php _e( 'Download Export File', 'echo-knowledge-base' ); ?></a>
				<?php } ?>
			</form>
		</div>
		
		<div id="kb_<?php echo $kb_id; ?>_import" class="epkb-manage-content__tab <?php echo ( $this->active_kb_tab == $kb_id && $this->active_action_tab == 'import' ) ? 'active' : ''; ?>  epkb-manage-content__tab--import">
			<form class="epkb-import-kbs" action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_id, 'active_action_tab' => 'import' ) ) ); ?>" method="post" enctype="multipart/form-data">
				<p><?php _e( 'You can import KB and add-ons configuration. Import of KB articles and categories is not yet supported.', 'echo-knowledge-base'); ?></p>
				<input type="hidden" name="_wpnonce_manage_kbs" value="<?php echo wp_create_nonce( "_wpnonce_manage_kbs" ); ?>"/>
				<input type="hidden" name="action" value="epkb_import_knowledge_base"/>
				<input type="hidden" name="emkb_kb_id" value="<?php echo $kb_id; ?>"/>
				<input class="epkb-form-label__input epkb-form-label__input--text" type="file" name="import_file"><br>
				<input type="submit" class="error-btn" value="<?php echo  __( 'Import', 'echo-knowledge-base' ) . ' ' . $kb_config['kb_name']; ?>" disabled /><br/>
			</form>
		</div><?php
	}

	// Handle actions that need reload of the page - manage tab and other from addons
	private function handle_form_actions() {
		
		if ( empty( $_REQUEST['action']) ) {
			return;
		}

		// clear any messages
		$this->message = array();
		
		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_manage_kbs'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_manage_kbs'], '_wpnonce_manage_kbs' ) ) {
			$this->message['error'] = __( 'Something went wrong (1)', 'echo-knowledge-base' );
			return;
		}
		
		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->message['error'] = __( 'You do not have permission.', 'echo-knowledge-base' );
			return;
		}
		
		// retrieve KB ID we are saving
		$kb_id = empty($_POST['emkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['emkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log("received invalid kb_id when archiving/deleting KB", $kb_id );
			$this->message['error'] = __( 'Something went wrong (2)', 'echo-knowledge-base' );
			return;
		}
		
		// retrieve current KB configuration
		$current_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $current_config ) ) {
			EPKB_Logging::add_log("Could not retrieve KB config when manage KB", $kb_id );
			$this->message['error'] = __( 'Something went wrong (5)', 'echo-knowledge-base' );
			return;
		}
		
		// EXPORT CONFIG
		if ( EPKB_Utilities::post( 'action' ) == 'epkb_export_knowledge_base' ) {
			$export = new EPKB_Export_Import();
			$this->message = $export->download_export_file( $kb_id );
			if ( empty($this->message) ) {
				exit;
			}
			return;
		}

		// IMPORT CONFIG
		if ( EPKB_Utilities::post( 'action' ) == 'epkb_import_knowledge_base' ) {
			$import = new EPKB_Export_Import();
			$this->message = $import->import_kb_config( $kb_id );
			return;
		}

		$this->message = apply_filters( 'eckb_handle_manage_kb_actions', $this->message, $kb_id, $current_config );
	}

	/**
	 * Check do we need to show CORE kbs page 
	 */
	public static function is_show_core_kbs_page() {
		
		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			return true;
		}
		
		if ( version_compare( Echo_Multiple_Knowledge_Bases::$version, '1.11.1', '>' ) ) {
			return true;
		}
		
		return false;
	}
}