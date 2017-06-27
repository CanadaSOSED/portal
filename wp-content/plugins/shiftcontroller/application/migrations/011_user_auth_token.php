<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_user_auth_token extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('token', 'users') ){
			$this->dbforge->add_column(
				'users',
				array(
					'token' => array(
						'type'		=> 'VARCHAR(128)',
						'null'		=> FALSE,
						'default'	=> '',
						),
					)
				);
		}
	}

	public function down()
	{
	}
}