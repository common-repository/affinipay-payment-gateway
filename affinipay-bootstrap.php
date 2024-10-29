<?php
/**
 *
 * AffiniPay Bootstrap
 *
 * @package affinipay-wordpress
 */

namespace AffiniPayWordPress;

/** Check Dependencies */
if ( ! function_exists( 'curl_init' ) ) {
	throw new \Exception( 'AffiniPay needs the PHP CURL extension' );
}
if ( ! function_exists( 'json_decode' ) ) {
	throw new \Exception( 'AffiniPay needs the JSON PHP extension.' );
}
if ( ! function_exists( 'mb_detect_encoding' ) ) {
	throw new \Exception( 'AffiniPay needs the Multibyte String PHP extension.' );
}

include_once( dirname( __FILE__ ) . '/vendor/affinipay/chargeio-php/lib/ChargeIO.php' );

/** Interfaces */
include_once( dirname( __FILE__ ) . '/lib/interfaces/AffiniPayClientInterface.php' );
include_once( dirname( __FILE__ ) . '/lib/interfaces/AffiniPayPaymentMethod.php' );

/** Errors */
include_once( dirname( __FILE__ ) . '/lib/errors/class-affinipay-error.php' );
include_once( dirname( __FILE__ ) . '/lib/errors/class-affinipay-api-error.php' );
include_once( dirname( __FILE__ ) . '/lib/errors/class-affinipay-authentication-error.php' );
include_once( dirname( __FILE__ ) . '/lib/errors/class-affinipay-invalid-request-error.php' );
include_once( dirname( __FILE__ ) . '/lib/errors/class-affinipay-wordpress-error.php' );
include_once( dirname( __FILE__ ) . '/lib/errors/class-affinipay-error-message.php' );

/** Infrastructure */
include_once( dirname( __FILE__ ) . '/lib/class-affinipay-client.php' );

/** WordPress */
include_once( dirname( __FILE__ ) . '/lib/interfaces/AffiniPayWordPressInterface.php' );
include_once( dirname( __FILE__ ) . '/lib/class-affinipay-wordpress.php' );
include_once( dirname( __FILE__ ) . '/lib/class-affinipay-settings.php' );
include_once( dirname( __FILE__ ) . '/lib/class-affinipay-shortcode.php' );
include_once( dirname( __FILE__ ) . '/lib/class-affinipay-payment-form.php' );
include_once( dirname( __FILE__ ) . '/lib/class-affinipay-receipt.php' );
