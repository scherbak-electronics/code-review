<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel\User;

/**
 * Class Collection
 * @package Cart2Quote\SalesRep\Model\ResourceModel\User
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
            'Cart2Quote\SalesRep\Model\User',
            'Cart2Quote\SalesRep\Model\ResourceModel\User'
        );
    }
}
