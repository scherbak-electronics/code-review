<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Model\Config;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

/**
 * Class YesNoUseConfig
 * @package Cart2Quote\Not2Order\Model\Config
 */
class YesNoUseConfig extends Boolean
{
    /**
     * Use Store setting
     *
     */
    const VALUE_USECONFIG = 2;

    /**
     * Retrieve all options array ( rewritten from parent )
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Use config'), 'value' => self::VALUE_USECONFIG],
                ['label' => __('No'), 'value' => self::VALUE_NO],
                ['label' => __('Yes'), 'value' => self::VALUE_YES],
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
            case self::VALUE_USECONFIG:
                return __('Use config');
        }

        return parent::getIndexOptionText($value);
    }
}
