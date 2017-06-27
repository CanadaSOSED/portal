<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_Admin_HC_Controller extends _Backend_HC_controller
{
	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_ADMIN );
	}

	function index( $tab = 'list' )
	{
		$model = HC_App::model('user');

		$layout = clone $this->layout;

	/* build content */
		$content = '';
		$method = '_content_' . $tab;
		if( method_exists($this, $method) ){
			$content = $this->{$method}( $model );
		}
		else {
			$extensions = HC_App::extensions();
			if( $extensions->has(array('admin/users/index', $tab)) ){
				$calling_parent = 'admin/users/index' . '/' . $tab;

				$content = $extensions->run(
					array('admin/users/index', $tab, $calling_parent)
					);
			}
		}

		$layout->set_partial(
			'content',
			$content
			);

	/* header */
		$layout->set_partial(
			'header',
			$this->render( 
				'admin/users/index/_header',
				array(
					)
				)
			);

	/* menubar */
		$layout->set_partial(
			'menubar',
			$this->render( 
				'admin/users/index/_menubar',
				array(
					'tab'		=> $tab,
					'object'	=> $model,
					)
				)
			);

	/* final layout */
		$this->layout->set_partial(
			'content',
			$this->render(
				'admin/users/index/index',
				array(
					'layout'	=> $layout,
					)
				)
			);
		$this->layout();
	}

	private function _content_list( $model )
	{
		$model = HC_App::model('user');
		$model->get();
		return $this->render( 
			'admin/users/index/list',
			array(
				'entries' => $model
				)
			);
	}

	private function _content_add( $model )
	{
		return Modules::run('admin/users/add/index');
	}

	function delete( $id )
	{
		$model = HC_App::model('user');
		$model
			->where('id', $id)
			->get()
			;
		$this->_check_model( $model );
		
		if( $model->delete() ){
			$msg = HCM::__('User deleted');
			$this->session->set_flashdata( 'message', $msg );
		}
		else {
			$errors = $model->errors();
			$msg = HCM::__('Error') . ': ' . join(' ', $errors);
			$this->session->set_flashdata( 'error', $msg );
		}

		$redirect_to = 'admin/users';
		$this->redirect( $redirect_to );
		return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */