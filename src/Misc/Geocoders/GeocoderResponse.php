<?php


namespace The7055inc\Shared\Misc\Geocoders;


class GeocoderResponse {
	public $success = false;
	public $latitude = null;
	public $longitude = null;
	public $provider = null;
	public $errors = array();
}