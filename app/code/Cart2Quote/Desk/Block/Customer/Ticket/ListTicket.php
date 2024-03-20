<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Customer\Ticket;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class ListTicket
 * @package Cart2Quote\Desk\Block\Customer\Ticket
 */
class ListTicket extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Product ticket collection
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection
     */
    protected $collection;

    /**
     * Ticket resource model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupFactory
     */
    protected $filterGroupFactory;

    /**
     * @var \Magento\Framework\Api\FilterFactory
     */
    protected $filterFactory;

    /**
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $messageRepositoryInterface;

    /**
     * ListTicket constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filterFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
        $this->currentCustomer = $currentCustomer;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroupFactory = $filterGroupFactory;
        $this->filterFactory = $filterFactory;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->getTickets()) {
            $toolbar = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'customer_ticket_list.toolbar'
            )->setCollection(
                $this->getTickets()
            );

            $this->setChild('toolbar', $toolbar);
        }

        return parent::_prepareLayout();
    }

    /**
     * Get tickets
     *
     * @return bool|\Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection
     */
    public function getTickets()
    {
        if (!($customerId = $this->currentCustomer->getCustomerId())) {
            return false;
        }

        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addCustomerFilter($customerId)
                ->setDateOrder();
        }

        return $this->collection;
    }

    /**
     * Get ticket link
     *
     * @return string
     */
    public function getTicketLink()
    {
        return $this->getUrl('desk/customer/view/');
    }

    /**
     * Get product link
     *
     * @return string
     */
    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * Load ticket
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $tickets = $this->getTickets();
        if ($tickets) {
            $tickets->load();
        }

        return parent::_beforeToHtml();
    }

    /**
     * Return label formatted for HTML
     *
     * @param string $label The label
     * @return string
     */
    public function getLabelHtml($label)
    {
        return $this->escapeHtml(ucfirst($label));
    }

    /**
     * @param \Cart2Quote\Desk\Model\Ticket $ticket
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function newTicketAmount($ticket)
    {
        $messages = $this->getMessages($ticket);
        $amount = count($messages);
        if ($amount > 0) {
            return $amount;
        }

        return 0;
    }

    /**
     * @param \Cart2Quote\Desk\Model\Ticket $ticket
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMessages($ticket)
    {
        $viewedTime = $ticket->getCustomerViewedAt();
        $ticketId = $ticket->getTicketId();
        $ticketFilter = $this->filterFactory->create()->setField('ticket_id')->setValue($ticketId);
        $privateFilter = $this->filterFactory->create()->setField('is_private')->setValue(0);
        $viewedFilter = $this->filterFactory->create()->setField('main_table.updated_at')->setValue($viewedTime)->setConditionType("gteq");

        $filterGroupTicketId = $this->filterGroupFactory->create()->setFilters([$ticketFilter]);
        $filterGroupIsPrivate = $this->filterGroupFactory->create()->setFilters([$privateFilter]);
        $filterViewed = $this->filterGroupFactory->create()->setFilters([$viewedFilter]);

        $this->searchCriteria->setFilterGroups([$filterGroupTicketId, $filterGroupIsPrivate, $filterViewed]);

        return $this->messageRepositoryInterface->getList($this->searchCriteria);
    }
}
