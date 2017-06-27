<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Migration extends CI_Migration
{
	protected $_migration_enabled = FALSE;
	protected $_migration_path = NULL;
	protected $_migration_version = 0;

	protected $_error_string = '';

	protected $_current_module = '';
	protected $_core_config = array();

	protected $_current_versions = array();

	public function __construct($config = array())
	{
		# Only run this constructor on main library load
		if( strtolower(get_parent_class($this)) !== 'ci_migration' )
		{
			return;
		}

		$config = array(
			);

	/* reinit core config */
		$config_files = array(
			APPPATH . 'config/migration.php',
			NTS_SYSTEM_APPPATH . 'config/migration.php',
			);
		foreach( $config_files as $cf )
		{
			if( file_exists($cf) )
			{
				require( $cf );
				break;
			}
		}

		$this->_core_config = $config;
//		$this->init_module();

		log_message('debug', 'Migrations class initialized');

		// Are they trying to use migrations while it is disabled?
//		if ($this->_migration_enabled !== TRUE) {
//			show_error('Migrations has been loaded but is disabled or set up incorrectly.');
//		}

		// Load migration language
//		$this->lang->load('migration');

		// They'll probably be using dbforge
		$this->load->dbforge();

		// If the migrations table is missing, make it
		if( !$this->db->table_exists('migrations') )
		{
			$this->dbforge->add_field(array(
				'module'  => array('type' => 'VARCHAR', 'constraint' => 20),
				'version' => array('type' => 'INT', 'constraint' => 3),
				));
			$this->dbforge->create_table('migrations', TRUE);
			$this->db->insert('migrations', 
				array(
					'module' => 'ci_core',
					'version' => 0
					)
				);
		}
		elseif( ! $this->db->field_exists('module', 'migrations') )
		{
			$this->dbforge->add_column(
				'migrations',
				array(
					'module' => array(
						'type'	=> 'VARCHAR(100)',
						'null'	=> TRUE,
						),
					)
				);
			$this->db->update( 'migrations', array('module' => 'ci_core') );
		}

		/* load current versions */
		foreach( $this->db->get('migrations')->result() as $row )
		{
			$module = strtolower( $row->module );
			$this->_current_versions[ $module ] = $row->version;
		}
	}

	public function current()
	{
		$return = TRUE;

	/* get modules */
		$modules = $this->get_modules();
		foreach( $modules as $module ){
			if( $this->init_module($module) ){
				$this_return = $this->version( $this->_migration_version );
				$return = $return && $this_return;
			}
		}
		return $return;
	}

	public function get_modules()
	{
		$CI =& ci_get_instance();
		$modules = $CI->config->get_modules();
		if( ! is_array($modules) ){
			$modules = array();
		}
		$modules = array_merge( array('ci_core'), $modules );
		return $modules;
	}

	public function init_module($module = 'ci_core')
	{
		if ($module === 'ci_core')
		{
			$config = $this->_core_config;
			if( (! isset($config['migration_path'])) OR (! $config['migration_path'])  )
			{
				$config['migration_path'] = APPPATH . 'migrations/';
			}
//			_print_r( $config );
		}
		else
		{
			list($path, $file) = Modules::find('migration', $module, 'config/');
			if ($path === FALSE)
			{
				return FALSE;
			}
			if (!$config = Modules::load_file($file, $path, 'config'))
			{
				return FALSE;
			}
			if( (! isset($config['migration_path'])) OR (! $config['migration_path'])  )
			{
				$config['migration_path'] = '../migrations';
			}
			$config['migration_path'] = $path . $config['migration_path'];
		}

		foreach ($config as $key => $val)
		{
			$this->{'_' . $key} = $val;
		}

		if ($this->_migration_enabled !== TRUE)
		{
			return FALSE;
		}

		$this->_migration_path = rtrim($this->_migration_path, '/') . '/';
		if (!file_exists($this->_migration_path))
		{
			return FALSE;
		}

		$this->_current_module = $module;
		return TRUE;
	}

	/**
	 * Migrate to a schema version
	 *
	 * Calls each migration step required to get to the schema version of
	 * choice
	 *
	 * @param    int $target_version Target schema version
	 * @return    mixed    TRUE if already latest, FALSE if failed, int if upgraded
	 */
	public function version($target_version)
	{
		$this->db->reset_data_cache();

		$start = $current_version = $this->_get_version();
		$stop  = $target_version;

		if ($target_version > $current_version) {
			// Moving Up
			++$start;
			++$stop;
			$step = 1;
		} else {
			// Moving Down
			$step = -1;
		}

		$method     = ($step === 1) ? 'up' : 'down';
		$migrations = array();

		// We now prepare to actually DO the migrations
		// But first let's make sure that everything is the way it should be
		for ($i = $start; $i != $stop; $i += $step) {
			$f = glob(sprintf($this->_migration_path . '%03d_*.php', $i));

			// Only one migration per step is permitted
			if (count($f) > 1) {
				$this->_error_string = 'migration_multiple_version' . $i;
				return FALSE;
			}

			// Migration step not found
			if (count($f) == 0) {
				// If trying to migrate up to a version greater than the last
				// existing one, migrate to the last one.
				if ($step == 1) {
					break;
				}

				// If trying to migrate down but we're missing a step,
				// something must definitely be wrong.
				$this->_error_string = 'migration_not_found' . ':' . $i;

				return FALSE;
			}

			$file = basename($f[0]);
			$name = basename($f[0], '.php');

			// Filename validations
			if (preg_match('/^\d{3}_(\w+)$/', $name, $match))
			{
				$this_migration = strtolower($match[1]);
//				$this_migration = ucfirst($match[1]);

				// Cannot repeat a migration at different steps
				if (in_array($this_migration, $migrations))
				{
					$this->_error_string = 'migration_multiple_version' . ':' . $match[1];
					return FALSE;
				}

				// include $f[0];
				include_once( $f[0] );

				if( $this->_current_module != 'ci_core' )
				{
					$this_migration = $this->_current_module . '_' . $this_migration;
				}
				$class = 'Migration_' . $this_migration;

				if( ! class_exists($class) )
				{
					$this->_error_string = 'migration_class_doesnt_exist' . ':' . $class;
					return FALSE;
				}

				if( ! is_callable(array($class, $method)) )
				{
					$this->_error_string = 'migration_missing_' . $method . '_method' . ':' . $class;
					return FALSE;
				}

				$migrations[] = $this_migration;
			}
			else
			{
				$this->_error_string = 'migration_invalid_filename' . ':' . $file;
				return FALSE;
			}
		}

		log_message('debug', 'Current migration: ' . $current_version);
		$version = $i + ($step == 1 ? -1 : 0);

		// If there is nothing to do so quit
		if ($migrations === array())
		{
			return TRUE;
		}

		log_message('debug', 'Migrating from ' . $method . ' to version ' . $version);

		// Loop through the migrations
		foreach ($migrations AS $migration) {
			
			// Run the migration class
			$class = 'Migration_' . ucfirst(strtolower($migration));
			call_user_func(array(new $class, $method));

			$current_version += $step;
			$this->_update_version($current_version);
		}

		log_message('debug', 'Finished migrating to ' . $current_version);

		return $current_version;
	}

// --------------------------------------------------------------------

	/**
	 * Retrieves current schema version
	 *
	 * @param string $module
	 * @return    int    Current Migration
	 */
	protected function _get_version($module = '')
	{
		if( ! $module )
		{
			$module = $this->_current_module;
		}
		$module = strtolower( $module );

		$return = isset($this->_current_versions[$module]) ? $this->_current_versions[$module] : 0;
		return $return;
	}

// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @param    int    Migration reached
	 * @param string $module
	 * @return    bool
	 */
	protected function _update_version($migrations, $module = '')
	{
		! $module AND $module = $this->_current_module;
		$row = $this->db->get_where('migrations', array('module' => $module))->row();
		if (count($row)) {
			return $this->db->where(array('module' => $module))->update('migrations', array('version' => $migrations));
		} else {
			return $this->db->insert('migrations', array('module' => $module, 'version' => $migrations));
		}
	}

// --------------------------------------------------------------------
}



/* End of file Migration.php */
/* Location: ./system/libraries/Migration.php */
