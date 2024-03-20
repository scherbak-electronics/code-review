<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Customer\Model;

use Cart2Quote\SalesRep\Observer\AfterSaveAbstractObserver;

/**
 * Class CustomerAfterSaveObserver
 * @package Cart2Quote\SalesRep\Observer\Customer\Model
 */
class CustomerAfterSaveObserver extends AfterSaveAbstractObserver
{

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * CustomerAfterSaveObserver constructor.
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository,
        \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
        parent::__construct($userRepository, $userFactory);
    }

    /**
     * Get the object type
     *
     * @return string
     */
    public function getTypeId()
    {
        return \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER;
    }

    /**
     * Get the user id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getUserId(\Magento\Framework\Model\AbstractModel $object)
    {
        $userId = false;

        /** @var array $customerPostData */
        $customerPostData = $this->request->getParam('customer', false);
        if (is_array($customerPostData) && isset($customerPostData['user_id']) ) {
            $userId = (int)$customerPostData['user_id'];
        }

        return $userId;
    }
}
