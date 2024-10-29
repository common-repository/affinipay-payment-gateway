<?php

namespace AffiniPayWordPress;

use ChargeIO_Charge;
use ChargeIO_OneTimeToken;
use ChargeIO_PaymentMethodReference;

class AffiniPay_Client implements AfiniPay_Client_Interface {

	var $operating_mode;
	public $public_key;
	public $account_id;
	public $secret_key;
	var $credentials;

	public function __construct() {
		$this->set_keys();
	}

	function set_keys() {
        $this->secret_key = get_option( 'affinipay_public_key' );
        $this->secret_key = get_option( 'affinipay_secret_key' );
		$this->set_credentials();
	}

	function set_credentials() {
		$this->credentials = new \ChargeIO_Credentials( $this->public_key, $this->secret_key );
	}

	public function set_operating_mode( $mode = 'test' ) {
		$this->operating_mode = ($mode == 'live' ? 'live' : 'test');
		$this->set_keys();

	}

	public function getMerchant() {
		try {
			$merchant = \ChargeIO_Merchant::findCurrentUsingCredentials( $this->credentials );
			return $merchant;
		} catch ( \ChargeIO_Error $e ) {
			throw $e;
		}
	}

	public function getTransaction( $id = null ) {
		if ( is_string( $id ) ) {
			try {
				return \ChargeIO_Transaction::findByIdUsingCredentials( $this->credentials, $id );
			} catch ( AffiniPay_Error $e ) {
				throw $e;
			}
		} else {
			throw new AffiniPay_WordPress_Error( AffiniPay_Error_Message::INVALID_REQUEST, '' );
		}
	}

	public function createToken( $params = [] ) {
	    $token = ChargeIO_OneTimeToken::createOneTimeCardUsingCredentials( $this->credentials, $params );
		return $token;
	}

	public function createCharge( $amount = 0, $token = null, $params = [] ) {
		$payment_method = new ChargeIO_PaymentMethodReference( array(
			'id' => $token,
		) );
		$charge = ChargeIO_Charge::createUsingCredentials( $this->credentials, $payment_method, $amount, $params );

		return $charge;
	}

	public function isValidAccount() {
		$valid_accounts = array();
		$merchant = $this->getMerchant();
		$merchant_accounts = $merchant->attributes['merchant_accounts'];

		foreach ( $merchant_accounts as $ma ) {
			if ( $ma['id'] == $this->account_id ) {
				array_push( $valid_accounts, $ma['id'] );
			}
		}
		return (count( $valid_accounts ) > 0 ? true : false);
	}


	public function void( AffiniPay_Charge $charge ) {
		// TODO: Implement void() method.
	}

	public function refund( AffiniPay_Credit $credit ) {
		// TODO: Implement refund() method.
	}

}
