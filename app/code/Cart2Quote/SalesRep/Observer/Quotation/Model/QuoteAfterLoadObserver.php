<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Quotation\Model;

use Cart2Quote\SalesRep\Observer\AbstractObserver;

/**
 * Class QuoteAfterLoadObserver
 * @package Cart2Quote\SalesRep\Observer\Quotation\Model
 */
class QuoteAfterLoadObserver extends AbstractObserver
{
    /**
     * Check if this is the quotation quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getDataObject() instanceof \Cart2Quote\Quotation\Model\Quote) {
            parent::execute($observer);
        }
    }

    /**
     * Get the object type
     *
     * @return string
     */
    public function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_QUOTATION;
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

    /**
     * Get the quote id because order id is not always available and it makes it easier to make the link to quotes.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getObjectId(\Magento\Framework\Model\AbstractModel $object)
    {
        $objectId = (int)$object->getId();
        if ($objectId === null && $object->getEntityId()) {
            $objectId = (int)$object->getEntityId();
        }

        return $objectId;
    }
}
