/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Osc
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

define([
    'underscore',
    'ko',
    'mageUtils',
    'Magento_Checkout/js/view/payment/list',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/payment/renderer-list',
    'uiLayout',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'mage/translate',
    'uiRegistry'
], function (_, ko, utils, Component, paymentMethods, rendererList, layout, checkoutDataResolver, $t, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magetop_Osc/payment-methods/list',
        },

        /**
         * Create renderer.
         *
         * @param {Object} paymentMethodData
         */
        createRenderer: function (paymentMethodData) {
            if (paymentMethodData.method === 'paypal_express') {
                var isRendererForMethod = false,
                    currentGroup,
                    component,
                    isContextCheckout = window.checkoutConfig.payment.paypalExpress.isContextCheckout;

                if(isContextCheckout) {
                    component = 'Magetop_Osc/js/view/payment/method-renderer/in-context/checkout-express';
                }

                registry.get(this.configDefaultGroup.name, function (defaultGroup) {
                    _.each(rendererList(), function (renderer) {

                        if (renderer.hasOwnProperty('typeComparatorCallback') &&
                            typeof renderer.typeComparatorCallback == 'function'
                        ) {
                            isRendererForMethod = renderer.typeComparatorCallback(renderer.type, paymentMethodData.method);
                        } else {
                            isRendererForMethod = renderer.type === paymentMethodData.method;
                        }

                        if (isRendererForMethod) {
                            currentGroup = renderer.group ? renderer.group : defaultGroup;

                            this.collectPaymentGroups(currentGroup);

                            layout([
                                this.createComponent(
                                    {
                                        config: renderer.config,
                                        component: component,
                                        name: renderer.type,
                                        method: paymentMethodData.method,
                                        item: paymentMethodData,
                                        displayArea: currentGroup.displayArea
                                    }
                                )]);
                        }
                    }.bind(this));
                }.bind(this));
            }
        },
    });
});
