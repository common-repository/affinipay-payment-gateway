<?php
/**
 * AffiniPay_Wordpress
 *
 * @package AffiniPayWordPress
 * @version 1.0
 * @description Make Payments on your website using the AffiniPay Payment Gateway
 */

/*
Plugin Name: AffiniPay Payment Gateway
Plugin URI: https://affinipay.com
Description: Make Payments using the AffiniPay Gateway
Author: AffiniPay, LLC
Version: 1.0
*/

namespace AffiniPayWordPress;

if ( ! function_exists( 'add_action' ) ) {
	echo 'Silence is Golden!';
	exit;
}

define( 'AFFINIPAY_VERSION', '1.0' );
define( 'AFFINIPAY_MINIMUM_WP_VERSION', '4.7' );
define( 'AFFINIPAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AFFINIPAY_PLUGIN_URL',  plugin_dir_url(__FILE__));
define( 'AFFINIPAY_PLUGIN_JS_URL', plugin_dir_url(__FILE__) . 'js' );
define( 'AFFINIPAY_API_URL', 'https://api.chargeio.com/v1' );
define( 'AFFINIPAY_CLIENT_JS_URL', 'https://api.chargeio.com/assets/api/v1/chargeio.min.js' );

include_once( 'affinipay-bootstrap.php' );

$affinipay = new AffiniPay_WordPress(__FILE__);
register_activation_hook( __FILE__, array( $affinipay, 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( $affinipay, 'plugin_deactivate' ) );
