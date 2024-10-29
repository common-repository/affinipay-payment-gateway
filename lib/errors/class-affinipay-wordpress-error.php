<?php

namespace AffiniPayWordPress;

use ChargeIO_Error;

class AffiniPay_WordPress_Error extends ChargeIO_Error {

	const PAYMENT_FORM_FAILURE = 'Unable to display the payment form';
	const INVALID_KEYS = 'Unable to authenticate with AffiniPay.  Please confirm that your public and secret keys are correct.';
	const INVALID_VALUE = 'Invalid Value for Object';
	const TRANSACTION_NOT_FOUND = 'Transaction Not Found';

	function __construct( $message, $json ) {
		parent::__construct( $message, $json );
	}
}
