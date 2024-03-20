/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_GroupedProductOption
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    $.widget('bss.fileValidation', {
        options: {},

        _create: function () {
            var that = this;

            $.validator.addMethod(
                'validate-fileextensions-option-id-' + that.options.optionId, function (v, elm) {
                    var extensionsList = that.options.extensions;
                    var extensions = extensionsList.split(',');
                    if (!v) {
                        return true;
                    }
                    with (elm) {
                        var ext = value.substring(value.lastIndexOf('.') + 1);
                        for (var i = 0; i < extensions.length; i++) {
                            if (ext === extensions[i]) {
                                return true;
                            }
                        }
                    }
                    return false;
                }, $.mage.__('Disallowed file type.')
            );

            $.validator.addMethod(
                'validate-image-height-width-option-id-' + that.options.optionId, function (v, elm) {
                    var imgwh, isValid = false;

                    if (!v) {
                        return true;
                    }

                    with (elm) {
                        if ((elm.files[0].name)) {
                            imgwh = {width: $(elm).data('img-width'), height: $(elm).data('img-height')};

                            if (imgwh && imgwh.width > 0 && imgwh.height > 0) {
                                isValid = !(imgwh.width > parseInt(that.options.imgMaxWidth) || imgwh.height > parseInt(that.options.imgMaxHeight))
                            }

                            return isValid;
                        }
                    }
                    return false;
                }, $.mage.__('Height and Width must not exceed') + ` (${that.options.imgMaxWidth} x ${that.options.imgMaxHeight}px)`);
        }
    });

    return $.bss.fileValidation;
});
