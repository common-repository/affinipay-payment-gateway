<?php

namespace AffiniPayWordPress;

use ChargeIO_Error;

class AffiniPay_WordPress implements AffiniPayWordPressInterface
{

    var $settings;
    var $filters;
    var $hooks;
    var $logger;
    var $errors;
    var $scripts;
    var $styles;
    var $shortcode;
    var $addons;
    var $payment_form;
    var $file;

    function __construct($file)
    {
        $this->file = $file;
        $this->settings = new AffiniPay_Settings($file);
        $this->errors = new ChargeIO_Error;
        $this->shortcode = new AffiniPay_Shortcode;
        $this->payment_form = new AffiniPay_Payment_Form;

        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('the_post', array($this, 'the_post'));

        add_action('wp_ajax_nopriv_affinipay_submit_charge', array($this->payment_form, 'submit_charge'));
        add_action('wp_ajax_affinipay_submit_charge', array($this->payment_form, 'submit_charge'));
    }

    function init()
    {
        $this->register_scripts();
        $this->register_styles();
        $this->register_shortcodes();
    }

    function admin_init()
    {
        $this->settings->admin_init();
        $this->register_admin_scripts();
        $this->register_admin_styles();
    }

    function register_scripts()
    {
        if (!wp_script_is('affinipay-lib', 'registered')) {
            wp_register_script('affinipay-lib', AFFINIPAY_CLIENT_JS_URL);
        }
        if (!wp_script_is('affinipay-scripts', 'registered')) {
            wp_register_script('affinipay-scripts', AFFINIPAY_PLUGIN_JS_URL . '/affinipay.min.js', array('jquery'));
        }
    }

    function register_styles()
    {
        if (!wp_style_is('affinipay-styles', 'registered')) {
            wp_register_style('affinipay-styles', AFFINIPAY_PLUGIN_URL . '/css/affinipay.min.css');
        }
    }

    function register_admin_scripts()
    {
        if (!wp_script_is('affinipay-admin-scripts', 'registered')) {
            wp_register_script('affinipay-admin-scripts', AFFINIPAY_PLUGIN_JS_URL . '/affinipay-admin.min.js', array('jquery'));
        }
    }

    function register_admin_styles()
    {
        if (!wp_style_is('affinipay-admin-styles', 'registered')) {
            wp_register_style('affinipay-admin-styles', AFFINIPAY_PLUGIN_URL . '/css/affinipay-admin.min.css');
        }
    }

    function register_shortcodes()
    {
        $this->shortcode->init();
    }

    function plugin_activate()
    {

    }

    function plugin_deactivate()
    {
        $this->settings->plugin_deactivate();
    }

    function the_post($post)
    {
        if (!$post) {
            return;
        }

        $receipt_id = get_option('affinipay_receipt_page');
        $is_payment_form = has_shortcode($post->post_content, 'affinipay-payment');
        $is_receipt_page = ($post->ID == $receipt_id ? true : false);

        wp_enqueue_style('affinipay-styles');

        if ($is_payment_form) {

            wp_enqueue_script('affinipay-lib');
            wp_enqueue_script('affinipay-scripts');

            $redirect_url = $receipt_id ? '/?page_id=' . $receipt_id : null;
            $public_key = get_option('affinipay_public_key');
            $api_params = array(
                'public_key' => $public_key,
                'redirect_url' => $redirect_url,
                'ajax_url' => admin_url('admin-ajax.php'),
            );

            wp_localize_script('affinipay-scripts', 'affinipay', $api_params);

            $this->payment_form->load_payment_form_ui_js();
        }

        if ($is_receipt_page) {

            if (!has_shortcode($post->post_content, 'affinipay-receipt')) {
                try {
                    echo $this->shortcode->receipt_page->render();
                } catch (AffiniPay_WordPress_Error $e) {
                    throw $e;
                }
            }
        }
    }
}
