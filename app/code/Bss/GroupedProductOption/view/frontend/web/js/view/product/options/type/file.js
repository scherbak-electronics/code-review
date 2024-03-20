define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('bss.p_options_file', {
        options: {
            url: window.URL || window.webkitURL
        },

        /**
         * Create widget
         *
         * @private
         */
        _create: function () {
            this._bind();
        },

        /**
         * Widget binding
         *
         * @private
         */
        _bind: function() {
            var self = this;

            /**
             * Set width and height for uploaded image file
             */
            $(self.element).change(function () {
                var file, img, imgEle = this;

                if (this.files[0] && (file = this.files[0])) {
                    img = new Image();
                    img.onload = function() {
                        $(imgEle).data('img-width', this.width);
                        $(imgEle).data('img-height', this.height);
                    };
                    img.src = self.options.url.createObjectURL(file);
                }
            });
        },
    });

    return $.bss.p_options_file;
});
