<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy;

use Cart2Quote\Quotation\Model\Carrier\QuotationShipping;

/**
 * Class Range
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy
 */
class Range extends \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\AbstractStrategy
{
    /**
     * @var boolean
     */
    public static $autoProposal = false;

    /**
     * Config path
     */
    const XML_CONFIG_PATH_AUTO_PROPOSAL_RANGES = 'cart2quote_quotation/proposal/auto_proposal_ranges';
    /**
     * Strategy identifier
     */
    const STRATEGY_IDENTIFIER = 'subtotal_ranges';
    /**
     * @var \Cart2Quote\AutoProposal\Model\Quote\Email\Sender\NotifySalesRepSender
     */
    private $notifySalesRepSender;
    /**
     * @var \Cart2Quote\AutoProposal\Helper\Range
     */
    private $rangeHelper;
    /**
     * @var \Cart2Quote\AutoProposal\Model\Range|bool
     */
    private $currentRange = false;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Range constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Cart2Quote\AutoProposal\Helper\Range $rangeHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender
     * @param \Cart2Quote\AutoProposal\Model\Quote\Email\Sender\NotifySalesRepSender $notifySalesRepSender
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Cart2Quote\AutoProposal\Helper\Range $rangeHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender,
        \Cart2Quote\AutoProposal\Model\Quote\Email\Sender\NotifySalesRepSender $notifySalesRepSender
    ) {
        parent::__construct($eventManager, $dateTime, $scopeConfig, $quoteProposalSender);

        $this->notifySalesRepSender = $notifySalesRepSender;
        $this->rangeHelper = $rangeHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \Cart2Quote\AutoProposal\Model\Range|bool
     */
    private function getCurrentRange()
    {
        if (!$this->currentRange) {
            $this->currentRange = $this->rangeHelper->getCurrentRange($this->getQuote());
        }

        return $this->currentRange;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setProposalPrices()
    {
        if (!$this->getCurrentRange()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('There are no ranges configured')
            );
        }

        if (!$this->getCurrentRange()->getDisableAutoProposal()) {
            self::$autoProposal = true;
            $this->getQuote()->setSubtotalProposal(100 - $this->getCurrentRange()->getDiscount(), true);
            if ($this->getCurrentRange()->getEnableShipping()) {
                $this->setAutoproposalShippingMethod();
            } else {
                $this->getQuote()->recollectQuote();
            }
            $this->getQuote()->save();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setAutoproposalShippingMethod()
    {
        $shippingMethodCode = sprintf('%s_%s', QuotationShipping::CODE, QuotationShipping::CODE);
        $this->getQuote()->setShippingMethod($shippingMethodCode);
        $this->getQuote()->setFixedShippingPrice($this->getCurrentRange()->getShippingAmount());
        //TODO Refactor so save is not needed here. It is used now to retrieve the fixed shipped price in Quotation shipping
        $this->getQuote()->save();
        $this->getQuote()->collectShippingRates();

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function send()
    {
        if (!$this->getCurrentRange()->getDisableAutoProposal()) {
            parent::send();
        }
        if ($this->getCurrentRange()->getNotifySalesrep()) {
            $this->notifySalesRepSender->send($this->getQuote());
        }

        return $this;
    }
}
