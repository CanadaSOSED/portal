<?php

if ( !class_exists( 'Afd_Helper' ) ) :

final class Afd_Helper
{
	
	public function includes( $files = false )
	{
		
		global $Afd;

		if( empty( $files ) )
			return false;
		
		if( is_array( $files ) ) {
			
			foreach( $files as $file ) {
			
				include_once( $Afd->plugin_dir . $file );
				
			}

		} else {
			
			include_once( $Afd->plugin_dir . $files );

		}

	}
	
	public function get_action_link( $remove_query = array() )
	{
		
		global $Afd;

		if( empty( $remove_query ) ) {
			
			$url = remove_query_arg( array( $Afd->Plugin->msg_notice ) );
			
		} else {
			
			$url = remove_query_arg( array_merge( $remove_query , array( $Afd->Plugin->msg_notice ) ) );
			
		}
		
		return esc_url_raw( $url );
		
	}
	
	public function get_author_link( $args = array() )
	{
		
		global $Afd;

		$url = $Afd->Links->author;
		
		if( !empty( $args['translate'] ) ) {

			$url .= 'please-translation/';

		} elseif( !empty( $args['donate'] ) ) {

			$url .= 'please-donation/';

		} elseif( !empty( $args['contact'] ) ) {

			$url .= 'contact-us/';

		}
		
		$url .= $this->get_utm_link( $args );
		
		return $url;

	}
	
	public function get_utm_link( $args = array() )
	{
		
		global $Afd;

		$utm = '?utm_source=' . $args['tp'];
		$utm .= '&utm_medium=' . $args['lc'];
		$utm .= '&utm_content=' . $Afd->ltd;
		$utm .= '&utm_campaign=' . str_replace( '.' , '_' , $Afd->ver );

		return $utm;

	}
	
	public function is_correctly_form( $post_data = array() )
	{
		
		global $Afd;
		
		if( empty( $post_data ) )
			return false;
		
		if( empty( $post_data[$Afd->Form->field] ) )
			return false;

		$form_field = strip_tags( $post_data[$Afd->Form->field] );

		if( $form_field !== $Afd->Form->UPFN )
			return false;

		return true;
		
	}
	
	public function get_object_cache( $chache_key = false )
	{
		
		global $Afd;
		
		if( empty( $chache_key ) )
			return false;

		return wp_cache_get( $chache_key , $Afd->ltd );

	}
	
	public function set_object_cache( $chache_key = false , $data = false )
	{
		
		global $Afd;
		
		if( empty( $chache_key ) )
			return false;

		wp_cache_set( $chache_key , $data , $Afd->ltd );

	}
	
	public function delete_object_cache( $chache_key = false )
	{
		
		global $Afd;
		
		if( empty( $chache_key ) )
			return false;

		return wp_cache_delete( $chache_key , $Afd->ltd );

	}
	
	public function get_main_blog_id()
	{
		
		return 1;
		
	}
	
	public function get_blog_id( $blog_id = false )
	{
		
		global $Afd;

		if( !empty( $blog_id ) ) {

			$blog_id = absint( $blog_id );
			
		} else {
			
			$blog_id = $Afd->Site->blog_id;
			
		}
		
		return $blog_id;

	}
	
	public function get_announce_types()
	{
		
		global $Afd;

		$announce_types = array(
			'normal' => array(
				'color' => __( 'Gray' ),
				'label' => __( 'Normal' , $Afd->ltd ),
			),
			'updated' => array(
				'color' => __( 'Green' ),
				'label' => __( 'Update' , $Afd->ltd ),
			),
			'error' => array(
				'color' => __( 'Red' ),
				'label' => __( 'Error' ),
			),
			'metabox' => array(
				'color' => __( 'White' ),
				'label' => __( 'Metabox' , $Afd->ltd ),
			),
			'nonstyle' => array(
				'color' => '',
				'label' => __( 'Non Styles' , $Afd->ltd ),
			),
		);

		return $announce_types;

	}
	
	public function get_date_periods()
	{

		global $Afd;

		$periods = array(
			'start' => __( 'Start Date' ),
			'end' => __( 'End Date' )
		);
		
		return $periods;

	}
	
	public function get_multisite_show_standard()
	{
		
		global $Afd;

		$multisite_show_standard = array(
			'all' => __( 'Default show to all child-sites' , $Afd->ltd ),
			'not' => __( 'Default show to not all child-sites' , $Afd->ltd )
		);
		
		return $multisite_show_standard;

	}

	public function get_all_user_roles()
	{

		global $Afd;
		global $wp_roles;

		$all_user_roles = array();
		$wp_user_roles = $wp_roles->roles;
		
		foreach ( $wp_user_roles as $role => $user_role ) {

			$user_role['label'] = translate_user_role( $user_role['name'] );
			$all_user_roles[$role] = $user_role;

		}

		return $all_user_roles;

	}

	public function get_plugin_version_checked()
	{
		
		global $Afd;

		$readme = file_get_contents( $Afd->plugin_dir . 'readme.txt' );

		$lines = explode( "\n" , $readme );
		
		$version_checked = '';

		foreach( $lines as $key => $line ) {

			if( strpos( $line , 'Requires at least: ' ) !== false ) {

				$version_checked .= str_replace( 'Requires at least: ' , '' ,  $line );
				$version_checked .= ' - ';

			} elseif( strpos( $line , 'Tested up to: ' ) !== false ) {

				$version_checked .= str_replace( 'Tested up to: ' , '' ,  $line );
				break;

			}

		}
		
		return $version_checked;

	}
	
	public function get_sites()
	{
		
		$get_sites = wp_get_sites( array( 'limit' => '' ) );
		
		return $get_sites;
		
	}
	
}

endif;
