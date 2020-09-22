<?php


namespace The7055inc\Shared\Misc;


use Carbon\Carbon;
use DateTimeZone;

/**
 * Class CarbonLocal
 * @package The7055inc\Shared\Misc
 */
class CarbonLocal extends Carbon {

	/**
	 * CarbonLocal constructor.
	 *
	 * @param null $time
	 * @param null $tz
	 *
	 * @throws \Exception
	 */
	public function __construct( $time = null, $tz = null ) {
		if(is_null($tz)) {
			$tz = wp_timezone();
		}
		parent::__construct( $time, $tz );
		$headers = Request::_get_headers();
		if(isset($headers['Mpl-Borwser-Timezone-Offset'])) {
			$offset = abs($headers['Mpl-Borwser-Timezone-Offset']);
			$this->shiftTimezone(new DateTimeZone('UTC'));
			$this->addMinutes($offset);
		}
	}

	/**
	 * @param string $format
	 * @param string $time
	 * @param DateTimeZone|null $timezone
	 *
	 * @return Carbon|\DateTime|false|CarbonLocal|void
	 */
	public static function createFromFormat( $format, $time, $timezone = null ) {

		$headers = Request::_get_headers();
		if(isset($headers['Mpl-Borwser-Timezone-Offset'])) {
			$object = parent::createFromFormat( $format, $time, wp_timezone() );
			$offset = abs($headers['Mpl-Borwser-Timezone-Offset']);
			$object->shiftTimezone(new DateTimeZone('UTC'));
			$object->addMinutes($offset);
		} else {
			if(!is_null($timezone)) {
				$tz = $time;
			} else {
				$tz = wp_timezone();
			}
			$object = parent::createFromFormat( $format, $time, $tz );
		}
		return $object;
	}
}