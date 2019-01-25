<?php

if ( !class_exists( 'Afd_Model_Abstract_Record' ) ) :

abstract class Afd_Model_Abstract_Record
{
	
	protected $record;
	protected $initial_data;
	protected $default_data;
	protected $pre_update_data = array( 'UPFN' => 'Y' );

	public function __construct() {}
	
	public function get_record_name()
	{
		
		return $this->record;
		
	}

	public function get_initial_data()
	{
		
		return $this->initial_data;

	}
	
	public function get_default_data()
	{
		
		return array_merge( $this->initial_data , $this->default_data );

	}
	
	protected function get_blog_record( $blog_id = false )
	{
		
		if( empty( $blog_id ) )
			return false;

		switch_to_blog( $blog_id );

		$data = get_option( $this->record );

		restore_current_blog();
		
		return $data;

	}

	protected function get_record()
	{
		
		global $Afd;

		if( $Afd->Site->is_multisite ) {

			$data = get_site_option( $this->record );
			
		} else {

			$data = get_option( $this->record );
			
		}
		
		return $data;

	}

	public function get_data()
	{
		
		$data = $this->get_record();
		
		if( empty( $data ) ) {

			$data = array();
			
		} else {
			
			$data = $this->data_format( $data );
			
		}
		
		return $data;
		
	}
	
	public function get_datas()
	{
		
		$datas = $this->get_record();
		
		if( empty( $datas ) ) {

			$datas = array();
			
		} else {
			
			foreach( $datas as $key => $data ) {
				
				$datas[$key] = $this->data_format( $data );

			}
			
		}
		
		return $datas;
		
	}
	
	public function get_blog_data( $blog_id = false )
	{
		
		if( empty( $blog_id ) )
			return false;
		
		$blog_id = absint( $blog_id );
		
		if( empty( $blog_id ) )
			return false;

		$data = $this->get_blog_record( $blog_id );
		
		if( empty( $data ) ) {

			$data = array();
			
		} else {
			
			$data = $this->data_format( $data );
			
		}
		
		return $data;
		
	}
	
	protected function update_record( $data )
	{
		
		global $Afd;

		if( $Afd->Site->is_multisite ) {

			update_site_option( $this->record , $data );
			
		} else {

			update_option( $this->record , $data );
			
		}
		
	}

	protected function update_blog_record( $data , $blog_id )
	{
		
		global $Afd;

		switch_to_blog( $blog_id );

		update_option( $this->record , $data );

		restore_current_blog();
		
	}

	protected function remove_record()
	{
		
		global $Afd;

		if( $Afd->Site->is_multisite ) {

			delete_site_option( $this->record );
			
		} else {

			delete_option( $this->record );
			
		}
		
	}
	
	protected function remove_blog_record( $blog_id )
	{
		
		global $Afd;

		switch_to_blog( $blog_id );

		delete_option( $this->record );

		restore_current_blog();
		
	}
	
	public function data_format( $data ) {}

}

endif;
