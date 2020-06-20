<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) && !is_plugin_active( 'advanced-custom-fields/acf.php' ) ){
	return;
}

class ACUI_ACF{
	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_filter( 'acui_not_meta_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'import' ), 10, 3 );		
	}

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, $this->get_user_fields_keys() );
	}

	function documentation(){
		$fields = $this->get_user_fields();
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "Advaced Custom Fields is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td>
				<?php _e( "You can import those fields, look at every group which fields you can import using the column names shown below.", 'import-users-from-csv-with-meta' ); ?>.
				<ul style="list-style:disc outside none; margin-left:2em;">
				<?php foreach ( $this->get_user_fields() as $group => $fields ): ?>
					<li><?php _e( "Group name", 'import-users-from-csv-with-meta' ); ?>: <em><?php echo $group; ?></em></li>
					<ul style="list-style:square inside none; margin-left:2em;">
						<?php foreach ( $fields as $field ): ?>
						<li><?php echo $field['label']; ?> <em>(type: <?php echo $field['type']; ?>)</em> - Column name in the CSV: <strong><?php echo $field['name']; ?></strong></li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php
	}

	function import( $headers, $row, $user_id ){
		$fields_positions = array();
		$types = $this->get_user_fields_types();

		foreach ( $types as $key => $type ) {
			$pos = array_search( $key, $headers );

			if( $pos === FALSE )
				continue;

			$fields_positions[ $pos ] = $key;
		}

		foreach ( $fields_positions as $pos => $key ) {
			$data = explode( ',', $row[ $pos ] );
			$data = array_filter( $data, function( $value ){ return $value !== ''; } );

			// slugs in relationship
			if( $types[ $key ] == 'relationship' && (string)(int)$data[0] != $data[0] ){
				foreach ( $data as $it => $slug ) {
					$data[ $it ] = $this->get_post_id_by_slug( $slug );
				}
			}

			$preexisting_values = get_field( $key, "user_" . $user_id );
			if( !empty( $preexisting_values ) ){
				$data = array_unique( array_merge( $preexisting_values, $data ) );
				$data = array_filter( $data, function( $value ) { return !is_null( $value ) && $value !== '' && $value != 0; } );
			}
			
			update_field( $key, $data, "user_" . $user_id );
		}
	}

	function get_user_fields(){
		$post_id = "user_new";
		$fields = array();
		
		$args = array(
			'user_id'	=> 'new',
			'user_form'	=> '#your-profile'
		);
		
		$field_groups = acf_get_field_groups( array( 'user_id' => 'new', 'user_form' => '#your-profile' ) );
		
		if( empty($field_groups) ) 
			return;
		
		acf_form_data( array( 'post_id'	=> "user_new", 'nonce' => 'user' ) );
		
		foreach( $field_groups as $field_group ) {
			$fields[ $field_group['title'] ] = acf_get_fields( $field_group );
		}

		return $fields;
	}

	function get_user_fields_keys(){
		$fields = $this->get_user_fields();
		$fields_keys = array();

		if( empty( $fields ) )
			return array();

		foreach ( $fields as $group => $fields_of_group ){
			foreach ( $fields_of_group as $field ){
				$fields_keys[] = $field['name'];
			}
		}

		return $fields_keys;
	}

	function get_user_fields_types(){
		$fields = $this->get_user_fields();
		$fields_keys = array();

		if( empty( $fields ) )
			return array();

		foreach ( $fields as $group => $fields_of_group ){
			foreach ( $fields_of_group as $field ){
				$fields_keys[ $field['name'] ] = $field['type'];
			}
		}

		return $fields_keys;
	}

	function get_post_id_by_slug( $slug ){
		global $wpdb;

		$page_path     = rawurlencode( urldecode( $slug ) );
		$page_path     = str_replace( '%2F', '/', $page_path );
		$page_path     = str_replace( '%20', ' ', $page_path );
		$parts         = explode( '/', trim( $page_path, '/' ) );
		$parts         = array_map( 'sanitize_title_for_query', $parts );
		$escaped_parts = esc_sql( $parts );

		$in_string = "'" . implode( "','", $escaped_parts ) . "'";

		$sql = "SELECT ID, post_name, post_parent, post_type FROM $wpdb->posts WHERE post_name IN ($in_string)";

		$pages = $wpdb->get_results( $sql, OBJECT_K );

		$revparts = array_reverse( $parts );

		$foundid = 0;

		foreach ( (array) $pages as $page ) {
			if ( $page->post_name == $revparts[0] ) {
				$count = 0;
				$p     = $page;

				while ( 0 != $p->post_parent && isset( $pages[ $p->post_parent ] ) ) {
					$count++;
					$parent = $pages[ $p->post_parent ];
					if ( ! isset( $revparts[ $count ] ) || $parent->post_name != $revparts[ $count ] ) {
						break;
					}
					$p = $parent;
				}

				if ( 0 == $p->post_parent && count( $revparts ) == $count + 1 && $p->post_name == $revparts[ $count ] ) {
					$foundid = $page->ID;
					if ( $page->post_type == $post_type ) {
						break;
					}
				}
			}
		}

		return $foundid;
	}
}

new ACUI_ACF();