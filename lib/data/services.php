<?php

namespace AffiniPayWP\Data;
include_once(dirname(__FILE__) . '/client.php');
include_once(dirname(__FILE__) . '/api.php');
include_once(dirname(__FILE__) . '/payment.php');
include_once(dirname(__FILE__) . '/settings.php');

class Services
{

    public static $client;

    static function init()
    {
        if (self::$client == null) {
            self::$client = new AffiniPay_Client();
            new Settings(self::$client);
            new Payment(self::$client);
        }

        return self::$client;
    }
}