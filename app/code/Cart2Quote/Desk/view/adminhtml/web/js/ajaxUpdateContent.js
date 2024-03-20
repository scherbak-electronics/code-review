/*
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    "jquery",
    "jquery/ui"
], function (_, $) {
    'use strict';

    $.widget('desk.ajaxUpdateContent', {
        options: {
            url: undefined,
            params: undefined,
            refreshInterval: 15/* seconds */ * 1000
        },

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function() {
            this.updateElementInterval();
        },

        /**
         * Makes ajax request to update an element on the page.
         *
         * @returns {*}
         */
        updateElement: function() {
            return function(e, url, postValue) {
                var updateElement = $.Deferred();

                if (!url) {
                    updateElement.resolve();
                }

                var params = {last_id: e.data('lastId'), id: e.data('ticketId')};

                $.ajax({
                    url: url + prepareParams(params),
                    dataType: 'json',
                    data: postValue,
                    timeout: 15000,
                    success: function (resp) {
                        if (resp.ajaxExpired) {
                            window.location.href = resp.ajaxRedirect;
                        }

                        if (resp.html !== "" && e.data('lastId') != resp.lastId) {
                            e.prepend(resp.html);
                            e.data('lastId', resp.lastId);
                        }
                    }
                });

                /**
                 * Format params
                 *
                 * @param {Object} params
                 * @returns {Array}
                 */
                function prepareParams(params){
                    var result = '?';

                    _.each(params, function (value, key) {
                        result += key + '=' + value + '&';
                    });

                    return result.slice(0, -1);
                }
            }
        },

        /**
         * Starts an interval with the updateElement function.
         */
        updateElementInterval: function(){
            var url = this.options.url;
            var element = this.element;
            var update = this.updateElement();
            var postValue = {form_key: $('#edit_form input[name="form_key"]').prop('value')};

            setInterval(update.bind(null, element, url, postValue), this.options.refreshInterval);
        },

        /**
         * For an update.
         */
        update: function(){
            var update = this.updateElement();
            update();
        }
    });

    return $.desk.ajaxUpdateContent;
});