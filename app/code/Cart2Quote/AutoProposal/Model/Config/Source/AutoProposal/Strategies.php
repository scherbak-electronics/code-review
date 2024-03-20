<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Cart2Quote
 * Used in creating options for Form element types config value selection
 *
 */

namespace Cart2Quote\AutoProposal\Model\Config\Source\AutoProposal;

/**
 * Class Strategies
 *
 * @package Cart2Quote\AutoProposal\Model\Config\Source\AutoProposal
 */
class Strategies implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * Strategies constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->options as $key => $option) {
            $optionArray[$key] = $option;
        }

        return $optionArray;
    }
}
