<?php


namespace The7055inc\Shared\Misc;

/**
 * Class MessageFlash
 * @package The7055inc\Shared\Misc
 */
class MessageFlash
{
    const FLASH_PREFIX = 'mpl_';

    /**
     * Flash message
     * @param $key
     * @param $message
     */
    public static function flash($key, $message)
    {
        set_transient(self::FLASH_PREFIX.$key, $message, DAY_IN_SECONDS);
    }

    /**
     * Unflash message
     * @param $key
     */
    public static function unflash($key)
    {
        delete_transient(self::FLASH_PREFIX.$key);
    }

    /**
     * Retrieve flashed message
     * @param $key
     * @param  bool  $unflash
     */
    public static function retrieve_flashed_data($key, $unflash = true)
    {
        $data = get_transient(self::FLASH_PREFIX.$key);
        if ( $unflash && ! empty($data)) {
            self::unflash($key);
        }
    }
}