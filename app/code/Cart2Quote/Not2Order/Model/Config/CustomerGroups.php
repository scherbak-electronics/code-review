<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;

/**
 * Class CustomerGroups
 * @package Cart2Quote\Not2Order\Model\Config
 */
class CustomerGroups implements ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    private $customerGroupCollection;

    /**
     * CustomerGroups constructor.
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
     */
    public function __construct(Collection $customerGroupCollection)
    {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}
