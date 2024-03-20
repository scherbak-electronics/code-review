<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Observer\Quotation\Adminhtml\Quote;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreateObserver
 * Observes the create quote controller in the admin
 * When an Admin creates a new quote, he is assigned as Sales Rep
 * @package Cart2Quote\SalesRep\Observer\Quotation\Adminhtml\Quote
 */
class CreateObserver implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    /**
     * User Factory
     *
     * @var \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory
     */
    protected $userFactory;

    /**
     * CreateObserver constructor.
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory,
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
    ) {
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currentAdminId = $this->authSession->getUser()->getId();
        $user = $this->userFactory->create();
        $user->setIsMain(true);
        $user->setObjectId($observer->getData('quote')->getId());
        $user->setTypeId('quotation');
        $user->setUserId($currentAdminId);

        $this->userRepository->save($user);
    }
}
