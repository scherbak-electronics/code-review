/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data'
    ],
    function ($, modal, customerData) {
        'use strict';

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Product Enquiry Form',
            buttons: false
        };
        var popup = modal(options, $('#enquiry_form_modal_popup'));
        $("#product_enquire_button").on('click', function () {
            $("#enquiry_form_modal_popup").modal("openModal");
        });
    }
);
