<?php


namespace The7055inc\Shared\Admin\RowActions;


use The7055inc\Shared\Misc\Util;


class CopyPostAction extends BaseAction {

	protected $notice_success;
	protected $notice_error;

	/**
	 * Constructor
	 *
	 * @param $post_types
	 * @param $params
	 */
	public function __construct( $post_types, $params = array() ) {

		$this->notice_success = __( 'Success! The post was duplicated successfully.', '7055inc' );
		$this->notice_error   = __( 'Irks! An error has occurred.', '7055inc' );

		parent::__construct( $post_types, $params );

	}

	/**
	 * Perform the copy action
	 */
	public function handle() {
		if ( ! ( isset( $_GET['post'] ) || ( isset( $_REQUEST['action'] ) && $this->action == $_REQUEST['action'] ) ) ) {
			wp_die( esc_attr__( 'No post has been supplied!', '7055inc-shared' ) );
		}

		$id = (int) ( isset( $_GET['post'] ) ? $_GET['post'] : $_REQUEST['post'] );

		if ( $id ) {
			$new_post_ID = Util::duplicate_post( $id );

			$post_type = get_post_type( $new_post_ID );
			$new_link  = admin_url( 'edit.php?post_type=' . $post_type );

			if ( $new_post_ID ) {
				$new_link = add_query_arg( [ 'dup_from' => $new_post_ID, 'is_success' => 1 ], $new_link );
			} else {
				$new_link = add_query_arg( [ 'dup_from' => $new_post_ID, 'is_success' => 0 ], $new_link );
			}

			$this->before_redirect( $new_post_ID );
			wp_redirect( $new_link );
			die;
		}

		wp_die( __( 'Unable to copy this post.' ) );

	}

	/**
	 * Handles post action before redirecting away.
	 *
	 * @param $new_post_id
	 *
	 * @return void
	 */
	protected function before_redirect( $new_post_id ) {
	}

	/**
	 * Prints the notices related to this action
	 *
	 * @return void
	 */
	public function print_notices() {
		if ( isset( $_REQUEST['dup_from'] ) ) {
			if ( isset( $_REQUEST['is_success'] ) ) {
				if ( $_REQUEST['is_success'] ) {
					printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-success', esc_html( $this->notice_success ) );
				} else {
					printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', esc_html( $this->notice_error ) );
				}
			}
		}
	}
}
