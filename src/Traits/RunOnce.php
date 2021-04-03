<?php


namespace The7055inc\Shared\Traits;

/**
 * Trait RunOnce
 * @package The7055inc\Shared\Traits
 */
trait RunOnce {
	/**
	 * Run something only once.
	 *
	 * @param $key
	 * @param $callback
	 */
	public function run_once( $key, $callback ) {
		$key = sprintf( '7055_run_once_%s', substr( md5( $key ), 0, 12));
		if ( false === wp_cache_get( $key ) ) {
			$callback();
			wp_cache_set( $key, '1' );
		}
	}
}