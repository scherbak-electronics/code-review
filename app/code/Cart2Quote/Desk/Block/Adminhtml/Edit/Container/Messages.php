<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Container;

/**
 * Class Messages
 * @package Cart2Quote\Desk\Block\Adminhtml\Edit\Container
 */
class Messages extends \Magento\Backend\Block\Template
{
    /**
     * Message model factory
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $messageRepositoryInterface;

    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Model\Ticket\MessageFactory
     */
    protected $messageModelFactory;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $searchCriteria;

    /**
     * Filter group
     *
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    protected $filterGroup;

    /**
     * API filter
     *
     * @var \Magento\Framework\Api\Filter
     */
    protected $filter;

    /**
     * Messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $messages = [];

    /**
     * Class Messages constructor
     *
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Cart2Quote\Desk\Model\Ticket\MessageFactory $messageModelFactory
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Cart2Quote\Desk\Model\Ticket\MessageFactory $messageModelFactory,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->messageModelFactory = $messageModelFactory;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;

        parent::__construct($context, $data);
    }

    /**
     * Get an array of messages
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface[]
     */
    public function getMessages()
    {
        if (!$this->messages && $this->getTicket()) {
            $this->filter->setField('ticket_id')->setValue($this->getTicket()->getId());
            $this->filterGroup->setFilters([$this->filter]);
            $this->searchCriteria->setFilterGroups([$this->filterGroup]);
            $this->messages = $this->messageRepositoryInterface->getList($this->searchCriteria);
        }

        return $this->messages;
    }

    /**
     * Get the ticket
     *
     * @return \Cart2Quote\Desk\Model\Ticket
     */
    public function getTicket()
    {
        /**
         * Parent set in layout.xml
         * @var \Cart2Quote\Desk\Block\Adminhtml\Edit\Form $parentBlock
         */
        $parentBlock = $this->getParentBlock();
        return $parentBlock->getTicket();
    }

    /**
     * Get the ticket ID
     *
     * @return int
     */
    public function getTicketId()
    {
        $ticketId = 0;
        if ($this->getTicket()) {
            $ticketId = $this->getTicket()->getTicketId();
        }

        return $ticketId;
    }

    /**
     * Get the AJAX update message URL
     *
     * @return string
     */
    public function getAjaxUpdateMessagesUrl()
    {
        $url =  $this->getUrl(
            '*/*/listmessage/',
            [
                'id' => $this->getTicketId()
            ]
        );

        return $url;
    }

    /**
     * Get the last message ID
     *
     * @return bool|int
     */
    public function getLastId()
    {
        $messages = $this->getMessages();
        /** @var \Cart2Quote\Desk\Api\Data\MessageInterface $firstMessage */
        $firstMessage = reset($messages);
        if ($firstMessage) {
            return $firstMessage->getId();
        } else {
            return false;
        }
    }
}
