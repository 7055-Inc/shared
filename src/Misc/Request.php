<?php


namespace The7055inc\Shared\Misc;

class Request
{
    /**
     * The request
     * @var array
     */
    protected $data = array();

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->data = $this->all();
    }

    /**
     * Is post request?
     * @return bool
     */
    public function is_post()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Return request parameter
     *
     * @param $param
     * @param  null  $default
     *
     * @return mixed|null
     */
    public function get($param, $default = null)
    {
        return isset($this->data[$param]) ? $this->data[$param] : $default;
    }

    /**
     * Return all the data
     * @return array
     */
    public function all()
    {
        return $_REQUEST + $_FILES;
    }

    /**
     * Return only specific data
     *
     * @param $keys
     *
     * @return array
     */
    public function only($keys)
    {
        $data = $this->all();
        $new  = array();

        foreach ($data as $key => $value) {

            if ( ! in_array($key, $keys)) {
                continue;
            }

            $new[$key] = $value;
        }

        return $new;
    }

    /**
     * Abort.
     *
     * @param $message
     */
    public function abort($message)
    {
        wp_die($message);
        die;
    }

    /**
     * Return the current request origin IP.
     * @return mixed|string|null
     */
    public function get_client_ip() {
        return Util::get_client_ip();
    }

    /**
     * Returns the current user ID
     * @return mixed
     */
    public function get_current_user_id() {
        if(!function_exists('\get_current_user_id')) {
            return null;
        }
        return \get_current_user_id();
    }
}