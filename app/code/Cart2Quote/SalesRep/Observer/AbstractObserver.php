<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AbstractObserver
 * @package Cart2Quote\SalesRep\Observer
 */
abstract class AbstractObserver implements ObserverInterface, ModelAbstractInterface
{
    /**
     * User repository
     *
     * @var \Cart2Quote\SalesRep\Api\UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * User Factory
     *
     * @var \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory
     */
    protected $userFactory;

    /**
     * TicketObserver constructor.
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
     */
    public function __construct(
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository,
        \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
    ) {
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
    }

    /**
     * The function that gets executed when the event is observed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\Model\AbstractModel $object */
        $object = $observer->getDataObject();
        if (!$object->getUserId() && $this->getObjectId($object)) {
            $user = $this->userRepository->getMainUserByAssociatedId(
                $this->getObjectId($object),
                $this->getTypeId()
            );

            if ($user->getId()) {
                $object->setUserId($user->getUserId());
                $object->setSalesRepId($user->getId());
            } else {
                $object->setUserId(0);
                $object->setSalesRepId(0);
            }

            $this->saveByCustomerSalesRep($object, $object->getSalesRepId());
        }
    }

    /**
     * Get the object Id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getObjectId(\Magento\Framework\Model\AbstractModel $object)
    {
        return $object->getId();
    }

    /**
     * Get customer type
     *
     * @return string
     */
    public function getCustomerType()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER;
    }

    /**
     * Create new user
     *
     * @param \Magento\Framework\Model\AbstractModel  $object
     * @param int $userId
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface
     */
    protected function createUser(\Magento\Framework\Model\AbstractModel $object, $userId)
    {
        $user = $this->userFactory->create();
        $user->setIsMain(true)
            ->setObjectId($this->getObjectId($object))
            ->setTypeId($this->getTypeId())
            ->setUserId($userId);

        return $this->userRepository->save($user);
    }

    /**
     * Save the object by the associated customer's sales rep
     *
     * @param $object
     * @param int $salesRepId
     * @return void
     */
    protected function saveByCustomerSalesRep($object, $salesRepId)
    {
        if (!$object instanceof \Magento\Customer\Model\Customer && $object->getCustomerId() && !$object->getUserId()) {
            $customerId = $object->getCustomerId();
            $user = $this->userRepository->getMainUserByAssociatedId(
                $customerId,
                $this->getCustomerType()
            );

            if ($user->getUserId() && $salesRepId > 0) {
                $user = $this->createUser($object, $user->getUserId());
                $object->setUserId($user->getUserId());
                $object->setSalesRepId($user->getId());
            }
        }
    }

    /**
     * Delete user
     *
     * @param \Magento\Framework\Model\AbstractModel  $object
     * @return $this
     */
    protected function deleteUser(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->userRepository->delete(
            $this->userRepository->getMainUserByAssociatedId(
                $this->getObjectId($object),
                $this->getTypeId()
            )
        );

        return $this;
    }
}
