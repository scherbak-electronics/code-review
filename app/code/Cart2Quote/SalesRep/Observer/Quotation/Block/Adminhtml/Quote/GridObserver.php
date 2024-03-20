<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Quotation\Block\Adminhtml\Quote;

use Cart2Quote\SalesRep\Observer\CollectionAbstract;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GridObserver
 * @package Cart2Quote\SalesRep\Observer\Quotation\Block\Adminhtml\Quote
 */
class GridObserver extends CollectionAbstract
{
    /**
     * The function that gets executed when the event is observed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid\Collection $collection */
        if ($observer->getCollection() instanceof \Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid\Collection) {
            $this->innerJoin($observer->getCollection());
        }
    }

    /**
     * Get the corresponding sales rep type
     *
     * @return string
     */
    public function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_QUOTATION;
    }
}
