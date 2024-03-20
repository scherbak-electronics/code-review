<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel;

use Cart2Quote\SalesRep\Api\Data\OrderInterface;
use Cart2Quote\SalesRep\Api\Data\OrderSearchResultsInterface;
use Magento\Framework\Api\SortOrder;

/**
 * Class OrderRepository
 * @package Cart2Quote\SalesRep\Model\ResourceModel
 */
class OrderRepository implements \Cart2Quote\SalesRep\Api\OrderRepositoryInterface
{
    /**
     * @var \Cart2Quote\SalesRep\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var Order
     */
    private $orderResourceModel;

    /**
     * @var OrderSearchResultsInterface
     */
    private $searchResultsFactory;

    /**
     * @var \Cart2Quote\SalesRep\Model\ResourceModel\Order\Grid\Collection
     */
    private $orderCollection;

    /**
     * OrderRepository constructor.
     *
     * @param \Cart2Quote\SalesRep\Model\OrderFactory $orderFactory
     * @param Order $orderResourceModel
     * @param OrderSearchResultsInterface $orderSearchResultsInterfaceFactory
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\Order\Grid\Collection $orderCollection
     */
    public function __construct(
        \Cart2Quote\SalesRep\Model\OrderFactory $orderFactory,
        \Cart2Quote\SalesRep\Model\ResourceModel\Order $orderResourceModel,
        OrderSearchResultsInterface $orderSearchResultsInterfaceFactory,
        \Cart2Quote\SalesRep\Model\ResourceModel\Order\Grid\Collection $orderCollection
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderResourceModel = $orderResourceModel;
        $this->searchResultsFactory = $orderSearchResultsInterfaceFactory;
        $this->orderCollection = $orderCollection;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(OrderInterface $order)
    {
        if ($existingOrder = $this->getMainTypeByAssociatedId($order->getId())) {
            $order->setId($existingOrder->getId());
        }

        $orderModel = $this->orderFactory->create();
        $orderModel->updateData($order);

        $this->orderResourceModel->save($orderModel);
        $orderModel->afterLoad();
        $order = $orderModel->getDataModel();

        return $order;
    }

    /**
     * @param int $orderId
     * @return OrderInterface
     */
    public function getById($orderId)
    {
        $orderModel = $this->orderFactory->create();
        $this->orderResourceModel->load($orderModel, $orderId);
        $orderModel->afterLoad();

        return $orderModel->getDataModel();
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchCriteriaInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->orderCollection;

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
        $order = [];
        /** @var \Cart2Quote\SalesRep\Model\Order $orderModel */
        foreach ($collection as $orderModel) {
            $order[] = $orderModel->getDataModel();
        }

        $searchResults->setItems($order);
        return $searchResults;
    }

    /**
     * @param OrderInterface $order
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(OrderInterface $order)
    {
        return $this->deleteById($order->getId());
    }

    /**
     * @param int $orderId
     * @return bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function deleteById($orderId)
    {
        $order = $this->getById($orderId);
        $this->save($order);

        return true;
    }
}
