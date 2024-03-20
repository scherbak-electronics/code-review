<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Cart2Quote\DeskMessageTemplate\Api\MessageRepositoryInterface;
use Cart2Quote\DeskMessageTemplate\Api\Data\MessageInterface;
use Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message as ResourceMessage;
use Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message\CollectionFactory as MessageCollectionFactory;
use Cart2Quote\DeskMessageTemplate\Api\Data\MessageSearchResultsInterfaceFactory;
use Cart2Quote\DeskMessageTemplate\Api\Data\MessageInterfaceFactory;

/**
 * Class MessageRepository
 * @package Cart2Quote\DeskMessageTemplate\Model
 */
class MessageRepository implements MessageRepositoryInterface
{
    /**
     * @var ResourceMessage
     */
    protected $resource;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var MessageCollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * @var MessageSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var MessageInterfaceFactory
     */
    protected $messageDataFactory;

    /**
     * MessageRepository constructor.
     *
     * @param ResourceMessage $resource
     * @param MessageFactory $messageFactory
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param MessageInterfaceFactory $messageDataFactory
     * @param MessageSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceMessage $resource,
        MessageFactory $messageFactory,
        MessageCollectionFactory $messageCollectionFactory,
        MessageInterfaceFactory $messageDataFactory,
        MessageSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->messageFactory = $messageFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->messageDataFactory = $messageDataFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Message data
     *
     * @param MessageInterface $message
     * @return MessageInterface
     * @throws CouldNotSaveException
     */
    public function save(MessageInterface $message)
    {
        try {
            $this->resource->save($message);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $message;
    }

    /**
     * Load Message data by given Message Id
     *
     * @param string $messageId
     * @return MessageInterface
     * @throws NoSuchEntityException
     */
    public function getById($messageId)
    {
        $message = $this->messageFactory->create();
        $this->resource->load($message, $messageId);

        if (!$message->getId()) {
            throw new NoSuchEntityException(__('Message with id "%1" does not exist.', $messageId));
        }

        return $message;
    }

    /**
     * Load Message data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return MessageSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var MessageCollection $collection */
        $collection = $this->messageCollectionFactory->create();

        /** @var MessageSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete Message
     *
     * @param MessageInterface $message
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(MessageInterface $message)
    {
        try {
            $this->resource->delete($message);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete Message by given Message Id
     *
     * @param int $messageId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($messageId)
    {
        return $this->delete($this->getById($messageId));
    }
}
