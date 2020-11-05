<?php


namespace The7055inc\Shared\Misc;


use Carbon\Carbon as parentAlias;

/**
 * Class CarbonLocal
 * @package The7055inc\Shared\Misc
 */
class CarbonLocal extends parentAlias {
	public function __construct( $time = null, $tz = null ) {
		parent::__construct( $time, $tz );

		$timezone = wp_timezone();

		if ( ! empty( $timezone ) ) {
			$this->setTimezone( $timezone );
		}
	}

	/**
	 * Create from format
	 *
	 * @param string $format
	 * @param string $time
	 * @param \DateTimeZone|null $timezone
	 *
	 * @return parentAlias|\DateTime|false|CarbonLocal|void
	 */
	public static function createFromFormat( $format, $time, $timezone = null ) {
		if ( is_null( $timezone ) ) {
			$timezone = wp_timezone();
		}

		return parent::createFromFormat( $format, $time, $timezone );
	}
}