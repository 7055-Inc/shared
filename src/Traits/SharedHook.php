<?php


namespace The7055inc\Shared\Traits;

/**
 * Trait SharedHook
 * @package The7055inc\Events\Misc
 */
trait SharedHook {

	/**
	 * Cache prefix
	 * @var string
	 */
	protected $cache_prefix = '7055';

	/**
	 * Cache group
	 * @var string
	 */
	protected $cache_group = '7055';

	/**
	 * List of hooks
	 * @var array
	 */
	protected $hooks = array();

	/**
	 * Utility to add hook
	 * @param $hook
	 */
	public function add($hook) {
		array_push($this->hooks, $hook);
	}

	/**
	 * Register hooks while preventing duplicate registrations
	 */
	public function register() {
		foreach ( $this->hooks as $hook ) {
			$tag = $this->cache_prefix . '_' . md5( $hook['name'] . '_' . $hook['callable'] );
			if ( false === wp_cache_get( $tag, $this->cache_group ) ) {
				$hook_method = $hook === 'filter' ? '\add_filter' : '\add_action';
				$hook_method( $hook['name'], array( $this, $hook['callable'] ), $hook['priority'], $hook['args'] );
				wp_cache_set( $tag, true, $this->cache_group );
			}
		}
	}

}