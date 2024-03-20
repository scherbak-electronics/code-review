/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Osc
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

define(['ko'], function (ko) {
    'use strict';
    var hasLogin = window.checkoutConfig.oscConfig.isAmazonAccountLoggedIn;
    return {
        isAmazonAccountLoggedIn: ko.observable(hasLogin),
        setLogin: function (value) {
            return this.isAmazonAccountLoggedIn(value);
        }
    };
});