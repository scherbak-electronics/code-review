<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Observer;

/**
 * Class CreateQuoteObserver
 *
 * @package Cart2Quote\AutoProposal\Observer
 */
class CreateQuoteObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Flag to make the observer is executed once
     *
     * @var bool
     */
    protected static $isCalled = false;
    /**
     * @var \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider
     */
    private $strategyProvider;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * QuoteRequestObserver constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider $strategyProvider
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider $strategyProvider
    ) {
        $this->strategyProvider = $strategyProvider;
        $this->logger = $logger;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        self::$isCalled = true;
        try {
            $quote = $this->quoteFactory->create()->load($observer->getResult()->getLastQuoteId());
            if ($this->strategyProvider->getStrategy()->isEnabled()) {
                $this->strategyProvider->getStrategy()->propose($quote);
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
