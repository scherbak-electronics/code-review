<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Model\Config;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

/**
 * Class YesNoCustomerGroup
 * @package Cart2Quote\Not2Order\Model\Config
 */
class YesNoCustomerGroup extends Boolean
{
    /**
     * Add extra option value
     *
     */
    const VALUE_CUSTOMERGROUP = 2;

    /**
     * Retrieve all options array ( rewritten from parent )
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Yes'), 'value' => self::VALUE_YES],
                ['label' => __('No'), 'value' => self::VALUE_NO],
                ['label' => __('Only for selected customer groups'), 'value' => self::VALUE_CUSTOMERGROUP],
            ];
        }

        return $this->_options;
    }

    /**
     * Get a text for index option value ( rewritten from parent )
     *
     * @param  string|int $value
     * @return string|bool
     */
    public function getIndexOptionText($value)
    {
        switch ($value) {
            case self::VALUE_YES:
                return __('Yes');
            case self::VALUE_NO:
                return __('No');
            case self::VALUE_CUSTOMERGROUP:
                return __('Only for selected customer groups');
        }

        return parent::getIndexOptionText($value);
    }
}
