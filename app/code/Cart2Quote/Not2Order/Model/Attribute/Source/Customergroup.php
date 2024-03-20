<?php
/**
 * ================================================================
 * Not2Order Model - Customergroup
 * ================================================================
 *
 * Data model that returns all available customer groups to populate
 * the backend renderer for the customergroups product attribute.
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
 * Class Customergroup
 *
 * @package Cart2Quote\Not2Order\Model\Attribute\Source
 */
class Customergroup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Customer groups
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_groupCollection;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $groupCollection
     */
    public function __construct(\Magento\Customer\Model\ResourceModel\Group\Collection $groupCollection)
    {
        $this->_groupCollection = $groupCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->_groupCollection->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
