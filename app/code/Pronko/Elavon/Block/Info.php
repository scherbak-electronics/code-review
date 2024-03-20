<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Block;

use Magento\Payment\Block\ConfigurableInfo;
use Magento\Sales\Model\Order\Payment;

/**
 * Class Info
 */
class Info extends ConfigurableInfo
{
    /**
     * Returns user friendly field label
     *
     * @param string $field
     * @return string
     */
    public function getLabel($field)
    {
        return ucwords(str_replace('_', ' ', $field));
    }
}
