<?php

namespace AffiniPayWP;
$GLOBALS['affiniConfig'] = array();

use AffiniPayWP\Data\Services;

class InvalidAccountError extends \Error
{
}

class InvalidPaymentTypeError extends \Error
{
}

class PaymentShortCode
{
    static $defaultAtts = [
        'title' => 'Make a Payment',
        'button_text' => 'Submit Payment',
        'amount' => 0,
        'account' => '',
        'amount_field' => 'no',
        'customer_fields' => 'no',
        'reference_field' => 'no',
        'customer_email_field' => 'yes',
        'customer_name_field' => 'no',
        'customer_name_field' => 'no',
        'customer_bank_account_holder_name' => 'no',
        'customer_bank_account_holder_firstname' => 'no',
        'customer_bank_account_holder_lastname' => 'no',
        'customer_address_field' => 'no',
        'customer_address2_field' => 'no',
        'customer_city_field' => 'no',
        'customer_state_field' => 'no',
        'customer_country_field' => 'no',
        'customer_postalcode_field' => 'no',
        'customer_phone_field' => 'no',
        'type' => ''
    ];

    function generate($atts = [], $content = null, $tag = null)
    {
        $attributes = self::filter(shortcode_atts(self::$defaultAtts, $atts, $tag));
        $result = '';
        if($atts['account'] == ''){
            return self::showError('No account value was defined on the payment shortcode.');
        }

        if($atts['type'] != 'creditcard' && $atts['type'] != 'echeck'){
            return self::showError('Invalid type on payment shortcode, type must be either creditcard or echeck');
        }

        try {
            $required = self::getRequiredFields($atts);
        } catch (\ChargeIO_AuthenticationError $e) {
            return self::showError('Check your API key in your WordPress AffiniPay Payment Gateway Settings page.');
        } catch (InvalidAccountError $e) {
            return self::showError( 'The account ' . $atts['account'] . ' does not match an active account for this merchant.');
        }

        self::addScripts($attributes, $required);
        return $result . "<div id='root'></div>";
    }

    function showError($message)
    {
        if(current_user_can('edit_post')){
            return '<div class="affinipay_error"><h3>Payment Form Error</h3> <span>' . $message . '</span></div>';
        }

        return '<div>Payment Form Error - Contact Site Administrator</div>';
    }

    function getRequiredFields($atts)
    {
        $client = Services::init();
        $required = $client->getRequiredFields($atts['account']);

        if ($required == null) {
            throw new InvalidAccountError();
        }

        return $required;
    }

    static function addScripts($payment, $required)
    {
        $config = array(
            'route' => 'Payment',
            'publicKey' => get_option('affinipay_public_key'),
            'data' => array(
                'fields' => $payment,
                'required' => $required,
                'receiptPage' => PaymentShortCode::getReceiptPage(),
                'nonce' => wp_create_nonce( 'wp_rest' )
            )
        );

        array_push($GLOBALS['affiniConfig'], $config);

        wp_localize_script('affini-payment', 'affinipay_options', $GLOBALS['affiniConfig']);
        wp_enqueue_style('affini-styles');
        wp_enqueue_script('hosted-io');
        wp_enqueue_script('affini-payment');
    }

    /**
     * Return public/private Post Permalink url or empty if the page is not public/private or doesn't exist
     *
     * @static
     *
     * @return string
     */
    static function getReceiptPage(){
        $pageId = get_option('affinipay_receipt_page');
        if($pageId == 'new' || $pageId == ''){
            return '';
        }

        $status = get_post_status($pageId);
        if($status != 'publish' && $status != 'private') {
            return '';
        }

        $permalink = get_post_permalink($pageId);
        if($permalink == home_url()){
            return '';
        }

        return $permalink;
    }

    function filter($fields)
    {
        $shortcode_amount = $fields["amount"];
        $shortcode_amount_cents = strval(max((int)(floor(floatval($shortcode_amount)*100.0)), 0));
        $fields["amount"] = $shortcode_amount_cents;
        $fields["amount_field_value"] = number_format(max(floatval($shortcode_amount),0), 2);

        // Invalid or no amount specified so make the amount field editable
        if($fields["amount"] == 0){
            $fields["amount_field_value"] = "";
            $fields["amount_field"] = "yes";
        }

        foreach (self::$defaultAtts as $key => $value) {
            if ($value !== "no" && $value !== "yes") continue;
            $fields[$key] = $fields[$key] == "yes" ? true : false;
        }

        if($fields["type"] == 'creditcard') {
            $fields['customer_name_field'] = true;
            $fields['customer_postalcode_field'] = true;
        } elseif ($fields["type"] == 'echeck') {
            $fields['customer_bank_account_holder_name'] = true;
            $fields['customer_bank_account_holder_firstname'] = true;
            $fields['customer_bank_account_holder_lastname'] = true;
        } else {
            return self::showError('Invalid type on payment shortcode, type must be either creditcard or echeck');
        }

        return $fields;
    }
}
