<?php
/**
 *
 * AffiniPay Test Response
 *
 * @package affinipay-wordpress
 */

namespace AffiniPayWordPress;


if ( ! isset( $public_key_results ) ) {
	$public_key_results = null;
}
if ( ! isset( $secret_key_results ) ) {
	$secret_key_results = null;
}
if ( ! isset( $account_id_results ) ) {
	$account_id_results = false;
}

$merchant = $secret_key_results;
$merchant_accounts = $merchant->attributes['merchant_accounts'];
$ach_accounts = $merchant->attributes['ach_accounts'];
$errors = $api_test_errors;

$result_test_public = ($public_key_results instanceof AffiniPay_Token ? 'PASS' : 'FAIL');
$result_test_secret = ($secret_key_results instanceof AffiniPay_Merchant ? 'PASS' : 'FAIL');
$result_test_account = ( true === $account_id_results ? 'PASS' : 'FAIL');

$reason_public = (array_key_exists( 'public', $errors ) ? $errors['public'] : null);
$reason_secret = (array_key_exists( 'secret', $errors ) ? $errors['secret'] : null);
$reason_account = (array_key_exists( 'account', $errors ) ? $errors['account'] : null);

?>

<div class="affinipay_test_response">
	<h1>Test Results: </h1>
	<div class="affinipay_test_header_row">
		<div class="col-sm-6">Test</div>
		<div class="col-sm-6">Status</div>
	</div>
	<div class="affinipay_test_row">
		<div class="col-sm-6">Public Key Test</div>
		<div class="col-sm-6"><?php echo esc_html( $result_test_public ); ?></div>
		<div class="col-sm-12"><?php echo esc_html( $reason_public ); ?></div>
	</div>
	<div class="affinipay_test_row">
		<div class="col-sm-6">Secret Key Test</div>
		<div class="col-sm-6"><?php echo esc_html( $result_test_secret );  ?></div>
		<div class="col-sm-12"><?php echo esc_html( $reason_secret ); ?></div>
	</div>
	<div class="affinipay_test_row">
		<div class="col-sm-6">Account ID Test</div>
		<div class="col-sm-6"><?php echo esc_html( $result_test_account ); ?></div>
		<div class="col-sm-12"><?php echo esc_html( $reason_account ); ?></div>
	</div>

	<?php
	if ( count( $errors ) == 0 ) {
	?>
	<div class="test_success"><h2>Merchant Information</h2></div>
	<div class="merchant_wrapper">

	<div><?php echo esc_html( $merchant->attributes['name'] ); ?></div>
	<div><?php echo esc_html( $merchant->attributes['address1'] ) . ' ' . esc_html( $merchant->attributes['address2'] ); ?></div>
	<div><?php echo esc_html( $merchant->attributes['city'] ) . ', ' . esc_html( $merchant->attributes['state'] ) . ' ' . esc_html( $merchant->attributes['postal_code'] ); ?></div>

	<h3>Merchant Accounts</h3>

	<?php
	if ( count( $merchant_accounts ) > 0 ) {
		?>
		<table width="100%" cellspacing="0" class="merchant_account_list">
			<tr>
				<th>Name</th>
				<th>Status</th>
				<th>Primary</th>
				<th>Trust</th>
			</tr>
			<?php
			foreach ( $merchant_accounts as $ma ) {
				$primary_class = ($ma['primary'] == 1 ? 'primary_account' : '');
				$is_primary = ($ma['primary'] == 1 ? 'YES' : 'NO');
				$is_trust = ($ma['trust_account'] == 1 ? 'YES' : 'NO');
				?>
				<tr class="<?php echo esc_html( $primary_class ); ?>">
					<td><?php echo esc_html( $ma['name'] ); ?></td>
					<td><?php echo esc_html( $ma['status'] ); ?></td>
					<td class="center"><?php echo esc_html( $is_primary ); ?></td>
					<td class="center"><?php echo esc_html( $is_trust ); ?></td>
					</tr>
				<?php }  // End foreach().
?>
			</table>
		<?php
	} else { ?>
		 <h4>No Merchant Accounts Found</h4>
		<?php
	}


	if ( count( $ach_accounts ) > 0 ) {
		?>
		<h3>ACH Accounts</h3>
		<table width="100%" cellspacing="0" class="merchant_account_list">
			<tr>
				<th>Name</th>
				<th>Status</th>
				<th>Banking</th>
			</tr>

			<?php
			foreach ( $ach_accounts as $ach ) {
				?>
				<tr>
					<td><?php echo esc_html( $ach['name'] ); ?></td>
					<td><?php echo esc_html( $ach['status'] ); ?></td>
					<td colspan="2">
						<?php echo esc_html( $ach['bank_name'] ); ?><br/>
						<?php echo esc_html( $ach['account_type'] ); ?><br/>

					</td>
					</tr>

					<?php
			} // End foreach().
?>
			</table>
			<?php

	}
	}// End if().
		?>
	</div>
</div>
