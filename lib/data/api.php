<?php

namespace AffiniPayWP\Data;

abstract class Api
{

    public $client;
    protected $name;
    private $namespace;

    abstract protected function register_routes();

    function __construct($client, $name)
    {
        $this->client = $client;
        $this->name = $name;
        $this->namespace = 'affinipay/v1';
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    function register($route, $verb, $method)
    {
        register_rest_route($this->namespace, $route, array(
            'methods' => $verb,
            'callback' => array($this, $method),
        ));
    }

    function registerAdmin($route, $verb, $method)
    {
        register_rest_route($this->namespace, $route, array(
            'methods' => $verb,
            'callback' => array($this, $method),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            }
        ));
    }
}

/**
 * @property  messages
 */
class ApiError extends \WP_ERROR
{

    public $messages;

    public function __construct($code = '', $_messages, $data = '')
    {
        parent::__construct($code, '', $data);
        $this->messages = $_messages;
    }
}