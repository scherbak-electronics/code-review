/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    "jquery",
    "jquery/ui"
], function (_, $) {
    'use strict';

    $.widget('salesrep.salesrepAssign', {
        options: {
            selectors: {
                details : '.salesrep-details',
                container : '.salesrep-user-container',
                icon : '.salesrep-icon',
                assign : '.salesrep-assign a',
                userList : '.salesrep-user-list',
                assignCancel : '.salesrep-assign-cancel a',
                assignSave : '.salesrep-assign-save a',
                assignChecked : '#stick-salesrep',
                dropdown: '#salesrep-dropdown'
            },
            url: undefined,
            id: 0,
            typeId: undefined,
            customerId: undefined
        },

        _create: function() {
            var self = this;

            self.addAssignEvent();
            self.addCancelEvent();

            $(self.options.selectors.assignSave).click(function(event){
                self.updateElement(
                    $(self.options.selectors.container),
                    self.options.url,
                    $(self.options.selectors.dropdown).val(),
                    self.options.id,
                    self.options.typeId,
                    $(self.options.selectors.assignChecked).prop('checked'),
                    self.options.customerId
                );
            });
        },

        showUsersDropDown: function() {
            $(this.options.selectors.details).hide();
            $(this.options.selectors.icon).hide();
            $(this.options.selectors.assign).hide();
            $(this.options.selectors.userList).show();
            $(this.options.selectors.assignCancel).show();
            $(this.options.selectors.assignSave).show();
        },

        hideUsersDropDown: function() {
            $(this.options.selectors.details).show();
            $(this.options.selectors.icon).show();
            $(this.options.selectors.assign).show();
            $(this.options.selectors.userList).hide();
            $(this.options.selectors.assignCancel).hide();
            $(this.options.selectors.assignSave).hide();
        },

        addAssignEvent: function() {
            var self = this;
            $(this.options.selectors.assign).click(function(event){
                event.preventDefault();
                self.showUsersDropDown();
            });
        },

        addCancelEvent: function() {
            var self = this;
            $(this.options.selectors.assignCancel).click(function(event){
                event.preventDefault();
                self.hideUsersDropDown();
            });
        },

        /**
         * Makes ajax request to update an element on the page.
         *
         * @returns {*}
         */
        updateElement: function(e, url, userId, id, typeId, sticky, customerId) {
            if (url != undefined && userId != undefined && id != 0 && typeId != undefined) {
                var self = this;
                $.ajax({
                    url: url,
                    timeout: 15000,
                    data: {
                        form_key: FORM_KEY,
                        user_id: userId,
                        id: id,
                        type_id: typeId,
                        stick_user: sticky,
                        customer_id: customerId
                    },
                    success: function (resp) {
                        if (resp.html !== "") {
                            $(e).html(resp.html);
                            self.hideUsersDropDown();
                            self.addAssignEvent();
                        }
                    }
                });
            }
        }
    });

    return $.salesrep.salesrepAssign;
});
