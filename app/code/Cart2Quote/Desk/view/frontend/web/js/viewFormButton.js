/*
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('desk.viewFormButton', {

        options: {
            bindOnClick: true,
            ticketFormSuccessSelector: '.ticket-form-success-content',
            ticketFormSelector: '.ticket-form-content'
        },

        /**
         * Bind the onclick event
         *
         * @private
         */
        _create: function () {
            if (this.options.bindOnClick) {
                this._bindOnClick();
            }
        },

        /**
         * Show/Hide the ticket fields on click
         *
         * @private
         */
        _bindOnClick: function () {
            var self = this;
            this.element.on('click', function (e) {
                e.preventDefault();
                $(self.options.ticketFormSuccessSelector).hide();
                $(self.options.ticketFormSelector).show();
            });
        },

        /**
         * Format params
         *
         * @param {Object} params
         * @returns {Array}
         */
        prepareParams: function (params) {
            var result = '?';

            _.each(params, function (value, key) {
                result += key + '=' + value + '&';
            });

            return result.slice(0, -1);
        }
    });

    return $.desk.viewFormButton
});
