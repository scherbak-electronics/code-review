<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Source;

use Magento\Framework\Option\ArrayInterface;
use Pronko\Elavon\Spi\FraudConfigInterface;

/**
 * Class FraudModeProvider
 * @package Pronko\Elavon\Source
 */
class FraudModeProvider implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => FraudConfigInterface::ACTIVE,
                'label' => __('Active'),
            ],
            [
                'value' => FraudConfigInterface::PASSIVE,
                'label' => __('Passive'),
            ],
            [
                'value' => FraudConfigInterface::OFF,
                'label' => __('Off'),
            ]
        ];
    }
}
