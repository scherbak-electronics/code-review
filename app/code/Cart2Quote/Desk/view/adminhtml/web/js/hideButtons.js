/*
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

require(['jquery'], function ($) {
    $("#submit_ticket").hide();
    $('#quotation_quote_view_tabs_quote_info').click(function () {
        $("#sections_popup_id").show();
        $("#saveQuote").show();
        $("#duplicate-button").show();
        $("button[data-ui-id='desk-tab-save-duplicate-button-dropdown']").show();
        $("#submit_ticket").hide();
        $("#edit").show();
    });

    $('#quotation_quote_view_tabs_desk_tab').click(function () {
        $("#sections_popup_id").hide();
        $("#saveQuote").hide();
        $("#duplicate-button").hide();
        $("button[data-ui-id='desk-tab-save-duplicate-button-dropdown']").hide();
        $("#submit_ticket").show();
        $("#edit").hide();
    });
});