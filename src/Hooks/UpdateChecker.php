<?php

namespace The7055inc\Shared\Hooks;

use The7055inc\Shared\Traits\RunOnce;

class UpdateChecker {

	use RunOnce;

	/**
	 * UpdateChecker constructor.
	 */
	public function __construct() {
		$this->run_once( '7055-update-checker', function () {
			add_action( 'wp_ajax_7055_update_check', array( $this, 'check_for_updates' ) );
			add_action( 'wp_ajax_nopriv_7055_update_check', array( $this, 'check_for_updates' ) );

			add_action( 'wp_ajax_7055_download_update', array( $this, 'download_update' ) );
			add_action( 'wp_ajax_nopriv_7055_download_update', array( $this, 'download_update' ) );

			add_action( 'wp_ajax_7055_update_check_key', array( $this, 'set_api_key' ) );
		} );
	}


	/**
	 * Check for updates
	 */
	public function check_for_updates() {

		// Gather plugin and release details.
		$plugins = $this->get_plugins();
		$plugin  = isset( $_GET['plugin'] ) ? $_GET['plugin'] : '';
		$release = $this->request( 'GET', 'repos/7055-inc/' . $plugin . '/releases/latest' );
		$details = isset( $plugins[ $plugin ] ) ? $plugins[ $plugin ] : array();
		if ( empty( $details ) ) {
			$this->abort();
		}

		// Try to get info. If release is available.
		if ( ! is_wp_error( $release ) ) {
			$data      = json_decode( $release, true );
			$asset_url = isset( $data['assets'][0]['browser_download_url'] ) ? $data['assets'][0]['browser_download_url'] : '';
			$asset_id  = isset( $data['assets'][0]['id'] ) ? $data['assets'][0]['id'] : '';

			if ( ! empty( $asset_id ) && strpos( $asset_url, 'release.zip' ) !== false ) {
				die( json_encode( array(
					'name'         => $details['name'],
					'version'      => $data['tag_name'],
					'download_url' => add_query_arg( array(
						'plugin' => $plugin,
						'action' => '7055_download_update'
					), admin_url( 'admin-ajax.php' ) ),
					'sections'     => $details['sections'],
				), JSON_PRETTY_PRINT ) );
			}
		}

		// Send 404 reply if release is not found.
		$this->abort();
	}


	/**
	 * Download update
	 */
	public function download_update() {

		set_time_limit( 600 );
		wp_raise_memory_limit( 'image' );

		// Gather plugin and release details.
		$plugins = $this->get_plugins();
		$plugin  = isset( $_GET['plugin'] ) ? $_GET['plugin'] : '';
		$details = isset( $plugins[ $plugin ] ) ? $plugins[ $plugin ] : array();
		$release = $release = $this->request( 'GET', 'repos/7055-inc/' . $plugin . '/releases/latest' );
		if ( empty( $details ) ) {
			$this->abort();
		}

		// Try to output the file.
		if ( ! is_wp_error( $release ) ) {
			$data      = json_decode( $release, true );
			$asset_url = isset( $data['assets'][0]['browser_download_url'] ) ? $data['assets'][0]['browser_download_url'] : '';
			$asset_id  = isset( $data['assets'][0]['id'] ) ? $data['assets'][0]['id'] : '';
			if ( ! empty( $asset_id ) && strpos( $asset_url, 'release.zip' ) !== false ) {

				// Obtain plugin contents
				$endpoint = 'repos/7055-inc/' . $plugin . '/releases/assets/' . $asset_id;
				$headers  = [ 'Accept' => 'application/octet-stream' ];
				$contents = $this->request( 'GET', $endpoint, [], $headers );

				if ( empty( $contents ) ) {
					$this->abort();
				}

				$file_name = sprintf( 'release-%s.zip', $data['tag_name'] );

				// Create tmp file
				$tmp_file = tmpfile();
				$tmp_path = stream_get_meta_data( $tmp_file )['uri'];
				if ( ! is_writable( dirname( $tmp_path ) ) ) {
					$this->abort();
				}

				// Put plugin contents in tmp file.
				file_put_contents( $tmp_path, $contents );

				// Stream file
				header( "Content-Description: File Transfer" );
				header( "Content-Type: application/zip" );
				header( "Content-Length: " . filesize( $tmp_path ) );
				header( "Content-Disposition: attachment; filename=\"" . $file_name . "\"" );
				readfile( $tmp_path );
				exit();
			}
		}

		// Send 404 response if unsuccessful.
		$this->abort();

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
			wp_die( 'Updated.' );
		}
	}

	/**
	 * Return plugins
	 * @return array[]
	 */
	protected function get_plugins() {
		return array(
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
	}

	/**
	 * Abort update check
	 * @return void
	 */
	protected function abort() {
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
	 * @param array $headers
	 *
	 * @return array|\WP_Error
	 */
	protected function request( $type, $endpoint, $data = array(), $headers = array() ) {

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

		if ( ! empty( $headers ) ) {
			$args['headers'] = array_merge( $args['headers'], $headers );
		}

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
			return $response['body'];
		}
	}
}