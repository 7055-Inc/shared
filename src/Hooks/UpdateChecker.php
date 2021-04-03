<?php

namespace The7055inc\Marketplace\Hooks;

use The7055inc\Shared\Traits\RunOnce;

class UpdateChecker {

	use RunOnce;

	/**
	 * UpdateChecker constructor.
	 */
	public function __construct() {
		$this->run_once( function () {
			add_action( 'wp_ajax_7055_update_check', array( $this, 'check_for_updates' ) );
			add_action( 'wp_ajax_nopriv_7055_update_check', array( $this, 'check_for_updates' ) );

			add_action( 'wp_ajax_7055_update_download', array( $this, 'update_download' ) );
			add_action( 'wp_ajax_nopriv_7055_update_download', array( $this, 'update_download' ) );

			add_action( 'wp_ajax_7055_update_check_key', array( $this, 'set_api_key' ) );
		} );
	}


	/**
	 * Check for updates
	 */
	public function check_for_updates() {

		$plugins = array(
			'marketplace' => array(
				'name'     => '7055 Inc - Multivendor Marketplace',
				'sections' => array(
					'description' => 'Properietary Multivendor Marketplace plugin for 7055 INC.',
				)
			),
			'events'      => array(
				'name'     => '7055 Inc - Multivendor Marketplace',
				'sections' => array(
					'description' => 'Properietary Events directory plugin for 7055 INC.',
				)
			)
		);


		$plugin  = isset( $_GET['plugin'] ) ? $_GET['plugin'] : '';
		$details = isset( $plugins[ $plugin ] ) ? $plugins[ $plugin ] : array();

		$release = $this->request( 'GET', 'repos/7055-inc/' . $plugin . '/releases/latest' );

		if ( ! is_wp_error( $release ) && ! empty( $details ) && isset( $release['assets'][0]['browser_download_url'] ) && strpos( $release['assets'][0]['browser_download_url'], 'release.zip' ) !== false ) {

			die( json_encode( array(
				'name'         => $details['name'],
				'version'      => $release['tag_name'],
				'download_url' => $release['assets'][0]['browser_download_url'],
				'sections'     => $details['sections'],
			), JSON_PRETTY_PRINT ) );


		} else {
			$this->abort_update_check();
		}

	}


	/**
	 * Download update
	 */
	public function update_download() {

		try {

			$plugin  = isset( $_GET['plugin'] ) ? $_GET['plugin'] : '';
			$details = isset( $plugins[ $plugin ] ) ? $plugins[ $plugin ] : array();
			$release = $release = $this->request( 'GET', 'repos/7055-inc/' . $plugin . '/releases/latest' );

			if ( ! empty( $details ) && isset( $release['assets'][0]['browser_download_url'] ) && strpos( $release['assets'][0]['browser_download_url'], 'release.zip' ) !== false ) {

				$url      = $release['assets'][0]['browser_download_url'];
				$contents = $release = $this->request( 'CONTENTS', $url );

				var_dump( $contents );
				die;

			} else {
				$this->abort_update_check();
			}

		} catch ( \Exception $e ) {
			error_log( '7055 Plugin Update Download Error: ' . $e->getMessage() );
			$this->abort_update_check();
		}

	}

	/**
	 * Stores Github API key
	 */
	public function set_api_key() {
		$key = isset( $_GET['key'] ) ? $_GET['key'] : '';
		if ( empty( $key ) ) {
			wp_die( 'No key provided.' );
		} else {
			update_option( '7055_updater_github_key', $_GET['key'] );
		}
	}

	/**
	 * Abort update check
	 * @return void
	 */
	protected function abort_update_check() {
		http_response_code( 404 );
		exit;
	}

	/**
	 * Returns Github API Key
	 */
	protected function get_token() {
		return get_option( '7055_updater_github_key' );
	}

	/**
	 * Perform GET request to Github api
	 *
	 * @param $type
	 * @param $endpoint
	 * @param array $data
	 *
	 * @return array|\WP_Error
	 */
	protected function request( $type, $endpoint, $data = array() ) {

		$type_lower = strtolower( $type );

		$normal_request = in_array( $type_lower, array( 'get', 'post' ) );

		if ( $normal_request ) {
			$url = sprintf( 'https://api.github.com/%s', ltrim( $endpoint, '/' ) );
		} else {
			$url = $endpoint;
		}

		$tok  = $this->get_token();
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $tok,
			),
		);

		if ( ! empty( $data ) ) {
			if ( $type_lower === 'post' ) {
				$args['body'] = $data;
			} else {
				$url = add_query_arg( $data, $url );
			}
		}

		if ( in_array( $type_lower, array( 'get', 'contents' ) ) ) {
			$response = wp_remote_get( $url, $args );
		} else {
			$response = wp_remote_post( $url, $args );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		} else {
			return $normal_request ? json_decode( $response['body'], true ) : $response['body'];
		}
	}
}