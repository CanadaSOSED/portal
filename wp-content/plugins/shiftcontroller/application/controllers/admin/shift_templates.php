<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shift_Templates_Admin_HC_Controller extends _Backend_HC_controller
{
	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_ADMIN );

		$this->form = HC_Lib::form()
			->set_input( 'name', 'text' )
			->set_input( 'time', 'timeframe', array('start' => 'start', 'end' => 'end') )
			->set_input( 'lunch_break', 'duration' )
			;
	}

	function index()
	{
		$model = HC_App::model('shift_template');
		$model->get();

		$this->layout->set_partial(
			'content',
			$this->render( 
				'admin/shift_templates/index',
				array(
					'entries' => $model
					)
				)
			);
		$this->layout();
	}

	function delete( $id )
	{
		$model = HC_App::model('shift_template');
		$model->get_by_id( $id );
		$this->_check_model( $model );

		if( $model->delete() ){
			$msg = HCM::__('OK');
			$this->session->set_flashdata( 'message', $msg );
		}
		else {
			$errors = $model->errors();
			$msg = HCM::__('Error') . ': ' . join(' ', $errors);
			$this->session->set_flashdata( 'error', $msg );
		}

		$redirect_to = 'admin/shift_templates';
		$this->redirect( $redirect_to );
		return;
	}

	function insert()
	{
		$post = $this->input->post();
		if( ! $post ){
			return;
		}

		$this->form->grab( $post );
		$values = $this->form->values();

		$model = HC_App::model('shift_template');
		$model->from_array( $values );

		if( $model->save() ){
			/* save and redirect here */
			$msg = HCM::__('OK');
			$this->session->set_flashdata( 
				'message',
				$msg
				);
			$redirect_to = 'admin/shift_templates';
			$this->redirect( $redirect_to );
		}
		else {
			$errors = $model->errors();
			$this->form->set_values( $values );
			$this->form->set_errors( $errors );

			$content = $this->render( 
				'admin/shift_templates/add',
				array(
					'form'	=> $this->form,
					)
				);

			$this->layout->set_partial(
				'content', 
				$content
				);
			$this->layout();
		}
	}

	function update( $id = 0 )
	{
		$model = HC_App::model('shift_template');
		$model->get_by_id( $id );
		$this->_check_model( $model );

		$post = $this->input->post();
		if( ! $post ){
			return;
		}

		$this->form->grab( $post );
		$values = $this->form->values();
		$model->from_array( $values );

		if( $model->save() ){
			/* save and redirect here */
			$msg = HCM::__('OK');
			$this->session->set_flashdata( 
				'message',
				$msg
				);
			$redirect_to = 'admin/shift_templates';
			$this->redirect( $redirect_to );
		}
		else {
			$errors = $model->errors();
			$this->form->set_values( $values );
			$this->form->set_errors( $errors );

			$content = $this->render( 
				'admin/shift_templates/edit',
				array(
					'form'	=> $this->form,
					'id' 	=> $id,
					)
				);

			$this->layout->set_partial(
				'content', 
				$content
				);
			$this->layout();
		}
	}

	function add()
	{
		$values = array(
			'time' => array( 0, 15*60 ),
			);

		$func_args = func_get_args();
		$values = array_merge( $values, hc_parse_args($func_args) );
		$errors = array();

	/* display form */
		$this->form->set_values( $values );
		$this->form->set_errors( $errors );

		$this->layout->set_partial(
			'content', 
			$this->render( 
				'admin/shift_templates/add',
				array(
					'form'	=> $this->form
					)
				)
			);
		$this->layout();
	}

	function edit( $id )
	{
		$model = HC_App::model('shift_template');
		$model->get_by_id( $id );
		$this->_check_model( $model );

		$errors = array();
		$values = $model->to_array();

	/* display form */
		$this->form->set_values( $values );
		$this->form->set_errors( $errors );

		$this->layout->set_partial(
			'content', 
			$this->render( 
				'admin/shift_templates/edit',
				array(
					'form'	=> $this->form,
					'id' 	=> $id,
					)
				)
			);
		$this->layout();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */