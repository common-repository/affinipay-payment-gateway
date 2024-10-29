<?php

namespace AffiniPayWP;

use ChargeIO_Error;

class Plugin
{
    var $settings;
    var $shortCode;

    function __construct($root, $client)
    {
        $this->settings = new Settings($root, $client);
        $this->shortCode = new PaymentShortCode();

        wp_register_script('hosted-io', AFFINIPAY_FIELDS_JS_URL);
        wp_register_script('affini-scripts', plugin_dir_url($root) . 'build/static/js/main.js', array(), '', true);
        wp_register_script('affini-payment', plugin_dir_url($root) . 'build/static/js/main.js', array(), '', true);
        wp_register_style('affini-styles', AFFINIPAY_WP_PLUGIN_URL . 'build/static/css/main.css');
        \ChargeIO::$apiUrl  = AFFINIPAY_API_URL;
    }

    function plugin_activate()
    {

    }

    function plugin_deactivate()
    {
        $this->settings->plugin_deactivate();
    }
}
