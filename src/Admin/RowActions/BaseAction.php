<?php


namespace The7055inc\Shared\Admin\RowActions;


abstract class BaseAction {

	protected $title;
	protected $description;
	protected $action;
	protected $cap;

	/**
	 * List of allowed post types;
	 * @var array
	 */
	protected $post_types;

	/**
	 * CopyEventAction constructor.
	 *
	 * @param $post_types
	 * @param  array  $params
	 */
	public function __construct( $post_types, $params = array() ) {
		if ( is_string( $post_types ) ) {
			$this->post_types = array( $post_types );
		} else {
			$this->post_types = $post_types;
		}
		$this->setup( $params );
	}

	/**
	 * Register row actions
	 */
	public function register() {
		add_filter( 'post_row_actions', array( $this, 'add_link' ), 10, 2 );
		add_action( 'admin_action_copy_post', array( $this, 'handle' ) );
		add_action( 'admin_notices', array( $this, 'print_notices' ), 99 );
	}

	/**
	 * Add Copy Link to the event rows
	 *
	 * @param  array string $actions
	 * @param  int  $id
	 *
	 * @return array $actions
	 */
	public function add_link( $actions, $id ) {

		global $post;

		$post_type_object = get_post_type_object( $post->post_type );

		if ( ! in_array( $post_type_object->name, $this->post_types ) ) {
			return $actions;
		}

		$actions[ $this->action ] = '<a href="' . $this->get_post_link( $post->ID )
		                            . '" title="'
		                            . esc_attr( $this->description )
		                            . '">' . $this->title . '</a>';

		return $actions;
	}


	/**
	 * Return link for copying post type
	 **
	 *
	 * @param  int  $id  , Default is 0
	 *
	 * @return string
	 */
	public function get_post_link( $id = 0 ) {

		if ( ! $post = get_post( $id ) ) {
			return null;
		}

		$action = null;
		$link   = admin_url( 'admin.php?post=' . $post->ID . '&action=' . $this->action );

		return apply_filters(
			'get_' . $this->action . '_post_link',
			wp_nonce_url( $link, "$action-{$post->post_type}_{$post->ID}" ),
			$post->ID
		);
	}

	/**
	 * Setup the post params
	 *
	 * @param  array  $params
	 *
	 * @return void
	 */
	public function setup( $params = array() ) {
		$this->action      = 'copy_post';
		$this->title       = __( 'Copy', '7055inc' );
		$this->description = __( 'Copy post to new draft', '7055inc' );

		if ( isset( $params['title'] ) && ! empty( $params['title'] ) ) {
			$this->title = $params['title'];
		}
		if ( isset( $params['description'] ) && ! empty( $params['description'] ) ) {
			$this->description = $params['description'];
		}
	}

	/**
	 * Handle the action
	 * @return mixed
	 */
	abstract public function handle();

	/**
	 * Prints the notices related to this action
	 *
	 * @return void
	 */
	abstract public function print_notices();
}
