<?php


namespace The7055inc\Shared\Admin\RowActions;


use The7055inc\Shared\Misc\Util;

class CopyPostAction extends BaseAction {

	/**
	 * Perform the copy action
	 */
	public function handle() {
		if ( ! ( isset( $_GET[ 'post' ] ) || ( isset( $_REQUEST[ 'action' ] ) && $this->action == $_REQUEST[ 'action' ] ) )  ) {
			wp_die( esc_attr__( 'No post has been supplied!', '7055inc-shared' ) );
		}

		$id = (int) ( isset( $_GET[ 'post' ] ) ? $_GET[ 'post' ] : $_REQUEST[ 'post' ] );

		if($id) {
			$new_post_ID = Util::duplicate_post($id);

			if($new_post_ID) {

				$url = get_edit_post_link($new_post_ID);
				$url = add_query_arg('copied', '1', $url);

				wp_redirect($url);
				die;
			}
		}

		wp_die(__('Unable to copy this post.'));

	}
}