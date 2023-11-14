<?php

namespace The7055inc\Shared\Hooks;

use The7055inc\Shared\Traits\SharedHook;

class MediaLibraryPermissions {

	use SharedHook;

	/**
	 * Filters constructor.
	 */
	public function __construct() {

		$this->add( array(
			'name'     => 'ajax_query_attachments_args',
			'callable' => 'my_account_show_current_user_attachments',
			'priority' => 10,
			'args'     => 1,
			'type'     => 'filter'
		) );

		$this->add( array(
			'name'     => 'media_view_strings',
			'callable' => 'my_account_disable_media_library_popup_menu_items',
			'priority' => 10,
			'args'     => 1,
			'type'     => 'filter',
		) );

		$this->add( array(
			'name' => 'woocommerce_prevent_admin_access',
			'callable' => 'allow_async_upload_access',
			'priority' => '10',
			'args' => 1,
			'type' => 'filter',
		));

		$this->register();

	}

	/**
	 * Allow async uploads from the front-end for WooCommerce 7.8+
	 *
	 * @param $prevent_access
	 *
	 * @return false|mixed
	 */
	public function allow_async_upload_access( $prevent_access ) {
		$script = isset( $_SERVER['SCRIPT_FILENAME'] ) ? basename( sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_FILENAME'] ) ) ) : '';
		if ( 'async-upload.php' === $script ) {
			$prevent_access = false;
		}

		return $prevent_access;
	}

	/**
	 * Show only the current user uploads in the my account pages.
	 *
	 * @param array $query
	 *
	 * @return array|mixed
	 */
	public function my_account_show_current_user_attachments( $query ) {

		$current_user = wp_get_current_user();

		if ( ! $current_user ) {
			return $query;
		}

		if ( current_user_can( 'administrator' ) ) {
			return $query;
		}

		$current_user_id = $current_user->ID;

		$query['author__in'] = array(
			$current_user_id
		);

		return $query;

	}

	/**
	 * Disables various menu items in media library popup in the front-end.
	 *
	 * @param $strings
	 *
	 * @return mixed
	 */
	public function my_account_disable_media_library_popup_menu_items( $strings ) {

		if ( is_admin() ) {
			return $strings;
		}

		$list = array(
			'insertFromUrlTitle',
			'createGalleryTitle',
			'createPlaylistTitle',
			'createVideoPlaylistTitle',
		);
		foreach ( $list as $key ) {
			if ( isset( $strings[ $key ] ) ) {
				unset( $strings[ $key ] );
			}
		}

		return $strings;
	}


}