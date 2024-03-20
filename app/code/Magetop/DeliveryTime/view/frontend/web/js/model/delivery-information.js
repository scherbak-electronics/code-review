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
 * @package     Magetop_DeliveryTime
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

define(
    [
        'ko',
        'uiComponent'
    ],
    function (ko, Component) {
        'use strict';

        return Component.extend({
            deliveryDate: ko.observable(),
            deliveryTime: ko.observable(),
            houseSecurityCode: ko.observable(),
            deliveryComment: ko.observable(),
            deliveryTimeOptions: ko.observableArray([])
        });
    }
);
