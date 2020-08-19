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
    public static function make($key, $message)
    {
        set_transient(self::FLASH_PREFIX.$key, $message, DAY_IN_SECONDS);
    }

    /**
     * Remove message
     * @param $key
     */
    public static function destroy($key)
    {
        delete_transient(self::FLASH_PREFIX.$key);
    }

    /**
     * Retrieve flashed message
     * @param $key
     * @param  bool  $delete
     */
    public static function get($key, $delete = true)
    {
        $data = get_transient(self::FLASH_PREFIX.$key);
        if ( $delete && ! empty($data)) {
            self::destroy($key);
        }
    }
}