<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_shift_type extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('type', 'shifts') ){
			$this->dbforge->add_column(
				'shifts',
				array(
					'type' => array(
						'type'		=> 'TINYINT',
						'null'		=> FALSE,
						'default'	=> 1, // SHIFT_HC_MODEL::TYPE_SHIFT,
						),
					)
				);
		}

		/* convert timeoffs */
		if( $this->db->table_exists('timeoffs') ){
			$query = $this->db->get('timeoffs');
			foreach( $query->result_array() as $row ){
				$new = array(
					'type'		=> 2, // SHIFT_HC_MODEL::TYPE_TIMEOFF, 
					'date'		=> $row['date'],
					'start'		=> $row['start'],
					'date_end'	=> $row['date_end'],
					'end'		=> $row['end'],
					'status'	=> $row['status'],
					'user_id'	=> $row['user_id'],
					);
				$this->db->insert('shifts', $new);
			}

			$this->dbforge->drop_table('timeoffs');
		}
	}

	public function down()
	{
	}
}