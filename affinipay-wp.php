<?php
/**
 * AffiniPay_WP
 *
 * @package AffiniPayWP
 * @version 1.0.5
 * @description React AffiniPay WP plugin
 */

/*
Plugin Name: AffiniPay Payments
Plugin URI: https://affinipay.com
Description: Make Payments using the AffiniPay Gateway
Author: AffiniPay, LLC
Version: 1.0.5
Author URI: https://affinipay.com
*/

namespace AffiniPayWP;

// Avoid direct access to this piece of code
if ( ! function_exists( 'add_action' ) ) {
	header( 'Location: /' );
	exit;
}

//$IS_DEVELOPMENT=true;
$IS_DEVELOPMENT=false;

define( 'AFFINIPAY_WP_VERSION', '1.1' );
define( 'AFFINIPAY_WP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AFFINIPAY_WP_PLUGIN_URL',  plugin_dir_url(__FILE__) );
define( 'AFFINIPAY_WP_PLUGIN_JS_URL', plugin_dir_url(__FILE__) . 'js' );

if($IS_DEVELOPMENT){
    define( 'AFFINIPAY_FIELDS_JS_URL', 'https://cdn.affinipay.com/hostedfields/qa/fieldGen.js');
    define( 'AFFINIPAY_API_URL', 'https://gateway.qa.affinipay.com/v1' );
}
else{
    define( 'AFFINIPAY_FIELDS_JS_URL', 'https://cdn.affinipay.com/hostedfields/release/fieldGen.js');
    define( 'AFFINIPAY_API_URL', 'https://api.chargeio.com/v1' );
}

if(!class_exists('ChargeIO')){
	include_once( dirname( __FILE__ ) . '/vendor/affinipay/chargeio-php/lib/ChargeIO.php' );
}

include_once( dirname( __FILE__ ) . '/lib/data/services.php' );
$client = \AffiniPayWP\Data\Services::init();

include_once( dirname( __FILE__ ) . '/lib/settings.php' );
include_once( dirname( __FILE__ ) . '/lib/plugin.php' );
include_once( dirname( __FILE__ ) . '/lib/payment-shortcode.php' );

$affinipay = new Plugin(__FILE__, $client);
register_activation_hook( __FILE__, array( $affinipay, 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( $affinipay, 'plugin_deactivate' ) );
add_shortcode( 'affinipay-payment', array('AffiniPayWP\PaymentShortCode','generate') );
