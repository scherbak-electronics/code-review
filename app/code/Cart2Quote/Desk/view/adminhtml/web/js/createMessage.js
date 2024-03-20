/*
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    "jquery/ui"
], function ($) {
    'use strict';

    $.widget('desk.createMessage', {
        options: {
            textArea: undefined,
            textAreaContainer: undefined,
            header: undefined,
            headerTitle: undefined,
            replyTypeButton: undefined,
            replyTypeButtonInternalNote: undefined,
            isPrivateInput: undefined,
            ticketDetailsBox: undefined
        },

        /**
         * Binds elements to the options and adds the events.
         *
         * @private
         */
        _create: function() {
            this._setOption('textArea', 'textarea');
            this._setOption('textAreaContainer', '.textarea-container');
            this._setOption('header', 'header');
            this._setOption('headerTitle', 'conversation-title');
            this._setOption('replyTypeButton', '.set-conversation-type li');
            this._setOption('replyTypeButtonInternalNote', '.set-conversation-type li.internal-note');
            this._setOption('isPrivateInput', '#is-private-message');
            this.options.ticketDetailsBox = $('.side-col');
            this.setClassTextareaOnFocus();
            this.removeClassTextareaOnBlur();
            this.setHeaderActiveOnClick();
            this.setConversationType();
            this.addFixedScrollBox();
        },

        /**
         * Set an option
         *
         * @param option
         * @param selector
         * @private
         */
        _setOption: function(option, selector){
            if(this.options[option] == undefined){
                this.options[option] = this.element.find(selector);
            }
        },

        /**
         * Set on focus on the textarea
         */
        setClassTextareaOnFocus: function() {
            var self = this;
            this.options.textArea.focus(
                function(){
                    self.options.textAreaContainer.addClass('focus')
                }
            );
        },

        /**
         * Remove focus element on blur
         */
        removeClassTextareaOnBlur: function() {
            var self = this;
            this.options.textArea.blur(
                function() {
                    self.options.textAreaContainer.removeClass('focus');
                }
            );
        },

        /**
         * Set active on click
         */
        setHeaderActiveOnClick: function() {
            var self = this;
            this.options.headerTitle.click(
                function() {
                    self.options.header.toggleClass('active');
                }
            );
        },

        /**
         * Adds the conversation type (isPrivate message) class
         */
        setConversationType: function() {
            var self = this;
            this.options.replyTypeButton.click(
                function(e) {
                    self.options.replyTypeButton.removeClass('active');
                    $(e.srcElement).addClass('active');

                    if (self.options.replyTypeButtonInternalNote.hasClass('active')) {
                        self.options.textAreaContainer.addClass('internal-note');
                        self.options.isPrivateInput.prop('checked', true);
                    } else {
                        self.options.textAreaContainer.removeClass('internal-note');
                        self.options.isPrivateInput.prop('checked', false);
                    }
                }
            )
        },

        /**
         * Adds the fix scroll bar when Magento's fixed bar is shown in the backend
         */
        addFixedScrollBox: function() {
            var self = this;
            $(window).scroll(
                function() {
                    if($( ".page-main-actions ._fixed").length == 0){
                        self.options.ticketDetailsBox.removeClass('ticket-box-fixed');
                    }else{
                        self.options.ticketDetailsBox.addClass('ticket-box-fixed');
                    }
                }
            );
        }
    });

    return $.desk.createMessage;
});