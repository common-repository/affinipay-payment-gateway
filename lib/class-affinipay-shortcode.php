<?php

namespace AffiniPayWordPress;

class AffiniPay_ShortCode {

	var $defaults;
	var $rendered;
	var $payment_form;
	var $receipt_page;

	function __construct() {
		$this->atts = [];
		$this->content = null;
		$this->tag = '';
		$this->rendered = [];
	}

	function init() {
		$this->payment_form_init();
		$this->receipt_page_init();
	}

	function payment_form_init() {
		$this->payment_form = new AffiniPay_Payment_Form;
		add_shortcode( 'affinipay-payment', array( $this->payment_form, 'init' ) );
		add_shortcode( 'affinipay-payment-form', array( $this->payment_form, 'render_payment_form' ) );
		add_shortcode( 'affinipay-payment-form-customer', array( $this->payment_form, 'render_customer_fields' ) );
		add_shortcode( 'affinipay-payment-form-recurring', array( $this->payment_form, 'render_recurring_section' ) );
		add_shortcode( 'affinipay-payment-amount-field', array( $this->payment_form, 'render_amount_field' ) );
		add_shortcode( 'affinipay-payment-reference-field', array( $this->payment_form, 'render_reference_field' ) );
		add_shortcode( 'affinipay-customer-email-field', array( $this->payment_form, 'render_customer_email_field' ) );
		add_shortcode( 'affinipay-customer-name-field', array( $this->payment_form, 'render_customer_name_field' ) );
		add_shortcode( 'affinipay-customer-address1-field', array( $this->payment_form, 'render_customer_address1_field' ) );
		add_shortcode( 'affinipay-customer-address2-field', array( $this->payment_form, 'render_customer_address2_field' ) );
		add_shortcode( 'affinipay-customer-city-field', array( $this->payment_form, 'render_customer_city_field' ) );
        add_shortcode( 'affinipay-customer-country-field', array( $this->payment_form, 'render_customer_country_field' ) );
		add_shortcode( 'affinipay-customer-state-field', array( $this->payment_form, 'render_customer_state_field' ) );
		add_shortcode( 'affinipay-customer-postal-code-field', array( $this->payment_form, 'render_customer_postal_code_field' ) );
		add_shortcode( 'affinipay-customer-phone-field', array( $this->payment_form, 'render_customer_phone_field' ) );
		add_shortcode( 'affinipay-payment-form-button', array( $this->payment_form, 'render_payment_form_button' ) );
		add_shortcode( 'affinipay-payment-form-open', array( $this->payment_form, 'render_payment_form_open' ) );
		add_shortcode( 'affinipay-payment-form-close', array( $this->payment_form, 'render_payment_form_close' ) );
		add_shortcode( 'affinipay-payment-form-wrapper-open', array( $this->payment_form, 'render_payment_form_wrapper_open' ) );
		add_shortcode( 'affinipay-payment-form-wrapper-close', array( $this->payment_form, 'render_payment_form_wrapper_close' ) );
	}

	function receipt_page_init() {
		$this->receipt_page = new AffiniPay_Receipt;
		add_shortcode( 'affinipay-receipt', array( $this->receipt_page, 'render' ) );
		add_shortcode( 'affinipay-receipt-date', array( $this->receipt_page, 'render_date' ) );
		add_shortcode( 'affinipay-receipt-title', array( $this->receipt_page, 'render_title' ) );
		add_shortcode( 'affinipay-receipt-amount', array( $this->receipt_page, 'render_amount' ) );
		add_shortcode( 'affinipay-receipt-authcode', array( $this->receipt_page, 'render_authcode' ) );
		add_shortcode( 'affinipay-receipt-transaction-id', array( $this->receipt_page, 'render_transaction_id' ) );
		add_shortcode( 'affinipay-receipt-currency', array( $this->receipt_page, 'render_currency' ) );
		add_shortcode( 'affinipay-receipt-card-type', array( $this->receipt_page, 'render_card_type' ) );
		add_shortcode( 'affinipay-receipt-card-mask', array( $this->receipt_page, 'render_card_mask' ) );
		add_shortcode( 'affinipay-receipt-card-month', array( $this->receipt_page, 'render_card_month' ) );
		add_shortcode( 'affinipay-receipt-card-year', array( $this->receipt_page, 'render_card_year' ) );
		add_shortcode( 'affinipay-receipt-cvv-result', array( $this->receipt_page, 'render_cvv_result' ) );
		add_shortcode( 'affinipay-receipt-avs-result', array( $this->receipt_page, 'render_avs_result' ) );
		add_shortcode( 'affinipay-receipt-customer-name', array( $this->receipt_page, 'render_customer_name' ) );
		add_shortcode( 'affinipay-receipt-customer-address1', array( $this->receipt_page, 'render_address' ) );
		add_shortcode( 'affinipay-receipt-customer-address2', array( $this->receipt_page, 'render_address2' ) );
		add_shortcode( 'affinipay-receipt-customer-city', array( $this->receipt_page, 'render_city' ) );
		add_shortcode( 'affinipay-receipt-customer-state', array( $this->receipt_page, 'render_state' ) );
		add_shortcode( 'affinipay-receipt-customer-postal', array( $this->receipt_page, 'render_postal' ) );
		add_shortcode( 'affinipay-receipt-customer-email', array( $this->receipt_page, 'render_email' ) );
		add_shortcode( 'affinipay-receipt-customer-phone', array( $this->receipt_page, 'render_phone' ) );
	}

}
