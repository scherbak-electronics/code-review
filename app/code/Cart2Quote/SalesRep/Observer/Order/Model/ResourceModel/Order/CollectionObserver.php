<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Order\Model\ResourceModel\Order;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CollectionObserver
 * @package Cart2Quote\SalesRep\Observer\Order\Model\ResourceModel\Order
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
            $this->innerJoin($observer->getCollection());
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
        $collection->getSelect()
            ->joinLeft(
                $collection->getTable('salesrep_user'),
                'object_id = `e`.'.$collection->getIdFieldName().
                ' AND '.$collection->getTable('salesrep_user').'.type_id = "'.$this->getTypeId().'"',
                $cols = '*',
                $schema = null
            );

        return $this;
    }

    /**
     * Get the corresponding sales rep type
     *
     * @return string
     */
    protected function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_ORDER;
    }
}
