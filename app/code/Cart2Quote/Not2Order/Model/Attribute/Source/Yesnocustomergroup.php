<?php
/**
 * ================================================================
 * Not2Order Model - Yesnocustomergroup
 * ================================================================
 *
 * Data model that returns the hide condition options to populate
 * the backend renderer for the hide ... product attribute.
 *
 * Copyright © 2016 Cart2Quote. All rights reserved.
 * See LICENSE.txt for license details.
 *
 * @package    Not2Order
 * @copyright  Cart2Quote 2016
 * @author     Rolf van der Kaaden <support@cart2quote.com>
 */

namespace Cart2Quote\Not2Order\Model\Attribute\Source;

/**
 * Class Yesnocustomergroup
 *
 * @package Cart2Quote\Not2Order\Model\Attribute\Source
 */
class Yesnocustomergroup extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    /**
     * Add extra option value
     */
    const VALUE_CUSTOMERGROUP = 2;

    /**
     * Retrieve all options array ( rewritten from parent )
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
     * @param  string|int $value
     * @return string|bool
     */
    public function getIndexOptionText($value)
    {
        switch ($value) {
            case self::VALUE_YES:
                return 'Yes';
            case self::VALUE_NO:
                return 'No';
            case self::VALUE_CUSTOMERGROUP:
                return 'Only for selected customer groups';
        }

        return parent::getIndexOptionText($value);
    }
}
