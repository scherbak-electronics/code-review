<?php
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

namespace Magetop\Osc\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CustomField
 * @package Magetop\Osc\Model\System\Config\Source
 */
class CustomField implements ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $result = [];

        $result[] = ['value' => '', 'label' => __('-- Please select --')];
        for ($i = 1; $i <= 3; $i++) {
            $result[] = ['value' => 'mposc_field_' . $i, 'label' => __('Custom Field %1', $i)];
        }

        return $result;
    }
}
