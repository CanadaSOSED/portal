<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_user_remember extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('login_hash', 'users') )
		{
			$this->dbforge->add_column(
				'users',
				array(
					'login_hash' => array(
						'type'		=> 'VARCHAR(128)',
						'null'		=> TRUE,
//						'default'	=> '',
						),
					)
				);
		}
	}

	public function down()
	{
	}
}