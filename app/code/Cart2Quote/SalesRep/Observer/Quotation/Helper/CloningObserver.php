<?php
/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer\Quotation\Helper;

use Cart2Quote\SalesRep\Model\TypeFactory;
use Cart2Quote\SalesRep\Model\ResourceModel\User\Collection;

/**
 * Class CloningObserver
 *
 * @package Cart2Quote\SalesRep\Observer\Quotation\Helper
 */
class CloningObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var TypeFactory
     */
    public $salesRepUserFactory;

    /**
     * @var Collection
     */
    public $userCollection;

    /**
     * CloningObserver constructor.
     *
     * @param \Cart2Quote\SalesRep\Model\TypeFactory
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\User\Collection
     */
    public function __construct(
        \Cart2Quote\SalesRep\Model\TypeFactory $salesRepUserFactory,
        \Cart2Quote\SalesRep\Model\ResourceModel\User\Collection $userCollection
    ) {
        $this->salesRepUserFactory = $salesRepUserFactory;
        $this->userCollection = $userCollection;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $ogUser = $this->userCollection->getItemById($observer->getData('salesRepUserId'));
        if (isset($ogUser)) {
            $newUser = $this->salesRepUserFactory->create();
            $excludeFromCopy = [
                'id',
                'object_id',
                'created',
                'updated'
            ];

            $userData = array_diff_key($ogUser->getData(), array_flip($excludeFromCopy));
            $newUser->setData($userData);
            $newUser->setObjectId($observer->getData('newObjectId'));
            $newUser->save();
        }
    }
}
