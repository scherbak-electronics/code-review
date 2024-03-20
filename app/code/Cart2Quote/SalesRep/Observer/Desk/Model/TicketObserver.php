<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Desk\Model;

/**
 * Class TicketObserver
 * @package Cart2Quote\SalesRep\Observer\SalesRep\Model
 */
class TicketObserver extends \Cart2Quote\SalesRep\Observer\AfterSaveAbstractObserver
{
    /**
     * Save the object if the user id is newly set.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        parent::execute($observer);

        if (!$observer->getObject()->getAssigneeId() && $userId = $observer->getObject()->getUserId()) {
            $observer->getObject()->setAssigneeId($userId)->save();
        }
    }

    /**
     * Get the object type
     *
     * @return string
     */
    public function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_TICKET;
    }

    /**
     * Get the user id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getUserId(\Magento\Framework\Model\AbstractModel $object)
    {
        return $object->getAssigneeId();
    }
}
