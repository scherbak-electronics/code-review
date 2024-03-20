/*
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('desk.viewTicketButton', {

        options: {
            bindOnClick: true,
            url: undefined
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
                var ticketId = e.currentTarget.getAttribute("data-ticket-id");
                window.location.replace(
                    self.options.url + self.prepareParams(
                        {id: ticketId}
                    )
                )
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

    return $.desk.viewTicketButton
});
