/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        var paymentType = 'elavon';
        var config = window.checkoutConfig.payment.elavon;

        if (config) {
            rendererList.push(
                {
                    type: paymentType,
                    component: 'Pronko_Elavon/js/view/payment/method-renderer/' +
                    config.connectionType
                }
            );
        }
        return Component.extend({});
    }
);