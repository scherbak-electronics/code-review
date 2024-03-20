<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Observer\Customer\Model\ResourceModel\Customer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CollectionObserver
 * @package Cart2Quote\SalesRep\Observer\Customer\Model\ResourceModel\Customer
 */
class CollectionObserver implements ObserverInterface
{
    /**
     * The function that gets executed when the event is observed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getCollection() instanceof \Magento\Customer\Model\ResourceModel\Customer\Collection) {
            if (!preg_match('/alert_.+/', $observer->getEvent()->getCollection()->getIdFieldName())) {
                $this->innerJoin($observer->getCollection());
            }
        }
    }

    /**
     * Inner join
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
     * @return $this
     */
    public function innerJoin(\Magento\Customer\Model\ResourceModel\Customer\Collection $collection)
    {
        $assembledSelect = $collection->getSelect()->assemble();

        if (strpos($assembledSelect, $collection->getTable('salesrep_user')) === false) {
            $collection->getSelect()
                ->joinLeft(
                    $collection->getTable('salesrep_user'),
                    $collection->getTable('salesrep_user') . '.object_id = `e`.' . $collection->getIdFieldName() .
                    ' AND ' . $collection->getTable('salesrep_user') .
                    '.type_id = "' . \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER . '"',
                    $cols = '*',
                    $schema = null
                );
        }
        return $this;
    }
}
