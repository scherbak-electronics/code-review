<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Model\Config\ReplacementButton;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Target
 * @package Cart2Quote\Not2Order\Model\Config\ReplacementButton
 */
class Target implements ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '_blank', 'label' => __('Open link in a new window or tab')],
            ['value' => '_self', 'label' => __('Open link in the same page')]
        ];
    }
}
