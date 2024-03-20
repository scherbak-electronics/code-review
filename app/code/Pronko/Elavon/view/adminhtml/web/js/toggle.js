/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */
/*browser:true*/
/*global define*/
define([
    'jquery',
    'prototype'
], function($) {
    'use strict';

    return function (optionsConfig) {
        var button = $('button.elavon-action');

        button.on('click', function(event) {
            var id = button.data('html-id'),
                url = button.data('toggle-url');

            Fieldset.toggleCollapse(id, url);
        });
    };
});