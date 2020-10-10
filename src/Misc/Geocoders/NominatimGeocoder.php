<?php


namespace The7055inc\Shared\Misc\Geocoders;


class NominatimGeocoder extends BaseGeocoder {

	/**
	 * Geocode specific address
	 *
	 * @param $address
	 *
	 * @return GeocoderResponse
	 */
	public function geocode( $address ) {
		// url encode the address
		$address = urlencode( $address );
		$url     = "http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q={$address}&format=json&limit=1";

		$response = \wp_remote_get( $url, array(
			'timeout'    => 60,
			'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36'
		) );

		$gresponse = new GeocoderResponse();
		$gresponse->provider = 'nominatim';

		if ( \is_wp_error( $response ) ) {
			$gresponse->success = false;
			$gresponse->errors  = array( $response->get_error_message() );

			return $gresponse;
		} else {
			$result = json_decode( $response['body'], true );

			if ( isset( $result[0]['lat'] ) && ! empty( $result[0]['lat'] ) && isset( $result[0]['lon'] ) && ! empty( $result[0]['lon'] ) ) {
				$gresponse->success   = true;
				$gresponse->latitude  = (double) $result[0]['lat'];
				$gresponse->longitude = (double) $result[0]['lon'];
			} else {
				$gresponse->success = false;
				$gresponse->errors  = array( 'Unable to geocode address.' );
			}
		}

		return $gresponse;
	}
}