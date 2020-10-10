<?php


namespace The7055inc\Shared\Misc\Geocoders;

/**
 * Class BaseGeocoder
 * @package The7055inc\Shared\Misc\Geocoders
 */
abstract class BaseGeocoder {

	/**
	 * Geocode specific address
	 * @param $address
	 *
	 * @return GeocoderResponse
	 */
	abstract public function geocode($address);

}