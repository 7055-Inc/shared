<?php


namespace The7055inc\Shared\Misc;

class FlashMessage
{
    public $key;
    public $status;
    public $message;
    public $errors;

    /**
     * FlashMessage constructor.
     *
     * @param $key
     * @param $status
     * @param $message
     * @param  array  $errors
     */
    public function __construct($key, $status, $message, $errors = array())
    {
        $this->key     = $key;
        $this->status  = $status;
        $this->message = $message;
        $this->errors  = $errors;
    }
}