<?php

namespace AffiniPayWordPress;

interface AfiniPay_Client_Interface {

	function set_credentials();
	function set_keys();
	function set_operating_mode();
	function getMerchant();
	function getTransaction();
	function isValidAccount();
	function createToken( $params = []);
	function createCharge( $amount = 0, $token = null, $params = []);
	function void( AffiniPay_Charge $charge);
	function refund( AffiniPay_Credit $credit);
}
