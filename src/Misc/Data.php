<?php


namespace The7055inc\Marketplace\Misc;


class Data
{
    /**
     * List of allowed socials
     * @return string[]
     */
    public static function get_allowed_socials()
    {
        return array(
            'facebook'  => 'Facebook',
            'twitter'   => 'Twitter',
            'linkedin'  => 'Linkedin',
            'instagram' => 'Instagram',
        );
    }
}