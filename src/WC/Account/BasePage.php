<?php


namespace The7055inc\Shared\WC\Account;

use The7055inc\Shared\Misc\Request;

/**
 * Class BasePage
 * @package The7055inc\Shared\WC\Account
 */
abstract class BasePage
{
    private $prefix = '7055_';

    /**
     * Tab slug
     * @var string
     */
    protected $slug = '';

    /**
     * Tab name
     * @var string
     */
    protected $name = '';


    /**
     * Add ajax endpoints
     * @var array
     */
    protected $ajax_endpoints = array();


    /**
     * The current request
     * @var
     */
    protected $request;


    public function __construct() {
        $this->request = new Request();
    }

    /**
     * The account menu items
     *
     * @param $items
     *
     * @return mixed
     */
    public function account_menu_items($items)
    {
        $items[$this->slug] = $this->name;

        return $items;
    }

    /**
     * The rewrite endpoint
     */
    public function add_rewrite_endpoint()
    {
        add_rewrite_endpoint($this->slug, EP_ROOT | EP_PAGES);
    }


    /**
     * The query vars
     *
     * @param $vars
     *
     * @return mixed
     */
    public function query_vars($vars)
    {
        array_push($vars, $this->slug);

        return $vars;
    }


    /**
     * Register the tab
     */
    public function register()
    {
        if (empty($this->slug)) {
            error_log(__('Unable to register my account tab. Empty slug.'));
        }

        add_action('woocommerce_account_menu_items', array($this, 'account_menu_items'));
        add_action('init', array($this, 'add_rewrite_endpoint'));
        add_filter('query_vars', array($this, 'query_vars'));
        add_action('woocommerce_account_'.$this->slug.'_endpoint', array($this, 'content'));

        if (count($this->ajax_endpoints)) {
            foreach ($this->ajax_endpoints as $ajax_endpoint) {
                if ( ! method_exists($this, $ajax_endpoint['callback'])) {
                    continue;
                }
                add_action('wp_ajax_'.$ajax_endpoint['key'], array($this, $ajax_endpoint['callback']));
                if( !$ajax_endpoint['is_private']) {
                    add_action('wp_ajax_nopriv_'.$ajax_endpoint['key'], array($this, $ajax_endpoint['callback']));
                }
            }
        }
    }

    /**
     * The tab content
     *
     * @return void
     */
    abstract public function content();

    /**
     * Add ajax endpoint
     *
     * @param $name
     * @param $callback
     * @param  bool  $is_private
     */
    protected function define_ajax_endpoint($name, $callback, $is_private = true)
    {
        $this->ajax_endpoints[$name] = array(
            'key'        => $this->prefix . str_replace('-', '_', $this->slug . '_' .$name),
            'callback'   => $callback,
            'is_private' => $is_private,
        );
    }

    /**
     * Is supage?
     *
     * @param $subpage
     *
     * @return bool
     */
    protected function is_subpage_query($subpage)
    {
        return isset($_GET['context']) && $_GET['context'] === $subpage;
    }

    /**
     * Return the query id
     * @return mixed|null
     */
    protected function get_query_id()
    {
        return isset($_GET['id']) ? $_GET['id'] : null;
    }

    /**
     * Render view
     *
     * @param $view
     * @param  array  $data
     */
    abstract function render($view, $data = array());


    /**
     * Send response accepted by datatables.
     *
     * @param int $draw
     * @param int $recordsTotal
     * @param int $recordsFiltered
     * @param array $data
     * @param  string  $error
     */
    protected function response_json_datatables($draw, $recordsTotal, $recordsFiltered, $data, $error = '')
    {
        $response = array(
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        );
        if ( ! empty($error)) {
            $response['error'] = $error;
        }
        echo json_encode($response);
        die;
    }
}