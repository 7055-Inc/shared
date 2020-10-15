<?php


namespace The7055inc\Shared\Hooks\Ajax;

use The7055inc\Shared\Misc\Request;

/**
 * Class BaseAjaxHandler
 * @package The7055inc\Marketplace\Shared\Handlers
 */
abstract class BaseAjaxHandler
{
    const TYPE_BOTH = 1;
    const TYPE_AUTH = 2;
    const TYPE_GUEST = 2;

    /**
     * The current request
     * @var Request
     */
    protected $request;

    /**
     * The ajax call identifier
     * @var string
     */
    protected $slug = '';

    /**
     * Is secure?
     * @var bool
     */
    protected $type = self::TYPE_BOTH;

    /**
     * BaseAjaxHandler constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Handle the ajax call
     * @return void
     */
    abstract function handle();

    /**
     * Register the Handler
     * @return void
     */
    public function register()
    {
        $register_guest = $this->type === self::TYPE_BOTH || $this->type === self::TYPE_GUEST;
        $register_auth  = $this->type === self::TYPE_BOTH || $this->type === self::TYPE_AUTH;

        if ($register_guest) {
            add_action('wp_ajax_nopriv_'.$this->slug, array($this, 'handle'));
        }

        if ($register_auth) {
            add_action('wp_ajax_'.$this->slug, array($this, 'handle'));
        }
    }

    /**
     * Respond
     *
     * @param $success
     * @param  array  $data
     */
    public function respond($success, $data = array())
    {
        if ($success) {
            wp_send_json_success($data);
        } else {
            wp_send_json_error($data);
        }
        exit;
    }
}