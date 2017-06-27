<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_conf_setup extends MY_Migration {
	public function up()
	{
		if( ! $this->db->table_exists('conf') ){
			// conf
			$this->dbforge->add_field(
				array(
					'id' => array(
						'type' => 'INT',
						'null' => FALSE,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
						),
					'name' => array(
						'type' => 'VARCHAR(255)',
						'null' => FALSE,
						),
					'value' => array(
						'type' => 'TEXT',
						'null' => TRUE,
						),
					)
				);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table('conf');
		}
	}

	public function down()
	{
	}
}