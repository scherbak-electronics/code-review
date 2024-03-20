/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */
/*browser:true*/
/*global define*/
define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($) {
    'use strict';

    return function (optionsConfig) {
        var changelog = $('<div/>').html(optionsConfig.changelog).modal({
            modalClass: 'changelog',
            title: $.mage.__('Elavon Release Notes'),
            buttons: [{
                text: 'Ok',
                click: function () {
                    this.closeModal();
                }
            }]
        });
        $('#elavon-changelog').on('click', function() {
            changelog.modal('openModal');
        });
    };
});