<?php

namespace The7055inc\Shared\Hooks;

use The7055inc\Shared\Misc\Util;

/**
 * Class BaseScripts
 * @package The7055inc\Shared\Hooks
 */
class BaseScripts {

	/**
	 * Enqueue Script
	 *
	 * @param $handle
	 * @param string $src
	 * @param array $deps
	 * @param false $ver
	 * @param false $in_footer
	 */
	protected function enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
	}

	/**
	 * Enqueue Script
	 *
	 * @param $handle
	 * @param string $src
	 * @param array $deps
	 * @param false $ver
	 * @param false $in_footer
	 */
	protected function register_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		wp_register_script( $handle, $src, $deps, $ver, $in_footer );
	}


	/**
	 * Enqueue style
	 *
	 * @param $handle
	 * @param string $src
	 * @param array $deps
	 * @param false $ver
	 * @param string $media
	 */
	protected function enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Enqueue style
	 *
	 * @param $handle
	 * @param string $src
	 * @param array $deps
	 * @param false $ver
	 * @param string $media
	 */
	protected function register_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		wp_register_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Localize script once
	 *
	 * @param $handle
	 * @param $object_name
	 * @param $i18n
	 */
	protected function localize_script( $handle, $object_name, $i18n ) {
		$key       = md5( md5( $object_name ) . md5( serialize( $i18n ) ) );
		$cache_key = substr( $key, 0, 12 );
		//$cache_key = sprintf( 'script_' . $handle );
		if ( false === wp_cache_get( $cache_key ) ) {
			wp_localize_script( $handle, $object_name, $i18n );
			wp_cache_set( $cache_key, 'localized' );
		}
	}

	/**
	 * Default datetime format
	 *
	 * @param string $type
	 *
	 * @return string|null
	 */
	protected function get_datetime_format( $type = 'js' ) {
		$format = Util::get_default_datetime_format();
		if ( isset( $format[ $type ] ) ) {
			return $format[ $type ];
		} else {
			return null;
		}
	}

	/**
	 * Returns the date format
	 *
	 * @param string $type
	 *
	 * @return mixed|null
	 */
	protected function get_date_format( $type = 'js' ) {
		$format = Util::get_default_date_format();
		if ( isset( $format[ $type ] ) ) {
			return $format[ $type ];
		} else {
			return null;
		}
	}

}