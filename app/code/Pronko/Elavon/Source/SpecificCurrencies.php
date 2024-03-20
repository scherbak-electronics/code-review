<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SpecificCurrencies
 */
class SpecificCurrencies implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('All Allowed Currencies')],
            ['value' => 1, 'label' => __('Specific Currencies')]
        ];
    }
}
