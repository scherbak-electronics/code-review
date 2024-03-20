<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ConnectionType
 */
class ConnectionType implements ArrayInterface
{
    const REMOTE = 'cc-form';
    const HOSTED = 'redirect';

    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::REMOTE,
                'label' => __('Remote Integration'),
            ],
            [
                'value' => self::HOSTED,
                'label' => __('Hosted Payment Page'),
            ]
        ];
    }
}
