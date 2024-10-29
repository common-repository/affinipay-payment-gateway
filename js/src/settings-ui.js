'use strict';
var initialize = function ($) {
    var submitButton = $('#btn_affinipay_settings_submit'),
        testResults = $('#affinipay_test_results'),
        receiptPage = $('#affinipay_receipt_page');

    var save = function(){
        return $.post(ajaxurl, {
            action: 'affinipay_settings_submit',
            receipt_page: receiptPage.val(),
            public_key: $("#affinipay_public_key").val(),
            secret_key: $("#affinipay_secret_key").val()
        }, function (response) {
            testResults.html(response);
        })
    };

    submitButton.on('click', function () {
        submitButton.prop('disabled', true);
        save().always(function () {
            submitButton.prop('disabled', false);
        })
    });
};

jQuery(document).ready(initialize);