<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_location_colors extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('color', 'locations') ){
			$this->dbforge->add_column(
				'locations',
				array(
					'color' => array(
						'type'		=> 'VARCHAR(8)',
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