<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Observer\Magento\Sales\Adminhtml;

use Cart2Quote\SalesRep\Api\Data\UserInterface;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderCreateProcessObserver
 * Observes the create order controller in the admin
 * When an Admin creates a new order, he is assigned as Sales Rep
 * @package Cart2Quote\SalesRep\Observer\Quotation\Adminhtml\Quote
 */
class OrderCreateProcessObserver implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * User Factory
     *
     * @var \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory
     */
    private $userFactory;

    /**
     * @var \Cart2Quote\SalesRep\Api\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * CreateObserver constructor.
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory,
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository,
        \Magento\Framework\App\State $state
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->state = $state;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteId = $observer->getOrder()->getQuoteId();
        if (!$quoteId) {
            return;
        }

        try {
            $quote = $this->quoteRepository->get($quoteId);
        } catch(\Exception $e) {
            // Do nothing
        }

        $linkedQuotationId = $quote->getLinkedQuotationId();
        if (isset($linkedQuotationId)) {
            $user = $this->userRepository->getMainUserByAssociatedId(
                $linkedQuotationId,
                \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_QUOTATION
            );

            $this->copyUser($user, $quoteId);
        } elseif($this->state->getAreaCode() === 'adminhtml') {
            $this->assignLoggedInSalesRep($quoteId);
        }
    }


    /**
     * Copy the salesrep to order
     *
     * @param UserInterface $user
     * @param int $quoteId
     * @return void
     */
    private function copyUser(UserInterface $user, $quoteId)
    {
        if ($user->getUserId()) {
            $orderUser = $this->userFactory->create();
            $orderUser->setObjectId($quoteId);
            $orderUser->setTypeId($this->getTypeId());
            $orderUser->setUserId($user->getUserId());
            $orderUser->setIsMain(true);

            $this->userRepository->save($orderUser);
        }
    }

    /**
     * Assign logged in salesrep to order
     * 
     * @param $quoteId
     */
    private function assignLoggedInSalesRep($quoteId)
    {
        $currentAdminId = $this->authSession->getUser()->getId();
        $user = $this->userFactory->create();
        $user->setIsMain(true);
        $user->setObjectId($quoteId);
        $user->setTypeId('order');
        $user->setUserId($currentAdminId);

        $this->userRepository->save($user);
    }

    /**
     * Get the object type
     *
     * @return string
     */
    public function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_ORDER;
    }

    /**
     * Get the user id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getUserId(\Magento\Framework\Model\AbstractModel $object)
    {
        return $object->getUserId();
    }

    /**
     * Get the quote id because order id is not always available and it makes it easier to make the link to quotes.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getObjectId(\Magento\Framework\Model\AbstractModel $object)
    {
        return $object->getQuoteId();
    }
}
