<?php

namespace AffiniPayWordPress;

use ChargeIO_Error;

class AffiniPay_Authentication_Error extends ChargeIO_Error {
	public function __construct( $message = null, $json = null ) {
		parent::__construct( $message );
		$this->json = $json;
	}
}
