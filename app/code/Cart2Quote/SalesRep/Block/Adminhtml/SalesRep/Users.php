<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Cart2Quote
 */
namespace Cart2Quote\SalesRep\Block\Adminhtml\SalesRep;

/**
 * Class Users
 * @package Cart2Quote\SalesRep\Block\Adminhtml\SalesRep
 */
class Users extends \Magento\Backend\Block\Template
{
    /**
     * User Collection
     *
     * @var \Cart2Quote\SalesRep\Model\AdminUser\Collection
     */
    private $userCollection;

    /**
     * Users constructor.
     * @param \Cart2Quote\SalesRep\Model\AdminUser\Collection $userCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\SalesRep\Model\AdminUser\Collection $userCollection,
        \Magento\Backend\Block\Template\Context $context,

        array $data
    ) {
        $this->userCollection = $userCollection;
        parent::__construct($context, $data);
    }

    /**
     * Get a list of admin users
     *
     * @return array
     */
    public function getUserList()
    {
        $this->userCollection
            ->addOrder('firstname', 'ASC')
            ->addOrder('lastname', 'ASC')
            ->load();

        return $this->userCollection->setAddUnassigned(false)->toOptionArray();
    }
}
