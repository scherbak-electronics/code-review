<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Customer\Quote\Ticket;

/**
 * Class View
 * @package Cart2Quote\Desk\Block\Customer\Quote\Ticket
 */
class View extends \Cart2Quote\Desk\Block\Customer\Ticket\View
{
    /**
     * Quote ID
     *
     * @var int
     */
    protected $quoteId;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quote;

    /**
     * Initialize ticket id
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setQuoteId($this->getRequest()->getParam('quote_id', false));
    }

    /**
     * Set the quote id
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        $this->quoteId = $quoteId;
        if ($quoteId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $quote = $objectManager->create('Cart2Quote\Quotation\Api\QuoteRepositoryInterface')->get($quoteId);

            if ($quote && $quote->getClonedQuote() && $quote->getLinkedQuotationId()) {
                $linkedQuoteId = $quote->getLinkedQuotationId();
                $backendQuote = $objectManager->create(
                    'Cart2Quote\Quotation\Api\QuoteRepositoryInterface'
                )->get($linkedQuoteId);
                $this->quoteId = $quote->getLinkedQuotationId();
                $this->quote = $backendQuote;
            }
        }

        return $this;
    }

    /**
     * Retrieve current quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if ($this->quote) {
            return $this->quote;
        }

        return $this->registry->registry('current_quote');
    }

    /**
     * @return int
     */
    public function getTicketId()
    {
        $ticketId = 0;

        $this->setTicketIdFromQuote();
        if ($this->ticketId != null) {
            $ticketId = $this->ticketId;
        }

        return $ticketId;
    }

    /**
     * Set ticket id if quote has ticket
     *
     * @return bool
     */
    public function setTicketIdFromQuote()
    {
        $quoteId = $this->quoteId;

        if ($quoteId) {
            $ticket = $this->ticketRepositoryInterface->getByQuoteId($quoteId);
            if (isset($ticket)) {
                $this->setTicketId($ticket->getId());

                return true;
            }
        }

        return false;
    }

    /**
     * Get the subject
     *
     * @return String
     */
    public function getSubject()
    {
        $subject = '';
        if ($this->getTicketData()->getSubject()) {
            $subject = $this->getTicketData()->getSubject();
        } elseif ($this->getQuote()) {
            $quotePrefix = $this->getQuote()->getIncrementId();
            if (isset($quotePrefix)) {
                $subject = $quotePrefix;
            }
        }

        return $subject;
    }
}
