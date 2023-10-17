<?php

namespace The7055inc\Shared\Traits;

/**
 * Trait Ajaxable
 * @package The7055inc\Shared\Traits
 */
trait Ajaxable {

	/**
	 * The slug identifier
	 * @var string
	 */
	protected $slug = '';

	/**
	 * The ajax endpoint prefix
	 * @var string
	 */
	protected $prefix = '7055_';

	/**
	 * Add ajax endpoints
	 * @var array
	 */
	protected $ajax_endpoints = array();

	/**
	 * The nonce key
	 * @var string
	 */
	protected $nonce = 'mpl_';

	/**
	 * Register ajax endpoints
	 */
	protected function register_ajax_endpoints() {
		if (count($this->ajax_endpoints)) {
			foreach ($this->ajax_endpoints as $ajax_endpoint) {

				$callback = [];
				if(is_string($ajax_endpoint['callback']) && method_exists($this, $ajax_endpoint['callback'])) {
					$callback = [$this, $ajax_endpoint['callback']];
				} else if(is_callable($ajax_endpoint['callback'])) {
					$callback = $ajax_endpoint['callback'];
				}

				add_action('wp_ajax_'.$ajax_endpoint['key'], $callback);
				if ( ! $ajax_endpoint['is_private']) {
					add_action('wp_ajax_nopriv_'.$ajax_endpoint['key'], $callback);
				}
			}
		}
	}

	/**
	 * Add ajax endpoint
	 *
	 * @param $name
	 * @param $callback
	 * @param  bool  $is_private
	 */
	protected function define_ajax_endpoint($name, $callback, $is_private = true)
	{
		$this->ajax_endpoints[$name] = array(
			'key'        => $this->prefix . str_replace('-', '_', $this->slug . '_' .$name),
			'callback'   => $callback,
			'is_private' => $is_private,
		);
	}

	/**
	 * Check the ajax referrer
	 */
	protected function check_ajax_referrer()
	{
		return \check_ajax_referer($this->nonce, '_nonce', false);
	}

	/**
	 * Standardized way to respond
	 *
	 * @param $success
	 * @param $data
	 */
	protected function response($success, $data = array())
	{
		if ($success) {
			wp_send_json_success($data);
		} else {
			wp_send_json_error($data);
		}
		die;
	}

}