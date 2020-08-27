<?php


namespace The7055inc\Shared\Misc;


class WC
{
    /**
     * Return the account page
     *
     * @param  null  $subpage
     *
     * @return false
     */
    public static function is_account_page($subpage = null) {
        if(!function_exists('is_account_page')) {
            return false;
        }
        return is_account_page();
    }

    public static function get_product_name($ID)
    {
        return '#'.$ID.' - '.get_the_title($ID);
    }

    public static function set_variation_regular_price($ID, $price)
    {
        update_post_meta($ID, '_regular_price', $price);
        update_post_meta($ID, '_price', $price);
        wc_delete_product_transients($ID);
    }

    public static function set_regular_price($ID, $price)
    {
        update_post_meta($ID, '_regular_price', $price);
        update_post_meta($ID, '_price', $price);
        wc_delete_product_transients($ID);
    }

    public static function get_variation_name($variationID)
    {
        $variation     = new \WC_Product_Variation($variationID);
        $variationName = $variation->get_title().' - '.'('.implode("/", $variation->get_variation_attributes()).')';

        return $variationName;
    }
    
    public static function get_variation_name_short($variationID)
    {
        $variation = new \WC_Product_Variation($variationID);

        return implode("/", $variation->get_variation_attributes());
    }

}