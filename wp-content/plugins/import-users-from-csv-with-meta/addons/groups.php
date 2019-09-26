<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( is_plugin_active( 'groups/groups.php' ) ){
	add_filter( 'acui_restricted_fields', 'acui_g_restricted_fields', 10, 1 );
	add_action( 'acui_documentation_after_plugins_activated', 'acui_g_documentation_after_plugins_activated' );
	add_action( 'post_acui_import_single_user', 'acui_g_post_import_single_user', 10, 3 );
}

function acui_g_restricted_fields( $acui_restricted_fields ){
	return array_merge( $acui_restricted_fields, array( 'group_id' ) );
}

function acui_g_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "Groups is activated", 'import-users-from-csv-with-meta' ); ?></th>
		<td>
			<?php _e( "You can import user and assign them to the users groups using the next format", 'import-users-from-csv-with-meta' ); ?>.
			<ul style="list-style:disc outside none; margin-left:2em;">
				<li><?php _e( "group_id as the column title", 'import-users-from-csv-with-meta' ); ?></li>
				<li><?php _e( "The value of each cell will be the ID of the group that you want to assign to this user", 'import-users-from-csv-with-meta' ); ?></li>
				<li><?php _e( "If you want to import multiple values, you can use a list using commas to separate items", 'import-users-from-csv-with-meta' ); ?></li>
			</ul>
		</td>
	</tr>
	<?php
}

function acui_g_post_import_single_user( $headers, $row, $user_id ){
	$pos = array_search( 'group_id', $headers );

	if( $pos === FALSE )
		return;

	// groups that appears in the CSV
	$user_groups_csv = explode( ',', $row[ $pos ] );
	$user_groups_csv = array_filter( $user_groups_csv, function( $value ){ return $value !== ''; } );

	// groups that user belongs to
	$groups_user = new Groups_User(  $user_id );
	$user_group_ids = $groups_user->group_ids;

	// first we look into all current user groups, if they do not appear in CSV it will be removed
	foreach ( $user_group_ids as $user_group_id ) {
		if( !in_array( $user_group_id, $user_groups_csv ) )
			Groups_User_Group::delete( $user_id, $user_group_id );
	}

	// finally we loop into groups that are present in CSV data, if they already exists, we do nothing, if not, we add it
	foreach ( $user_groups_csv as $user_group_csv ) {
		if( !in_array( $user_group_csv, $user_group_ids ) )
			Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' => $user_group_csv ) );
	}
}