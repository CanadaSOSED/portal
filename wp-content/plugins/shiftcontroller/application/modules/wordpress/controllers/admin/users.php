<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_Admin_Wordpress_HC_controller extends _Backend_HC_controller
{
	var $form = NULL;

	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_ADMIN );

		$this->form = HC_Lib::form()
			;

		$defaults = array();
		$app_conf = HC_App::app_conf();
		$wum = HC_App::model('wordpress_user');
		$wordpress_roles = $wum->wp_roles();

		foreach( $wordpress_roles as $role_value => $role_name ){
			$field_name = 'role_' . $role_value;
			$this->form
				->set_input( $field_name, 'dropdown' )
				;
			$default = $app_conf->get( 'wordpress_' . $field_name );
			$defaults[ $field_name ] = $default;
		}

		$this->form
			->set_input( 'append_role_name', 'checkbox' )
			;
		$this->form->set_values( $defaults );
	}

	function edit( $id )
	{
		$args = func_get_args();
		$args = hc_parse_args( $args, TRUE );
		$id = isset($args['user']) ? $args['user'] : NULL;

		if( is_object($id) ){
			$id = $id->id;
		}

		// redirect to WP admin user edit
		$link = get_edit_user_link( $id );
		$this->redirect( $link );
		exit;
	}

	function edit_menubar( $user )
	{
		return $this->render(
			'wordpress/admin/users/edit_menubar',
			array(
				)
			);
	}

	function add()
	{
		// redirect to WP admin user add
		$link = admin_url( 'user-new.php' );
		$this->redirect( $link );
		exit;
	}

	function add_menubar()
	{
		return $this->render(
			'wordpress/admin/users/add_menubar',
			array(
				)
			);
	}

	function sync_menubar()
	{
		return $this->render(
			'wordpress/admin/users/sync_menubar',
			array(
				)
			);
	}
	function sync()
	{
		$app_title = $this->config->item('nts_app_title');

		$wum = HC_App::model('wordpress_user');
		$wordpress_roles = $wum->wp_roles();
		$wordpress_count_users = count_users();
		$is_setup = 0;

		$this->layout->set_partial(
			'content', 
			$this->render(
				'wordpress/admin/users/sync',
				array(
					'post_to'				=> 'wordpress/admin/users/syncrun',
					'app_title'				=> $app_title,
					'form'					=> $this->form,
					'wordpress_roles'		=> $wordpress_roles,
					'wordpress_count_users'	=> $wordpress_count_users,
					'is_setup'				=> $is_setup,
					)
				)
			);
		$this->layout();
	}

	function syncrun()
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
				return $this->sync();
			}
			else {
				$app_conf = HC_App::app_conf();
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
				$archived_count = $wum->get_last_count('archived');

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
					$msg = 'Synchronized ' . $success_count . ' ';
					$msg .= ($success_count_users > 1) ? 'users' : 'user';
					
					if( $archived_count ){
						$msg .= '<br>Archived ' . $archived_count . ' ';
						$msg .= ($archived_count > 1) ? 'users' : 'user';
					}

					$this->session->set_flashdata( 'message', $msg );
					$this->redirect('admin/users');
					return;
				}
				else {
					$this->redirect('wordpress/admin/users');
				}
			}
		}
		return $this->sync();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */