/*
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

require(['jquery'], function ($) {
    if ($('#ticketForm').val().length != 0) {
        $('#submit_ticket-button').attr('disabled', false);
        $('#ticketDropdown').attr('disabled', false);
    } else {
        $('#submit_ticket-button').attr('disabled', true);
        $('#ticketDropdown').attr('disabled', true);
    }

    $('#ticketForm').keyup(function(){
        if($(this).val().length !=0) {
            $('#submit_ticket-button').attr('disabled', false);
            $('#ticketDropdown').attr('disabled', false);
        } else {
            $('#submit_ticket-button').attr('disabled', true);
            $('#ticketDropdown').attr('disabled', true);
        }
    })
});