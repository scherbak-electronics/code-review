/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['jquery'], function($){
    $('#desk_template_message').change(function(){
        if (this.value) {
            var current = $('#desk_message').val();
            $('#desk_message').val(current + " \n" +  this.value);
        }
    })
});