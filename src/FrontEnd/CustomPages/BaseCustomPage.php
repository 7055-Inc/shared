<?php

namespace The7055inc\Shared\FrontEnd\CustomPages;

use The7055inc\Shared\Misc\Request;

/**
 * Class BaseCustomPage
 * @package The7055inc\Shared\FrontEnd\CustomPages
 */
abstract class BaseCustomPage {

	/**
	 * The custom page identifier
	 * @var string
	 */
	protected $id = '';

	/**
	 * Rewrite rule regex
	 * @var string
	 */
	protected $regex;

	/**
	 * Rewrite rule query
	 * @var string
	 */
	protected $query;

	/**
	 * Rewrite rule after
	 * @var string
	 */
	protected $after = 'top';

	/**
	 * Query vars
	 * @var array
	 */
	protected $query_vars;

	/**
	 * Is flushed? Should we re-flush?
	 * @var bool
	 */
	protected $flushed;

	/**
	 * The current request
	 * @var Request
	 */
	protected $request;

	/**
	 * BaseCustomPage constructor.
	 */
	public function register() {

		$this->request = new Request();
		$this->flushed = (int) get_option('mpl_'.$this->id.'_custom_page_flushed');
		add_filter( 'query_vars', array( $this, 'register_query_vars' ), 0, 1 );
		add_action('template_redirect', array($this, 'render'));
		add_action('init', array($this, 'register_rewrite_rule'));
	}

	/**
	 * Render page
	 * @return mixed
	 */
	abstract public function render();

	/**
	 * Returns query var
	 *
	 * @param $var
	 *
	 * @return mixed
	 */
	public function get_query_var($var) {
		return \get_query_var($var);
	}

	/**
	 * Register query vars
	 *
	 * @param $vars
	 *
	 * @return mixed
	 */
	public function register_query_vars($vars) {
		if(is_array($this->query_vars)) {
			$vars = array_merge($vars, $this->query_vars);
		}
		return $vars;
	}

	/**
	 * Register rewrite rule
	 *
	 * @return void
	 */
	public function register_rewrite_rule() {

		if(emptY($this->regex)) {
			error_log('Custom Page Register Rewrite Rule: Regex should not be empty.');
			return;
		}

		if(emptY($this->query)) {
			error_log('Custom Page Register Rewrite Rule: Query should not be empty.');
			return;
		}

		add_rewrite_rule( $this->regex, $this->query, $this->after );

		// Hard flush, if needed.
		if ( !$this->flushed ) {
			flush_rewrite_rules( true );
			update_option( 'mpl_'.$this->id.'_custom_page_flushed', 1 );
			$this->flushed = 1;
		}
	}
}