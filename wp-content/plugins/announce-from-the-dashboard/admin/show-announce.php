<?php

if ( !class_exists( 'Afd_Admin_Show_Announce' ) ) :

final class Afd_Admin_Show_Announce
{

	private $assets_url;
	private $script_slug;

	private $announces;

	public function __construct()
	{

		global $Afd;

		if( $Afd->Env->is_ajax ) {

			return false;

		}

		$this->assets_url   = $Afd->plugin_url . trailingslashit( basename( dirname( __FILE__ ) ) ) . trailingslashit( 'assets' );
		$this->script_slug  = $Afd->ltd . '_announce';

		add_action( $Afd->ltd . '_before_admin_init' , array( $this , 'before_init' ) );
		add_action( $Afd->ltd . '_admin_init' , array( $this , 'admin_init' ) );
		add_action( $Afd->ltd . '_do_announce' , array( $this , 'do_announce' ) );
		add_action( $Afd->ltd . '_admin_ajax' , array( $this , 'admin_ajax' ) );

	}

	public function before_init()
	{

		global $Afd;

		add_filter( $Afd->ltd . '_before_announce' , array( $this , 'refine_multisite' ) , 5 );
		add_filter( $Afd->ltd . '_before_announce' , array( $this , 'refine_user_role' ) , 5 );
		add_filter( $Afd->ltd . '_before_announce' , array( $this , 'refine_date_range' ) , 5 );

		add_filter( $Afd->ltd . '_apply_content' , 'wptexturize' );
		add_filter( $Afd->ltd . '_apply_content' , 'convert_smilies' );
		add_filter( $Afd->ltd . '_apply_content' , 'convert_chars' );
		add_filter( $Afd->ltd . '_apply_content' , 'wpautop' );
		add_filter( $Afd->ltd . '_apply_content' , 'shortcode_unautop' );
		add_filter( $Afd->ltd . '_apply_content' , 'prepend_attachment' );
		add_filter( $Afd->ltd . '_apply_content' , 'do_shortcode' , 11 );

		add_action( $Afd->ltd . '_do_announce' , array( $this , 'do_announce' ) );

	}

	public function refine_multisite( $announces )
	{

		global $Afd;

		if( empty( $announces ) ) {

			return $announces;

		}

		if( !$Afd->Site->is_multisite ) {

			return $announces;

		}

		$current_site_announces = array();

		foreach( $announces as $key => $data ) {

			if( empty( $data['standard'] ) ) {

				continue;

			}

			$standard = $data['standard'];
			$subsites = array();

			if( !empty( $data['subsites'] ) ) {

				$subsites = $data['subsites'];

			}

			if( $standard == 'all' ) {

				if( !array_key_exists( $Afd->Site->blog_id , $subsites ) ) {

					$current_site_announces[$key] = $data;

				}

			} elseif( $standard == 'not' ) {

				if( array_key_exists( $Afd->Site->blog_id , $subsites ) ) {

					$current_site_announces[$key] = $data;

				}

			}

		}

		return $current_site_announces;

	}

	public function refine_user_role( $announces )
	{

		global $Afd;

		if( empty( $announces ) ) {

			return $announces;

		}

		if( empty( $Afd->User->user_role ) ) {

			return $announces;

		}

		$user_announces = array();

		foreach( $announces as $key => $data ) {

			if( !empty( $data['role'][$Afd->User->user_role] ) ) {

				$user_announces[$key] = $data;

			}

		}

		return $user_announces;

	}

	public function refine_date_range( $announces )
	{

		global $Afd;

		if( empty( $announces ) ) {

			return $announces;

		}

		$date_range_announces = array();

		$current_time_stamp = current_time( 'timestamp' );

		if( $Afd->Site->is_multisite ) {

			$main_blog_id = $Afd->Helper->get_main_blog_id();

			switch_to_blog( $main_blog_id );

			$current_time_stamp = current_time( 'timestamp' );

			restore_current_blog();

		}

		foreach( $announces as $key => $data ) {

			if( empty( $data['range']['start'] ) && empty( $data['range']['end'] ) ) {

				$date_range_announces[$key] = $data;

			} else {

				$range_flag = true;

				if( !empty( $data['range']['start'] ) && !empty( $data['date']['start'] ) ) {

					$timestamp = strtotime( $data['date']['start'] );

					if( $current_time_stamp < $timestamp ) {

						$range_flag = false;

					}

				}

				if( !empty( $data['range']['end'] ) && !empty( $data['date']['end'] ) ) {

					$timestamp = strtotime( $data['date']['end'] );

					if( $current_time_stamp > $timestamp ) {

						$range_flag = false;

					}

				}

				if( $range_flag ) {

					$date_range_announces[$key] = $data;

				}

			}


		}

		return $date_range_announces;

	}

	public function admin_init()
	{

		global $Afd;

		do_action( $Afd->ltd . '_do_announce' );

	}

	public function do_announce()
	{

		global $Afd;

		if( $Afd->Env->is_network_admin ) {

			return false;

		}

		add_action( 'load-index.php' , array( $this , 'screen_dashboard' ) );

	}

	public function screen_dashboard()
	{

		global $Afd;

		$announces = $Afd->Api->get_announces();

		if( empty( $announces ) ) {

			return false;

		}

		$this->announces = $announces;

		add_action( 'admin_print_scripts' , array( $this , 'admin_print_scripts' ) );
		add_action( 'admin_notices' , array( $this , 'announce_notices' ) , 99 );
		add_action( 'wp_dashboard_setup' , array( $this , 'wp_dashboard_setup' ) );

	}

	public function admin_print_scripts()
	{

		global $Afd;

		wp_enqueue_style( $this->script_slug ,  $this->assets_url . 'css/announce.css' , array() , $Afd->ver );

	}

	public function announce_notices()
	{

		global $Afd;

		if( empty( $this->announces ) ) {

			return false;

		}

		foreach( $this->announces as $announce_id => $announce ) {

			if( !in_array( $announce['type'] , array( 'normal' , 'updated' , 'error' , 'nonstyle' ) ) ) {

				continue;

			}

			$class = 'announce ';

			if( in_array( $announce['type'] , array( 'updated' , 'error' ) ) ) {

				$class .= $announce['type'];

			} else {

				$class .= 'updated ' . $announce['type'];

			}

			printf( '<div class="%s" id="announce-id-%d">' , $class , $announce_id );

			if( !empty( $announce['title'] ) ) {

				printf( '<p style="font-size:15px;"><strong>%s</strong></p>' , $announce['title'] );

			}

			echo $Afd->Api->content_format( $announce['content'] );

			echo '</div>';

		}

	}

	public function wp_dashboard_setup()
	{

		global $Afd;

		if( empty( $this->announces ) ) {

			return false;

		}

		foreach( $this->announces as $announce_id => $announce ) {

			if( $announce['type'] != 'metabox' ) {

				continue;

			}

			$metabox_id = 'announce-id-' . $announce_id;

			add_meta_box( $metabox_id , $announce['title'] , array( $this , 'dashboard_do_metabox' ) , 'dashboard' , 'normal' , 'high' , array( 'announce_id' => $announce_id ) );
			add_filter( 'postbox_classes_dashboard_' . $metabox_id , array( $this , 'postbox_classes' ) );

		}

	}

	public function dashboard_do_metabox( $output , $metabox )
	{

		global $Afd;

		$announce_id = $metabox['args']['announce_id'];

		if( empty( $this->announces[$announce_id] ) ) {

			return false;

		}

		$announce = $this->announces[$announce_id];

		echo $Afd->Api->content_format( $announce['content'] );

	}

	public function postbox_classes( $classes = array() )
	{

		global $Afd;

		$classes[] = $Afd->ltd . '-metabox';

		return $classes;

	}

	public function admin_ajax() {}

}

new Afd_Admin_Show_Announce();

endif;
