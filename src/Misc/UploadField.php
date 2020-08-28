<?php


namespace The7055inc\Shared\Misc;

/**
 * Class UploadField
 * @package The7055inc\Shared\Misc
 */
class UploadField
{

    /**
     * Custom image upload field
     * @param $key
     * @param $current_value
     * @param $placeholder
     */
    public function the_field( $key, $current_value, $placeholder = '' ) {

        $media_id = $current_value;
        if ( ! empty( $media_id ) && is_numeric( $media_id ) ) {
            $current_src = wp_get_attachment_image_src( $media_id, 'thumbnail' );
            $current_src = $current_src[0];
        } else {
            $current_src = $placeholder;
            $media_id    = '';
        }
        ?>
        <div class="upload">
            <img data-src="<?php echo $placeholder; ?>" src="<?php echo $current_src; ?>" width="120px"/>
            <div>
                <input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $media_id; ?>"/>
                <button type="submit" class="upload_image_button button">Upload</button>
                <button type="submit" class="remove_image_button button">&times;</button>
            </div>
        </div>
        <?php
    }


    public static function script() {
        ?>

        <script>
            (function($){
                // The "Upload" button
                $(document).on('click', '.upload_image_button', function() {
                    var send_attachment_bkp = wp.media.editor.send.attachment;
                    var button = $(this);
                    wp.media.editor.send.attachment = function(props, attachment) {
                        $(button).parent().prev().attr('src', attachment.url);
                        $(button).prev().val(attachment.id);
                        wp.media.editor.send.attachment = send_attachment_bkp;
                    };
                    wp.media.editor.open(button);
                    return false;
                });
                // The "Remove" button (remove the value from input type='hidden')
                $(document).on('click', '.remove_image_button', function() {
                    var answer = confirm('Are you sure?');
                    if (answer) {
                        var src = $(this).parent().prev().attr('data-src');
                        $(this).parent().prev().attr('src', src);
                        $(this).prev().prev().val('');
                    }
                    return false;
                });
            })(jQuery);
        </script>

        <?php
    }

}