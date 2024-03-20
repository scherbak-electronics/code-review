<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Customer\Ticket;

use Magento\Catalog\Model\Product;
use Cart2Quote\Desk\Model\Ticket;

/**
 * Class View
 * @package Cart2Quote\Desk\Block\Customer\Ticket
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * Message model factory
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $messageRepositoryInterface;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $searchCriteria;

    /**
     * Filter group
     *
     * @var \Magento\Framework\Api\Search\FilterGroupFactory
     */
    protected $filterGroupFactory;

    /**
     * Filter Factory
     *
     * @var \Magento\Framework\Api\FilterFactory
     */
    protected $filterFactory;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Ticket ID
     *
     * @var int
     */
    protected $ticketId;

    /**
     * The ticket
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected $ticket;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $dataHelper = null;

    /**
     * View constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filterFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Cart2Quote\Desk\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->registry = $context->getRegistry();
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->ticket = $ticket;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroupFactory = $filterGroupFactory;
        $this->filterFactory = $filterFactory;
        $this->currentCustomer = $currentCustomer;
        $this->dataHelper = $dataHelper;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize ticket id
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTicketId($this->getRequest()->getParam('id', false));
    }

    /**
     * Get ticket data
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    public function getTicketData()
    {
        if ($this->getTicketId() && !$this->ticket->getId()) {
            $this->ticket = $this->ticketRepositoryInterface->getById($this->getTicketId());
        }

        if ($this->ticket->getId() == null) {
            $this->fillDefaultValues();
        }

        return $this->ticket;
    }

    /**
     * Return ticket customer url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('ticket/customer');
    }

    /**
     * Get formatted date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

    /**
     * Get formatted time
     *
     * @param string $date
     * @return string
     */
    public function timeFormat($date)
    {
        return $this->formatTime($date);
    }

    /**
     * Block to HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }

    /**
     * Set the ticket id
     *
     * @param int $id
     * @return $this
     */
    public function setTicketId($id)
    {
        $this->ticketId = $id;
        return $this;
    }

    /**
     * Get the ticket id
     *
     * @return int
     */
    public function getTicketId()
    {
        $ticketId = 0;

        if ($this->ticketId != null) {
            $ticketId = $this->ticketId;
        }

        return $ticketId;
    }

    /**
     * Get the assignee name. If no assignee is set then return unassigned.
     *
     * @return \Magento\Framework\Phrase|null|string
     */
    public function getAssignedTo()
    {
        $assigneeName = $this->getTicketData()->getAssigneeName();
        if (empty($assigneeName)) {
            return __("Unassigned");
        } else {
            return $assigneeName;
        }
    }

    /**
     * Get the formatted create date of the ticket.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        $createdAt = $this->getTicketData()->getCreatedAt();
        return $this->dateFormat($createdAt).' '.$this->timeFormat($createdAt);
    }

    /**
     * Sets the default values for a new ticket
     *
     * @return $this
     */
    public function fillDefaultValues()
    {
        $defaultPriorityId = $this->dataHelper->getDefaultPriority();
        $defaultPriorityCode = $this->dataHelper->getDefaultPriorityCode();

        $this->ticket->setPriorityId($defaultPriorityId);
        $this->ticket->setPriority($defaultPriorityCode);

        $this->ticket->setStatusId(1);
        $this->ticket->setStatus(\Cart2Quote\Desk\Model\Ticket\Status::STATUS_OPEN);

        return $this;
    }

    /**
     * @return int
     */
    public function canEditTitle()
    {
        return $this->dataHelper->getCustomerCanEdit();
    }
}
