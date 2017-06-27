<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Update_Shifts_HC_Controller extends _Front_HC_Controller
{
	protected $form_edit = NULL;

	function __construct()
	{
		parent::__construct();

		$this->form_edit = HC_Lib::form()
			->set_input( 'status', 'radio' )
			->set_input( 'date', 'date' )
			->set_input( 'time', 'timeframe', array('start' => 'start', 'end' => 'end') )
			->set_input( 'lunch_break', 'duration' )
			->set_input( 'user', 'hidden' )
			->set_input( 'location', 'hidden' )
			;

		$this->form_assign = HC_Lib::form()
			->set_input( 'user', 'radio' )
			;
	}

	function index( $id )
	{
		$acl = HC_App::acl();
		$extensions = HC_App::extensions();
		$args = func_get_args();
		$id = array_shift($args);

		$model = HC_App::model('shift');
		$model
			->where('id', $id)
			->get()
			;
		$this->_check_model( $model );

		$acl = HC_App::acl();
		if( ! $acl->set_object($model)->can('edit') ){
			return;
		}

		$original_model = clone $model;

	/* supplied as parameters */
		$values = hc_parse_args( $args );

	/* if post supplied */
		$form = $this->form_edit;
		$post = $this->input->post();

		if( $post ){
			$form->grab( $post );
			$form_values = $form->values();
			$values = array_merge( $values, $form_values );
		}

		$relname = 'user';
		$unassign = FALSE;

		if( 
			($values[$relname] === '0') OR 
			($values[$relname] === 0) OR
			($values[$relname] == '') OR
			($values[$relname] === NULL)
			){
			// delete user relation
			unset( $values[$relname] );
			if( $model->user_id ){
				$unassign = TRUE;
			}
		}

		if( ! $values ){
			return $this->_zoom( $model, 'time' );
		}

		if( $values['location'] === NULL ){
			unset( $values['location'] );
		}

		$related = $model->from_array( $values );

		if( ! $acl->set_object($model)->can('validate') ){
			$msg = HCM::__('Permission Denied');
			$this->session->set_flashdata(
				'error_ajax',
				$msg
				);
			$referrer = ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
			$this->redirect( $referrer, array(), 3 );
			return;
		}

		$action_result1 = $model->save($related);
		$action_result2 = TRUE;

		if( $unassign ){
			$rel = $model->{$relname}->get();
			$action_result2 = $model->delete($rel, $relname);
		}

		if( $action_result1 && $action_result2 ){
		/* extensions */
			$extensions->run(
				'shifts/update',
				$post,
				$model
				);

		/* save and redirect here */
			if( $id ){
				$msg = sprintf( HCM::_n('%d shift updated', '%d shifts updated', 1), 1 );
			}
			else {
				$msg = sprintf( HCM::_n('%d shift added', '%d shifts added', 1), 1 );
			}

			$this->session->set_flashdata(
				'message',
				$msg
				);

			$redirect_to = 'shifts/zoom/index/id/' . $id;
//			$redirect_to = '-referrer-';

		/* what to refresh on referring page */
			$parent_refresh = $model->present_calendar_refresh();
			$parent_refresh = array_keys($parent_refresh);
			$this->redirect( $redirect_to, $parent_refresh );
		}
		else {
		/* final layout */
			$this->layout->set_partial(
				'content',
				Modules::run('shifts/zoom/index', 'id', $model)
				);
			$this->layout();
		}
	}
}