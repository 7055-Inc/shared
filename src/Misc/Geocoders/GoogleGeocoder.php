<?php


namespace The7055inc\Shared\Misc\Geocoders;

/**
 * Class GoogleGeocoder
 * @package The7055inc\Shared\Misc\Geocoders
 */
class GoogleGeocoder extends BaseGeocoder {

	protected $api_key;

	/**
	 * GoogleGeocoder constructor.
	 *
	 * @param $api_key
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Geocode specific address
	 *
	 * @param $address
	 *
	 * @return mixed
	 */
	/**
	 * Returns latitude and longitude of the address
	 *
	 * @param $address
	 *
	 * @return GeocoderResponse
	 */
	public function geocode( $address ) {

		$gresponse = new GeocoderResponse();
		$gresponse->provider = 'google';

		$addr = urlencode( $address );
		$url  = "https://maps.google.com/maps/api/geocode/json?key=" . $this->api_key . "&address=" . $addr;

		$response = \wp_remote_get( $url, array(
			'timeout'    => 60,
			'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36'
		) );

		if ( is_wp_error( $response ) ) {
			$gresponse->success = false;
			$gresponse->errors  = array( $response->get_error_message() );
			return $gresponse;
		} else {
			$data = $response['body'];
			$data = json_decode( $data );
			if ( $data->status == "OK" ) {
				$gresponse->success   = true;
				$gresponse->latitude  = $data->results[0]->geometry->location->lat;
				$gresponse->longitude = $data->results[0]->geometry->location->lng;
			} else  {
				$gresponse->success = false;
				$gresponse->errors  = array();
				if(isset($data->error_message)) {
					array_push($gresponse->errors, $data->error_message);
				}
			}
		}

		return $gresponse;
	}
}