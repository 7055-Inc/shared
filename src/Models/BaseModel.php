<?php


namespace The7055inc\Shared\Models;


use The7055inc\Shared\Misc\Util;

/**
 * Class BaseModel
 * @package The7055inc\Marketplace\Models
 */
class BaseModel {

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
	 *
	 * @param $property
	 * @param string $targetFormat
	 * @param string $sourceFormat
	 *
	 * @return mixed|null
	 */
	public function format_datetime( $property, $sourceFormat = 'Y-m-d H:i:s', $targetFormat = 'sysdefault' ) {

		if ( isset( $this->$property ) ) {
			$timezone = $this->get_timezone();

			if ( $targetFormat === 'sysdefault' ) {
				$formatting   = Util::get_default_datetime_format();
				$targetFormat = $formatting['php'];
			}

			return Util::convert_date( $this->$property, $sourceFormat, $targetFormat, 'UTC', $timezone );
		}

		return null;
	}

	/**
	 * Obtain the current timezone
	 *
	 * @return \DateTimeZone|null
	 */
	private function get_timezone() {
		return Util::get_timezone();
	}

	/**
	 * Convert to object
	 * @return array
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

}