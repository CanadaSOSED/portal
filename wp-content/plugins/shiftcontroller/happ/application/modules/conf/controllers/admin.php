<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Conf_HC_Controller extends _Backend_HC_controller
{
	private $params = array();
	private $tabs = array();

	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_ADMIN );

		$fields = $this->config->items('settings');
		$defaults = array();

		$this->form = HC_Lib::form();
		$app_conf = HC_App::app_conf();

		foreach( $fields as $fn => $f ){
			$defaults[$fn] = $app_conf->get($fn);
			$this->form->set_input( $fn, $f['type'] );
			switch( $f['type'] ){
				case 'time':
					$this->form->input_call($fn, 'set_conf', array('min', 0));
					$this->form->input_call($fn, 'set_conf', array('max', 24*60*60));
					break;
			}
		}

		$this->form->set_values( $defaults );
	}

	private function _get_tabs( $fields ){
		$tabs = array();
		foreach( $fields as $fn => $f ){
			$this_tab = 'core';
			if( strpos($fn, ':') !== FALSE ){
				list( $this_tab, $this_short_fn ) = explode( ':', $fn );
			}

			if( ! isset($tabs[$this_tab])){
				$tabs[$this_tab] = array();
			}
			$tabs[$this_tab][] = $fn;
		}
		return $tabs;
	}

	function reset( $what )
	{
		$app_conf = HC_App::app_conf();

		// update
		$fields = $this->config->items('settings');
		foreach( $fields as $fn => $f ){
			$app_conf->reset( $fn );
		}

	// redirect back
		$this->session->set_flashdata( 'message', HCM::__('Settings reset to default') );
		$this->redirect( 'conf/admin/' . $what );
	}

	function update( $tab = 'core' )
	{
		$app_conf = HC_App::app_conf();

		$fields = $this->config->items('settings');
		$tabs = $this->_get_tabs( $fields );
		$these_fields = $tabs[$tab];

		$validator = new HC_Validator;
		foreach( $these_fields as $fn ){
			$f = $fields[$fn];
			if( isset($f['rules']) ){
				$validator->set_rules( $fn, $f['rules'] );
			}
		}

		$post = $this->input->post();
		$this->form->grab( $post );
		$values = $this->form->values();

		if( $values && ($validator->run($values) == TRUE) ){
			reset( $these_fields );
			foreach( $these_fields as $fn ){
				$app_conf->set( $fn, $values[$fn] );
			}

		// redirect back
			$msg = HCM::__('Settings updated');
			$this->session->set_flashdata( 'message', $msg );

			$to = 'conf/admin/index/' . $tab;
			$this->redirect( $to );
		}
		else {
			$errors = $validator->error();
			$this->form->set_values( $values );
			$this->form->set_errors( $errors );

			$fields = $this->config->items('settings');

		/* render view */
			$this->layout->set_partial(
				'content', 
				$this->render( 
					'conf/admin/index',
					array(
						'fields'	=> $fields,
						'form'		=> $this->form,
						'tab'		=> $tab,
						'tabs'		=> $tabs,
						)
					)
				);
			$this->layout();
		}
	}

	function index( $tab = 'core' )
	{
		$fields = $this->config->items('settings');
		$ri = HC_Lib::ri();
		if( $ri ){
			unset( $fields['login_with'] );
		}

		$tabs = $this->_get_tabs( $fields );

	/* render view */
		$this->layout->set_partial(
			'content', 
			$this->render( 
				'conf/admin/index',
				array(
					'fields'	=> $fields,
					'form'		=> $this->form,
					'tab'		=> $tab,
					'tabs'		=> $tabs,
					)
				)
			);
		$this->layout();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */