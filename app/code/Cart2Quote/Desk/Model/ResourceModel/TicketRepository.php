<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel;

use Cart2Quote\Desk\Api\Data\TicketInterface;
use Cart2Quote\Desk\Api\Data\TicketSearchResultsInterfaceFactory;
use Cart2Quote\Desk\Model\ResourceModel\Ticket;
use Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection;
use Cart2Quote\Desk\Model\Ticket\Status;
use Cart2Quote\Desk\Model\TicketFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;

/**
 * Class TicketRepository
 * @package Cart2Quote\Desk\Model\ResourceModel
 */
class TicketRepository implements \Cart2Quote\Desk\Api\TicketRepositoryInterface
{
    /**
     * Ticket Factory
     *
     * @var TicketFactory
     */
    protected $ticketFactory;

    /**
     * Ticket Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket
     */
    protected $ticketResourceModel;

    /**
     * Search Results Factory
     *
     * @var TicketSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * Ticket Collection
     *
     * @var Collection
     */
    protected $ticketCollection;

    /**
     * Status Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status
     */
    protected $statusResourceModel;

    /**
     * Priority Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority
     */
    protected $priorityResourceModel;

    /**
     * Status Factory
     *
     * @var \Cart2Quote\Desk\Model\Ticket\StatusFactory
     */
    protected $statusFactory;

    /**
     * Cart2Quote Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $dataHelper;

    /**
     * TicketRepository constructor
     *
     * @param TicketFactory $ticketFactory
     * @param Ticket $ticketResourceModel
     * @param TicketSearchResultsInterfaceFactory $searchResultsFactory
     * @param Collection $ticketCollection
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status $statusResourceModel
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority $priorityResourceModel
     * @param \Cart2Quote\Desk\Model\Ticket\StatusFactory $statusFactory
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     */
    public function __construct(
        TicketFactory $ticketFactory,
        Ticket $ticketResourceModel,
        TicketSearchResultsInterfaceFactory $searchResultsFactory,
        Collection $ticketCollection,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status $statusResourceModel,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority $priorityResourceModel,
        \Cart2Quote\Desk\Model\Ticket\StatusFactory $statusFactory,
        \Cart2Quote\Desk\Helper\Data $dataHelper
    ) {
        $this->ticketFactory = $ticketFactory;
        $this->ticketResourceModel = $ticketResourceModel;
        $this->ticketCollection = $ticketCollection;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->statusResourceModel = $statusResourceModel;
        $this->priorityResourceModel = $priorityResourceModel;
        $this->statusFactory = $statusFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TicketInterface $ticket)
    {
        $ticket = $this->addPriority($ticket);
        $ticket = $this->addStatus($ticket);
        $this->validate($ticket);

        $ticketModel = $this->ticketFactory->create();
        $ticketModel->updateData($ticket);

        $this->ticketResourceModel->save($ticketModel);
        $ticketModel->afterLoad();
        $ticket = $ticketModel->getDataModel();
        return $ticket;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($ticketId)
    {
        $ticketModel = $this->ticketFactory->create();
        $this->ticketResourceModel->load($ticketModel, $ticketId);
        $ticketModel->afterLoad();
        return $ticketModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getByQuoteId($quoteId)
    {
        $this->ticketCollection->addFieldToFilter('quote_id', $quoteId);
        $this->ticketCollection->addFieldToFilter('deleted', 0);
        $ticketModel = $this->ticketCollection->getLastItem();

        $ticketModel->afterLoad();

        return $ticketModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->ticketCollection;

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $tickets = [];
        /** @var \Cart2Quote\Desk\Model\Ticket $ticketModel */
        foreach ($collection as $ticketModel) {
            $tickets[] = $ticketModel->getDataModel();
        }

        $searchResults->setItems($tickets);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TicketInterface $ticket)
    {
        return $this->deleteById($ticket->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ticketId)
    {
        $ticket = $this->getById($ticketId);
        $ticket->setDeleted(1);
        $this->save($ticket);
        return true;
    }

    /**
     * Validate ticket attribute values.
     *
     * @param TicketInterface $ticket
     * @throws InputException
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     *
     * @return void
     */
    protected function validate(TicketInterface $ticket)
    {
        $exception = new InputException();

        if (!\Zend_Validate::is(trim($ticket->getSubject()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('subject')));
        }

        if (!\Zend_Validate::is(trim($ticket->getCustomerId()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('customer_id')));
        }

        if (!\Zend_Validate::is(trim($ticket->getStatusId()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('status_id')));
        }

        if (!\Zend_Validate::is(trim($ticket->getPriorityId()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('priority_id')));
        }

        if (!\Zend_Validate::is(trim($ticket->getStoreId()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('store_id')));
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     *
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $field = $filter->getField();
            $value = $filter->getValue();
            if (isset($field) && isset($value)) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $field;
                $conditions[] = [$conditionType => $value];
            }

        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Create priority if empty
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    protected function addPriority(TicketInterface $ticket)
    {
        if (!\Zend_Validate::is(trim($ticket->getPriorityId()), 'NotEmpty')) {
            $ticket->setPriorityId($this->dataHelper->getDefaultPriority());
        }

        return $ticket;
    }

    /**
     * Create status if empty
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    protected function addStatus(TicketInterface $ticket)
    {
        if (!\Zend_Validate::is(trim($ticket->getStatusId()), 'NotEmpty')) {
            $status = $this->statusFactory->create();
            $this->statusResourceModel->loadByCode($status, Status::STATUS_OPEN);
            $ticket->setStatusId($status->getId());
        }

        return $ticket;
    }
}