<?php

namespace The7055inc\Shared\FrontEnd\Shortcodes;

/**
 * Class BaseShortcode
 * @package The7055inc\Marketplace\FrontEnd\Shortcodes
 */
abstract class BaseShortcode {

	/**
	 * The shortcode tag
	 * @var string
	 */
	protected $tag;

	/**
	 * Render the shortcode
	 * @return string
	 */
	abstract public function do_shortcode();

	/**
	 * Register shortcode
	 */
	public function register() {
		if(empty($this->tag)) {
			return;
		}
		add_shortcode($this->tag, array($this, 'do_shortcode'));
	}

}