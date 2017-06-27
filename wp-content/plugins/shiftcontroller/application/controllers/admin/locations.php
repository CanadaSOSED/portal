<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Locations_Admin_HC_Controller extends _Backend_HC_controller
{
	var $form = NULL;

	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_ADMIN );

		$this->form = HC_Lib::form()
			->set_input( 'name', 'text' )
			->set_input( 'description', 'textarea' )
			->set_input( 'color', 'colorpicker' )
			;
	}

	function index()
	{
		$entries = array();

		$model = HC_App::model('location');
		$model->get();

		$this->layout->set_partial(
			'content', 
			$this->render( 
				'admin/locations/index',
				array(
					'entries' => $model
					)
				)
			);
		$this->layout();
	}

	function delete( $id )
	{
		$model = HC_App::model('location');
		$model
			->where('id', $id)
			->get($id);
		$this->_check_model( $model );

		if( $model->delete() ){
			$msg = HCM::__('Location deleted');	
			$this->session->set_flashdata( 'message', $msg );
		}
		else {
			$errors = $model->errors();
			$msg = HCM::__('Error') . ': ' . join(' ', $errors);
			$this->session->set_flashdata( 'error', $msg );
		}

		$redirect_to = 'admin/locations';
		$this->redirect(
			$redirect_to,
			TRUE // force redirect for parent window if modal is used
			);
		return;
	}

	function up( $id )
	{
		$model = HC_App::model('location');
		$model
			->where('id', $id)
			->get($id);
		$this->_check_model( $model );

		$model->up();

		$msg = array(
			$model->present_title(),
			HCM::__('Move Up'),
			HCM::__('OK')
			);
		$msg = join( ': ', $msg );
		$this->session->set_flashdata( 'message', $msg );

		$redirect_to = 'admin/locations';
		$this->redirect( $redirect_to );
		return;
	}

	function down( $id )
	{
		$model = HC_App::model('location');
		$model
			->where('id', $id)
			->get($id);

		$model->down();

		$msg = array(
			$model->present_title(),
			HCM::__('Move Down'),
			HCM::__('OK')
			);
		$msg = join( ': ', $msg );
		$this->session->set_flashdata( 'message', $msg );

		$redirect_to = 'admin/locations';
		$this->redirect(
			$redirect_to,
			TRUE // force redirect for parent window if modal is used
			);
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

		$model = HC_App::model('location');
		$model->from_array( $values );

		if( $model->save() ){
			/* save and redirect here */
			$msg = HCM::__('Location added');
			$this->session->set_flashdata( 
				'message',
				$msg
				);
			$redirect_to = 'admin/locations';
			$this->redirect( $redirect_to );
		}
		else {
			$errors = $model->errors();
			$this->form->set_values( $values );
			$this->form->set_errors( $errors );

			$content = $this->render( 
				'admin/locations/add',
				array(
					'form'	=> $this->form,
					// 'id' 	=> $id,
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
		$model = HC_App::model('location');
		$model
			->where('id', $id)
			->get($id);
		$this->_check_model( $model );

		$original_model = clone $model;

		$post = $this->input->post();
		if( ! $post ){
			return;
		}

		$this->form->grab( $post );
		$values = $this->form->values();
		$model->from_array( $values );

		if( $model->save() ){
			/* save and redirect here */
			$msg = HCM::__('Location updated');
			$this->session->set_flashdata( 
				'message',
				$msg
				);
			$redirect_to = 'admin/locations';
			$this->redirect( $redirect_to );
		}
		else {
			$errors = $model->errors();
			$this->form->set_values( $values );
			$this->form->set_errors( $errors );

			$content = $this->render( 
				'admin/locations/edit',
				array(
					'form'	=> $this->form,
					'id' 	=> $id,
					'model'	=> $original_model,
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
		$model = HC_App::model('location');
		$model->id = $model->select_max('id')->get()->id + 1;

		$values = array(
			'color'	=> $model->present_color()
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
				'admin/locations/add',
				array(
					'form'	=> $this->form
					)
				)
			);
		$this->layout();
	}

	function edit( $id )
	{
		$model = HC_App::model('location');
		$model
			->where('id', $id)
			->get($id);
		$this->_check_model( $model );

		$errors = array();
		$values = $model->to_array();
		$values['color'] = $model->present_color();

	/* display form */
		$this->form->set_values( $values );
		$this->form->set_errors( $errors );

		$this->layout->set_partial(
			'content', 
			$this->render( 
				'admin/locations/edit',
				array(
					'form'	=> $this->form,
					'id' 	=> $id,
					'model'	=> $model,
					)
				)
			);
		$this->layout();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */