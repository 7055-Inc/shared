<?php

namespace The7055inc\Shared\Models;

use The7055inc\Shared\Traits\ObjectTime;

/**
 * Class BaseModel
 * @package The7055inc\Marketplace\Models
 */
class BaseModel {

	use ObjectTime;

	/**
	 * Construct from array
	 *
	 * @param $params
	 *
	 * @return $this
	 */
	public static function from_array( $params ) {
		$instance = new static();
		foreach ( $params as $key => $value ) {
			$instance->$key = $value;
		}

		return $instance;
	}

	/**
	 * Convert to object
	 * @return array
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

    /**
     * Returns edit url.
     * @return string
     */
    public function get_edit_url() {
        _doing_it_wrong('get_edit_url() needs implementation.');
        return '';
    }

}