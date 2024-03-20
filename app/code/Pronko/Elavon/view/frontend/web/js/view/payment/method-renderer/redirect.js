/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Pronko_Elavon/js/view/form-builder',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/customer-data',
        'knockout'
    ],
    function ($, Component, fullScreenLoader, formBuilder, errorProcessor, customerData, ko) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Pronko_Elavon/payment/redirect',
                code: 'elavon',
                redirectAfterPlaceOrder: false,
                isInAction: ko.observable(null)
            },

            placePendingPaymentOrder: function () {
                if (this.placeOrder()) {
                    this.isInAction(true);
                }
                this.isInAction(false);
            },

            afterPlaceOrder: function () {
                var self = this;

                $.get(this.getTransactionDataUrl())
                    .done(function (response) {
                        customerData.invalidate(['cart', 'checkout-data']);
                        formBuilder.build(response).submit();
                    }).fail(function (response) {
                        errorProcessor.process(response, self.messageContainer);
                        fullScreenLoader.stopLoader();
                        self.isInAction(false);
                        self.isPlaceOrderActionAllowed(true);
                    });
            },

            getTransactionDataUrl: function() {
                return window.checkoutConfig.payment[this.getCode()].transactionDataUrl;
            }
        });
    }
);
