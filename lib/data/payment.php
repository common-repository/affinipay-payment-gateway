<?php

namespace AffiniPayWP\Data;

use WP_Error;
use ChargeIO_ApiError;

class Payment extends Api
{

    function __construct($client)
    {
        parent::__construct($client, "payment");
    }

    function register_routes()
    {
        $this->register('/charge/', 'POST', 'charge');
    }

    function charge($request)
    {
        $data = $request->get_json_params();
        $params = array(
            "id" => $data['form_data']['id'],
            "name" => $data['name'],
            "email" => $data['email']
        );

        try {
            return $this->client->createCharge(intval($data['form_data']['amount']), $data['id'], $params);
        } catch (ChargeIO_ApiError $error) {
            return new WP_ERROR('Error', '', array('status' => 403, 'messages' => $error->messages));
        }
    }
}