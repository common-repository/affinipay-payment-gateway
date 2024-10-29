<?php

namespace AffiniPayWP\Data;

use WP_Error;

class Settings extends Api
{

    function __construct($client)
    {
        parent::__construct($client, 'settings');
    }

    function register_routes()
    {
        $this->registerAdmin('/save/', 'POST', 'save');
    }

    public function save($data)
    {
        update_option('affinipay_receipt_page', $data['receiptPage']);
        $this->client->save_keys($data['publicKey'], $data['secretKey']);

        try {
            return array("error" => "", "merchants" => $this->client->select_accounts());
        } catch (\ChargeIO_AuthenticationError $e) {
            $this->client->clear_keys();
            return new WP_Error('authentication_error', 'Invalid credentials', array('status' => 403));
        }
    }
}