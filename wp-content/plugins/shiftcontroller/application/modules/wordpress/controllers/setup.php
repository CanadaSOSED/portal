<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
include_once( NTS_SYSTEM_APPPATH . 'controllers/setup.php' );

class Setup_Wordpress_HC_Controller extends Setup_HC_Controller
{
	var $form = NULL;

	function __construct()
	{
		parent::__construct();
		$this->form = HC_Lib::form()
			;

		$wum = HC_App::model('wordpress_user');
		$wordpress_roles = $wum->wp_roles();

		foreach( $wordpress_roles as $role_value => $role_name ){
			$field_name = 'role_' . $role_value;
			$field_name = str_replace(' ', '_', $field_name );
			$this->form
				->set_input( $field_name, 'dropdown' )
				;
			}

		$this->form
			->set_input( 'append_role_name', 'checkbox' )
			;
	}

	private function _get_old_version()
	{
		$return = NULL;

		$dbprefix_version = $this->config->item('nts_dbprefix_version');
		if( strlen($dbprefix_version) ){
			$old_prefixes = array();
			$db = clone $this->db;

			$core_prefix = substr($db->dbprefix, 0, -(strlen($dbprefix_version)+1));

			$my_version = substr($dbprefix_version, 1);
			$old_version = $my_version - 1;
			while( $old_version >= 1 ){
				$old_prefixes[] = 'v' . $old_version;
				$old_version--;
			}
			$old_prefixes[] = '';

			foreach( $old_prefixes as $op ){
				$test_prefix = strlen($op) ? $core_prefix . $op . '_' :  $core_prefix;
				$db->dbprefix = $test_prefix;
				if( $this->check_setup($db) ){
					$return = $op;
					break;
				}
			}
		}
		return $return;
	}

	function index()
	{
		$app_title = $this->config->item('nts_app_title');

		/* check if we have an older version installed */
		$offer_upgrade = 0;
		$old_version = $this->_get_old_version();
		if( $old_version !== NULL ){
			$offer_upgrade = 1;
		}

		if( $offer_upgrade ){
			$this->redirect('setup/upgrade');
			return;
		}

		$wum = HC_App::model('wordpress_user');
		$wordpress_roles = $wum->wp_roles();
		$wordpress_count_users = count_users();
		$is_setup = 1;

		$this->layout->set_partial(
			'content', 
			$this->render(
				'wordpress/admin/users/sync',
				array(
					'post_to'				=> 'wordpress/setup/run',
					'app_title'				=> $app_title,
					'form'					=> $this->form,
					'wordpress_roles'		=> $wordpress_roles,
					'wordpress_count_users'	=> $wordpress_count_users,
					'offer_upgrade'			=> $offer_upgrade,
					'is_setup'				=> $is_setup,
					)
				)
			);
		$this->layout();
	}

	function run()
	{
		$validator = new HC_Validator;
		$wum = HC_App::model('wordpress_user');
		$wordpress_roles = $wum->wp_roles();
		$wordpress_count_users = count_users();

		foreach( $wordpress_roles as $role_value => $role_name ){
			$field_name = 'role_' . $role_value;
			$validator->set_rules( $field_name, 'trim|required' );
		}

		$post = $this->input->post();
		$this->form->grab( $post );
		$values = $this->form->values();

		if( $post ){
			if( $validator->run($values) == FALSE ){
				$errors = $validator->error();

				$this->form->set_values( $values );
				$this->form->set_errors( $errors );

			/* render view */
				return $this->index();
			}
			else {
			/* run setup */	
			/* reset tables */
				$this->_drop_tables();

			/* setup tables */
				$this->load->library('migration');
				if ( ! $this->migration->current()){
					show_error($this->migration->error_string());
					return false;
				}

				$app_conf = HC_App::app_conf();
				$app_conf->init();
				$setup_ok = TRUE;

				$append_role_name = $values['append_role_name'];

			/* save settings */
				reset( $values );
				foreach( $values as $k => $v ){
					$app_conf->set( 'wordpress_' . $k, $v );
				}

				$um = HC_App::model('user');

			/* all users */
				$result = $wum->sync_all( $values );
				if( $result !== TRUE ){
					$msg = array();
					foreach( $result as $uid => $user_result ){
						foreach( $user_result as $k => $v ){
							$msg[] = 'User ID=' . $uid . ': ' . $k . ': ' . $v;
						}
					}
					$msg = join( '<br>', $msg );
					$this->session->set_flashdata( 'error', $msg );
					$setup_ok = FALSE;
				}

				$success_count = $wum->get_last_count('success');

			/* this user */
				$current_user = wp_get_current_user();
				$result = $wum->sync( $current_user->ID, $um->_const('LEVEL_ADMIN') );
				if( $result !== TRUE ){
					$msg = array();
					foreach( $result as $k => $v ){
						$msg[] = $k . ': ' . $v;
					}
					$msg = join( '<br>', $msg );
					$this->session->set_flashdata( 'error', $msg );
					$setup_ok = FALSE;
				}
				// $success_count++;

				if( $setup_ok ){
				/* default settings */
					$email_from = get_bloginfo('admin_email');
					$email_from_name = get_bloginfo('name');

					$app_conf->set( 'email_from',			$email_from );
					$app_conf->set( 'email_from_name',	$email_from_name );

					$msg = 'Imported ' . $success_count . ' ';
					$msg .= ($count_users > 1) ? 'users' : 'user';

					$this->session->set_flashdata( 'message', $msg );
					$this->redirect('setup/ok');
					return;
				}
				else {
					$this->_drop_tables();
					$this->redirect('wordpress/setup');
				}
			}
		}
		return $this->index();
	}
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */