<?php

namespace AffiniPayWordPress;

class AffiniPay_Invalid_Request_Error extends AffiniPay_API_Error {
	public function __construct( $json ) {
		parent::__construct( $json );
	}
}
