<?php
?>
<h3>Start Collecting Payments</h3>
Account: <select id="merchant_accounts" style="width:50%">
    <?php foreach ( $merchant_accounts as $ma ) { ?>
        <option value="<?php echo esc_html( $ma['id'] ); ?>"><?php
            echo esc_html( $ma['name'] );
            if($ma['trust_account'] == 1) echo " (Trust)";
            ?>

        </option>
    <?php } ?>
</select>
<p>Add the following shortcode to the content area on any page to display the payment form.</p>
<textarea id="shortcode_display" style="overflow: auto;-webkit-box-shadow: none;" readonly>
[affinipay-payment
    account=
    amount=29.95]
</textarea>

<script type="text/javascript">
    var shortCode = '[affinipay-payment account=]';
    (function ($) {
        var accounts = $('#merchant_accounts'),
            shortDisplay = $('#shortcode_display'),
            shortFromAccount = function(account){
                return shortCode.replace('account=', 'account=' + account);
            },
            setDisplay = function(){
                shortDisplay.text(shortFromAccount(accounts.val()));
                shortDisplay.fadeOut(100).fadeIn(100)
            };

        accounts.change(setDisplay);
        shortDisplay.click(function(){
            shortDisplay.select();
        });
        setDisplay();
    })(jQuery)
</script>