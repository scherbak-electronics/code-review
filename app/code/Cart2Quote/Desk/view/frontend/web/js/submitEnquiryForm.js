/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'jquery',
        'mage/translate',
        'jquery/ui',
        'Magento_Ui/js/modal/modal'
    ], function ($, $t, customerData, modal) {
        $.widget('desk.submitEnquiry', {

            options: {
                processStart: null,
                processStop: null,
                bindSubmit: true,
                enquiryMessageBoxSelector: '#enquiry_message_enquiry_form',
                viewYourTicketSelector: '#view-ticket-button',
                submitEnquiryFormButtonSelector: '#enquiry_form_submit_form',
                submitEnquiryButtonDisabledClass: 'disabled',
                submitEnquiryButtonTextWhileAdding: $t("Sending..."),
                submitEnquiryButtonTextAdded: $t("Enquiry Submitted"),
                submitEnquiryButtonTextFailed: $t("Sending Failed"),
                submitEnquiryButtonTextDefault: $t("Make Product Enquiry"),
                submitEnquiryButtonTextWhenDone: $t("Submit Additional Enquiry")
            },

            _create: function () {
                var self = this;
                this.element.on('submit', function (e) {
                    e.preventDefault();
                    $(this).validation();
                    if($(this).validation('isValid')){
                        self.submitForm($(this));
                    }
                });
            },

            /**
             * Check if the loader is enabled.
             *
             * @returns {null}
             */
            isLoaderEnabled: function () {
                return this.options.processStart && this.options.processStop;
            },

            /**
             * Bind alternative submit action.
             *
             * @param form
             */
            submitForm: function (form) {
                this.ajaxSubmit(form);
            },

            /**
             * Submits the form via AJAX.
             * Disable the submit enquiry button while sending.
             * Enabled the submit enquiry button on submit success, and activate success popup.
             *
             * @param form
             */
            ajaxSubmit: function (form) {
                var self = this;
                $(self.options.minicartSelector).trigger('contentLoading');
                self.disableSendMessageButton(form);

                $.ajax({
                    url: form.attr('action'),
                    data: form.serialize(),
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function () {
                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStart);
                        }
                    },
                    success: function (res) {
                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStop);
                        }

                        if (res.messages) {
                            $(self.options.messagesSelector).html(res.messages);
                            $('.page-title').hide();
                            $("#enquiry_form_modal_popup").modal("closeModal");
                            self.popupSuccess();
                            $(self.options.enquiryMessageBoxSelector).val('');
                            self.enableSendMessageButton(form);
                        }

                        if (res.backUrl) {
                            window.location = res.backUrl;
                            return;
                        }
                    }
                });
            },

            /**
             * Disable the submit enquiry button while processing.
             *
             * @param form
             */
            disableSendMessageButton: function (form) {
                var sendMessageButton = $(form).find(this.options.submitEnquiryFormButtonSelector);
                sendMessageButton.addClass(this.options.submitEnquiryButtonDisabledClass);
                sendMessageButton.attr('title', this.options.submitEnquiryButtonTextWhileAdding);
                sendMessageButton.find('span').text(this.options.submitEnquiryButtonTextWhileAdding);
            },

            /**
             * Enable the submit enquiry button after processing.
             *
             * @param form
             */
            enableSendMessageButton: function (form) {
                var sendMessageButton = $(form).find(this.options.submitEnquiryFormButtonSelector);
                sendMessageButton.removeClass(this.options.submitEnquiryButtonDisabledClass);
                sendMessageButton.attr('title', this.options.submitEnquiryButtonTextWhenDone);
                sendMessageButton.find('span').text(this.options.submitEnquiryButtonTextWhenDone);
            },

            /**
             * Brief success popup.
             */
            popupSuccess: function() {
                var modalOptions = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: 'Your enquiry has been submitted successfully!',
                    buttons: false
                };
                var popup = modal(modalOptions, $('#enquiry_success_modal_popup'));
                    $("#enquiry_success_modal_popup").modal("openModal");

                    setTimeout( function() {
                        $("#enquiry_success_modal_popup").modal("closeModal");
                    }, 1750)
            }
        });

        return $.desk.submitEnquiry
    }
);
