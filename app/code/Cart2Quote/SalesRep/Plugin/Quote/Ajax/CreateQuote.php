<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Plugin\Quote\Ajax;

use Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid\Collection;

/**
 * Class CreateQuote
 *
 * @package Cart2Quote\SalesRep\Plugin\Quote\Ajax
 */
class CreateQuote
{
    /**
     * @var \Cart2Quote\SalesRep\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * CreateQuote constructor.
     *
     * @param \Cart2Quote\SalesRep\Model\Order $order
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
      \Cart2Quote\SalesRep\Model\Order $order,
      \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
      \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->order = $order;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Cart2Quote\Quotation\Controller\Quote\Ajax\CreateQuote $subject
     */
    public function beforeProcessAction(\Cart2Quote\Quotation\Controller\Quote\Ajax\CreateQuote $subject)
    {
        $quote = $subject->getOnepage()->getQuote();
        $customerId = $quote->getCustomerId();

        if (!isset($customerId)) {
            $email = $subject->getRequest()->getParam('customer_email');

            if (isset($email)) {
                $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();

                try {
                    $customer = $this->customerRepository->get($email, $currentWebsiteId);
                    $customerId = $customer->getId();
                } catch(\Magento\Framework\Exception\NoSuchEntityException $exception){
                    $customerId = null;
                }
            }
        }

        if ($this->order->isStickySet($customerId)) {
            $this->order->setStickyAssigned($quote);
        } else if ($this->order->isAssignedSalesRepsSet()) {
            if ($quote->getUserId() === null) {
                $quote->setUserId($this->order->getUserId());
                $this->order->createUser($quote, $quote->getUserId());
            }
        }
    }
}
