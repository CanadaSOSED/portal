<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_convert_trades extends CI_Migration {
	public function up()
	{
	/* remove trades table and set the trade column for shifts for pending trades */
		if( ! $this->db->field_exists('has_trade', 'shifts') )
		{
			$this->dbforge->add_column(
				'shifts',
				array(
					'has_trade' => array(
						'type' 		=> 'TINYINT',
						'null'		=> FALSE,
						'default'	=> 0
						),
					)
				);
		}

		if( $this->db->table_exists('trades') )
		{
		// TRADE_MODEL:STATUS_PENDING - set the trade column
			$this->db->where( 'status', 1 );
			$this->db->select( 'shift_id' );
			$query = $this->db->get( 'trades' );
			foreach( $query->result_array() as $row )
			{
				$this->db
					->where('id', $row['shift_id'])
					->set('has_trade', 1)
					->update('shifts')
					;
			}

		// TRADE_MODEL:STATUS_APPROVED - remove current user
			$this->db->where('status', 2);
			$this->db->select( 'shift_id' );
			$query = $this->db->get( 'trades' );
			foreach( $query->result_array() as $row ){
				$this->db
					->where('id', $row['shift_id'])
					->set('user_id', NULL, FALSE)
					->update('shifts')
					;
			}

		// TRADE_MODEL:STATUS_ACCEPTED - switch the shift to the new user
			$this->db->where('status', 3);
			$this->db->select( array('shift_id', 'to_user_id') );
			$query = $this->db->get( 'trades' );
			foreach( $query->result_array() as $row ){
				$this->db
					->where('id', $row['shift_id'])
					->update( 'shifts', 
						array(
							'user_id'	=> $row['to_user_id']
							)
						)
					;
			}

			// TRADE_MODEL:STATUS_DENIED - DO NOTHING
//			$this->db->where('status', 4);

			// TRADE_MODEL:STATUS_COMPLETED - DO NOTHING
//			$this->db->where('status', 5);
		}

	/* now delete the trades table */
		if( $this->db->table_exists('trades') ){
			$this->dbforge->drop_table('trades');
		}
	}

	public function down()
	{
	}
}