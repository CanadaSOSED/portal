<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_usernames extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('username', 'users') ){
			$this->dbforge->add_column(
				'users',
				array(
					'username' => array(
						'type'		=> 'VARCHAR(255)',
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