<?php


namespace The7055inc\Shared\Misc;


class WC {
	/**
	 * Return the account page
	 *
	 * @param null $subpage
	 *
	 * @return false
	 */
	public static function is_account_page( $subpage = null ) {
		if ( ! function_exists( 'is_account_page' ) ) {
			return false;
		}

		return is_account_page();
	}

	public static function get_product_name( $ID ) {
		return '#' . $ID . ' - ' . get_the_title( $ID );
	}

	/**
	 * Set regular price
	 *
	 * @param $ID
	 * @param $price
	 *
	 * @return bool
	 */
	public static function set_regular_price( $ID, $price ) {
		$product = wc_get_product( $ID );
		if ( $product ) {
			$product->set_regular_price( $price );
			$product->save();
			wc_delete_product_transients( $ID );

			return true;
		}

		return false;
	}

	/**
	 * Set variation regular price
	 *
	 * @param $ID
	 * @param $price
	 *
	 * @return bool
	 */
	public static function set_variation_regular_price( $ID, $price ) {
		return self::set_regular_price( $ID, $price );
	}

	/**
	 * Set sale price
	 *
	 * @param $ID
	 * @param $price
	 * @param null $start_date
	 * @param null $end_date
	 *
	 * @return bool
	 */
	public static function set_sale_price( $ID, $price, $start_date = null, $end_date = null ) {
		$product = wc_get_product( $ID );
		if ( $product ) {
			$product->set_sale_price( $price );
			$product->set_date_on_sale_from( $start_date );
			$product->set_date_on_sale_to( $end_date );
			$product->set_price( $product->get_regular_price( 'edit' ) );
			$product->save();
			wc_delete_product_transients( $ID );
			return true;
		}

		return false;
	}

	/**
	 * Set variation sale price
	 *
	 * @param $ID
	 * @param $price
	 *
	 * @return bool
	 */
	public static function set_variation_sale_price( $ID, $price ) {
		return self::set_sale_price( $ID, $price );
	}


	/**
	 * Return the variation name
	 *
	 * @param $variationID
	 *
	 * @return string
	 */
	public static function get_variation_name( $variationID ) {
		$variation     = new \WC_Product_Variation( $variationID );
		$variationName = $variation->get_title() . ' - ' . '(' . implode( "/", $variation->get_variation_attributes() ) . ')';

		return $variationName;
	}

	/**
	 * Return the variation short name
	 *
	 * @param $variationID
	 *
	 * @return string
	 */
	public static function get_variation_name_short( $variationID ) {
		$variation = new \WC_Product_Variation( $variationID );

		return implode( "/", $variation->get_variation_attributes() );
	}

}