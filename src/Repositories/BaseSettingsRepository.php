<?php

namespace The7055inc\Shared\Repositories;

/**
 * Class BaseSettingsRepository
 * @package The7055inc\Shared\Repositories
 */
class BaseSettingsRepository {

	/**
	 * Database key
	 * @var string
	 */
	protected $key = '';

	/**
	 * Settings
	 * @var array
	 */
	protected $settings;

	/**
	 * BaseSettingsRepository constructor.
	 */
	public function __construct() {
		$this->settings = (array) get_option( $this->key );
	}

	/**
	 * Set value
	 * @param $key
	 * @param $value
	 */
	public function set( $key, $value ) {
		$this->settings[ $key ] = $value;
	}

	/**
	 * Set all
	 * @param $values
	 */
	public function setAll( $values ) {
		if ( is_array( $values ) ) {
			$this->settings = $values;
		} else {
			$this->settings = array();
		}
	}

	/**
	 * Get value
	 * @param null $key
	 *
	 * @return array|mixed|null
	 */
	public function get( $key = null ) {
		if ( is_null( $key ) ) {
			return $this->settings;
		}

		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
	}

	/**
	 * Save value
	 */
	public function save() {
		update_option( $this->key, $this->settings );
	}

}