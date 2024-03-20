/*
 * Copyright (c) 2021. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/url'
], function ($, url) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            options: {
                selectorAddToCart: '[data-role=addToCartButton]',
                selectorAddToCartInstant: '#instant-purchase',
                selectorAddToCartInstantExtra: '[data-action=checkout-form-submit]',
                selectorPrice: '.c2q_n2o_price',
                selectorPayPalAddToCart: '#paypal-smart-button'
            },

            _init: function () {
                this._super();
                this._CheckForButtonChanges();
                this._CheckForPriceDisplayChanges();
            },

            _OnClick: function ($this, widget, eventName) {
                this._super($this, widget, eventName);
                widget._CheckForButtonChanges();
                $(".c2q_n2o_price").empty();
                widget._CheckForPriceDisplayChanges();
            },

            _CheckForPriceDisplayChanges: function () {
                var widget = this,

                    // default display price value is false
                    displayPrice = false,

                    hideprice = widget.options.jsonConfig.not2order_hide_price,
                    displayPriceYesNo = widget.element.parents(widget.options.selectorProduct)
                        .find(widget.options.selectorPrice),
                    selectedProduct = this.getProduct()

                if (typeof selectedProduct !== "undefined") {
                    displayPrice = hideprice[selectedProduct] == 'undefined' ? false : hideprice[selectedProduct];
                }

                url.setBaseUrl(BASE_URL);
                var product = this.getProduct();
                $.ajax({
                    url: url.build('not2order/configurable/showprice'),
                    type: 'POST',
                    data: {id: product},
                    dataType: 'json',
                    showLoader: true
                }).done(function (data) {
                    if (data.showPrice) {
                        displayPrice = true;
                        $(".product-info-price").append(data.productPrice);
                    }
                })

                //show or hide price
                displayPriceYesNo.toggle(displayPrice);
            },

            _CheckForButtonChanges: function () {
                var widget = this,

                    //default cart button is false
                    showCartButton = false,

                    not2orderable = widget.options.jsonConfig.is_not2orderable,

                    cartButton = widget.element.parents(widget.options.selectorProduct)
                        .find(widget.options.selectorAddToCart),
                    cartInstant = widget.element.parents(widget.options.selectorProduct)
                        .find(widget.options.selectorAddToCartInstant),
                    cartInstantExtra = widget.element.parents(widget.options.selectorProduct)
                        .find(widget.options.selectorAddToCartInstantExtra),
                    cartPayPalButton = widget.element.parents(widget.options.selectorProduct)
                        .find(widget.options.selectorPayPalAddToCart)

                var selectedProduct = this.getProduct();

                cartButton.toggle(showCartButton);
                cartInstant.toggle(showCartButton);
                cartInstantExtra.toggle(showCartButton);
                cartPayPalButton.toggle(showCartButton);

                if (typeof selectedProduct !== "undefined") {
                    showCartButton = not2orderable[selectedProduct] == 'undefined' ? false : not2orderable[selectedProduct];
                }

                url.setBaseUrl(BASE_URL);
                var product = this.getProduct();
                $.ajax({
                    url: url.build('not2order/configurable/showbutton'),
                    type: 'POST',
                    data: {id: product},
                    dataType: 'json',
                    showLoader: true
                }).done(function (data) {
                    if (data.showButton) {
                        showCartButton = true;
                    }
                })

                //show or hide cart buttons
                cartButton.toggle(showCartButton);
                cartInstant.toggle(showCartButton);
                cartInstantExtra.toggle(showCartButton);
                cartPayPalButton.toggle(showCartButton);
            },
        });

        return $.mage.SwatchRenderer;
    }
});
