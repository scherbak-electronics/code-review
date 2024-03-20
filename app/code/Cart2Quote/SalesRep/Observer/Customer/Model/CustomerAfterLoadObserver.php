<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Customer\Model;

use Cart2Quote\SalesRep\Observer\AbstractObserver;

/**
 * Class CustomerAfterLoadObserver
 * @package Cart2Quote\SalesRep\Observer\Customer\Model
 */
class CustomerAfterLoadObserver extends AbstractObserver
{
    /**
     * Get the object type
     *
     * @return string
     */
    public function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER;
    }

    /**
     * Get the user id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getUserId(\Magento\Framework\Model\AbstractModel $object)
    {
        return $object->getUserId();
    }
}
