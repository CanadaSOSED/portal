<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Zoom_Users_Admin_HC_Controller extends _Backend_HC_controller
{
	protected $views_path = 'admin/users/zoom';

	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_MANAGER );
	}

	function index()
	{
		$args = hc_parse_args( func_get_args(), TRUE );
		if( ! (isset($args['id'])) ){
			echo 'PARAMS MISSING IN admin/users/zoom/index<br>';
			return;
		}

	/* PARAMS */
		$id = $args['id'];
		$tab = isset($args['tab']) ? $args['tab'] : 'edit';
		$subtab = isset($args['subtab']) ? $args['subtab'] : '';

		if( is_object($id) ){
			$model = $id;
		}
		else {
			$model = HC_App::model('user');
			$model
				->where('id', $id)
				->get()
				;
			$this->_check_model( $model );
		}

	/* build content */
		$subheader = NULL;
		$content = '';
		$method = '_content_' . $tab;

		if( method_exists($this, $method) ){
			$content = $this->{$method}( $model );
		}
		else {
			$extensions = HC_App::extensions();
			if( $extensions->has(array('admin/users/zoom', $tab)) ){
				$calling_parent = 'admin/users/zoom/index/id/' . $id . '/tab/' . $tab;

				$pass_arg = isset($args['_pass']) ? $args['_pass'] : array();

				array_unshift(
					$pass_arg,
					array('admin/users/zoom', $tab, $calling_parent)
					);

				$pass_arg[] = 'user';
				$pass_arg[] = $model->id;

				$content = call_user_func_array(
					array($extensions, 'run'),
					$pass_arg
					);

				$subheader = $extensions->run(
					array('admin/users/zoom/menubar', $tab),
					$model
					);
			}
		}

	/* CONTENT */
		$content = $this->render(
			$this->views_path . '/index',
			array(
				'subheader'	=> $subheader,
				'content'	=> $content,
				)
			);

		$this->layout->set_partial(
			'content',
			$content
			);

	/* HEADER */
		$this->layout->set_partial(
			'header_ajax',
			$this->render(
				$this->views_path . '/_header',
				array(
					'object'	=> $model,
					)
				)
			);

	/* MENUBAR */
		$this->layout->set_partial(
			'sidebar',
			$this->render( 
				$this->views_path . '/_menubar',
				array(
					'tab'		=> $tab,
					'object'	=> $model,
					)
				)
			);

		$this->layout();
	}

	private function _content_edit( $model )
	{
		$ri = HC_Lib::ri();
		if( $ri ){
			return $this->render( 
				'admin/users/zoom/view',
				array(
					'object'	=> $model,
					)
				);
		}
		else {
			$form = HC_Lib::form()
				->set_input( 'first_name',	'text' )
				->set_input( 'last_name',	'text' )
				->set_input( 'email',		'text' )
				->set_input( 'username',	'text' )
				->set_input( 'level',		'select' )
				;

			$form->set_values( $model->to_array() );
			$form->set_errors( $model->errors() );

			return $this->render( 
				'admin/users/zoom/edit',
				array(
					'form'		=> $form,
					'object'	=> $model,
					)
				);
		}
	}

	private function _content_password( $model )
	{
		$form = HC_Lib::form()
			->set_input( 'password',			'password' )
			->set_input( 'confirm_password',	'password' )
			;

		if( $ri = HC_Lib::ri() ){
			$form->set_readonly();
		}

		// $form->set_values( $model->to_array() );
		$form->set_errors( $model->errors() );

		return $this->render( 
			'admin/users/zoom/password',
			array(
				'form'		=> $form,
				'object'	=> $model,
				)
			);
	}
}