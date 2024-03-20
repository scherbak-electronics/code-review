<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Customer\Ticket\View;

use Magento\Catalog\Model\Product;
use Cart2Quote\Desk\Model\Ticket;

/**
 * Class Message
 * @package Cart2Quote\Desk\Block\Customer\Ticket\View
 */
class Message extends \Cart2Quote\Desk\Block\Customer\Quote\Ticket\View
{
    const FIRST_MESSAGE_KEY = 0;

    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

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
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Model\Ticket\MessageFactory
     */
    protected $messageModelFactory;

    /**
     * List of Messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $messages = [];

    /**
     * Message constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     * @param Ticket\MessageFactory $messageModelFactory
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
        \Cart2Quote\Desk\Model\Ticket\MessageFactory $messageModelFactory,
        array $data = []
    ) {
        $this->messageModelFactory = $messageModelFactory;

        parent::__construct(
            $context,
            $ticketRepositoryInterface,
            $messageRepositoryInterface,
            $ticket,
            $searchCriteria,
            $filterGroupFactory,
            $filterFactory,
            $currentCustomer,
            $dataHelper,
            $data
        );
    }

    /**
     * Get the list of messages by ticket ID
     *
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMessages()
    {
        if (!$this->messages && $this->getTicketId()) {
            $ticketFilter = $this->filterFactory->create()->setField('ticket_id')->setValue($this->getTicketId());
            $privateFilter = $this->filterFactory->create()->setField('is_private')->setValue(0);

            $filterGroupTicketId = $this->filterGroupFactory->create()->setFilters([$ticketFilter]);
            $filterGroupIsPrivate = $this->filterGroupFactory->create()->setFilters([$privateFilter]);

            $this->searchCriteria->setFilterGroups([$filterGroupTicketId, $filterGroupIsPrivate]);
            $this->messages = $this->messageRepositoryInterface->getList($this->searchCriteria);
        }

        return $this->messages;
    }

    /**
     * Get the message model
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @return $this
     */
    public function getModel(\Cart2Quote\Desk\Api\Data\MessageInterface $message)
    {
        return $this->messageModelFactory->create()->updateData($message);
    }

    /**
     * Combine all the classes
     *
     * @param int $key
     * @return string
     */
    public function getTicketDetailClass($key)
    {
        $class = '';
        $class .= $this->getFirstClass($key);
        $class .= $this->getLastClass($key);
        if (empty($class)) {
            $class .= $this->getDefaultClass();
        }

        $class .= ' '.$this->getOwnerClass();
        return $class;
    }

    /**
     * Get the default message CSS class
     *
     * @return string
     */
    public function getDefaultClass()
    {
        return 'message-details';
    }

    /**
     * Get the CSS class of the first message
     *
     * @param int $key
     * @return string
     */
    public function getFirstClass($key)
    {
        $class = '';
        if ($key == self::FIRST_MESSAGE_KEY) {
            $class = 'message-details-first';
        }

        return $class;
    }

    /**
     * Get the CSS class of the last message
     *
     * @param int $key
     * @return string
     */
    public function getLastClass($key)
    {
        $class = '';
        $messages = $this->getMessages();
        if ($key == count($messages) - 1) {
            $class = 'message-details-last';
        }

        return $class;
    }

    /**
     * Get the CSS class of the owner (customer or admin)
     *
     * @return string
     */
    public function getOwnerClass()
    {
        $message = $this->getMessage();
        if ($message && $message->getUserId()) {
            $class = 'owner-admin';
        } else {
            $class = 'owner-customer';
        }

        return $class;
    }

    /**
     * Get the URL to update the ticket list
     *
     * @return string
     */
    public function getAjaxUpdateMessagesUrl()
    {
        return $this->getUrl('desk/customer_message/listmessage/');
    }

    /**
     * Get the last ID from the messages
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
