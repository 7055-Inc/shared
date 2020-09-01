<?php


namespace The7055inc\Shared\Misc;


use Carbon\Carbon;

/**
 * Class CarbonLocal
 * @package The7055inc\Shared\Misc
 */
class CarbonLocal extends Carbon
{
    public function __construct($time = null, $tz = null)
    {
        parent::__construct($time, $tz);

        $timezone = wp_timezone();

        if ( ! empty($timezone)) {
            $this->setTimezone($timezone);
        }
    }
}