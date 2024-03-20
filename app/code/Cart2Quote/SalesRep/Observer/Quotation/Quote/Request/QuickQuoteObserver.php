<?php
/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Quotation\Quote\Request;

/**
 * Class QuickQuoteObserver
 *
 * @package Cart2Quote\SalesRep\Observer\Quotation\Quote\Request
 */
class QuickQuoteObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Cart2Quote\SalesRep\Model\Order
     */
    private $order;

    /**
     * QuickQuoteObserver constructor.
     *
     * @param \Cart2Quote\SalesRep\Model\Order $order
     */
    public function __construct(
        \Cart2Quote\SalesRep\Model\Order $order
    ) {
        $this->order = $order;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        if ($this->order->isAssignedSalesRepsSet()) {
            $quoteUserId = $quote->getUserId();
            if ($quoteUserId === null || $quoteUserId == 0) {
                $quote->setUserId($this->order->getUserId());
                $this->order->createUser($quote, $quote->getUserId());
            }
        }
    }
}
