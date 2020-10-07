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

		$this->register();

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

}