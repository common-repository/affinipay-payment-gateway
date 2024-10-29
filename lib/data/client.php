<?php

namespace AffiniPayWP\Data;

use ChargeIO_Charge;
use ChargeIO_OneTimeToken;
use ChargeIO_PaymentMethodReference;
use WP_Error;

class AffiniPay_Client
{
    public $public_key;
    public $secret_key;
    var $credentials;

    public function __construct()
    {
        $this->set_credentials(get_option('affinipay_public_key'), get_option('affinipay_secret_key'));
    }

    public function save_keys($_public, $_secret)
    {
        update_option('affinipay_public_key', $_public);
        update_option('affinipay_secret_key', $_secret);
        $this->set_credentials($_public, $_secret);
    }

    public function clear_keys()
    {
        delete_option('affinipay_public_key');
        delete_option('affinipay_secret_key');
        $this->set_credentials(get_option('affinipay_public_key'), get_option('affinipay_secret_key'));
    }

    private function set_credentials($_public, $_secret)
    {
        $this->public_key = $_public;
        $this->secret_key = $_secret;
        $this->credentials = new \ChargeIO_Credentials($this->public_key, $this->secret_key);
    }

    public function getMerchant()
    {
        try {
            $merchant = \ChargeIO_Merchant::findCurrentUsingCredentials($this->credentials);
            return $merchant;
        } catch (\ChargeIO_Error $e) {
            throw $e;
        }
    }

    public function getTransaction($id = null)
    {
        if (is_string($id)) {
            try {
                return \ChargeIO_Transaction::findByIdUsingCredentials($this->credentials, $id);
            } catch (AffiniPay_Error $e) {
                throw $e;
            }
        } else {
            throw new AffiniPay_WordPress_Error(AffiniPay_Error_Message::INVALID_REQUEST, '');
        }
    }

    public function createToken($params = [])
    {
        $token = ChargeIO_OneTimeToken::createOneTimeCardUsingCredentials($this->credentials, $params);
        return $token;
    }

    public function createCharge($amount = 0, $token = null, $params = [])
    {
        $payment_method = new ChargeIO_PaymentMethodReference(array(
            'id' => $token,
        ));

        $charge = ChargeIO_Charge::createUsingCredentials($this->credentials, $payment_method, $amount, $params);

        return $charge;
    }

    public function select_accounts()
    {
        $merchant = $this->getMerchant();
        update_option('affinipay_public_key', $merchant->attributes['public_key']);
        $merchant_accounts = $merchant->attributes['merchant_accounts'];

        return $merchant_accounts;
    }

    public function getRequiredFields($account_id)
    {
        $merchant = $this->getMerchant();
        $merchant_accounts = $merchant->attributes['merchant_accounts'];
        $required = null;

        foreach ($merchant_accounts as $ma) {
            if ($ma['id'] == $account_id && $ma['status'] == 'ACTIVE') {
                $required = explode(',', $ma['required_payment_fields']);
                break;
            }
        }

        return $required;
    }
}