<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab;

/**
 * Class ViewTicket
 * @package Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab
 */
class ViewTicket extends \Magento\Backend\Block\Widget implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Quote ID
     *
     * @var int
     */
    protected $quoteId;

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Ticket Repository
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * Ticket
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $ticket;

    /**
     * ViewTicket constructor.
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Ticket');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Support Desk');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        if ($this->isGuestQuote()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }

    /**
     * Get Quote Id
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getQuote()->getId();
    }

    /**
     * Set ticket from quote
     */
    public function setQuoteTicket()
    {
        $quoteId = $this->getQuoteId();

        if ($quoteId) {
            $ticket = $this->ticketRepositoryInterface->getByQuoteId($quoteId);
            if (isset($ticket)) {
                if ($ticket->getId()) {
                    $this->ticket = $ticket;
                }
            }
        }
    }

    /**
     * Get the ticket from registry
     *
     * @return \Cart2Quote\Desk\Model\Ticket|null
     */
    public function getTicket()
    {
        if (!$this->ticket) {
            $this->setQuoteTicket();
        }

        return $this->ticket;
    }

    /**
     * Get the ticket id
     *
     * @return int
     */
    public function getTicketId()
    {
        return $this->ticket->getId();
    }

    /**
     * @return bool
     */
    private function isGuestQuote()
    {
        $quote = $this->getQuote();
        $customerId = $quote->getCustomerId();

        if ($customerId != \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID) {
            return false;
        }

        return true;
    }
}
