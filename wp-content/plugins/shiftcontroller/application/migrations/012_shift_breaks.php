<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_shift_breaks extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('lunch_break', 'shifts') ){
			$this->dbforge->add_column(
				'shifts',
				array(
					'lunch_break' => array(
						'type' => 'INT',
						'null' => FALSE,
						'default'	=> 0,
						),
					)
				);
		}

		if( ! $this->db->field_exists('lunch_break', 'shift_templates') ){
			$this->dbforge->add_column(
				'shift_templates',
				array(
					'lunch_break' => array(
						'type' => 'INT',
						'null' => FALSE,
						'default'	=> 0,
						),
					)
				);
		}
	}

	public function down()
	{
	}
}