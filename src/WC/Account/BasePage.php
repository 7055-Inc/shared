<?php


namespace The7055inc\Shared\WC\Account;

use The7055inc\Shared\Traits\Ajaxable;

use The7055inc\Shared\Misc\Request;
use The7055inc\Shared\Misc\SessionFlash;
use The7055inc\Shared\Misc\Util;

/**
 * Class BasePage
 * @package The7055inc\Shared\WC\Account
 */
abstract class BasePage {
	use Ajaxable;

	/**
	 * The flash param
	 * @const
	 */
	const FLASH_PARAM = '_lp';

	/**
	 * Tab slug
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Tab name
	 * @var string
	 */
	protected $name = '';

	/**
	 * Tab url
	 * @var string
	 */
	protected $url = null;

	/**
	 * The current request
	 * @var Request
	 */
	protected $request;

	/**
	 * Restrict to certain roles only. Leave blank for all roles.
	 *
	 * @var array
	 */
	protected $roles = array();


	public function __construct() {
		$this->request = new Request();
	}

	/**
	 * The account menu items
	 *
	 * @param $items
	 *
	 * @return mixed
	 */
	public function account_menu_items( $items ) {

		if ( ! $this->can_access() ) {
			return $items;
		}

		$items[ $this->slug ] = $this->name;

		return $items;
	}

	/**
	 * The rewrite endpoint
	 */
	public function add_rewrite_endpoint() {
		add_rewrite_endpoint( $this->slug, EP_ROOT | EP_PAGES );
	}


	/**
	 * The query vars
	 *
	 * @param $vars
	 *
	 * @return mixed
	 */
	public function query_vars( $vars ) {
		array_push( $vars, $this->slug );

		return $vars;
	}


	/**
	 * Register the tab
	 */
	public function register() {
		// Validate slug
		if ( empty( $this->slug ) ) {
			error_log( __( 'Unable to register my account tab. Empty slug.' ) );
		}

		// Register the required actions and ajax endpoints.
		add_action( 'woocommerce_account_menu_items', array( $this, 'account_menu_items' ) );
		add_action( 'init', array( $this, 'add_rewrite_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'woocommerce_account_' . $this->slug . '_endpoint', array( $this, '_content' ) );

		$this->register_ajax_endpoints();
	}

	/**
	 * The page content
	 */
	public function _content() {

		if ( ! $this->can_access() ) {
			echo '<p>' . __( 'Permission denied.' ) . '</p>';

			return;
		}

		$this->content();
	}

	/**
	 * The tab content
	 *
	 * @return void
	 */
	abstract public function content();

	/**
	 * Is supage?
	 *
	 * @param $subpage
	 *
	 * @return bool
	 */
	protected function is_subpage_query( $subpage ) {
		return isset( $_GET['context'] ) && $_GET['context'] === $subpage;
	}

	/**
	 * Return the query id
	 * @return mixed|null
	 */
	protected function get_query_id() {
		return isset( $_GET['id'] ) ? $_GET['id'] : null;
	}

	/**
	 * Render view
	 *
	 * @param $view
	 * @param array $data
	 */
	abstract function render( $view, $data = array() );


	/**
	 * Send response accepted by datatables.
	 *
	 * @param int $draw
	 * @param int $recordsTotal
	 * @param int $recordsFiltered
	 * @param array $data
	 * @param string $error
	 */
	protected function response_json_datatables( $draw, $recordsTotal, $recordsFiltered, $data, $error = '' ) {
		$response = array(
			'draw'            => $draw,
			'recordsTotal'    => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data'            => $data
		);
		if ( ! empty( $error ) ) {
			$response['error'] = $error;
		}
		echo json_encode( $response );
		die;
	}

	/**
	 * Standardized ajax way to flash messages with redirect
	 *
	 * @param $success
	 * @param $url
	 * @param $message
	 * @param array $errors
	 */
	protected function response_redirect_flashed( $success, $url, $message, $errors = array() ) {
		$flash_key = uniqid( '_' );
		SessionFlash::make( $flash_key, $success, $message, $errors );
		$url = add_query_arg( self::FLASH_PARAM, $flash_key, $url );
		$this->response( $success, array(
			'redirect' => $url,
		) );
	}

	/**
	 * Returns the root url
	 */
	protected function set_root_url() {

		if ( ! is_null( $this->url ) ) {
			return $this->url;
		}

		if ( function_exists( '\wc_get_account_endpoint_url' ) ) {
			$this->url = \wc_get_account_endpoint_url( $this->slug );
		} else {
			$this->url = null;
		}

		return $this->url;
	}

	/**
	 * List of allowed actions
	 * @return array
	 */
	public static function actions() {
		return array();
	}


	/**
	 * Can write to object?
	 *
	 * @param $object_author_id
	 *
	 * @return bool
	 */
	protected function can_write( $object_author_id ) {
		$current_user_id = $this->request->get_current_user_id();

		if ( ! $current_user_id ) {
			return false;
		}

		return intval( $object_author_id ) == intval( $current_user_id );
	}


	/**
	 * Check if the current user can access this page
	 * @return bool
	 */
	protected function can_access() {

		$allow = false;
		if ( ! empty( $this->roles ) ) {
			foreach ( $this->roles as $role ) {
				if ( Util::is_current_user_in_role( $role ) ) {
					$allow = true;
				}
			}
		} else {
			$allow = true;
		}

		return $allow;

	}


	/**
	 * Render actions
	 *
	 * @param $item_id
	 * @param array $actions
	 *
	 * @return string
	 */
	public function render_actions( $item_id, $actions = array() ) {
		if ( empty( $actions ) ) {
			$actions = static::actions();
		}
		$actions_html = '';
		if ( count( $actions ) > 0 ) {
			$actions_html    = '<ul class="mpl-table-actions">';
			$base_action_url = add_query_arg( 'id', $item_id, $this->url );
			foreach ( $actions as $action_slug => $action ) {
				$action_url   = add_query_arg( 'context', $action_slug, $base_action_url );
				$action_name  = $action['name'];
				$action_title = $action['title'];
				$is_modal     = isset( $action['modal'] ) ? (bool) $action['modal'] : '';
				$endpoint     = isset( $action['endpoint'] ) && $this->ajax_endpoints[ $action['endpoint'] ]['key'] ? $this->ajax_endpoints[ $action['endpoint'] ]['key'] : '';
				if ( ! empty( $endpoint ) ) {
					$action_url = add_query_arg( array(
						'id'       => isset( $item_id ) ? $item_id : null,
						'action'   => $endpoint,
						'_wpnonce' => wp_create_nonce( $this->nonce ),
					), admin_url( 'admin-ajax.php' ) );
				}
				$action_icon  = isset( $action['icon'] ) ? $action['icon'] : '';
				$action_icon  = sprintf( '<span class="mpl-icon-%s"></span>', $action_icon );
				$actions_html .= sprintf( '<li><a %s title="%s" data-id="%s" class="%s" href="%s">%s%s</a></li>', ($is_modal ? 'rel="modal:open"' : ''), $action_title, $item_id, ( 'mpl-action-' . $action_slug ), $action_url, $action_icon, $action_name );

			}
			$actions_html .= '</ul>';
		}

		return $actions_html;
	}
}