<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Cart2Quote
 */
namespace Cart2Quote\SalesRep\Block\Adminhtml\Container;

/**
 * Class Order
 * @package Cart2Quote\SalesRep\Block\Adminhtml\Container
 */
class Order extends \Cart2Quote\SalesRep\Block\Adminhtml\Container
{
    /**
     * Get the ID of the associated object
     *
     * @return int
     */
    public function getId()
    {
        return $this->getParentBlock()->getOrder()->getQuoteId();
    }

    /**
     * Get type id
     *
     * @return string
     */
    public function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_ORDER;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getParentBlock()->getOrder()->getCustomerId();
    }
}
