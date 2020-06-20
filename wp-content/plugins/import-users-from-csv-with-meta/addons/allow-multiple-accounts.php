<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'allow-multiple-accounts/allow-multiple-accounts.php' ) ){
	return;
}

add_action( 'acui_tab_import_before_import_button', 'acui_allow_multiple_accounts_tab_import_before_import_button' );
add_action( 'acui_tab_cron_before_log', 'acui_allow_multiple_accounts_tab_cron_before_log' );
function acui_allow_multiple_accounts_tab_import_before_import_button(){
	?>
	<h2><?php _e( 'Allow multiple accounts compatibility', 'import-users-from-csv-with-meta'); ?></h2>

	<table class="form-table">
		<tbody>
		<tr class="form-field form-required">
			<th scope="row"><label><?php _e( 'Repeated email in different users?', 'import-users-from-csv-with-meta' ); ?></label></th>
			<td>
				<select name="allow_multiple_accounts">
					<option value="not_allowed"><?php _e( 'Not allowed', 'import-users-from-csv-with-meta' ); ?></option>
					<option value="allowed"><?php _e( 'Allowed', 'import-users-from-csv-with-meta' ); ?></option>
				</select>
				<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/allow-multiple-accounts/"><?php _e( 'Allow Multiple Accounts', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta'); ?>)</strong>. <?php _e('Allow multiple user accounts to be created having the same email address.','import-users-from-csv-with-meta' ); ?></p>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
}

function acui_allow_multiple_accounts_tab_cron_before_log(){
	?>
	<h2><?php _e( 'Allow Multiple Accounts compatibility', 'import-users-from-csv-with-meta'); ?></h2>

	<table class="form-table">
		<tbody>

		<tr class="form-field form-required">
			<th scope="row"><label><?php _e( 'Repeated email in different users?', 'import-users-from-csv-with-meta' ); ?></label></th>
			<td>
				<input type="checkbox" name="allow_multiple_accounts" value="yes" <?php if( $allow_multiple_accounts == "allowed" ) echo "checked='checked'"; ?>/>
				<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/allow-multiple-accounts/"><?php _e( 'Allow Multiple Accounts', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta'); ?>)</strong>. <?php _e('Allow multiple user accounts to be created having the same email address.','import-users-from-csv-with-meta' ); ?></p>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
}

function acui_hack_email( $email ) {
	if ( ! is_email( $email ) ) {
		return;
	}

	$old_email = $email;

	for ( $i = 0; ! $skip_remap && email_exists( $email ); $i++ ) {
		$email = str_replace( '@', "+ama{$i}@", $old_email );
	}

	return $email;
}

function acui_hack_restore_remapped_email_address( $user_id, $email ) {
	global $wpdb;

	$wpdb->update(
		$wpdb->users,
		array( 'user_email' => $email ),
		array( 'ID' => $user_id )
	);

	clean_user_cache( $user_id );	
}