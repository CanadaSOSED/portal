<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_manager_user extends CI_Migration {
	public function up()
	{
	// set level from 2 to 3
		$this->db->where('level', 2); 
		$this->db->update( 'users', array('level' => 3) ); 
	}

	public function down()
	{
		$this->db->where('level', 3); 
		$this->db->update( 'users', array('level' => 2) ); 
	}
}
