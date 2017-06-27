<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$app = CI::$APP->config->item('nts_app');

/**
 * Data Mapper Configuration
 *
 * Global configuration settings that apply to all DataMapped models.
 */

$dbprefix = isset($GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX'] : NTS_DB_TABLES_PREFIX;
$dbprefix_version = CI::$APP->config->item('nts_dbprefix_version');
if( $dbprefix_version ){
	$dbprefix = $dbprefix . $dbprefix_version . '_'; 
}

$config['prefix'] = $dbprefix;
$config['join_prefix'] = '';
//$config['error_prefix'] = '<p>';
$config['error_prefix'] = '';
//$config['error_suffix'] = '</p>';
$config['error_suffix'] = '';
$config['created_field'] = 'created';
$config['updated_field'] = 'updated';
$config['local_time'] = FALSE;
$config['unix_timestamp'] = TRUE; // changed from default
$config['timestamp_format'] = '';
$config['lang_file_format'] = 'model_${model}';
$config['field_label_lang_format'] = '${model}_${field}';
$config['auto_transaction'] = FALSE;
$config['auto_populate_has_many'] = FALSE;
$config['auto_populate_has_one'] = FALSE;
$config['all_array_uses_ids'] = FALSE;
// set to FALSE to use the same DB instance across the board (breaks subqueries)
// Set to any acceptable parameters to $CI->database() to override the default.
$config['db_params'] = '';
// Uncomment to enable the production cache
// $config['production_cache'] = 'datamapper/cache';
$config['extensions_path'] = 'datamapper';
$config['extensions'] = array('array');

/* End of file datamapper.php */
/* Location: ./application/config/datamapper.php */
