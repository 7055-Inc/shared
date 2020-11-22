<?php

namespace The7055inc\Shared\Traits;

use The7055inc\Shared\Misc\Util;

/**
 * Trait ObjectTime
 * @package The7055inc\Shared\Traits
 */
trait ObjectTime {

	/**
	 * Specific time format
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
	 * Human readable time format
	 *
	 * @param $property
	 * @param string $sourceFormat
	 *
	 * @return string
	 */
	public function format_datetime_for_humans( $property, $sourceFormat = 'Y-m-d H:i:s' ) {
		if ( isset( $this->$property ) ) {
			$timezone = $this->get_timezone();
			$instance = Util::create_datetime( $this->$property, $sourceFormat );
			$instance->setTimezone( $timezone );

			return $instance->diffForHumans();
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
}