<?php
/**
 * Data Store Abstract
 *
 * @package WP_Job_Manager_REST/Data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Job_Manager_REST_Data_Store_Abstract
 * An abstract Data_Store class that contains a model factory
 */
abstract class WP_Job_Manager_REST_Data_Store_Abstract implements WP_Job_Manager_REST_Interfaces_Data_Store {

	/**
	 * Definition
	 *
	 * @var WP_Job_Manager_REST_Model_Factory
	 */
	protected $model_factory;

	/**
	 * Type Serializers
	 *
	 * @var array
	 */
	private $type_serializers;

	/**
	 * WP_Job_Manager_REST_Data_Store_Abstract constructor.
	 *
	 * @param null|WP_Job_Manager_REST_Model_Factory $model_factory Def.
	 * @param array                 $args Args.
	 */
	public function __construct( $model_factory = null, $args = array() ) {
		$this->type_serializers = array();
		$this->args = $args;
		if ( is_a( $model_factory, 'WP_Job_Manager_REST_Model_Factory' ) ) {
			$this->set_model_factory( $model_factory );
		}
	}

	/**
	 * Set Definition
	 *
	 * @param WP_Job_Manager_REST_Model_Factory $factory Def.
	 *
	 * @return WP_Job_Manager_REST_Interfaces_Data_Store $this
	 */
	private function set_model_factory( $factory ) {
		$this->model_factory = $factory;
		$this->configure();
		return $this;
	}

	/**
	 * Configure
	 */
	protected function configure() {
	}

	/**
	 * Get Definition
	 *
	 * @return WP_Job_Manager_REST_Model_Factory
	 */
	public function get_model_factory() {
		return $this->model_factory;
	}
}
