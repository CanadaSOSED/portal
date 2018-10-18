<?php

if ( !class_exists( 'Afd_Api' ) ) :

final class Afd_Api
{

	public function __construct() {}
	
	public function get_announces()
	{
		
		global $Afd;
		
		$chache_key = __FUNCTION__;

		$cache = $Afd->Helper->get_object_cache( $chache_key );

		if( !empty( $cache ) ) {

			return $cache;
			
		}

		$Afd_Model_Announces = new Afd_Model_Announces();

		$announces = $Afd_Model_Announces->get_datas();

		$announces = apply_filters( $Afd->ltd . '_before_announce' , $announces );

		$Afd->Helper->set_object_cache( $chache_key , $announces );
		
		return $announces;

	}
	
	public function content_format( $content )
	{
		
		global $Afd;

		$content = apply_filters( $Afd->ltd . '_apply_content' , $content );
		
		return $content;
		
	}
	
}

endif;
