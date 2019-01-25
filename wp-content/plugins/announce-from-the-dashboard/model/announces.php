<?php

if ( !class_exists( 'Afd_Model_Announces' ) ) :

final class Afd_Model_Announces extends Afd_Model_Abstract_Record
{

	public function __construct()
	{
		
		global $Afd;
		
		$this->record = $Afd->main_slug;
		
		$this->initial_data = array(
			'title' => '',
			'type' => '',
			'content' => '',
			'role' => array(),
			'range' => array(
				'start' => '',
				'end' => '',
			),
			'date' => array(
				'start' => '',
				'end' => '',
			),
			'standard' => '',
			'subsites' => array(),
		);
		
		$this->default_data = array(
			'title' => '',
			'type' => 'normal',
			'content' => '',
			'role' => array(),
			'range' => array(
				'start' => false,
				'end' => false,
			),
			'date' => array(
				'start' => current_time( 'mysql' ),
				'end' => current_time( 'mysql' ),
			),
			'standard' => 'not',
			'subsites' => array(),
		);
		
		parent::__construct();

	}
	
	public function get_announce( $id = false )
	{
		
		if( $id === false ) {
			
			return false;
			
		}

		$update_data = $this->get_datas();
		
		$id = intval( $id );

		return $update_data[$id];
		
	}

	public function add_data( $post_data = array() )
	{
		
		global $Afd;

		$errors = new WP_Error();

		if( empty( $post_data['data']['add'] ) ) {
			
			$errors->add( 'not_add' , sprintf( __( 'Empty Add Data.' , $Afd->ltd ) ) );
			return $errors;
			
		}
		
		if( !empty( $post_data['data']['add']['role'] ) ) {
			
			$user_roles = array();
			
			foreach( $post_data['data']['add']['role'] as $role ) {
				
				$role = strip_tags( $role );
				$user_roles[$role] = 1;
				
			}
			
			unset( $post_data['data']['add']['role'] );
			
			$post_data['data']['add']['role'] = $user_roles;
			
		}

		if( $Afd->Site->is_multisite ) {
			
			if( !empty( $post_data['data']['add']['subsites'] ) ) {
					
				$site_ids = array();
					
				foreach( $post_data['data']['add']['subsites'] as $site_id ) {
					
					$site_id = intval( $site_id );
					$site_ids[$site_id] = 1;
						
				}
					
				unset( $post_data['data']['add']['subsites'] );
					
				$post_data['data']['add']['subsites'] = $site_ids;
					
			}
				
		}
		
		$format_data = $this->data_format( $post_data['data']['add'] );
		$errors = $this->validate_data( $format_data );
		$error_codes = $errors->get_error_codes();
			
		if( !empty( $error_codes ) ) {
				
			return $errors;
				
		}

		$update_data = $this->get_datas();
		
		$update_data[] = $format_data;

		$this->update_record( $update_data );
		
		return $errors;
				
	}

	public function update_data( $post_data = array() )
	{
		
		global $Afd;
		
		$errors = new WP_Error();

		if( empty( $post_data['data']['edit'] ) ) {
			
			$errors->add( 'not_edit' , sprintf( __( 'Empty Update Data.' , $Afd->ltd ) ) );
			return $errors;
			
		}
		
		foreach( $post_data['data']['edit'] as $announce_id => $data ) {

			$announce_id = intval( $announce_id );

			if( !empty( $data['role'] ) ) {
				
				$user_roles = array();
				
				foreach( $data['role'] as $role ) {
					
					$role = strip_tags( $role );
					$user_roles[$role] = 1;
					
				}
				
				unset( $post_data['data']['edit'][$announce_id]['role'] );
				
				$post_data['data']['edit'][$announce_id]['role'] = $user_roles;
				
			}

			if( $Afd->Site->is_multisite ) {
			
				if( !empty( $data['subsites'] ) ) {
					
					$site_ids = array();
					
					foreach( $data['subsites'] as $site_id ) {
						
						$site_id = intval( $site_id );
						$site_ids[$site_id] = 1;
						
					}
					
					unset( $post_data['data']['edit'][$announce_id]['subsites'] );
					
					$post_data['data']['edit'][$announce_id]['subsites'] = $site_ids;
					
				}
				
			}
			
		}
		
		$format_data = $this->data_format( $post_data['data']['edit'][$announce_id] );
		$errors = $this->validate_data( $format_data );
		$error_codes = $errors->get_error_codes();
			
		if( !empty( $error_codes ) ) {
				
			return $errors;
				
		}

		$update_data = $this->get_datas();
		
		$update_data[$announce_id] = $format_data;
		
		$this->update_record( $update_data );
		
		return $errors;

	}
	
	public function update_sort_datas( $post_data = array() )
	{
		
		global $Afd;
		
		$errors = new WP_Error();

		if( empty( $post_data ) ) {

			$errors->add( 'empty_data' , 'Empty data' );
			return $errors;
			
		}
		
		$get_datas = $this->get_datas();
		
		if( empty( $get_datas ) ) {
			
			$errors->add( 'empty_data' , 'Empty data' );
			return $errors;

		}

		$update_data = array();
		
		foreach( $post_data as $sort_id ) {
			
			$update_data[$sort_id] = $get_datas[$sort_id];
			
		}
		
		if( $get_datas === $update_data ) {
			
			$errors->add( 'no_change' , sprintf( __( 'There is no %s.' , $Afd->ltd ) , __( 'change item' , $Afd->ltd ) ) );
			return $errors;

		}
		
		$this->update_record( $update_data );

		return $errors;

	}
	
	public function remove_datas( $post_data = array() )
	{
		
		global $Afd;

		$errors = new WP_Error();

		$update_data = $this->get_datas();
		
		if( empty( $post_data['data']['delete'] ) ) {
			
			$errors->add( 'not_ids' , sprintf( __( 'There is no %s.' , $Afd->ltd ) , __( 'delete ID' , $Afd->ltd ) ) );
			return $errors;
			
		}
		
		$delete_ids = array_unique( $post_data['data']['delete'] );
		
		foreach( $delete_ids as $delete_id ) {
			
			$delete_id = intval( $delete_id );
			unset( $update_data[$delete_id] );
			
		}
		
		$this->update_record( $update_data );
		
		return $errors;
				
	}
	
	public function data_format( $data )
	{
		
		global $Afd;

		if( empty( $data ) ) {

			return false;
			
		}
		
		if( is_object( $data ) ) {
			
			$data = (array) $data;
			
		}
		
		$new_data = $this->default_data;
		
		if( !empty( $data['title'] ) )
			$new_data['title'] = strip_tags( $data['title'] );
		unset( $data['title'] );

		if( !empty( $data['type'] ) )
			$new_data['type'] = strip_tags( $data['type'] );
		unset( $data['type'] );

		if( !empty( $data['content'] ) )
			$new_data['content'] = stripslashes_deep( $data['content'] );
		unset( $data['content'] );

		if( !empty( $data['role'] ) ) {
			
			foreach( $data['role'] as $role => $v ) {
				
				$role = strip_tags( $role );
				$new_data['role'][$role] = 1;
				
			}
			
		}
		unset( $data['role'] );

		if( !empty( $data['range']['start'] ) )
			$new_data['range']['start'] = 1;
		unset( $data['range']['start'] );

		if( !empty( $data['range']['end'] ) )
			$new_data['range']['end'] = 1;
		unset( $data['range']['end'] );
		
		if( !empty( $data['date']['start'] ) )
			$new_data['date']['start'] = strip_tags( $data['date']['start'] );
		unset( $data['date']['start'] );

		if( !empty( $data['date']['end'] ) )
			$new_data['date']['end'] = strip_tags( $data['date']['end'] );
		unset( $data['date']['end'] );

		if( !empty( $data['standard'] ) )
			$new_data['standard'] = strip_tags( $data['standard'] );
		unset( $data['standard'] );

		if( !empty( $data['subsites'] ) ) {
			
			foreach( $data['subsites'] as $blog_id => $v ) {
				
				$blog_id = intval( $blog_id );
				$new_data['subsites'][$blog_id] = 1;
				
			}
			
		}
		unset( $data['subsites'] );

		return $new_data;
		
	}
	
	public function validate_data( $data )
	{
		
		global $Afd;

		$errors = new WP_Error();
		
		if( empty( $data['type'] ) ) {
			
			$errors->add( 'not_type' , sprintf( __( 'There is no %s.' , $Afd->ltd ) , __( 'Announce type' , $Afd->ltd ) ) );
			return $errors;

		}

		if( !empty( $data['range']['start'] ) && !empty( $data['range']['end'] ) ) {
			
			if( empty( $data['date']['start'] ) or empty( $data['date']['end'] ) ) {
				
				$errors->add( 'not_date' , sprintf( __( 'There is no %s.' , $Afd->ltd ) , __( 'Date' ) ) );
				return $errors;

			}
			
			$start_date = strtotime( $data['date']['start'] );
			$end_date = strtotime( $data['date']['end'] );
			
			if( $start_date > $end_date ) {
				
				$errors->add( 'time_is_compare' , __( 'Please correctly set the time.' , $Afd->ltd ) );
				return $errors;

			}

		}
		
		return $errors;

	}
	
}

endif;
