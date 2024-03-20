<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Observer;

/**
 * Class DefaultObserver
 *
 * Backward compatibility where all extended classes from Cart2Quote\Quotation\Controller\Quote\Ajax\AjaxAbstract
 * use "Default" as event prefix
 *
 * @package Cart2Quote\AutoProposal\Observer
 * @deprecated see \Cart2Quote\AutoProposal\Observer\CreateQuoteObserver
 */
class DefaultObserver extends \Cart2Quote\AutoProposal\Observer\CreateQuoteObserver
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!CreateQuoteObserver::$isCalled &&
            $observer->getAction() instanceof \Cart2Quote\Quotation\Controller\Quote\Ajax\CreateQuote
        ) {
            parent::execute($observer);
        }
    }
}
