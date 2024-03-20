<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;

use Cart2Quote\Desk\Api\Data\MessageInterface;
use Cart2Quote\Desk\Api\Data\MessageSearchResultsInterfaceFactory;
use Cart2Quote\Desk\Model\ResourceModel\Ticket\Message;
use Cart2Quote\Desk\Model\Ticket\MessageFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;

/**
 * Class MessageRepository
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket
 */
class MessageRepository implements \Cart2Quote\Desk\Api\MessageRepositoryInterface
{
    /**
     * Message Factory
     *
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * Message Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message
     */
    protected $messageResourceModel;

    /**
     * Search Result Factory
     *
     * @var MessageSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * Message Collection
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\Collection
     */
    protected $messageCollectionFactory;

    /**
     * Class MessageRepository constructor
     *
     * @param MessageFactory $messageFactory
     * @param Message $messageResourceModel
     * @param MessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param Collection|Message\Collection $messageCollectionFactory
     */
    public function __construct(
        MessageFactory $messageFactory,
        Message $messageResourceModel,
        MessageSearchResultsInterfaceFactory $searchResultsFactory,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\CollectionFactory $messageCollectionFactory
    ) {
        $this->messageFactory = $messageFactory;
        $this->messageResourceModel = $messageResourceModel;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(MessageInterface $message)
    {
        $this->validate($message);
        $messageModel = $this->messageFactory->create();
        $messageModel->updateData($message);
        $this->messageResourceModel->save($messageModel);
        $messageModel->_afterLoad();
        return $messageModel->getDataModel()->setId($messageModel->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getById($ticketId)
    {
        $message = $this->messageFactory->create();
        $this->messageResourceModel->load($message, $ticketId);
        return $message->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->messageCollectionFactory->create();

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
        } else {
            $collection->addOrder(\Cart2Quote\Desk\Api\Data\MessageInterface::CREATED_AT, 'DESC');
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $messages = [];

        /** @var \Cart2Quote\Desk\Model\Ticket\Message $messageModel */
        foreach ($collection as $messageModel) {
            $messageModel->_afterLoad();
            $messages[] = $messageModel->getDataModel();
        }

        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(MessageInterface $message)
    {
        return $this->deleteById($message->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($messageId)
    {
        $message = $this->messageFactory->create()->setId($messageId);
        $this->messageResourceModel->delete($message);
        return true;
    }

    /**
     * Validate ticket attribute values.
     *
     * @param MessageInterface $message
     * @throws InputException
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     *
     * @return void
     */
    protected function validate(MessageInterface $message)
    {
        $exception = new InputException();
        /**
         * Check message
         */
        if (!\Zend_Validate::is(trim($message->getMessage()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('message')));
        }

        /**
         * Check customer id and user id is both not set.
         * One of them needs to be set to make sure who is sending the message.
         */
        if (!\Zend_Validate::is(trim($message->getCustomerId()), 'NotEmpty') &&
            !\Zend_Validate::is(trim($message->getUserId()), 'NotEmpty')) {
            $exception->addError(
                __("%fieldName1 or %fieldName2 needs to be set, you cannot them both unset.",
                    [
                        'fieldName1' => 'customer_id',
                        'fieldName2' => 'user_id'
                    ]
                )
            );
        }

        /**
         * Check customer id and user id if both is set
         * A message cannot be submitted by a admin user or a customer at once. It is send by one of them.
         */
        if (\Zend_Validate::is(trim($message->getCustomerId()), 'NotEmpty') &&
            \Zend_Validate::is(trim($message->getUserId()), 'NotEmpty')) {
            $exception->addError(
                __("%fieldName1 or %fieldName2 cannot be both set, " .
                    "you need to set only the %fieldName1 or %fieldName2.",
                    [
                        'fieldName1' => 'customer_id',
                        'fieldName2' => 'user_id'
                    ]
                )
            );
        }

        /**
         * Check ticket id
         */
        if (!\Zend_Validate::is(trim($message->getTicketId()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('ticket_id')));
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
}