<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer;

use Cart2Quote\SalesRep\Observer\CollectionAbstractInterface;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CollectionAbstract
 * @package Cart2Quote\SalesRep\Observer
 */
abstract class CollectionAbstract implements ObserverInterface, CollectionAbstractInterface
{
    /**
     * Inner join
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid\Collection $collection
     * @return $this
     */
    public function innerJoin(\Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid\Collection $collection)
    {
        $assembledSelect = $collection->getSelect()->assemble();

        if (strpos($assembledSelect, $collection->getTable('salesrep_user')) === false) {
            $collection->getSelect()
                ->joinLeft(
                    $collection->getTable('salesrep_user'),
                    $collection->getTable('salesrep_user') . '.object_id = entity_id AND ' .
                    $collection->getTable('salesrep_user') . '.type_id = "' . $this->getTypeId() . '"',
                    $cols = '*',
                    $schema = null
                );
        }

        return $this;
    }
}
