define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery-ui-modules/widget'
], function ($, _, mageTemplate, utils) {
    'use strict';

    var globalOptions = {
        productId: null,
        priceHolderSelector: '.price-box', //data-role="priceBox"
        optionsSelector: '.product-custom-option',
        optionConfig: {},
        optionHandlers: {},
        optionTemplate: '<%= data.label %>' +
            '<% if (data.finalPrice.value > 0) { %>' +
            ' +<%- data.finalPrice.formatted %>' +
            '<% } else if (data.finalPrice.value < 0) { %>' +
            ' <%- data.finalPrice.formatted %>' +
            '<% } %>',
        controlContainer: 'dd'
    };

    /**
     * Custom option preprocessor
     * @param  {jQuery} element
     * @param  {Object} optionsConfig - part of config
     * @param {Number} optQty - Option QTY
     * @return {Object}
     */
    function defaultGetOptionValue(element, optionsConfig, optQty = undefined) {
        var changes = {},
            optionValue = element.val(),
            optionId = utils.findOptionId(element[0]),
            optionName = element.prop('name'),
            optionType = element.prop('type'),
            optionHash = optionName;
        if (element.attr('data-option-id')) {
            optionId = element.attr('data-option-id');
        }
        var optionConfig = optionsConfig[optionId];
        changes[optionHash] = {};
        switch (optionType) {
            case 'text':
            case 'textarea':
                if (optionValue) {
                    changes[optionHash] = optionConfig.prices;
                    changes[optionHash].priceType = optionConfig.type;
                }
                break;

            case 'radio':
                if (element.is(':checked') && optionConfig[optionValue]) {
                    changes[optionHash] = optionConfig[optionValue].prices;
                    changes[optionHash].priceType = optionConfig[optionValue].type;
                }
                break;

            case 'select-one':
                if (optionConfig[optionValue]) {
                    changes[optionHash] = optionConfig[optionValue].prices;
                    changes[optionHash].priceType = optionConfig[optionValue].type;
                }
                break;

            case 'select-multiple':
                _.each(optionConfig, function (row, optionValueCode) {
                    optionHash = optionName + '##' + optionValueCode;
                    changes[optionHash] = {};
                    if (_.contains(optionValue, optionValueCode)) {
                        changes[optionHash] = row.prices;
                        changes[optionHash].priceType = row.type;
                    }
                });
                break;

            case 'checkbox':
                optionHash = optionName + '##' + optionValue;
                changes[optionHash] = {};
                if (element.is(':checked')) {
                    changes[optionHash] = optionConfig[optionValue].prices;
                    changes[optionHash].priceType = optionConfig[optionValue].type;
                }
                break;

            case 'file':
                // Checking for 'disable' property equal to checking DOMNode with id*="change-"
                if (optionValue || element.prop('disabled')) {
                    changes[optionHash] = optionConfig.prices;
                    changes[optionHash].priceType = optionConfig.type;
                }
                break;
        }

        changes[optionHash].optQty = optQty || 1;
        return changes;
    }

    $.widget('mage.priceOptions', {
        options: globalOptions,

        /**
         * @private
         */
        _init: function initPriceBundle() {
            $(this.options.optionsSelector, this.element).trigger('change');
        },

        /**
         * Widget creating method.
         * Triggered once.
         * @private
         */
        _create: function createPriceOptions() {
            var form = this.element,
                options = $(this.options.optionsSelector, form),
                priceBox = $(this.options.priceHolderSelector, $(this.options.optionsSelector).element);

            if (priceBox.data('magePriceBox') &&
                priceBox.priceBox('option') &&
                priceBox.priceBox('option').priceConfig
            ) {
                if (priceBox.priceBox('option').priceConfig.optionTemplate) {
                    this._setOption('optionTemplate', priceBox.priceBox('option').priceConfig.optionTemplate);
                }
                this._setOption('priceFormat', priceBox.priceBox('option').priceConfig.priceFormat);
            }

            this._applyOptionNodeFix(options);

            _.each(this.options.optionConfig, (optionConfig) => {
                if (optionConfig.prices) {
                    _.each(optionConfig.prices, (price) => {
                        price.baseAmount = price.amount;
                    });
                } else {
                    _.each(optionConfig, (childOptConfig) => {
                        _.each(childOptConfig.prices, (price) => {
                            price.baseAmount = price.amount;
                        });
                    });
                }
            });

            options.on('change', this._onOptionChanged.bind(this));
            $(this.options.optionsSelector, form)
                .closest(this.options.controlContainer)
                .find('.bss-option-qty input').on('change', this._onOptionQtyChanged.bind(this));
        },

        _onOptionQtyChanged: function (event) {
            const option = $(event.target).closest(this.options.controlContainer).find(this.options.optionsSelector),
                qty = $(event.target).val();

            if (option.val()) {
                var changes,
                    handler = this.options.optionHandlers[option.data('role')],
                    productId = option.closest('.bss-gpo-custom-option').attr('data-product-id');

                option.data('optionContainer', option.closest(this.options.controlContainer));

                if (handler && handler instanceof Function) {
                    changes = handler(option, this.options.optionConfig, this);
                } else {
                    changes = defaultGetOptionValue(option, this.options.optionConfig, qty);
                }
                if (productId) {
                    changes.productId = productId;
                }

                $(this.options.priceHolderSelector).trigger('updatePrice', changes);
            }
        },

        /**
         * Custom option change-event handler
         * @param {Event} event
         * @private
         */
        _onOptionChanged: function onOptionChanged(event) {
            var changes,
                option = $(event.target),
                handler = this.options.optionHandlers[option.data('role')],
                productId = option.closest('.bss-gpo-custom-option').attr('data-product-id'),
                qty = option.closest(this.options.controlContainer).find('.bss-option-qty input').val();

            option.data('optionContainer', option.closest(this.options.controlContainer));

            if (handler && handler instanceof Function) {
                changes = handler(option, this.options.optionConfig, this);
            } else {
                changes = defaultGetOptionValue(option, this.options.optionConfig, qty);
            }
            if (productId) {
                changes.productId = productId;
            }
            $(this.options.priceHolderSelector).trigger('updatePrice', changes);
        },

        /**
         * Helper to fix issue with option nodes:
         *  - you can't place any html in option ->
         *    so you can't style it via CSS
         * @param {jQuery} options
         * @private
         */
        _applyOptionNodeFix: function applyOptionNodeFix(options) {
            var config = this.options,
                format = config.priceFormat,
                template = config.optionTemplate;

            template = mageTemplate(template);
            options.filter('select').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    optionConfig = config.optionConfig && config.optionConfig[optionId];

                $element.find('option').each(function (idx, option) {
                    var $option,
                        optionValue,
                        toTemplate,
                        prices;

                    $option = $(option);
                    optionValue = $option.val();

                    if (!optionValue && optionValue !== 0) {
                        return;
                    }

                    toTemplate = {
                        data: {
                            label: optionConfig[optionValue] && optionConfig[optionValue].name
                        }
                    };
                    prices = optionConfig[optionValue] ? optionConfig[optionValue].prices : null;

                    if (prices) {
                        _.each(prices, function (price, type) {
                            var value = +price.amount;

                            value += _.reduce(price.adjustments, function (sum, x) { //eslint-disable-line
                                return sum + x;
                            }, 0);
                            toTemplate.data[type] = {
                                value: value,
                                formatted: utils.formatPrice(value, format)
                            };
                        });

                        $option.text(template(toTemplate));
                    }
                });
            });
        },

        /**
         * Custom behavior on getting options:
         * now widget able to deep merge accepted configuration with instance options.
         * @param  {Object}  options
         * @return {$.Widget}
         * @private
         */
        _setOptions: function setOptions(options) {
            $.extend(true, this.options, options);
            this._super(options);

            return this;
        }
    });

    return $.mage.priceOptions;
});

