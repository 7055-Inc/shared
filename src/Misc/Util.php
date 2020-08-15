<?php


namespace The7055inc\Shared\Misc;


class Util
{
	public static function us_states()
	{
		$states = array(
			'AL' => 'ALABAMA',
			'AK' => 'ALASKA',
			'AS' => 'AMERICAN SAMOA',
			'AZ' => 'ARIZONA',
			'AR' => 'ARKANSAS',
			'CA' => 'CALIFORNIA',
			'CO' => 'COLORADO',
			'CT' => 'CONNECTICUT',
			'DE' => 'DELAWARE',
			'DC' => 'DISTRICT OF COLUMBIA',
			'FM' => 'FEDERATED STATES OF MICRONESIA',
			'FL' => 'FLORIDA',
			'GA' => 'GEORGIA',
			'GU' => 'GUAM GU',
			'HI' => 'HAWAII',
			'ID' => 'IDAHO',
			'IL' => 'ILLINOIS',
			'IN' => 'INDIANA',
			'IA' => 'IOWA',
			'KS' => 'KANSAS',
			'KY' => 'KENTUCKY',
			'LA' => 'LOUISIANA',
			'ME' => 'MAINE',
			'MH' => 'MARSHALL ISLANDS',
			'MD' => 'MARYLAND',
			'MA' => 'MASSACHUSETTS',
			'MI' => 'MICHIGAN',
			'MN' => 'MINNESOTA',
			'MS' => 'MISSISSIPPI',
			'MO' => 'MISSOURI',
			'MT' => 'MONTANA',
			'NE' => 'NEBRASKA',
			'NV' => 'NEVADA',
			'NH' => 'NEW HAMPSHIRE',
			'NJ' => 'NEW JERSEY',
			'NM' => 'NEW MEXICO',
			'NY' => 'NEW YORK',
			'NC' => 'NORTH CAROLINA',
			'ND' => 'NORTH DAKOTA',
			'MP' => 'NORTHERN MARIANA ISLANDS',
			'OH' => 'OHIO',
			'OK' => 'OKLAHOMA',
			'OR' => 'OREGON',
			'PW' => 'PALAU',
			'PA' => 'PENNSYLVANIA',
			'PR' => 'PUERTO RICO',
			'RI' => 'RHODE ISLAND',
			'SC' => 'SOUTH CAROLINA',
			'SD' => 'SOUTH DAKOTA',
			'TN' => 'TENNESSEE',
			'TX' => 'TEXAS',
			'UT' => 'UTAH',
			'VT' => 'VERMONT',
			'VI' => 'VIRGIN ISLANDS',
			'VA' => 'VIRGINIA',
			'WA' => 'WASHINGTON',
			'WV' => 'WEST VIRGINIA',
			'WI' => 'WISCONSIN',
			'WY' => 'WYOMING',
			'AE' => 'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
			'AA' => 'ARMED FORCES AMERICA (EXCEPT CANADA)',
			'AP' => 'ARMED FORCES PACIFIC'
		);

		return $states;
	}

	/**
	 * Return view
	 *
	 * @param $root
	 * @param $_view
	 * @param array $_data
	 *
	 * @return false|string
	 */
	public static function get_view($root, $_view, $_data = array())
	{
		$_view = str_replace('/', DIRECTORY_SEPARATOR, $_view);

		$_path = trailingslashit($root).'views'.DIRECTORY_SEPARATOR.$_view.'.php';

		if (file_exists($_path)) {

			if ( ! empty($_data)) {
				extract($_data);
			}
			ob_start();
			include($_path);

			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Format author name
	 *
	 * @param $author
	 * @param $author_name
	 * @param $author_email
	 *
	 * @return string|void
	 */
	public static function format_author_name($author, $author_name, $author_email)
	{
		$final = '';
		if ($author) {
			$user = get_user_by('id', $author);
			if ($user) {
				$final = ucfirst($user->display_name);
			} else {
				$final = __('Unknown');
			}
		} else {
			if ( ! empty($author_name)) {
				$final .= $author_name;
			}
			if (empty($author_name)) {
				if ( ! empty($author_email)) {
					$final .= $author_email;
				}
			}
			if (empty($final)) {
				$final = __('Unknown');
			}
		}

		return $final;
	}

	/**
	 * Format author
	 * @param $ID
	 *
	 * @return string|void
	 */
	public static function format_author($ID) {
		return self::format_author_name($ID, NULL, NULL);
	}


	/**
	 * The default date format
	 * @return string[]
	 */
	public static function get_default_date_format()
	{
		return array(
			'js'  => 'M d yy', // Should be the same as php
			'php' => 'M d Y', // Should be the same as js
			'db'  => 'Y-m-d', // Default database format
		);
	}

	/**
	 * The default date format
	 * @return string[]
	 */
	public static function get_default_datetime_format()
	{
		return array(
			'js'  => 'M d yy HH:mm:ss', // Should be the same as php
			'php' => 'M d Y H:i:s', // Should be the same as js
			'db'  => 'Y-m-d H:i:s', // Default database format
		);
	}

	/**
	 * Convert date string from one format to another format
	 *
	 * @param $date
	 * @param $sourceFormat
	 * @param $targetFormat
	 *
	 * @return mixed
	 */
	public static function convert_date($date, $sourceFormat, $targetFormat)
	{
		try {
			$dt = \DateTime::createFromFormat($sourceFormat, $date);

			if(!is_object($dt)) {
				return null;
			}

			return $dt->format($targetFormat);
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Ensure the date is always saved in the db correctly
	 * @param $date
	 *
	 * @return mixed
	 */
	public static function prepare_date_for_db($date)
	{
		$default_format = self::get_default_date_format();
		if ($default_format['php'] != $default_format['db']) {
			$date = self::convert_date($date, $default_format['php'], $default_format['db']);
		}

		return $date;
	}

	/**
	 * Ensure the date is always displayed in the public correctly
	 * @param $date
	 *
	 * @return mixed
	 */
	public static function format_date_for_public($date)
	{
		$default_format = self::get_default_date_format();
		if ($default_format['php'] != $default_format['db']) {
			$date = self::convert_date($date, $default_format['db'], $default_format['php']);
		}

		return $date;
	}

	/**
	 * Ensure the datetime is always displayed in the public correctly
	 *
	 * @param $date
	 *
	 * @return string
	 */
	public static function format_datetime_for_public($date)
	{
		$default_format = self::get_default_datetime_format();
		if ($default_format['php'] != $default_format['db']) {
			$date = self::convert_date($date, $default_format['db'], $default_format['php']);
		}

		return $date;
	}

	/**
	 * Return the client IP
	 * @return mixed|string
	 */
	public static function get_client_ip()
	{
		$ipaddress = null;
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
			$ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}

		return $ipaddress;
	}

	/**
	 * Link post
	 *
	 * @param $id
	 * @param  bool  $ext
	 *
	 * @return string|void
	 */
	public static function link_post($id, $ext = true)
	{

		$_post = get_post($id);

		if (empty($_post)) {
			$link = __('Unknown');
		} else {
			$target = $ext ? 'target="_blank"' : '';
			$link   = '<a '.$target.' href="'.get_permalink($id).'">'.$_post->post_title.'</a>';
		}

		return $link;
	}

	/**
	 * Custom image upload field
	 * @param $key
	 * @param $current_value
	 * @param $placeholder
	 */
	public static function media_upload_field( $key, $current_value, $placeholder = '' ) {

		$media_id = $current_value;
		if ( ! empty( $media_id ) && is_numeric( $media_id ) ) {
			$current_src = wp_get_attachment_image_src( $media_id, 'thumbnail' );
			$current_src = $current_src[0];
		} else {
			$current_src = $placeholder;
			$media_id    = '';
		}
		if(empty($current_src)) {
			$current_src = 'https://placehold.it/120x120';
		}
		?>
		<div class="upload">
			<img data-src="<?php echo $placeholder; ?>" src="<?php echo $current_src; ?>" width="120px"/>
			<div>
				<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $media_id; ?>"/>
				<button type="submit" class="upload_image_button button"><?php _e('Upload'); ?></button>
				<button type="submit" class="remove_image_button button">&times;</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Generate reported item
	 * @param $itemID
	 *
	 * @return string|void
	 */
	public static function format_reported_item($itemID)
	{
		$reported_item = '';
		if (empty($itemID)) {
			$reported_item = __('Unknown');
		} else {
			$reported_item = get_the_title($itemID);
		}

		return $reported_item;
	}

	public static function format_edit_link($itemID, $new_tab = false) {
		$title = get_the_title($itemID);
		$link = self::link_edit_post($itemID);
		$target = $new_tab ? 'target="_blank"' : '';
		return '<a href="'.$link.'" '.$target.'>'.$title.'</a>';
	}

	/**
	 * Generate admin edit link
	 *
	 * @param $id
	 *
	 * @return string|null
	 */
	public static function link_edit_post($id) {
		return get_edit_post_link($id);
	}

	/**
	 *
	 * @param $id
	 * @param  bool  $ext
	 *
	 * @return string|void
	 */
	public static function link_user($id, $ext = true)
	{

		$user = get_user_by('id', $id);

		if (empty($user)) {
			$link = __('Unknown');
		} else {
			$target = $ext ? 'target="_blank"' : '';
			$url    = admin_url('user-edit.php?user_id='.$id);
			$link   = '<a '.$target.' href="'.$url.'">'.$user->display_name.'</a>';
		}

		return $link;
	}

	public static function link_vendor($id, $ext = true)
	{

		$name = get_user_meta($id, 'company_name', true);
		if (empty($name)) {
			$_user = get_user_by('id', $id);
			$name  = $_user->display_name;
		}

		$url    = admin_url('admin.php?page='.MPL_ADMIN_PAGE.'&action=edit&id='.$id);
		$target = $ext ? 'target="_blank"' : '';

		return '<a '.$target.' href="'.$url.'">'.$name.'</a>';

	}

	/**
	 * Format currency
	 *
	 * @param $price
	 *
	 * @return string
	 */
	public static function format_currency($price)
	{
		if ( ! is_numeric($price)) {
			return $price;
		}

		if (function_exists('wc_price')) {
			$price = wc_price($price);
		} else {
			$formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
			$price     = $formatter->formatCurrency($price, 'USD');
		}

		return $price;
	}

    /**
     * Format status
     * @param $status
     */
	public static function format_status($status) {
	    return '<span class="mpl-status mpl-status-'.$status.'">'.ucfirst($status).'</span>';
    }

	/**
	 * Duplicate post entry
	 *
	 * @param $id
	 *
	 * @return false|int|\WP_Error
	 */
	public static function duplicate_post($id) {

		$source = get_post($id);

		$src_params = array(
			'post_title',
			'post_content',
			'post_parent',
			'post_author',
            'post_type',
		);

		$new_post = array();

		// Copy post
		foreach($src_params as $src_param) {
			if(isset($source->$src_param)) {
				$new_post[$src_param] = $source->$src_param;
			}
		}

		// Insert post
		$new_id = wp_insert_post($new_post);
		if(is_wp_error($new_id)) {
			return false;
		}

		// Copy Metadata
		$meta = get_post_meta($source->ID);
		foreach($meta as $key => $v_arr) {
			$value = is_array($v_arr) ? $v_arr[0] : $v_arr;
			update_post_meta($new_id, $key, $value);
		}
		return $new_id;
	}

}