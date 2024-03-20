<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel;

/**
 * Class Order
 * @package Cart2Quote\SalesRep\Model\ResourceModel
 */
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('salesrep_order', 'id');
    }
}

