<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Api\Data;

/**
 * Interface RangeInterface
 *
 * @package Cart2Quote\AutoProposal\Api\Data
 */
interface RangeInterface
{
    const DISCOUNT_IDENTIFIER = 'discount';
    const DISABLE_AUTOPROPOSAL_IDENTIFIER = 'disable_autoproposal';
    const ENABLE_SHIPPING_IDENTIFIER = 'enable_shipping';
    const SHIPPING_AMOUNT_IDENTIFIER = 'shipping_amount';
    const NOTIFY_SALESREP_IDENTIFIER = 'notify_salesrep';
    const MIN_VALUE_IDENTIFIER = 'min_value';
    const MAX_VALUE_IDENTIFIER = 'max_value';

    public function getDiscount();

    public function getDisableAutoProposal();

    public function getEnableShipping();

    public function getShippingAmount();

    public function getNotifySalesrep();

    public function getMinValue();

    public function getMaxValue();
}