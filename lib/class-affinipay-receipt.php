<?php

namespace AffiniPayWordPress;

class AffiniPay_Receipt extends AffiniPay_ShortCode {

	var $affinipay_client;
	var $transaction;

	function __construct( $atts = array() ) {
		foreach ( $atts as $att ) {
			array_push( $this->atts, $att );
		}
	}

	function init( $atts = [], $content = null, $tag = null ) {

		$output = null;
		$this->atts = array_change_key_case( (array) $atts, CASE_LOWER );
		$this->content = $content;

		$this->atts = shortcode_atts([
			'title' => 'Payment Received',
			'show_receipt_title' => 'yes',
			'show_transaction_amount' => 'yes',
			'show_transaction_currency' => 'no',
			'show_transaction_id' => 'yes',
			'show_transaction_date' => 'yes',
			'show_transaction_authorization_code' => 'yes',
			'show_transaction_card_type' => 'yes',
			'show_transaction_card_number' => 'yes',
			'show_transaction_exp_month' => 'yes',
			'show_transaction_exp_year' => 'yes',
			'show_transaction_reference' => 'yes',
			'show_transaction_custom_fields' => 'no',
			'show_cvv_result' => 'no',
			'show_avs_result' => 'no',
			'show_customer_name' => 'yes',
			'show_customer_address' => 'yes',
			'show_customer_address2' => 'yes',
			'show_customer_city' => 'yes',
			'show_customer_state' => 'yes',
			'show_customer_postal_code' => 'yes',
			'show_customer_email' => 'yes',
			'show_customer_phone' => 'yes',
		], $atts, $tag);

		return $this->render();
	}

	function render() {
		ob_start();
		$message = null;
		$this->affinipay_client = new AffiniPay_Client;
		if ( ! is_admin() ) {
			$tid = (array_key_exists( 'payment', $_GET ) ? $_GET['payment'] : null);
			$t = $this->affinipay_client->getTransaction( $tid );

			if ( $t instanceof AffiniPay_Transaction ) {
				$this->transaction = $t->attributes;

				$override = get_template_directory() . '/affinipay-receipt.php';

				if ( file_exists( $override ) ) {
					include_once( $override );
				} else {
					include_once( AFFINIPAY_PLUGIN_DIR . '/views/affinipay-receipt.php' );
				}
			}

			if ( $t instanceof AffiniPay_Invalid_Request_Error ) {
				$message = $t->getMessage();
			}
			if ( $t instanceof AffiniPay_ApiError ) {
				$message = $t->getMessage();
			}
			if ( $t instanceof AffiniPay_WordPress_Error ) {
				$message = $t->getMessage();
			}

			if ( $message != null ) {
				include_once( AFFINIPAY_PLUGIN_DIR . '/views/affinipay-error.php' );
			}
		}
		return ob_get_clean();
	}

	function render_date() {
		$date = null;
		if ( array_key_exists( 'created', $this->transaction ) ) {
			$date = $this->format_date( $this->transaction['created'] );
		}
		return $date;
	}

	function render_title( $atts = [], $content = null, $tag = null ) {
		$title_atts = shortcode_atts([
			'text' => 'Payment Received',
		], $atts, $tag);

		return $title_atts['text'];
	}

	function render_amount() {
		return $this->format_money( $this->transaction['amount'] );
	}

	function render_authcode() {
		$authcode = null;
		if ( array_key_exists( 'authorization_code', $this->transaction ) ) {
			$authcode = $this->transaction['authorization_code'];
		}
		return $authcode;
	}

	function render_currency() {
		$currency = null;
		if ( array_key_exists( 'currency', $this->transaction ) ) {
			$currency = $this->transaction['currency'];
		}
		return $currency;
	}

	function render_transaction_id() {
		$id = null;
		if ( array_key_exists( 'id', $this->transaction ) ) {
			$id = $this->transaction['id'];
		}
		return $id;
	}

	function render_card_type() {
		$type = null;
		if ( array_key_exists( 'card_type', $this->transaction['method'] ) ) {
			$type = $this->transaction['method']['card_type'];
		}
		return $type;
	}

	function render_card_mask() {
		$mask = null;
		if ( array_key_exists( 'number', $this->transaction['method'] ) ) {
			$mask = $this->transaction['method']['number'];
		}
		return $mask;
	}

	function render_card_month() {
		$month = null;
		if ( array_key_exists( 'exp_month', $this->transaction['method'] ) ) {
			$month = $this->transaction['method']['exp_month'];
		}
		return $month;
	}

	function render_card_year() {
		$year = null;
		if ( array_key_exists( 'exp_year', $this->transaction['method'] ) ) {
			$year = $this->transaction['method']['exp_year'];
		}
		return $year;
	}

	function render_cvv_result() {
		$cvv = null;
		if ( array_key_exists( 'cvv_result', $this->transaction ) ) {
			$cvv = $this->transaction['cvv_result'];
		}
		return $cvv;
	}

	function render_avs_result() {
		$avs = null;
		if ( array_key_exists( 'avs_result', $this->transaction ) ) {
			$avs = $this->transaction['avs_result'];
		}
		return $avs;
	}

	function render_customer_name() {
		$name = null;
		if ( array_key_exists( 'name', $this->transaction['method'] ) ) {
			$name = $this->transaction['method']['name'];
		}
		return $name;
	}

	function render_address() {
		$address = null;
		if ( array_key_exists( 'address1', $this->transaction['method'] ) ) {
			$address = $this->transaction['method']['address1'];
		}
		return $address;
	}

	function render_address2() {
		$address2 = null;
		if ( array_key_exists( 'address2', $this->transaction['method'] ) ) {
			$address2 = $this->transaction['method']['address2'];
		}
		return $address2;
	}

	function render_city() {
		$city = null;
		if ( array_key_exists( 'city', $this->transaction['method'] ) ) {
			$city = $this->transaction['method']['city'];
		}
		return $city;
	}

	function render_state() {
		$state = null;
		if ( array_key_exists( 'state', $this->transaction['method'] ) ) {
			$state = $this->transaction['method']['state'];
		}
		return $state;
	}

	function render_postal() {
		$postal = null;
		if ( array_key_exists( 'postal_code', $this->transaction['method'] ) ) {
			$postal = $this->transaction['method']['postal_code'];
		}
		return $postal;
	}

	function render_email() {
		$email = null;
		if ( array_key_exists( 'email', $this->transaction['method'] ) ) {
			$email = $this->transaction['method']['email'];
		}
		return $email;
	}

	function render_phone() {
		$phone = null;
		if ( array_key_exists( 'phone', $this->transaction['method'] ) ) {
			$phone = $this->transaction['method']['phone'];
		}
		return $phone;
	}

	private function format_money( $amount = 0 ) {
		return '$' . $amount / 100;
	}
	private function format_date( $date = null ) {
		if ( $date ) {
			$time = strtotime( $date );
			$formatted_date = strftime( '%m/%d/%y', $time );
			return $formatted_date;
		}
	}

}
