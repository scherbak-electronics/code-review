/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function ($, Component) {
        'use strict';

        var config = window.checkoutConfig.payment.elavon;

        return Component.extend({
            defaults: {
                template: 'Pronko_Elavon/payment/form',
                code: 'elavon',
                ccSsIssueCardType: 'MI'
            },

            initialize: function () {
                var self = this;

                this._super();
            },

            /**
             * @returns {String}
             */
            getCode: function () {
                return this.code;
            },

            /**
             * Check if payment is active
             *
             * @returns {Boolean}
             */
            isActive: function () {
                return this.getCode() === this.isChecked();
            },

            /**
             * Get full selector name
             *
             * @param {String} field
             * @returns {String}
             */
            getSelector: function (field) {
                return '#' + this.getCode() + '_' + field;
            },

            /**
             * @returns {Boolean}
             */
            validate: function () {
                var form = $(this.getSelector('payment-form'));
                    form.validation();
                var validator = form.validate();

                return validator.form();
            },

            /**
             * @returns {boolean}
             */
            hasSsCardType: function() {
                var types = this.getCcAvailableTypes(),
                    _self = this,
                    result = false;
                $.each(types, function(key, value) {
                    if (key === _self.ccSsIssueCardType) {
                        if (_self.hasSsIssueNumber()) {
                            result = true;
                        }
                    }
                });
                return result;
            },

            /**
             *
             * @returns {Array}
             */
            hasSsIssueNumber: function() {
                return config.hasSsIssueNumber;
            },

            /**
             * @returns {Array}
             */
            getSsStartYearsValues: function() {
                return _.map(config.ssStartYears, function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    };
                });
            },

            /**
             *
             * @returns {*|jQuery|HTMLElement}
             */
            getCreditCardSsIssue: function() {
                return $(this.getSelector('cc_issue')).val();
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_ss_start_month': this.creditCardSsStartMonth(),
                        'cc_ss_start_year': this.creditCardSsStartYear(),
                        'cc_ss_issue': this.getCreditCardSsIssue(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber()
                    }
                };
            }
        });
    }
);
