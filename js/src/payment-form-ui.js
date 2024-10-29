'use strict';
var affiniForm = {
    isValidCard: function (raw) {
        var number = raw.replace(/\s/g, ''),
            len = number ? number.length : 0,
            arr = [0, 2, 4, 6, 8, 1, 3, 5, 7, 9],
            bit = 1,
            sum = 0;

        while (len--) {
            sum += !(bit ^= 1) ? parseInt(number[len], 10) : arr[number[len]];
        }

        return  sum % 10 === 0 && sum > 0;
    },
    formatAmount: function(field){
        var val = parseFloat(field.val().trim().replace('$',''));
        if(isNaN(val)) return field.val('');
        field.val(val.toFixed(2));
    }
};

(function ( $ ) {


    $.fn.AffiniPayPaymentForm = function() {
        var form = this;

        const card_types = {
            visa: {
                brand: 'visa',
                patterns: /^4/,
                format: /^(\d{4})(\d{4})?(\d{4})?(\d{4})?$/,
                length: [13, 16],
                cvcLength: [3],
                luhn: !0
            },
            mastercard: {
                brand: 'mastercard',
                patterns: /^(51|52|53|54|55|22|23|24|25|26|27)/,
                format: /^(\d{4})(\d{4})?(\d{4})?(\d{4})?$/,
                length: [16],
                cvcLength: [3],
                luhn: !0
            },
            amex: {
                brand: 'amex',
                patterns: /^(34|37)/,
                format: /^(\d{4})(\d{6})?(\d{5,6})?$/,
                length: [15],
                cvcLength: [3, 4],
                luhn: !0
            },
            diners: {
                brand: 'diners',
                patterns: /^(30|36|38|39)/,
                format: /^(\d{4})(\d{4})?(\d{4})?(\d{4})?$/,
                length: [14],
                cvcLength: [3],
                luhn: !0
            },
            discover: {
                brand: 'discover',
                patterns: /^(60|64|65)/g,
                format: /^(\d{4})(\d{4})?(\d{4})?(\d{4})?$/,
                length: [16],
                cvcLength: [3],
                luhn: !0
            },
            jcb: {
                brand: 'jcb',
                patterns: /^35/,
                format: /^(\d{4})(\d{4})?(\d{4})?(\d{4})?$/,
                length: [16],
                cvcLength: [3],
                luhn: !0
            }
        };
        const validation_tests = {
            numeric: /[0-9]/,
            alpha: /[A-Z]/i,
            any: /.+/,
            alphanumeric: /^[a-z\d\-_\s]+$/i,
            state: /^[A-Z]{2}$/,
            postal_code: /^[\w\d\s-]{3,9}$/,
            email: /^[^@\s]+@[^@\s]+\.[^@\s]+$/,
            month: /^\d{2}$/,
            year: /^\d{4}$/,
            cvv: /^\d{3,4}$/
        };
        var card_brand = null;
        var $card_number = $('#number');
        var $btnSubmit = $('#btn_payment_submit');
        var errors = [];

        $btnSubmit.prop('disabled', 'disabled');

        $('#amount').on('blur', function(){
            affiniForm.formatAmount($(this));
        });

        var monthExpiration = $('#exp_month');
        var yearExpiration = $('#exp_year');
        monthExpiration.on('change', validateExpiration);
        yearExpiration.on('change', validateExpiration);

        function validateExpiration(){
            var field = $('.affinipay-exp_year label');
            var errorField = $('.affinipay-exp_year span');
            var theDate = new Date(monthExpiration.val()+'/1/'+ yearExpiration.val());
            var isValidDate = theDate > new Date();
            field.toggleClass('affinipay_error', !isValidDate);
            errorField.toggleClass('affinipay_error', !isValidDate).html(!isValidDate ? 'Card is expired' : '');
            isErrored('exp_year', !isValidDate);
        }

        this.find('input').each(function () {
            $(this)
                .on('change', PaymentFormChangeHandler)
                .on('keyup', PaymentFormKeyPressHandler)
                .on('keydown', checkMaxLength)
                .on('focus', PaymentFormFocusHandler)
                .on('blur', PaymentFormBlurHandler);
        });

        function PaymentFormFocusHandler(e) {
            e.preventDefault();
        }

        function PaymentFormKeyPressHandler(e) {
            var target = $(e.currentTarget);
            if (target.attr('id') == 'number') setCardValidationRule(e);
            if (target.hasClass('affinipay_error')) {
                PaymentFormBlurHandler(e);
            }
        }


        function checkMaxLength(e){
            if(e.key.length > 1) return;

            var target = $(e.currentTarget);
            var el = $('.affinipay-' + target.attr('id') + ' span');
            if (target.attr('maxlength') != target.val().length) {
                el.removeClass('warning');
                return;
            }

            el.addClass('warning').html('Length may not exceed ' + target.attr('maxlength') + ' characters.');
        }

        function PaymentFormBlurHandler(e) {
            var val = $(e.currentTarget).val(),
                rule_type = $(e.currentTarget).data('validate'),
                rule = null,
                isValid = true,
                container = '.affinipay-' + $(e.currentTarget).attr('id'),
                v = {
                    field: e.currentTarget,
                    error_label: container + ' span',
                    rule_type: rule_type,
                    required: $(e.currentTarget).attr('required'),
                    value: val
            };


            if(rule_type === 'cc'){
                setCardTypeFromNumber(e);
                isValid = affiniForm.isValidCard(val);
            }
            else if (typeof(rule_type) !== 'undefined') {
                rule = validation_tests[rule_type];
                isValid = validForRule(val.trim(), rule);
            }

            if(!v.required && val.trim() === '') {
                isValid = true;
            }

            if (isValid === true) {
                var textLabel = v.error_label.replace('span', 'label');
                $(e.currentTarget).removeClass('affinipay_error');
                $(v.error_label).removeClass('affinipay_error').html('');
                $(textLabel).removeClass('affinipay_error');

                var c = $(e.currentTarget).attr('id');
                isErrored(c, false);
            } else {
                handleValidationError(v);
            }
        }

        function PaymentFormChangeHandler(e) {
            e.preventDefault();
        }

        function setCardValidationRule(e) {
            var cardTypeFromNumber = setCardTypeFromNumber(e);
            if (cardTypeFromNumber != null) {
                return cardTypeFromNumber.patterns;
            } else {
                return new RegExp(/\*/);
            }
        }

        function setCardTypeFromNumber(e) {
            var brand = null,
                val = $card_number.val();
            if (val !== '') {
                if (card_types.visa.patterns.test(val)) brand = 'visa';
                if (card_types.mastercard.patterns.test(val)) brand = 'mastercard';
                if (card_types.discover.patterns.test(val)) brand = 'discover';
                if (card_types.amex.patterns.test(val)) brand = 'amex';
                if (card_types.jcb.patterns.test(val)) brand = 'jcb';
            }
            return setCardBrand(brand, e);
        }

        function setCardBrand(t, e) {
            card_brand = card_types[t];
            $card_number.removeClass('visa mastercard amex discover');
            $card_number.addClass(t);
            formatCardNumber(e);
            return card_brand;
        }

        function formatCardNumber(e) {
            if (e.keyCode === 8 || card_brand == null) {
                return;
            }

            var formatted = '',
                raw_string = $card_number.val(),
                matches = raw_string.replace(/\D/g, '').match(card_brand.format);

            if (!matches) {
                return;
            }

            for(var x = 1; x < 5; x++){
                formatted += (typeof(matches[x]) !== 'undefined' ? matches[x].replace(/$/, ' ') : '');
            }

            $card_number.val(formatted);
        }

        function validForRule(str, regex) {

            var isValid = false;

            if (regex != null && str != null) {
                isValid = regex.test(str);
            }
            if (regex == null) {
                isValid = true;
            }
            return isValid;
        }

        function handleValidationError(data) {
            if (data.value.trim() === '') {
                displayError(data, 'Cannot be blank');
                return;
            }

            var message;

            switch (data.rule_type) {
                case 'email':
                    message = 'Please enter a valid Email Address';
                    break;
                case 'numeric':
                    message = 'Must contain only numbers';
                    break;
                case 'alpha':
                    message = 'Must contain only letters';
                    break;
                case 'alphanumeric':
                    message = 'Must contain only letters and numbers';
                    break;
                case 'month':
                    message = data.value.length !== 2 ? 'Must be in MM format' : 'Must be a number';
                    break;
                case 'year':
                    message = data.value.length !== 4 ? 'Must be in YYYY format' : 'Must be a number';
                    break;
                case 'postal_code':
                    message = data.value.length < 3 ? 'Length must be at least 3 characters' : 'Please enter a valid Postal Code';
                    break;
                case 'cvv':
                    message = data.value.length < 3 ? 'Length must be at least 3 characters' : 'Please enter a valid CVV.';
                    break;
                case 'phone':
                    message = 'Must be a number';
                    break;
                case 'cc':
                    message = 'Please enter a valid Card Number.';
                    break;
                default:
                    $(data.error_label).removeClass('affinipay_error').html('');
                    return;
            }


            displayError(data, message);
        }

        function displayError(data, message) {
            data.txt = data.error_label.replace('span', 'label');
            $(data.field).addClass('affinipay_error');
            $(data.error_label).addClass('affinipay_error').html(message);
            $(data.txt).addClass('affinipay_error');

            var c = $(data.field).attr('id');
            isErrored(c, true);
        }

        function isErrored(id, inError){
            var index = errors.indexOf(id);
            if(inError){
                if (index < 0) {
                    errors.push(id);
                }
            } else {
                if (index > -1) {
                    errors.splice(index, 1);
                }
            }

            $btnSubmit.prop('disabled', errors.length > 0);
        }

        affiniForm.validateAllFields = function(){
            validateExpiration();
            form.find('input').each(function(){
               PaymentFormBlurHandler({currentTarget: this});
            });

            return errors.length === 0;
        }
    };
}( jQuery ));
