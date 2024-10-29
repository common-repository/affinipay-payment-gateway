(function( $ ) {
    ChargeIO.init({ public_key: affinipay.public_key });
    ChargeIO.ready(function() {

        // Cache Selector Objects
        var form = $('#frm_affinipay_payment'),
            btnSubmit = $('#btn_payment_submit'),
            amount = $('#amount'),
            account = $('#frm_affinipay_payment #account'),
            reference = $('#reference'),
            number = $('#number'),
            exp_month = $('#exp_month'),
            exp_year = $('#exp_year'),
            cvv = $('#cvv'),
            name = $('#name'),
            address1 = $('#address1'),
            address2 = $('#address2'),
            city = $('#city'),
            state = $('#state'),
            postal_code = $('#postal_code'),
            email = $('#email'),
            account = $("#account"),
            phone = $('#phone'),
            recurring_interval = $('#recurring_interval'),
            recurring_ends = $('#recurring_ends'),
            extendChargeIO = function(charge_params){
                if (typeof(name) !== 'undefined') charge_params.customer_name = name.val();
                if (typeof(address1) !== 'undefined') charge_params.customer_address = address1.val();
                if (typeof(address2) !== 'undefined') charge_params.customer_address2 = address2.val();
                if (typeof(city) !== 'undefined') charge_params.customer_city = city.val();
                if (typeof(state) !== 'undefined') charge_params.customer_state = state.val();
                if (typeof(postal_code) !== 'undefined') charge_params.customer_postal = postal_code.val();
                if (typeof(email) !== 'undefined') charge_params.customer_email = email.val();
                if (typeof(phone) !== 'undefined') charge_params.customer_phone = phone.val();
                if (typeof(recurring_interval) !== 'undefined') charge_params.recurring_interval = recurring_interval.val();
                if (typeof(recurring_ends) !== 'undefined') charge_params.recurring_ends = recurring_ends.val();
                return charge_params;
            },
            formComplete = function(data){
                var emailSent = data.method.email ? "<span>An email has been sent to " + data.method.email + "</span>" : '';
                form.fadeOut(100, function(){ $(this).replaceWith("<h2>Payment Successful</h2>" + emailSent) });
            },
            onSubmit = function(e){
                e.preventDefault();
                if(!affiniForm.validateAllFields()) return;
                $(e.currentTarget).prop("disabled", true);
                affiniForm.formatAmount(amount);
                var paymentJson = ChargeIO.payment_params(form);
                paymentJson.amount = parseInt(amount.val().replace(/\D/g,''));
                ChargeIO.create_token(paymentJson, function(token) {
                    var charge_params = {
                        action: 'affinipay_submit_charge',
                        amount: token.form_data.amount,
                        token: token.id,
                        redirect_url: affinipay.redirect_url,
                        account_id: account.val()
                    };

                    extendChargeIO(charge_params);
                    removeNameAttributes();

                    $.post(affinipay.ajax_url, charge_params)
                        .done(function(data) {
                            if(charge_params.redirect_url){
                                window.location = charge_params.redirect_url + '&payment=' + data.id;
                                return;
                            }

                            formComplete(data);
                        })
                        .fail(function(resp) {
                            var data = resp.responseJSON || { "messages": [ { message: "An unexpected error occurred" }] };
                            processErrorMessages(data);
                        })
                        .always(function(){
                            btnSubmit.attr('disabled', false);
                        });
                });
            };

        // Prevent the Form from submitting to the server
        form.on('submit', function(e) {
            e.preventDefault();
            return false;
        });

        btnSubmit.click(onSubmit);

        function processErrorMessages(err) {

            addNameAttributes();

            var formWrapper = $('.affinipay_payment_form'),
                form = $('#frm_affinipay_payment'),
                ap_error = $('#affinipay_error_message');

            if (ap_error.length) ap_error.html('').css('display', 'none');

            var err_message = '';

            err.messages.forEach(function(error) {
                err_message += error.message +'<br/>';
                updateFields(error);
            });

            var msg = '<div id="affinipay_error_message" class="affinipay_error_label alert alert-danger">'+err_message +'</div>';

            if (ap_error.length) {
                ap_error.html(err_message).css('display', 'block').addClass('alert alert-danger').fadeOut(100).fadeIn(100);
            } else {
                formWrapper.before(msg);
            }


            $('html, body').animate({
                scrollTop: $(formWrapper).offset().top - 120
            }, 300);
        }
    });

    function removeNameAttributes() {
        number.removeAttribute('name');
        exp_month.removeAttribute('name');
        exp_year.removeAttribute('name');
        cvv.removeAttribute('name');
    }

    function addNameAttributes() {
        number.name = 'number';
        exp_month.name = 'exp_month';
        exp_year.name = 'exp_year';
        cvv.name = 'cvv';
    }

    function updateFields(data) {
        if (data.code == 'card_number_invalid') applyToField(number, data.message);
        if (data.code == 'invalid_data') {
            if (data.context == 'method.number') applyToField(number, data.message);
            if (data.context == 'method.exp_month') applyToField(exp_month, data.message);
            if (data.context == 'method.exp_year')  applyToField(exp_year, data.message);
            if (data.context == 'method.cvv') applyToField(cvv, data.message);
        }
    }

    function applyToField(el, msg) {

        var sel = '#'+ $(el).attr('id') +' label span';
        var lbl = '#'+ $(el).attr('id') +' label';

        $(el).addClass('affinipay_error');
        $(lbl).addClass('affinipay_error');

        $(sel)
            .addClass('affinipay_error')
            .css('display', 'block')
            .html(msg);
    }

})(jQuery);


