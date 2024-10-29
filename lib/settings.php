<?php

namespace AffiniPayWP;

class Settings
{
    var $root;
    var $client;

    function __construct($root, $client)
    {
        $this->root = $root;
        $this->client = $client;
        add_action('admin_menu', array($this, 'add_admin_menu_page'));
    }

    function load_scripts()
    {
        wp_localize_script('affini-scripts', 'affinipay_options', array(
            'route' => 'Settings',
            'data' => array(
                'receiptPage' => get_option('affinipay_receipt_page', ''),
                'secretKey' => get_option('affinipay_secret_key', ''),
                'merchants' => $this->merchantsOrEmpty(),
                'pages' => $this->pagesOrEmpty(),
                'nonce' => wp_create_nonce( 'wp_rest' )
            )
        ));
        wp_enqueue_script('affini-scripts');
        wp_enqueue_style('affini-styles');
    }

    private function pagesOrEmpty()
    {
        $pages = [];
        foreach (array_merge(get_pages(), get_posts()) as $p) {
            array_push($pages, array('id' => $p->ID, 'title' => $p->post_title));
        }
        return $pages;
    }

    private function merchantsOrEmpty()
    {
        try {
            return $this->client->select_accounts();
        } catch (\ChargeIO_AuthenticationError $e) {
            return array();
        }
    }

    function add_admin_menu_page()
    {
        add_filter( 'plugin_action_links_' . plugin_basename($this->root), array( $this, 'add_action_links' ) );
        add_options_page(
            'AffiniPay Payments',
            'Affinipay Payments',
            'administrator',
            'affinipay-settings',
            array($this, 'display_settings_page')
        );
    }

    function add_action_links($links)
    {
        return array_merge($links, array(
            '<a href="' . admin_url('options-general.php?page=affinipay-settings') . '">Settings</a>'
        ));
    }

    function display_settings_page()
    {
        $this->load_scripts();
        echo '<div id="root"></div>';
    }

    function plugin_deactivate()
    {
        delete_option('affinipay_receipt_page');
        $this->client->clear_keys();
    }
}
