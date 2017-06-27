<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// class Setup_HC_Controller extends MX_Controller
class Setup_HC_Controller extends MY_HC_Base_Base_Controller
{
	protected $is_setup = TRUE;
	private $form = NULL;

	function __construct()
	{
		parent::__construct();

		$this->form = HC_Lib::form()
			->set_input( 'first_name', 'text' )
			->set_input( 'last_name', 'text' )
			->set_input( 'email', 'text' )
			->set_input( 'password', 'password' )
			->set_input( 'confirm_password', 'password' )
			;
	}

	protected function _drop_tables()
	{
		$this->db->reset_data_cache();

		$app = HC_App::app();
		$my_table_prefix = isset($GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX'] : NTS_DB_TABLES_PREFIX;
		$dbprefix_version = $this->config->item('nts_dbprefix_version');
		if( $dbprefix_version ){
			$my_table_prefix = $my_table_prefix . $dbprefix_version . '_'; 
		}

		$tables = array();
		$sth = $this->db->query("SHOW TABLES LIKE '" . $my_table_prefix . "%'");
		foreach( $sth->result_array() as $r ){
			reset( $r );
			foreach( $r as $k => $v ){
				$tables[] = $v;
			}
		}
		reset( $tables );
		foreach( $tables as $t ){
			$this->db->query("DROP TABLE " . $t . "");
		}
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

	function upgrade()
	{
		$old_version = $this->_get_old_version();

		if( $old_version === NULL ){
			echo "Can't find the old version";
			exit;
			// $this->session->set_flashdata( 'error', "Can't find the old version" );
			// $this->redirect();
			// return;
		}

		$db = clone $this->db;
		$dbprefix_version = $this->config->item('nts_dbprefix_version');
		$core_prefix = substr($db->dbprefix, 0, -(strlen($dbprefix_version)+1));

		$old_prefix = strlen($old_version) ? $core_prefix . $old_version . '_' : $core_prefix;
		$new_prefix = $core_prefix . $dbprefix_version . '_';

		$db->dbprefix = $old_prefix;
		$old_tables = array();
		$all_old_tables = $db->list_tables();
		foreach( $all_old_tables as $ot ){
			if( substr($ot, 0, strlen($old_prefix)) == $old_prefix ){
				$old_tables[] = substr($ot, strlen($old_prefix));
			}
		}

		if( ! $old_tables ){
			echo "Can't find the old version";
			exit;
			// $this->session->set_flashdata( 'error', "Can't find the old version" );
			// $this->redirect();
			// return;
		}

	/* now to the core */
		$db->dbprefix = $core_prefix;
		$db->reset_data_cache();

		$is_error = FALSE;
		foreach( $old_tables as $ot ){
			$sql = 'CREATE TABLE `' . $new_prefix . $ot . '` LIKE `' . $old_prefix . $ot . '`';
			if( FALSE === $db->simple_query($sql) ){
				$error_no = $db->_error_number();
				$error_msg = $db->_error_message();
				$is_error = TRUE;
				echo 'Database error: ' . $error_no . ': ' . $error_msg . '<br>';
				exit;
			}

			$sql = 'INSERT INTO `' . $new_prefix . $ot . '` SELECT * FROM `' . $old_prefix . $ot . '`';
			if( FALSE === $db->simple_query($sql) ){
				$error_no = $db->_error_number();
				$error_msg = $db->_error_message();
				$is_error = TRUE;
				echo 'Database error: ' . $error_no . ': ' . $error_msg . '<br>';
				exit;
			}
		}

		if( ! $is_error ){
			$this->session->set_flashdata( 'message', HCM::__('OK') );
			$this->redirect();
		}
		return;
	}

	function index()
	{
		$page_title = $this->config->item('nts_app_title') . ' :: ' . 'Installation';

		/* check if we have an older version installed */
		$offer_upgrade = 0;
		$old_version = $this->_get_old_version();
		if( $old_version !== NULL ){
			$offer_upgrade = 1;
		}

		if( $offer_upgrade ){
			// $this->redirect('setup/upgrade');
		}

		$this->layout->set_partial(
			'content', 
			$this->render(
				'setup',
				array(
					'page_title'	=> $page_title,
					'form'			=> $this->form,
					'offer_upgrade'	=> $offer_upgrade,
					)
				)
			);
		$this->layout();
	}

	function run()
	{
		$validator = new HC_Validator;
		// $validator->set_rules('first_name',			'trim|required');

		$post = $this->input->post();
		$this->form->grab( $post );
		$values = $this->form->values();

		$errors = array();
		if( $values && $validator->run($values) ){
		/* run setup */
		/* reset tables */
			$this->_drop_tables();

		/* setup tables */
			$this->load->library('migration');
			if ( ! $this->migration->current()){
				show_error( $this->migration->error_string());
				return false;
			}

			$setup_ok = TRUE;
		/* admin user */
			$um = HC_App::model('user');
			$um->from_array( $values );
			$um->level = $um->_const('LEVEL_ADMIN');

			if( $um->save() ){
				$email_from = $values['email'];
				$email_from_name = $values['first_name'] . ' ' . $values['last_name'];
			}
			else {
				$errors = array_merge($errors, $um->errors() );
				$this->_drop_tables();
				$setup_ok = FALSE;
			}

			if( $setup_ok ){
			/* default settings */
				$app_conf = HC_App::app_conf();
				$app_conf->init(); // to reload database
				$app_conf->set( 'email_from',		$email_from );
				$app_conf->set( 'email_from_name',	$email_from_name );

				$this->session->set_flashdata( 'message', HCM::__('OK') ); # message sent on succesful setup
				$this->redirect('setup/ok' );
				return;
			}
		}
		$errors = array_merge($errors, $validator->errors() );
		$this->form->set_errors( $errors );
		return $this->index();
	}

	function ok()
	{
		$page_title = $this->config->item('nts_app_title') . ' :: ' . 'Installation';
		$this->layout->set_partial(
			'content', 
			$this->render(
				'setup_ok',
				array(
					'page_title'	=> $page_title,
					)
				)
			);
		$this->layout();
	}
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */