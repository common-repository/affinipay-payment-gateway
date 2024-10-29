<?php

namespace AffiniPayWordPress;

use ChargeIO_Error;

class AffiniPay_API_Error extends ChargeIO_Error {

	var $messages = array();
	var $errors = array();

	public function __construct( $json ) {
		parent::__construct( null, $json );
		$attrs = json_decode( $json, true );

		if ( array_key_exists( 'messages', $attrs ) ) {
			foreach ( $attrs['messages'] as $message ) {
				if ( $message['level'] == 'error' && $message['message'] ) {
					if ( empty( $this->errors ) ) {
						$this->code = $message['code'];
					}
					$this->messages[] = $message;
					$this->errors[] = $message['message'];
				}
			}
		}

		if ( empty( $this->errors ) ) {
			$this->messages[] = array(
				'code' => 'application_error',
				'level' => 'error',
			);
			$this->errors[] = 'Unknown Error';
			$this->code = 'application_error';
		}

		$this->message = $this->errors[0];
	}
}
