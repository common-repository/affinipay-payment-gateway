<?php

namespace AffiniPayWordPress;

class AffiniPay_Payment_Form extends AffiniPay_ShortCode {

	var $atts;
	var $content;
	var $tag;
	var $rendered;
	var $policy;
	var $required;
	var $account_id;
	var $mode;
	private $valid_recurring;
	private $form_fields;
	private $affinipay_client;

	function __construct( $atts = array() ) {
		$this->rendered = array();
		$this->policy = array();
		$this->affinipay_client = new AffiniPay_Client;
		$this->mode = get_option( 'affinipay_operating_mode' );

		foreach ( $atts as $att ) {
			array_push( $this->atts, $att );
		}
		$this->valid_recurring = array(
			'MONTH',
			'MONTHLY',
			'WEEK',
			'WEEKLY',
			'YEAR',
			'YEARLY',
		);

		$this->form_fields = array(
			'reference' => array(
				'field' => 'reference',
				'label' => 'Reference',
				'placeholder' => 'Example: Invoice 123',
				'validation' => 'reference',
                'maxlength' => "128"
			),
			'amount' => array(
				'field' => 'amount',
				'label' => 'Amount',
				'placeholder' => '29.95',
				'validation' => 'numeric',
			),
			'customer_email' => array(
				'field' => 'email',
				'label' => 'Email Address',
				'placeholder' => 'email@example.com',
				'validation' => 'email',
			),
			'customer_name' => array(
				'field' => 'name',
				'label' => 'Cardholder Name',
				'placeholder' => 'Joe Cardholder',
				'validation' => 'alpha'
			),
			'customer_address' => array(
				'field' => 'address1',
				'label' => 'Mailing Address',
				'placeholder' => '3700 N Capital of Texas',
				'validation' => 'any',
			),
			'customer_address2' => array(
				'field' => 'address2',
				'label' => 'Apt / Suite',
				'placeholder' => '',
				'validation' => null,
			),
			'customer_city' => array(
				'field' => 'city',
				'label' => 'City',
				'placeholder' => 'Austin',
				'validation' => 'alpha',
			),
            'customer_country' => array(
                'field' => 'country',
                'label' => 'Country',
                'placeholder' => '',
                'validation' => 'alphanumeric',
            ),
			'customer_state' => array(
				'field' => 'state',
				'label' => 'State',
				'placeholder' => 'TX',
				'validation' => 'state',
			),
			'customer_postal_code' => array(
				'field' => 'postal_code',
				'label' => 'Postal Code',
				'placeholder' => '78730',
				'validation' => 'postal_code',
                'maxlength' => "9"
			),
			'customer_phone' => array(
				'field' => 'phone',
				'label' => 'Phone',
				'placeholder' => '(123) 456-7890',
				'validation' => 'phone',
                'maxlength' => '22'
			),
			'number' => array(
				'field' => 'number',
				'label' => 'Card Number',
				'placeholder' => '4242 4242 4242 4242',
				'validation' => 'cc',
                'required' => true
			),
			'exp_month' => array(
				'field' => 'exp_month',
				'label' => 'Month',
				'placeholder' => '01',
				'validation' => 'month',
			),
			'exp_year' => array(
				'field' => 'exp_year',
				'label' => 'Year',
				'placeholder' => '2018',
				'validation' => 'year',
			),
			'cvv' => array(
				'field' => 'cvv',
				'label' => 'CVV',
				'placeholder' => '123',
				'validation' => 'cvv',
			),
		);
	}

	function init( $atts = [], $content = null, $tag = null ) {
		$output = null;
		$this->atts = array_change_key_case( (array) $atts, CASE_LOWER );
		$this->content = $content;

		$this->atts = shortcode_atts([
			'title' => 'Make a Payment',
			'button_text' => 'Submit Payment',
			'amount' => 0,
			'account' => '',
			'amount_field' => 'no',
			'customer_fields' => 'no',
			'recurs' => 'no',
			'recurring_description' => '',
			'recurring_interval' => 1,
			'reference_field' => 'no',
			'customer_email_field' => 'yes',
			'customer_name_field' => 'yes',
			'customer_address_field' => 'no',
			'customer_address2_field' => 'no',
			'customer_city_field' => 'no',
            'customer_country_field' => 'no',
			'customer_state_field' => 'no',
			'customer_postalcode_field' => 'no',
			'customer_phone_field' => 'no'
		], $atts, $tag);

		if (!is_ssl()) {
			return '<div class="alert alert-danger">' . 'Payment form unavailable because an SSL connection was not detected. Contact the webmaster.' . '</div>';
		}

	    try {
	        if(!$this->get_required_fields()) return $this->showInvalidAccount();
	    } catch ( \ChargeIO_Error $e ) {
	        if ( $e instanceof AffiniPay_AuthenticationError ) {
	            if ( $this->affinipay_client->operating_mode != 'live' ) {
	                return '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
	            }
	        }
	    }

	    if($this->atts['account'] == ''){
	        return $this->showInvalidAccount();
	    }

    	if ( $this->is_sane() ) {
			return $this->render();
		} else {
			if ( $this->mode != 'live' ) {
				return '<div class="affinipay_error">Invalid Configuration. Please check your API keys.</div>';
			}
		}
	}

	function showInvalidAccount(){
        return '<div class="affinipay_error">Invalid Shortcode - Account not valid</div>';
    }

	function get_required_fields() {
	    $hasValidAccount = false;
		try {
			$merchant = $this->affinipay_client->getMerchant();
            foreach ( $merchant->attributes['merchant_accounts'] as $account ) {
                if ( $account['id'] == $this->atts['account']  ) {
                    $hasValidAccount = true;
                    $this->required = $account['required_payment_fields'];
                    break;
                }
            }
		} catch ( \ChargeIO_Error $e ) {
			throw $e;
		}

		return $hasValidAccount;
	}

	public function render() {
		$output = null;
		$output .= apply_filters( 'affinipay_before_payment_form', '' );
        $output .= do_shortcode( '[affinipay-payment-form-open]' );
		$output .= do_shortcode( '[affinipay-payment-form-wrapper-open]' );

		if ( array_key_exists( 'customer_fields', $this->atts ) ) {
			if ( $this->atts['customer_fields'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_customer_fields', '' );
				$output .= do_shortcode( '[affinipay-payment-form-customer]' );
				$output .= apply_filters( 'affinipay_after_render_customer_fields', '' );
			}
		}

		if ( array_key_exists( 'amount_field', $this->atts ) ) {
			$output .= apply_filters( 'affinipay_before_render_amount_field', '' );
			$output .= do_shortcode( '[affinipay-payment-amount-field]' );
			$output .= apply_filters( 'affinipay_after_render_amount_field', '' );
		}

		$output .= '<input type="hidden" id="account" name="account" value="' . $this->atts['account'] . '" />';

		if ( array_key_exists( 'reference_field', $this->atts ) ) {
			if ( $this->atts['reference_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_reference_field', '' );
				$output .= do_shortcode( '[affinipay-payment-reference-field]' );
				$output .= apply_filters( 'affinipay_after_render_reference_field', '' );
			}
			if ( stristr( $this->required, 'reference' ) && ! in_array( 'reference', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_reference_field', '' );
				$output .= do_shortcode( '[affinipay-payment-reference-field]' );
				$output .= apply_filters( 'affinipay_after_render_reference_field', '' );
			}
		}

		if ( array_key_exists( 'customer_email_field', $this->atts ) ) {
			if ( $this->atts['customer_fields'] == 'yes' || $this->atts['customer_email_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_email_field', '' );
				$output .= do_shortcode( '[affinipay-customer-email-field]' );
				$output .= apply_filters( 'affinipay_after_render_email_field', '' );
			}
			if ( stristr( $this->required, 'email' ) && ! in_array( 'email', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_email_field', '' );
				$output .= do_shortcode( '[affinipay-customer-email-field]' );
				$output .= apply_filters( 'affinipay_after_render_email_field' );
			}
		}

		if ( array_key_exists( 'customer_name_field', $this->atts ) ) {
			if ( $this->atts['customer_name_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_name_field', '' );
				$output .= do_shortcode( '[affinipay-customer-name-field]' );
				$output .= apply_filters( 'affinipay_after_render_name_field', '' );
			}
			if ( stristr( $this->required, 'name' ) && ! in_array( 'name', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_name_field', '' );
				$output .= do_shortcode( '[affinipay-customer-name-field]' );
				$output .= apply_filters( 'affinipay_after_render_name_field', '' );
			}
		}

		if ( array_key_exists( 'customer_address_field', $this->atts ) ) {
			if ( $this->atts['customer_address_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_address_field', '' );
				$output .= do_shortcode( '[affinipay-customer-address1-field]' );
				$output .= apply_filters( 'affinipay_after_render_address_field', '' );
			}
			if ( stristr( $this->required, 'address1' ) && ! in_array( 'address1', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_address_field', '' );
				$output .= do_shortcode( '[affinipay-customer-address1-field]' );
				$output .= apply_filters( 'affinipay_after_render_address_field', '' );
			}
		}

		if ( array_key_exists( 'customer_address2_field', $this->atts ) ) {
			if ( $this->atts['customer_address2_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_address2_field', '' );
				$output .= do_shortcode( '[affinipay-customer-address2-field]' );
				$output .= apply_filters( 'affinipay_after_render_address2_field', '' );
			}
			if ( stristr( $this->required, 'address2' ) && ! in_array( 'address2', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_address2_field', '' );
				$output .= do_shortcode( '[affinipay-customer-address2-field]' );
				$output .= apply_filters( 'affinipay_after_render_address2_field', '' );
			}
		}

		if ( array_key_exists( 'customer_city_field', $this->atts ) ) {
			if ( $this->atts['customer_city_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_city_field', '' );
				$output .= do_shortcode( '[affinipay-customer-city-field]' );
				$output .= apply_filters( 'affinipay_after_render_city_field', '' );
			}
			if ( stristr( $this->required, 'city' ) && ! in_array( 'city', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_city_field', '' );
				$output .= do_shortcode( '[affinipay-customer-city-field]' );
				$output .= apply_filters( 'affinipay_after_render_city_field', '' );
			}
		}

        if ( array_key_exists( 'customer_country_field', $this->atts ) ) {
            if ( $this->atts['customer_fields'] == 'yes' || $this->atts['customer_country_field'] == 'yes' ) {
                $output .= apply_filters( 'affinipay_before_render_country_field', '' );
                $output .= do_shortcode( '[affinipay-customer-country-field]' );
                $output .= apply_filters( 'affinipay_after_render_country_field', '' );
            }
            if ( stristr( $this->required, 'country' ) && ! in_array( 'country', $this->rendered ) ) {
                $output .= apply_filters( 'affinipay_before_render_country_field', '' );
                $output .= do_shortcode( '[affinipay-customer-country-field]' );
                $output .= apply_filters( 'affinipay_after_render_country_field', '' );
            }
        }

		if ( array_key_exists( 'customer_state_field', $this->atts ) ) {
			if ( $this->atts['customer_state_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_state_field', '' );
				$output .= do_shortcode( '[affinipay-customer-state-field]' );
				$output .= apply_filters( 'affinipay_after_render_state_field', '' );
				array_push( $this->rendered, 'state' );
			}
			if ( stristr( $this->required, 'state' ) && ! in_array( 'state', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_state_field', '' );
				$output .= do_shortcode( '[affinipay-customer-state-field]' );
				$output .= apply_filters( 'affinipay_after_render_state_field', '' );
				array_push( $this->rendered, 'state' );
			}
		}

		if ( array_key_exists( 'customer_postalcode_field', $this->atts ) ) {
			if ( $this->atts['customer_postalcode_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_postalcode_field', '' );
				$output .= do_shortcode( '[affinipay-customer-postal-code-field]' );
				$output .= apply_filters( 'affinipay_after_postalcode_field', '' );
			}
			if ( stristr( $this->required, 'postal_code' ) && ! in_array( 'postal_code', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_postalcode_field', '' );
				$output .= do_shortcode( '[affinipay-customer-postal-code-field]' );
				$output .= apply_filters( 'affinipay_after_postalcode_field', '' );
			}
		}

		if ( array_key_exists( 'customer_phone_field', $this->atts ) ) {
			if ( $this->atts['customer_phone_field'] == 'yes' ) {
				$output .= apply_filters( 'affinipay_before_render_phone_field', '' );
				$output .= do_shortcode( '[affinipay-customer-phone-field]' );
				$output .= apply_filters( 'affinipay_after_render_phone_field', '' );
			}
			if ( stristr( $this->required, 'phone' ) && ! in_array( 'phone', $this->rendered ) ) {
				$output .= apply_filters( 'affinipay_before_render_phone_field', '' );
				$output .= do_shortcode( '[affinipay-customer-phone-field]' );
				$output .= apply_filters( 'affinipay_after_render_phone_field', '' );
			}
		}

		$output .= apply_filters( 'affinipay_before_render_payment_fields', '' );
		$output .= do_shortcode( '[affinipay-payment-form]' );
		$output .= apply_filters( 'affinipay_after_render_payment_fields', '' );

		if ( array_key_exists( 'recurs', $this->atts ) ) {
			if ( in_array( strtoupper( $this->atts['recurs'] ), $this->valid_recurring ) ) {
				$this->set_recurring_variants();
				$output .= apply_filters( 'affinipay_before_render_recurring', '' );
				$output .= do_shortcode( '[affinipay-payment-form-recurring]' );
				$output .= apply_filters( 'affinipay_after_render_recurring', '' );
			}
		}

		$output .= do_shortcode( '[affinipay-payment-form-button]' );
		$output .= do_shortcode( '[affinipay-payment-form-wrapper-close]' );
        $output .= do_shortcode( '[affinipay-payment-form-close]' );
		$output .= apply_filters( 'affinipay_after_payment_form', '' );

		if ( ! is_null( $this->content ) ) {
			$output .= apply_filters( 'the_content', $this->content );
		}

		return $output;
	}

	public function renderOptions($start, $end){
	    $result = '';
        for ($current = $start; $current <= $end; $current++) {
            $item = str_pad($current, 2, "0", STR_PAD_LEFT);
            $result .= '
                <option value="'.$item.'">'.$item.'</option>
            ';
        }

        return $result;
    }

	public function render_payment_form() {
        $months = $this->renderOptions(1, 12);
        $years = $this->renderOptions(intval(date("Y")), intval(date("Y")) + 20);
		$output = null;
        $required = stristr( $this->required, 'cvv') ? 'required' : '';

		$output .= apply_filters( 'affinipay_before_payment_form', '' );
		$output .= '
            <div class="affinipay-number">
                <label for="number">' . $this->form_fields['number']['label'] . '*<span></span></label>
                <input type="text" class="form-control" id="' . $this->form_fields['number']['field'] . '" name="' . $this->form_fields['number']['field'] . '" placeholder="' . $this->form_fields['number']['placeholder'] . '" data-validate="' . $this->form_fields['number']['validation'] . '" required>
            </div>
            <div class="affinipay-exp_year">
                <label for="exp_year">Expiration Date*<span></span></label>
                 <select data-validate="month" id="' . $this->form_fields['exp_month']['field'] . '" name="' . $this->form_fields['exp_month']['field'] . '"  class="form-control">'
            . $months .
            '
                </select>
                <select data-validate="year" id="' . $this->form_fields['exp_year']['field'] . '" name="' . $this->form_fields['exp_year']['field'] . '"  class="form-control">'
                         . $years .
                        '
                </select>
            </div>
            <div class="affinipay-cvv">
                <label for="cvv">' . $this->form_fields['cvv']['label'] . '*<span></span></label>
                <input type="text" pattern="[0-9]*" maxlength="4" class="form-control" id="' . $this->form_fields['cvv']['field'] . '" name="' . $this->form_fields['cvv']['field'] . '" placeholder="' . $this->form_fields['cvv']['placeholder'] . '"' . $required . ' data-validate="' . $this->form_fields['cvv']['validation'] . '">
            </div>';
		$output .= apply_filters( 'affinipay_after_payment_form', '' );
		return $output;
	}

	function render_customer_fields() {
		$output = null;

		$output .= do_shortcode( '[affinipay-customer-name-field]' );
		$output .= do_shortcode( '[affinipay-customer-address1-field]' );
		$output .= do_shortcode( '[affinipay-customer-address2-field]' );
		$output .= do_shortcode( '[affinipay-customer-city-field]' );
		$output .= do_shortcode( '[affinipay-customer-state-field]' );
		$output .= do_shortcode( '[affinipay-customer-postal-code-field]' );
		$output .= do_shortcode( '[affinipay-customer-email-field]' );
		$output .= do_shortcode( '[affinipay-customer-phone-field]' );
		return $output;
	}

	function render_recurring_section() {

		$output = null;

		// if ($this->atts['recurring_fields'] == 'yes') {
		// $output .= '
		// <div class="frm_row">
		// <div class="col-sm-3">First Charge:</div>
		// <div class="col-sm-9">Today</div>
		// </div>
		// <div class="frm_row">
		// <div class="col-sm-3">Recurs:</div>
		// <div class="col-sm-6">
		// <select class="form-control" id="recurring_charge_recur_frequency" name="recurring_interval">
		// <option value="WEEKLY">Every Week</option>
		// <option value="BI_WEEKLY">Every Two Weeks</option>
		// <option selected="selected" value="MONTHLY">Every Month</option>
		// <option value="BI_MONTHLY">Twice a Month</option>
		// <option value="TWO_MONTHS">Every Two Months</option>
		// <option value="THREE_MONTHS">Every Three Months</option>
		// <option value="SIX_MONTHS">Every Six Months</option>
		// <option value="YEAR">Every Year</option>
		// </select>
		// </div>
		// <div class="col-sm-3">
		// on '. date('l') .'
		// </div>
		// </div>
		// <div class="frm_row">
		// <div class="col-sm-3">Ends:</div>
		// <div class="col-sm-5">
		// <input type="radio" id="recurring_ends_never" name="recuring_ends" value="0"> Never<br />
		// <input type="radio" id="recurring_ends_paid" name="recuring_ends" value="1"> When total paid
		// </div>
		// <div class="col-sm-4">
		// <input type="text" id="recurring_max_amount" name="max_amount" value="" placeholder="Total Amount">
		// </div>
		// </div>';
		// } else {
			$output .= '<input type="hidden" id="recurring_interval" name="recurring_interval" value="' . $this->atts['recurs'] . '">';
			$output .= '<input type="hidden" id="recurring_ends" name="recuring_ends" value="1" />';
		// }
		return $output;
	}

	function render_payment_form_wrapper_open() {
		return '<h1>' . $this->atts['title'] . '</h1><span>*Required fields</span><div class="affinipay_payment_form">';
	}

	function render_payment_form_wrapper_close() {
		return '</div>';
	}

	function render_payment_form_open() {
		return '<form id="frm_affinipay_payment" method="post">';
	}

	function render_payment_form_close() {
		return '</form>';
	}

	function render_amount_field() {
		if ( ! in_array( 'amount_field', $this->rendered ) ) {
			array_push( $this->rendered, 'amount_field' );
			if ( $this->atts['amount_field'] == 'yes' ) {
			    $data = $this->form_fields['amount'];
                $data['value'] = number_format($this->atts['amount'], 2);
				return $this->render_input_field( $data, true );
			} else {
				if ( $this->atts['amount'] == 0 ) {
					return $this->render_input_field( $this->form_fields['amount'], true );
				} else {

					return '<input type="hidden" id="amount" name="amount" value="' . number_format($this->atts['amount'],2) . '" placeholder="amount" data-validate="numeric" required/>';
				}
			}
		}
		return null;
	}

	function render_reference_field() {
		$content = $this->render_input_field( $this->form_fields['reference'] );
		return $content;
	}

	function render_customer_email_field() {
		$content = $this->render_input_field( $this->form_fields['customer_email'] );
		return $content;
	}

	function render_customer_name_field() {
		$content = $this->render_input_field( $this->form_fields['customer_name'] );
		return $content;
	}

	function render_customer_address1_field() {
		$content = $this->render_input_field( $this->form_fields['customer_address'] );
		return $content;
	}

	function render_customer_address2_field() {
		$content = $this->render_input_field( $this->form_fields['customer_address2'] );
		return $content;
	}

	function render_customer_city_field() {
		$content = $this->render_input_field( $this->form_fields['customer_city'] );
		return $content;
	}

    function render_customer_country_field() {
        $content = $this->render_input_field( $this->form_fields['customer_country'] );
        return $content;
    }

	function render_customer_state_field() {
		$content = $this->render_input_field( $this->form_fields['customer_state'] );
		return $content;
	}

	/**
	 * @return string
	 */
	function render_customer_postal_code_field() {
		$content = $this->render_input_field( $this->form_fields['customer_postal_code'] );
		return $content;
	}

	function render_customer_phone_field() {
		$content = $this->render_input_field( $this->form_fields['customer_phone'] );
		return $content;
	}

	function render_payment_form_button() {
		if ( ! in_array( 'payment_button', $this->rendered ) ) {
			array_push( $this->rendered, 'payment_button' );
			return '<button class="btn" id="btn_payment_submit">' . $this->atts['button_text'] . '</button>';
		}
	}

	function load_payment_form_ui_js() {
	?>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					$('#frm_affinipay_payment').AffiniPayPaymentForm();
				});
			})(jQuery);
		</script>
	<?php }

	public function submit_charge(){
	    $params = [];
		$amount = (array_key_exists( 'amount', $_POST ) ? $_POST['amount'] : null);
        $params["account_id"] = (array_key_exists( 'account_id', $_POST ) ? $_POST['account_id'] : null);
		$token = (array_key_exists( 'token', $_POST ) ? $_POST['token'] : null);
		$name = (array_key_exists( 'customer_name', $_POST ) ? $_POST['customer_name'] : null);
		$address = (array_key_exists( 'customer_address', $_POST ) ? $_POST['customer_address'] : null);
		$address2 = (array_key_exists( 'customer_address2', $_POST ) ? $_POST['customer_address2'] : null);
		$city = (array_key_exists( 'customer_city', $_POST ) ? $_POST['customer_city'] : null);
		$state = (array_key_exists( 'customer_state', $_POST ) ? $_POST['customer_state'] : null);
		$postal_code = (array_key_exists( 'customer_postal', $_POST ) ? $_POST['customer_postal'] : null);
		$email = (array_key_exists( 'customer_email', $_POST ) ? $_POST['customer_email'] : null);
		$phone = (array_key_exists( 'customer_phone', $_POST ) ? $_POST['customer_phone'] : null);
		$recur_interval = (array_key_exists( 'recurring_interval', $_POST ) ? $_POST['recurring_interval'] : null);
		$recur_ends = (array_key_exists( 'recurring_ends', $_POST ) ? $_POST['recurring_ends'] : null);

		if ( $name != null ) $params['name'] = $name;
		if ( $address != null ) $params['address1'] = $address;
		if ( $address2 != null ) $params['address2'] = $address2;
		if ( $city != null ) $params['city'] = $city;
		if ( $state != null ) $params['state'] = $state;
		if ( $postal_code != null ) $params['postal_code'] = $postal_code;
		if ( $email != null ) $params['email'] = $email;
        if ( $phone != null ) $params['phone'] = $phone;


		if ( $recur_interval != null && $recur_ends != null ) {
			$recurring = array(
				'interval_unit' => $recur_interval,
				'interval_delay' => $recur_ends,
			);
			$params['recur'] = $recurring;
		}

		try {
			$gw_resp = $this->affinipay_client->createCharge( $amount, $token, $params );
            wp_send_json( $gw_resp->attributes, 200);
		} catch ( \Exception $e ) {
            wp_send_json( json_decode($e->getJson()), 500);
        }
    }

	private function set_recurring_variants() {
		switch ( strtoupper( $this->atts['recurs'] ) ) {

			case 'MONTHLY':
				$this->atts['recurs'] = 'MONTH';
				break;

			case 'WEEKLY':
				$this->atts['recurs'] = 'WEEK';
				break;

			case 'YEARLY':
				$this->atts['recurs'] = 'YEAR';
				break;

			default:
				break;
		}
	}

	private function is_sane() {
		$valid_config = $this->check_empty_keys();
		if ( $this->mode == 'live' && $valid_config ) {
			$this->affinipay_client->set_operating_mode( 'live' );
			$valid_config = $this->verify_https();
		}

		return $valid_config && $this->atts['account'] != '';
	}

	private function check_empty_keys() {
        $public = get_option( 'affinipay_public_key' );
        $secret = get_option( 'affinipay_secret_key' );

        return $public !== '' && $secret !== '';
	}

	function verify_https() {
		// TODO: Research a more reliable way to determine if the payment form is requested over https
		return false;
	}

    /**
     * @param string
     * @param bool $forceRequired
     * @return string
     */
	function render_input_field( $opts, $forceRequired = false) {
		$field = (array_key_exists( 'field', $opts ) ? $opts['field'] : null);
		$placeholder = (array_key_exists( 'placeholder', $opts ) ? $opts['placeholder'] : null);
		$validation = (array_key_exists( 'validation', $opts ) ? $opts['validation'] : null);
		$label = (array_key_exists( 'label', $opts ) ? $opts['label'] : null);
        $maxlength = (array_key_exists( 'maxlength', $opts ) ? ' maxlength="'.$opts['maxlength'] . '" ': null);
        $value = (array_key_exists( 'value', $opts ) ? ' value="'.$opts['value'] . '" ': null);
        $required = $forceRequired || stristr( $this->required, $opts['field']) ? 'required' : '';
        if($required !== ''){
            $label .= '*';
        }

		if ( ! in_array( $field, $this->rendered ) ) {
			$html = '<div class="affinipay-' . $field . '">
                    <label for="' . $field . '">' . $label . '<span></span></label>
                    <input type="text" class="form-control" id="' . $field . '"' . $value . $maxlength . ' name="' . $field . '" placeholder="' . $placeholder . '"' . $required . ' data-validate="' . $validation . '" />
                    </div>';
			array_push( $this->rendered, $field );
			return $html;
		} else {
			return '';
		}
	}
}
