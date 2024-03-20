<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer;

/**
 * Class AfterSaveAbstractObserver
 * @package Cart2Quote\SalesRep\Observer
 */
abstract class AfterSaveAbstractObserver extends AbstractObserver
{
    /**
     * The function that gets executed when the event is observed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\Model\AbstractModel $object */
        $object = $observer->getDataObject();
        $userId = $this->getUserId($object);
        if ($userId > 0) {
            $this->createUser($object, $userId);
        } elseif ($userId === 0) {
            $this->deleteUser($object);
        }

        $this->saveByCustomerSalesRep($object, 1);
    }
}
