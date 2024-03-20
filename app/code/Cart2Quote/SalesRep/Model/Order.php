<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model;

/**
 * Class Order
 * This class has nothing to do with Magento Sales Order
 * This is to assign salesrep order
 *
 * @package Cart2Quote\SalesRep\Model
 */
class Order extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Order Factory
     *
     * @var \Cart2Quote\SalesRep\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Cart2Quote\SalesRep\Helper\Data
     */
    private $helper;

    /**
     * @var \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory
     */
    private $userFactory;

    /**
     * @var \Cart2Quote\SalesRep\Api\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var null|\Cart2Quote\SalesRep\Model\Data\User
     */
    private $stickUser = null;

    /**
     * @var \Cart2Quote\SalesRep\Model\ResourceModel\Order\Collection
     */
    private $orderCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Order constructor.
     *
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
     * @param \Cart2Quote\SalesRep\Helper\Data $helper
     * @param \Cart2Quote\SalesRep\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Order $resource
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\Order\Collection $orderCollection
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\Order\Grid\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository,
        \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory,
        \Cart2Quote\SalesRep\Helper\Data $helper,
        \Cart2Quote\SalesRep\Model\OrderFactory $orderFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\SalesRep\Model\ResourceModel\Order $resource,
        \Cart2Quote\SalesRep\Model\ResourceModel\Order\Collection $orderCollection,
        \Cart2Quote\SalesRep\Model\ResourceModel\Order\Grid\Collection $resourceCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
        $this->helper = $helper;
        $this->orderFactory = $orderFactory;
        $this->orderCollection = $orderCollection;
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        $salesRepUsers = $this->helper->getAssignedSalesReps();

        return $this->checkOrder($salesRepUsers);
    }

    /**
     * @param int $salesRepUsers
     * @return int
     */
    public function checkOrder($salesRepUsers)
    {
        //$lastOrder = $this->getCollection()->getLastItem();
        $lastOrder = $this->orderCollection->getLastItem();
        if ($lastOrder !== null) {
            $lastOrderKey = $lastOrder->getOrder();
            $nextOrder = $this->findNextOrder($salesRepUsers, $lastOrderKey);
        } else {
            $nextOrder = 0;
        }

        // save order
        $order = $this->orderFactory->create();
        $userId = $salesRepUsers[$nextOrder];
        $order->setOrder($nextOrder);
        $order->setUserId($userId);
        $order->setStoreId($this->storeManager->getStore()->getId());
        $order->save();

        return $salesRepUsers[$nextOrder];
    }


    /**
     * @param array $array
     * @param int $value
     * @return int
     */
    public function findNextOrder($array, $value)
    {
        $nextKey = $value + 1;
        if ($nextKey >= count($array)) {
            // reached end of array, reset
            $nextKey = 0;
        }

        return $nextKey;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $userId
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface
     */
    public function createUser(\Magento\Framework\Model\AbstractModel $object, $userId)
    {
        $user = $this->userFactory->create();
        $user->setIsMain(true)
            ->setObjectId($object->getId())
            ->setTypeId('quotation')
            ->setUserId($userId);

        return $this->userRepository->save($user);
    }

    /**
     * Check if set assigned sales reps is set.
     *
     * @return bool
     */
    public function isAssignedSalesRepsSet()
    {
        if ($this->helper->getAssignedSalesReps() == null) {
            return false;
        }

        return true;
    }

    /**
     * @param int|null $customerId
     * @return bool
     */
    public function isStickySet($customerId)
    {
        if (isset($customerId)) {
            $user = $this->userRepository->getMainUserByAssociatedId(
                $customerId,
                \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER
            );

            if ($user->getId()) {
               $this->stickUser = $user;

                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function setStickyAssigned(\Magento\Quote\Model\Quote $quote)
    {
        $this->createUser($quote, $this->stickUser->getUserId());
    }
}
