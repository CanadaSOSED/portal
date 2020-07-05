<?php

/**
 * Manages WordPress Meme Shortcode options.
 *
 */
class WPGens_RAF_DB
{

    
    /**
     * @var string Raf DB Version Number
     */
    private $raf_db_version;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table = 'gens_raf';

	/**
	 * Meta Table
	 *
	 * @var string
	 */
	protected $meta_table = 'gens_rafmeta';

    /**
     * Constructor.
     *
     */
    public function __construct(){
        $this->raf_db_version = get_site_option('raf_db_version') || '1.0';
        add_action( 'plugins_loaded', array( $this, 'register_table' ));
        add_action( 'new_raf_data', array( $this, 'insert_new_raf_data' ), 10, 3);
    }
    
    /***** Getters *****/
    

	/**
	 * Get table name
	 * @return mixed
	 */
	public function get_table_name() {
		global $wpdb;
		return apply_filters( strtolower( __CLASS__ ) . '_table_name', $wpdb->prefix . $this->table );
	}

	/**
	 * Get the meta table name.
	 */
	public function get_meta_table_name() {
		global $wpdb;
		return apply_filters( strtolower( __CLASS__ ) . '_meta_table_name', $wpdb->prefix . $this->meta_table );
	}
	/**
	 * Get all results from the table.
	 *
	 * @return array|null|object
	 */
	public function total_records() {
		global $wpdb;

		$total_records = $wpdb->get_var( "SELECT COUNT(*) FROM " . $this->get_table_name() );

		return $total_records;
	}

	/**
	 * Get one single result by ID.
	 *
	 * @param int $id
	 *
	 * @return array|null|object|void
	 */
	public function get_by_id( $id = 0 ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM " . $this->get_table_name() . " WHERE ID=%d", $id );

		$results = $wpdb->get_row( $sql, ARRAY_A );

		return $results ? $results : array();
	}

	/**
	 * Find by meta value
	 *
	 * @param int $id
	 *
	 * @return array|null|object|void
	 */
	public function get_by_meta_key_value( $key, $value ) {
		global $wpdb;

        $total_records = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM " . $this->get_meta_table_name() . " WHERE meta_key=%s AND meta_value=%s", $key, $value) );
        
        return $total_records;
	}

	/**
	 * Get results by page
	 *
	 * @param int $id
	 *
	 * @return array|null|object|void
	 */
	public function get_by_page( $page = 1, $items_per_page = 20 ) {
        global $wpdb;
        
        $offset = ($page * $items_per_page) - $items_per_page;

		$results = $wpdb->get_results( "SELECT * FROM " . $this->get_table_name() . " ORDER BY time DESC LIMIT " .$offset.", ". $items_per_page, ARRAY_A );

		return $results ? $results : array();
	}

	/**
	 * Get results by a column.
	 *
	 * @param $column
	 * @param $value
	 *
	 * @return array|null|object
	 */
	public function get_by_column( $column, $value ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM " . $this->get_table_name() . " WHERE $column=%s", $value );

		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results ? $results : array();
	}

	/***** DB Setters (Insert, Update) *****/


	/**
	 * Insert a record.
	 *
	 * @param      $data
	 * @param null $format
	 *
	 * @return bool|int
	 */
	public function insert( $data, $format = null ) {
		global $wpdb;

		$ret = $wpdb->insert( $this->get_table_name(), $data, $format );

		return $ret ? $wpdb->insert_id : false;
	}

	/**
	 * Update a Row by ID.
	 *
	 * @param      $id
	 * @param      $data
	 * @param null $format
	 *
	 * @return bool
	 */
	public function update( $id, $data, $format = null ) {
		global $wpdb;

		$old = $this->get_by_id( $id );
		$ret = $wpdb->update( $this->get_table_name(), $data, array( 'ID' => $id ), $format, array( '%d') );

		return $ret ? true : false;
	}

	/***** DB Removers *****/

	/**
	 * Delete records.
	 *
	 * @param      $where
	 * @param null $format
	 *
	 * @return bool
	 */
	public function delete( $where, $format = null ) {
		global $wpdb;

		$ret = $wpdb->delete( $this->get_table_name(), $where, $format );

		return $ret ? true : false;
	}

	/**
	 * Delete by ID.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete_by_id( $id ) {
		return $this->delete( array( 'ID' => $id ), array( '%d' ) );
	}

	/***** DB META DATA *****/

	/**
	 * Get the level meta data.
	 *
	 * @param      $id
	 * @param      $key
	 * @param bool $single
	 *
	 * @return mixed
	 */
	public function get_meta( $id, $key, $single = true ) {
		return get_metadata( 'gens_raf', $id, $key, $single );
    }
    
	/**
	 * Add Meta
	 *
	 * @param $id
	 * @param $key
	 * @param $value
	 *
	 * @return false|int
	 */
	public function add_meta( $id, $key, $value ) {
		return add_metadata( 'gens_raf', $id, $key, $value );
    }
    
	/**
	 * Update Meta.
	 *
	 * @param        $id
	 * @param        $key
	 * @param string $value
	 * @param string $prev_value
	 *
	 * @return bool|int
	 */
	public function update_meta( $id, $key, $value = '', $prev_value = '' ) {
		return update_metadata( 'gens_raf', $id, $key, $value, $prev_value );
    }
    
	/**
	 * Delete Meta.
	 *
	 * @param        $id
	 * @param        $key
	 * @param string $value
	 * @param bool   $delete_all
	 *
	 * @return bool
	 */
	public function delete_meta( $id, $key, $value = '', $delete_all = false ) {
		return delete_metadata( 'gens_raf', $id, $key, $value, $delete_all );
    }
    
    /**
	 * Registers the table with $wpdb so the metadata api can find it.
	 *	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	public function register_table() {
		global $wpdb;
		// Create/update DB if version is different.
		if ( get_site_option( 'raf_db_version' ) != $this->raf_db_version ) {
			$this->raf_create_db_table();
		}
		$wpdb->gens_rafmeta = $this->get_meta_table_name();
    }

    /**
	 * Create RAF & RAF Meta DB Tables
	 *	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
    public function raf_create_db_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $this->get_table_name();
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            type varchar(20) NOT NULL,
            PRIMARY KEY id (id)
        ) $charset_collate;";

        $meta_table_name = $this->get_meta_table_name();
        $sql .= "CREATE TABLE $meta_table_name (
            meta_id bigint(20) NOT NULL AUTO_INCREMENT,
            gens_raf_id bigint(20) NOT NULL DEFAULT '0',
            meta_key varchar(255) DEFAULT NULL,
		    meta_value longtext,
            PRIMARY KEY meta_id (meta_id),
            KEY gens_raf_id (gens_raf_id),
		    KEY meta_key (meta_key)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option( 'raf_db_version', $this->raf_db_version );
    }

	/**
	 * Insert new RAF Data
	 *
	 * @param string $type
	 * @param array  $meta
	 */
    public function insert_new_raf_data($type,$meta = array()){
        $id = $this->insert(array('time'=> current_time("Y-m-d H:i:s"),'type' => $type));
        if($id) {
            foreach ($meta as $meta_key => $meta_value) {
                $this->add_meta($id, $meta_key, $meta_value);
            }
        }
    }
    
}