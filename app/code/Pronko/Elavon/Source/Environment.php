<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Environment
 */
class Environment implements ArrayInterface
{
    /**#@+
     * Config names constants
     */
    const PRODUCTION = 'production';
    const SANDBOX = 'sandbox';
    /**#@-*/

    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PRODUCTION,
                'label' => __('Production'),
            ],
            [
                'value' => self::SANDBOX,
                'label' => __('Sandbox'),
            ]
        ];
    }
}
