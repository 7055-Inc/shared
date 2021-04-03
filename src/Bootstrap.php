<?php

namespace The7055inc\Shared;

use The7055inc\Shared\Hooks\UpdateChecker;

class Bootstrap {

	/**
	 * Bootstrap constructor.
	 */
	public function __construct() {
		require_once dirname( dirname( __FILE__ ) ) . '/lib/plugin-update-checker/plugin-update-checker.php';
		new UpdateChecker();
	}
}