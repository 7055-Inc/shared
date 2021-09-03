<?php

namespace The7055inc\Shared\Admin\Metaboxes;

abstract class Base {
	protected $id;
	protected $title;
	protected $post_types = array();
	protected $context = 'advanced';
	protected $priority = 'high';
	protected $capability = 'edit_post';

	public function register() {
		add_action( 'add_meta_boxes', array( $this, 'add' ), 15, 1 );
		add_action( 'save_post', array( $this, 'save' ), 15, 2 );
	}

	/**
	 * Add metabox
	 *
	 * @param $post_type
	 */
	public function add( $post_type ) {
		if ( empty( $this->id ) ) {
			return;
		}

		if ( ! in_array( $post_type, $this->post_types ) ) {
			return;
		}

		add_meta_box(
			$this->id,
			$this->title,
			array( $this, 'render' ),
			$post_type,
			$this->context,
			$this->priority
		);

	}

	/**
	 * Save metabox
	 *
	 * @param  int  $post_id
	 * @param  \WP_Post  $post
	 *
	 * @return mixed
	 */
	public function save( $post_id, $post ) {

		// Add nonce for security and authentication.
		$key          = $this->get_nonce_key();
		$nonce_action = $key . '_a';
		$nonce_name   = $key . '_n';
		$nonce_value  = isset( $_POST[ $nonce_name ] ) ? $_POST[ $nonce_name ] : '';


		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_value, $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( $this->capability, $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$this->save_meta_box( $post_id, $post );

	}


	/**
	 * Render metabox
	 *
	 * @param  \WP_Post  $post
	 */
	public function render( $post ) {
		$key      = $this->get_nonce_key();
		$n_action = $key . '_a';
		$n_name   = $key . '_n';
		wp_nonce_field( $n_action, $n_name );
		$this->render_meta_box( $post );
	}


	/**
	 * Render metabox
	 *
	 * @param  \WP_Post  $post
	 *
	 * @return mixed
	 */
	abstract public function render_meta_box( $post );

	/**
	 * Save metabox
	 *
	 * @param  int  $post_id
	 * @param  \WP_Post  $post
	 *
	 * @return mixed
	 */
	abstract public function save_meta_box( $post_id, $post );

	/**
	 * Generate unique nonce key
	 * @return string
	 */
	public function get_nonce_key() {
		$post_types = implode( ',', $this->post_types );

		return 'n_' . md5( $this->id . '_' . $post_types );
	}

}
