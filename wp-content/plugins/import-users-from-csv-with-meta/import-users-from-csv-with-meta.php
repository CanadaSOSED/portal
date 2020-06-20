<?php
/*
Plugin Name:	Import and export users and customers
Plugin URI:		https://www.codection.com
Description:	Using this plugin you will be able to import and export users or customers choosing many options and interacting with lots of other plugins
Version:		1.15.6.8
Author:			codection
Author URI: 	https://codection.com
License:     	GPL2
License URI: 	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: import-users-from-csv-with-meta
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) 
	exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if( is_plugin_active( 'buddypress/bp-loader.php' ) ){
	if ( defined( 'BP_VERSION' ) )
		acui_loader();
	else
		add_action( 'bp_init', 'acui_loader' );
}
else
	acui_loader();

function acui_loader(){
	register_activation_hook( __FILE__,'acui_init' ); 
	register_deactivation_hook( __FILE__, 'acui_deactivate' );
	add_action( "plugins_loaded", "acui_init" );
	add_action( "admin_menu", "acui_menu" );
	add_action( 'admin_enqueue_scripts', 'acui_admin_enqueue_scripts' );
	add_filter( 'plugin_row_meta', 'acui_plugin_row_meta', 10, 2 );
	add_action( 'wp_ajax_acui_delete_attachment', 'acui_delete_attachment' );
	add_action( 'wp_ajax_acui_bulk_delete_attachment', 'acui_bulk_delete_attachment' );
	add_filter( 'wp_check_filetype_and_ext', 'acui_wp_check_filetype_and_ext', PHP_INT_MAX, 4 );

	if( is_plugin_active( 'buddypress/bp-loader.php' ) && file_exists( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' ) ){
		require_once( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' );	
	}

	if( get_option( 'acui_show_profile_fields' ) == true ){
		add_action( "show_user_profile", "acui_extra_user_profile_fields" );
		add_action( "edit_user_profile", "acui_extra_user_profile_fields" );
		add_action( "personal_options_update", "acui_save_extra_user_profile_fields" );
		add_action( "edit_user_profile_update", "acui_save_extra_user_profile_fields" );
	}

	// classes
	foreach ( glob( plugin_dir_path( __FILE__ ) . "classes/*.php" ) as $file ) {
	    include_once( $file );
	}

	// includes
	foreach ( glob( plugin_dir_path( __FILE__ ) . "include/*.php" ) as $file ) {
	    include_once( $file );
	}

	// addons
	foreach ( glob( plugin_dir_path( __FILE__ ) . "addons/*.php" ) as $file ) {
	    include_once( $file );
	}
	
	require_once( "importer.php" );
}

function acui_init(){
	load_plugin_textdomain( 'import-users-from-csv-with-meta', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	acui_activate();
}

function acui_get_default_options_list(){
	return array(
		'acui_columns' => array(),
		// emails
		'acui_mail_subject' => __('Welcome to', 'import-users-from-csv-with-meta') . ' ' . get_bloginfo("name"),
		'acui_mail_body' => __('Welcome,', 'import-users-from-csv-with-meta') . '<br/>' . __('Your data to login in this site is:', 'import-users-from-csv-with-meta') . '<br/><ul><li>' . __('URL to login', 'import-users-from-csv-with-meta') . ': **loginurl**</li><li>' . __( 'Username', 'import-users-from-csv-with-meta') . '= **username**</li><li>Password = **password**</li></ul>',
		'acui_mail_template_id' => 0,
		'acui_mail_attachment_id' => 0,
		'acui_enable_email_templates' => false,
		// cron
		'acui_cron_activated' => false,
		'acui_cron_send_mail' => false,
		'acui_cron_send_mail_updated' => false,
		'acui_cron_delete_users' => false,
		'acui_cron_delete_users_assign_posts' => 0,
		'acui_cron_change_role_not_present' => false,
		'acui_cron_change_role_not_present_role' => 0,
		'acui_cron_path_to_file' => '',
		'acui_cron_path_to_move' => '',
		'acui_cron_path_to_move_auto_rename' => false,
		'acui_cron_period' => '',
		'acui_cron_role' => '',
		'acui_cron_update_roles_existing_users' => '',
		'acui_cron_log' => '',
		'acui_cron_allow_multiple_accounts' => 'not_allowed',
		// frontend
		'acui_frontend_send_mail'=> false,
		'acui_frontend_send_mail_updated' => false,
		'acui_frontend_mail_admin' => false,
		'acui_frontend_delete_users' => false,
		'acui_frontend_delete_users_assign_posts' => 0,
		'acui_frontend_change_role_not_present' => false,
		'acui_frontend_change_role_not_present_role' => 0,
		'acui_frontend_role' => '',
		'acui_frontend_update_existing_users' => false,
		'acui_frontend_update_roles_existing_users' => false,
		// emials
		'acui_manually_send_mail' => false,
		'acui_manually_send_mail_updated' => false,
		'acui_automatic_wordpress_email' => false,
		'acui_automatic_created_edited_wordpress_email' => false,
		// profile fields
		'acui_show_profile_fields' => false
	);
}

function acui_activate(){
	$acui_default_options_list = acui_get_default_options_list();
		
	foreach ( $acui_default_options_list as $key => $value) {
		add_option( $key, $value, '', false );		
	}
}

function acui_deactivate(){
	wp_clear_scheduled_hook( 'acui_cron' );
}

function acui_admin_enqueue_scripts() {
	wp_enqueue_style( 'acui_css', plugins_url( 'assets/style.css', __FILE__ ), false, '1.0.0' );
}

function acui_delete_options(){
	$acui_smtp_options = array (
		'acui_settings' => 'wordpress',
		'acui_mailer' => 'smtp',
		'acui_mail_set_return_path' => 'false',
		'acui_smtp_host' => 'localhost',
		'acui_smtp_port' => '25',
		'acui_smtp_ssl' => 'none',
		'acui_smtp_auth' => false,
		'acui_smtp_user' => '',
		'acui_smtp_pass' => ''
	);

	$acui_default_options_list = array_merge( acui_get_default_options_list(), $acui_smtp_options );
		
	foreach ( $acui_default_options_list as $key => $value) {
		delete_option( $key );		
	}
}

function acui_get_wp_users_fields(){
	return array( "id", "user_email", "user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim", "yim", "user_registered", "password", "user_pass", "locale", "show_admin_bar_front", "user_login" );
}

function acui_get_restricted_fields(){
	$wp_users_fields = acui_get_wp_users_fields();
	$wp_min_fields = array( "Username", "Email", "bp_group", "bp_group_role", "role"  );
	$acui_restricted_fields = array_merge( $wp_users_fields, $wp_min_fields );
	
	return apply_filters( 'acui_restricted_fields', $acui_restricted_fields );
}

function acui_get_not_meta_fields(){
	return apply_filters( 'acui_not_meta_fields', array() );
}

function acui_menu() {
	add_submenu_page( 'tools.php', __( 'Import and export users and customers', 'import-users-from-csv-with-meta' ), __( 'Import and export users and customers', 'import-users-from-csv-with-meta' ), 'create_users', 'acui', 'acui_options' );
}

function acui_plugin_row_meta( $links, $file ){
	if ( strpos( $file, basename( __FILE__ ) ) !== false ) {
		$new_links = array(
					'<a href="https://www.paypal.me/imalrod" target="_blank">' . __( 'Donate', 'import-users-from-csv-with-meta' ) . '</a>',
					'<a href="mailto:contacto@codection.com" target="_blank">' . __( 'Premium support', 'import-users-from-csv-with-meta' ) . '</a>',
					'<a href="http://codection.com/tienda" target="_blank">' . __( 'Premium plugins', 'import-users-from-csv-with-meta' ) . '</a>',
				);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}

function acui_detect_delimiter( $file ) {
    $delimiters = array(
        ';' => 0,
        ',' => 0,
        "\t" => 0,
        "|" => 0
    );

    $handle = @fopen($file, "r");
    $firstLine = fgets($handle);
    fclose($handle); 
    foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($firstLine, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
}

function acui_string_conversion( $string ){
	if(!preg_match('%(?:
    [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
    |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
    |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
    |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
    |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
    |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
    |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
    )+%xs', $string)){
		return utf8_encode($string);
    }
	else
		return $string;
}

function acui_user_id_exists( $user_id ){
	if ( get_userdata( $user_id ) === false )
	    return false;
	else
	    return true;
}

function acui_get_roles( $user_id ){
	$roles = array();
	$user = new WP_User( $user_id );

	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			$roles[] = $role;
	}

	return $roles;
}

function acui_get_editable_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);
    $list_editable_roles = array();

    foreach ($editable_roles as $key => $editable_role)
		$list_editable_roles[$key] = $editable_role["name"];
	
    return $list_editable_roles;
}

function acui_check_options(){
	if( get_option( "acui_mail_body" ) == "" )
		update_option( "acui_mail_body", __( 'Welcome,', 'import-users-from-csv-with-meta' ) . '<br/>' . __( 'Your data to login in this site is:', 'import-users-from-csv-with-meta' ) . '<br/><ul><li>' . __( 'URL to login', 'import-users-from-csv-with-meta' ) . ': **loginurl**</li><li>' . __( 'Username', 'import-users-from-csv-with-meta' ) . ' = **username**</li><li>' . __( 'Password', 'import-users-from-csv-with-meta' ) . ' = **password**</li></ul>' );

	if( get_option( "acui_mail_subject" ) == "" )
		update_option( "acui_mail_subject", __('Welcome to','import-users-from-csv-with-meta') . ' ' . get_bloginfo("name") );
}

function acui_admin_tabs( $current = 'homepage' ) {
    $tabs = array( 
    		'homepage' => __( 'Import', 'import-users-from-csv-with-meta' ),
    		'export' => __( 'Export', 'import-users-from-csv-with-meta' ),
    		'frontend' => __( 'Frontend', 'import-users-from-csv-with-meta' ), 
    		'cron' => __( 'Cron import', 'import-users-from-csv-with-meta' ), 
    		'columns' => __( 'Extra profile fields', 'import-users-from-csv-with-meta' ), 
    		'meta-keys' => __( 'Meta keys', 'import-users-from-csv-with-meta' ), 
    		'mail-options' => __( 'Mail options', 'import-users-from-csv-with-meta' ), 
    		'doc' => __( 'Documentation', 'import-users-from-csv-with-meta' ), 
    		'donate' => __( 'Donate/Patreon', 'import-users-from-csv-with-meta' ), 
    		'shop' => __( 'Shop', 'import-users-from-csv-with-meta' ), 
    		'help' => __( 'Hire an expert', 'import-users-from-csv-with-meta' ),
    		'new_features' => __( 'New features', 'import-users-from-csv-with-meta' )
    );

    $tabs = apply_filters( 'acui_tabs', $tabs );

    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
       	$class = ( $tab == $current ) ? ' nav-tab-active' : '';

        if( $tab == "shop"  ){
			$href = "https://codection.com/tienda/";	
			$target = "_blank";
        }
		else{
			$href = "?page=acui&tab=$tab";
			$target = "_self";
		}

		echo "<a class='nav-tab$class' href='$href' target='$target'>$name</a>";

    }
    echo '</h2>';
}

function acui_fileupload_process( $form_data, $is_cron = false, $is_frontend  = false ) {
	if ( !defined( 'DOING_CRON' ) && ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'codection-security' ) ) ){
		wp_die( __( 'Nonce check failed', 'import-users-from-csv-with-meta' ) ); 
	}

	if( empty( $_FILES['uploadfile']['name'] ) || $is_frontend ):
  		$path_to_file = wp_normalize_path( $form_data["path_to_file"] );
  		
		if( validate_file( $path_to_file ) !== 0 ){
			wp_die( __( 'Error, path to file is not well written', 'import-users-from-csv-with-meta' ) . ": $path_to_file" );
		} 

		if( !file_exists ( $path_to_file ) ){
			wp_die( __( 'Error, we cannot find the file', 'import-users-from-csv-with-meta' ) . ": $path_to_file" );
		}

		acui_import_users( $path_to_file, $form_data, 0, $is_cron, $is_frontend );
	else:
  		$uploadfile = wp_handle_upload( $_FILES['uploadfile'], array( 'test_form' => false, 'mimes' => array('csv' => 'text/csv') ) );

		if ( !$uploadfile || isset( $uploadfile['error'] ) ) {
			wp_die( __( 'Problem uploading file to import. Error details: ' . var_export( $uploadfile['error'], true ), 'import-users-from-csv-with-meta' ));
		} else {
			acui_import_users( $uploadfile['file'], $form_data, acui_get_attachment_id_by_url( $uploadfile['url'] ), $is_cron, $is_frontend );
		}
	endif;
}

function acui_manage_extra_profile_fields( $form_data ){
	if ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'codection-security' ) ) {
		wp_die( __( 'Nonce check failed', 'import-users-from-csv-with-meta' ) ); 
	}

	if( isset( $form_data['show-profile-fields-action'] ) && $form_data['show-profile-fields-action'] == 'update' )
		update_option( "acui_show_profile_fields", isset( $form_data["show-profile-fields"] ) && $form_data["show-profile-fields"] == "yes" );

	if( isset( $form_data['reset-profile-fields-action'] ) && $form_data['reset-profile-fields-action'] == 'reset' )
		update_option( "acui_columns", array() );
}

function acui_save_mail_template( $form_data ){
	if ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'codection-security' ) ) {
		wp_die( __( 'Nonce check failed', 'import-users-from-csv-with-meta' ) ); 
	}

	$automatic_wordpress_email = sanitize_text_field( $form_data["automatic_wordpress_email"] );
	$automatic_created_edited_wordpress_email = sanitize_text_field( $form_data["automatic_created_edited_wordpress_email"] );
	$subject_mail = sanitize_text_field( $form_data["subject_mail"] );
	$body_mail = wp_kses_post( stripslashes( $form_data["body_mail"] ) );
	$template_id = intval( $form_data["template_id"] );
	$email_template_attachment_id = intval( $form_data["email_template_attachment_id"] );

	update_option( "acui_automatic_wordpress_email", $automatic_wordpress_email );
	update_option( "acui_automatic_created_edited_wordpress_email", $automatic_created_edited_wordpress_email );
	update_option( "acui_mail_subject", $subject_mail );
	update_option( "acui_mail_body", $body_mail );
	update_option( "acui_mail_template_id", $template_id );
	update_option( "acui_mail_attachment_id", $email_template_attachment_id );

	$template_id = absint( $form_data["template_id"] );

	if( !empty( $template_id  ) ){
		wp_update_post( array(
			'ID'           => $template_id,
			'post_title'   => $subject_mail,
			'post_content' => $body_mail,
		) );

		update_post_meta( $template_id, 'email_template_attachment_id', $email_template_attachment_id );
	}
	?>
	<div class="updated">
       <p><?php _e( 'Mail template and options updated correctly', 'import-users-from-csv-with-meta' )?></p>
    </div>
    <?php
}

function acui_extra_user_profile_fields( $user ) {
	$acui_restricted_fields = acui_get_restricted_fields();
	$headers = get_option("acui_columns");

	if( is_array( $headers ) && !empty( $headers ) ):
?>
	<h3>Extra profile information</h3>
	
	<table class="form-table"><?php
	foreach ( $headers as $column ):
		if( in_array( $column, $acui_restricted_fields ) )
			continue;
	?>
		<tr>
			<th><label for="<?php echo $column; ?>"><?php echo $column; ?></label></th>
			<td><input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" value="<?php echo esc_attr(get_the_author_meta($column, $user->ID )); ?>" class="regular-text" /></td>
		</tr>
		<?php
	endforeach;
	?>
	</table><?php
	endif;
}

function acui_save_extra_user_profile_fields( $user_id ){
	$headers = get_option("acui_columns");
	$acui_restricted_fields = acui_get_restricted_fields();

	$post_filtered = filter_input_array( INPUT_POST );

	if( is_array( $headers ) && count( $headers ) > 0 ):
		foreach ( $headers as $column ){
			if( in_array( $column, $acui_restricted_fields ) )
				continue;

			$column_sanitized = str_replace(" ", "_", $column);
			update_user_meta( $user_id, $column, $post_filtered[$column_sanitized] );
		}
	endif;
}

function acui_delete_attachment() {
	check_ajax_referer( 'codection-security', 'security' );

	if( ! current_user_can( 'manage_options' ) )
		wp_die( __('You are not an adminstrator', 'import-users-from-csv-with-meta' ) );

	$attach_id = absint( $_POST['attach_id'] );
	$mime_type  = (string) get_post_mime_type( $attach_id );

	if( $mime_type != 'text/csv' )
		_e('This plugin only can delete the type of file it manages, CSV files.', 'import-users-from-csv-with-meta' );

	$result = wp_delete_attachment( $attach_id, true );

	if( $result === false )
		_e( 'There were problems deleting the file, please check file permissions', 'import-users-from-csv-with-meta' );
	else
		echo 1;

	wp_die();
}

function acui_bulk_delete_attachment(){
	check_ajax_referer( 'codection-security', 'security' );

	if( ! current_user_can( 'manage_options' ) )
		wp_die( __('You are not an adminstrator', 'import-users-from-csv-with-meta' ) );	

	$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
	$old_csv_files = new WP_Query( $args_old_csv );
	$result = 1;

	while($old_csv_files->have_posts()) : 
		$old_csv_files->the_post();

		$mime_type  = (string) get_post_mime_type( get_the_ID() );
		if( $mime_type != 'text/csv' )
			wp_die( __('This plugin only can delete the type of file it manages, CSV files.', 'import-users-from-csv-with-meta' ) );

		if( wp_delete_attachment( get_the_ID(), true ) === false )
			$result = 0;
	endwhile;
	
	wp_reset_postdata();

	echo $result;

	wp_die();
}

// try to solve the CSV upload problem
function acui_wp_check_filetype_and_ext( $values, $file, $filename, $mimes ) {
	if ( extension_loaded( 'fileinfo' ) ) {
		// with the php-extension, a CSV file is issues type text/plain so we fix that back to 
		// text/csv by trusting the file extension.
		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$real_mime = finfo_file( $finfo, $file );
		finfo_close( $finfo );
		if ( $real_mime === 'text/plain' && preg_match( '/\.(csv)$/i', $filename ) ) {
			$values['ext']  = 'csv';
			$values['type'] = 'text/csv';
		}
	} else {
		// without the php-extension, we probably don't have the issue at all, but just to be sure...
		if ( preg_match( '/\.(csv)$/i', $filename ) ) {
			$values['ext']  = 'csv';
			$values['type'] = 'text/csv';
		}
	}
	return $values;
}

// wp-access-areas functions
 function acui_set_cap_for_user( $capability , &$user , $add ) {
	$has_cap = $user->has_cap( $capability );
	$is_change = ($add && ! $has_cap) || (!$add && $has_cap);
	if ( $is_change ) {
		if ( $add ) {
			$user->add_cap( $capability , true );
			do_action( 'wpaa_grant_access' , $user , $capability );
			do_action( "wpaa_grant_{$capability}" , $user );
		} else if ( ! $add ) {
			$user->remove_cap( $capability );
			do_action( 'wpaa_revoke_access' , $user , $capability );
			do_action( "wpaa_revoke_{$capability}" , $user );
		}
	}
}

// misc
function acui_get_attachment_id_by_url( $url ) {
	$wp_upload_dir = wp_upload_dir();
	// Strip out protocols, so it doesn't fail because searching for http: in https: dir.
	$dir = set_url_scheme( trailingslashit( $wp_upload_dir['baseurl'] ), 'relative' );

	// Is URL in uploads directory?
	if ( false !== strpos( $url, $dir ) ) {

		$file = basename( $url );

		$query_args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'key'     => '_wp_attachment_metadata',
					'compare' => 'LIKE',
					'value'   => $file,
				),
			),
		);

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $attachment_id ) {
				$meta          = wp_get_attachment_metadata( $attachment_id );
				$original_file = basename( $meta['file'] );
				$cropped_files = wp_list_pluck( $meta['sizes'], 'file' );

				if ( $original_file === $file || in_array( $file, $cropped_files ) ) {
					return (int) $attachment_id;
				}
			}
		}
	}

	return false;
}