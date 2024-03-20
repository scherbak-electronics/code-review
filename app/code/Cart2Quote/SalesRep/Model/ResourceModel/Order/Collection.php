<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel\Order;

/**
 * Class Collection
 * This class has nothing to do with Magento Sales Order
 * This is to assign salesrep order
 *
 * @package Cart2Quote\SalesRep\Model\ResourceModel\Order
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Cart2Quote\SalesRep\Model\Order',
            'Cart2Quote\SalesRep\Model\ResourceModel\Order'
        );
    }
}
