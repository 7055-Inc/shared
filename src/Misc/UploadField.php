<?php


namespace The7055inc\Shared\Misc;

/**
 * Class UploadField
 * @package The7055inc\Shared\Misc
 */
class UploadField {

	/**
	 * Custom image upload field
	 *
	 * @param $key
	 * @param $current_value
	 * @param $placeholder
	 */
	public static function the_field( $key, $current_value, $placeholder = '', $display_callback = null, $additional_params = [] ) {

		$additional_params = wp_parse_args( $additional_params, [
			'show-attachment-preview' => 1,
		] );

		$media_id = $current_value;
		if ( ! empty( $media_id ) && is_numeric( $media_id ) ) {
			$current_src = wp_get_attachment_image_src( $media_id, 'thumbnail' );
			$current_src = $current_src[0];
		} else {
			$current_src = $placeholder;
			$media_id    = '';
		}

        if(  (int) $additional_params['show-attachment-preview'] === 0 ) {
            $current_src = $placeholder;
        }

		$data_additional_params = '';
		foreach ( $additional_params as $param_key => $val ) {
			$data_additional_params .= sprintf( 'data-%s="%s"', $param_key, $val );
		}

		?>

        <div class="upload" <?php echo $data_additional_params; ?>>
			<?php if ( ! is_null( $display_callback ) && is_callable( $display_callback ) ): ?>

				<?php $display_callback( $key, $current_value, $placeholder, $data_additional_params ); ?>

			<?php else: ?>
                <img data-src="<?php echo $placeholder; ?>" src="<?php echo $current_src; ?>" width="120px"/>
                <div>
                    <input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $media_id; ?>"/>
                    <button type="submit" class="upload_image_button button"><?php echo !empty($current_value) ? 'Change' : 'Upload'; ?></button>
                    <button type="submit" class="remove_image_button button">&times;</button>
                </div>
			<?php endif; ?>

        </div>
		<?php
	}


	public static function script() {

		static $displayed = false;

		if ( $displayed ) {
			return;
		}

		$displayed = true;

		?>

        <style>#menu-item-gallery, #menu-item-playlist, #menu-item-video-playlist {
                display: none;
            }</style>
        <script>
            (function ($) {
                // The "Upload" button
                $(document).on('click', '.upload_image_button', function () {
                    var send_attachment_bkp = wp.media.editor.send.attachment;
                    var button = $(this);
                    var show_attachment_preview = $(this).closest('.upload').data('show-attachment-preview')
                    wp.media.editor.send.attachment = function (props, attachment) {
                        if (show_attachment_preview) {
                            $(button).parent().prev().attr('src', attachment.url);
                        }
                        $(button).prev().val(attachment.id);
                        wp.media.editor.send.attachment = send_attachment_bkp;
                    };
                    wp.media.editor.open(button, {
                        frame: 'post',
                        state: 'insert',
                        multiple: false
                    });
                    return false;
                });
                // The "Remove" button (remove the value from input type='hidden')
                $(document).on('click', '.remove_image_button', function () {
                    var show_attachment_preview = $(this).closest('.upload').data('show-attachment-preview')
                    var answer = confirm('Are you sure?');
                    if (answer) {
                        var src = $(this).parent().prev().attr('data-src');
                        if (show_attachment_preview) {
                            $(this).parent().prev().attr('src', src);
                        }
                        $(this).prev().prev().val('');
                    }
                    return false;
                });
            })(jQuery);
        </script>

		<?php
	}

}
