<?php

/**
 * Plugin Name:       7055 Inc - Shared
 * Plugin URI:        7055inc.com
 * Description:       Shared functionality used Marketplace and Events plugins
 * Version:           1.0.0
 * Author:            Darko Gjorgjijoski
 * Text Domain:       7055inc_shared
 * Domain Path:       /languages
 */

if ( ! defined('WPINC')) {
	exit;
}

if (file_exists(EV_PATH.'vendor/autoload.php')) {
	require_once EV_PATH.'vendor/autoload.php';

	new \The7055inc\Shared\Bootstrap();

} else {
	wp_die('It looks like you are using development version. Please run composer install to install the composer dependencies.');
}