<?php


namespace The7055inc\Shared\Misc;

/**
 * Class MessageFlash
 * @package The7055inc\Shared\Misc
 */
class SessionFlash
{
    const FLASH_PREFIX = 'mpl_';

    /**
     * Flash message
     *
     * @param  string  $key
     * @param  string  $status
     * @param  string|array  $message
     * @param  array  $errors
     */
    public static function make($key, $status, $message, $errors = array())
    {
        $_message = new FlashMessage($key, $status, $message, $errors);
        set_transient(self::FLASH_PREFIX.$key, $_message, DAY_IN_SECONDS);
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
     *
     * @param $key
     * @param  bool|FlashMessage  $delete
     *
     * @return mixed
     */
    public static function get($key, $delete = true)
    {
        $data = get_transient(self::FLASH_PREFIX.$key);
        if ($delete && false !== $data) {
            self::destroy($key);
        }

        return $data;
    }
}