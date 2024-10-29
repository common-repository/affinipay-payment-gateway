<?php
/**
 *
 * Admin Settings View
 *
 * @package affinipay-wordpress
 */

namespace AffiniPayWordPress;

$pages = get_pages();
$posts = get_posts();

$available = array_merge($pages, $posts);
$merchant_accounts = [];
try {
    $merchant_accounts = $this->affinipay_client->getMerchant()->attributes['merchant_accounts'];
}
catch(\ChargeIO_Error $e){}
?>
<h1>AffiniPay Payment Gateway Settings</h1>
<div class="affinipay_wrapper">
    <div class="col-sm-8">
        <div class="section">
            <h3>Current Configuration</h3>
            <div class="frm_row">
                <label>Public Key</label>
                <input title="Secret Key" class="form-control" type="text" name="affinipay_public_key"
                       id="affinipay_public_key" value="<?php echo esc_html(get_option('affinipay_public_key')); ?>">
            </div>
            <div class="frm_row">
                <label>Secret Key</label>
                <input title="Secret Key" class="form-control" type="text" name="affinipay_secret_key"
                       id="affinipay_secret_key" value="<?php echo esc_html(get_option('affinipay_secret_key')); ?>">
            </div>


            <div class="frm_row">
                <label for="affinipay_receipt_page">Receipt Page</label>
                <select class="form-control" id="affinipay_receipt_page" name="affinipay_receipt_page">
                    <option value="new">(Default Receipt Page)</option>
                    <?php

                    $receipt_page = get_option('affinipay_receipt_page');
                    foreach ($available as $p) {
                        if (!has_shortcode($p->post_content, 'affinipay-payment')) {
                            if ($receipt_page == $p->ID) {
                                echo '<option value="' . esc_html($p->ID) . '" selected="selected">' .
                                    esc_html($p->post_title) .
                                    '</option>';
                            } else {
                                echo '<option value="' . esc_html($p->ID) . '">' .
                                    esc_html($p->post_title) .
                                    '</option>';
                            }
                        }
                    }
                    ?>

                </select>
            </div>
            <button type="submit" class="btn btn-lg" id="btn_affinipay_settings_submit">Save Settings</button>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="section" id="affinipay_test_results">

            <?php
            if ($merchant_accounts) {
                include 'admin-accounts-response.php';
            }
            else{
                include 'admin-settings-default.php';
            }
            ?>
        </div>
    </div>
</div>
