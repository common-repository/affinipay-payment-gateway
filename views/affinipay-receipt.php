<?php

namespace AffiniPayWordPress;

$date = (array_key_exists( 'created', $this->transaction ) ? $this->format_date( $this->transaction['created'] ) : null);
$amount = (array_key_exists( 'amount', $this->transaction ) ? $this->format_money( $this->transaction['amount'] ) : null);
$customer_name = (array_key_exists( 'name', $this->transaction['method'] ) ? $this->transaction['method']['name'] : null);
$address = (array_key_exists( 'address1', $this->transaction['method'] ) ? $this->transaction['method']['address1'] : null);
$address2 = (array_key_exists( 'address2', $this->transaction['method'] ) ? $this->transaction['method']['address2'] : null);
$city = (array_key_exists( 'city', $this->transaction['method'] ) ? $this->transaction['method']['city'] : null);
$state = (array_key_exists( 'state', $this->transaction['method'] ) ? $this->transaction['method']['state'] : null);
$postal = (array_key_exists( 'postal_code', $this->transaction['method'] ) ? $this->transaction['method']['postal_code'] : null);
$email = (array_key_exists( 'email', $this->transaction['method'] ) ? $this->transaction['method']['email'] : null);
$phone = (array_key_exists( 'phone', $this->transaction['method'] ) ? $this->transaction['method']['phone'] : null);
$card_type = (array_key_exists( 'card_type', $this->transaction['method'] ) ? $this->transaction['method']['card_type'] : null);
$number = (array_key_exists( 'number', $this->transaction['method'] ) ? $this->transaction['method']['number'] : null);
$exp_month = (array_key_exists( 'exp_month', $this->transaction['method'] ) ? $this->transaction['method']['exp_month'] : null);
$exp_year = (array_key_exists( 'exp_year', $this->transaction['method'] ) ? $this->transaction['method']['exp_year'] : null);
$transaction_id = (array_key_exists( 'id', $this->transaction ) ? $this->transaction['id'] : null);
$auth_code = (array_key_exists( 'authorization_code', $this->transaction ) ? $this->transaction['authorization_code'] : null);
$cvv_result = (array_key_exists( 'cvv_result', $this->transaction ) ? $this->transaction['cvv_result'] : null);
$avs_result = (array_key_exists( 'avs_result', $this->transaction ) ? $this->transaction['avs_result'] : null);

?>

<div class="affinipay_receipt">
	<h2>Transaction Approved</h2>
	<div class="row">
		<div class="col-sm-6">
			<h3>Customer</h3>
			<div>
				<?php echo esc_html( $customer_name ); ?><br />
				<?php echo esc_html( $address ) . ' ' . esc_html( $address2 ); ?><br />
				<?php echo esc_html( $city ) . ', ' . esc_html( $state ) . ' ' . esc_html( $postal ); ?><br />
				<?php echo esc_html( $email ); ?><br />
				<?php echo esc_html( $phone ); ?><br />
			</div>
		</div>
		<div class="col-sm-6">
			<h3>Transaction</h3>
			<div>
				Amount: <?php echo esc_html( $amount ); ?><br />
				Date: <?php echo esc_html( $date ); ?><br />
				Auth Code: <?php echo esc_html( $auth_code ); ?><br />
				<?php echo esc_html( $card_type ); ?>: <?php echo esc_html( $number ); ?><br />
				Expires: <?php echo esc_html( $exp_month ) . '/' . esc_html( $exp_year ); ?><br />
				CVV Result: <?php echo esc_html( $cvv_result ); ?><br />
				AVS Result: <?php echo esc_html( $avs_result ); ?><br />
			</div>
		</div>
	</div>
</div>
