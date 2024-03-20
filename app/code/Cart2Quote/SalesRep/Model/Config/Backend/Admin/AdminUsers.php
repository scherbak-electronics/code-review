<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Config\Backend\Admin;

/**
 * Class AdminUsers
 *
 * @package Cart2Quote\SalesRep\Model\Config\Backend\Admin
 */
class AdminUsers implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * User Collection
     *
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $userCollection = null;

    /**
     * AdminUsers constructor
     *
     * @param \Magento\User\Model\ResourceModel\User\Collection $userCollection
     */
    public function __construct(\Magento\User\Model\ResourceModel\User\Collection $userCollection)
    {
        $this->userCollection = $userCollection;
    }

    /**
     * Convert user list to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getUserList();
    }

    /**
     * Get a list of admin users
     *
     * @return array
     */
    protected function getUserList()
    {
        $users = [];
        $this->userCollection->addOrder('lastname', 'ASC');
        foreach ($this->userCollection as $user) {
            $values['value'] = $user->getId();
            $values['label'] = $user->getName();
            $users[] = $values;
        }

        return $users;
    }
}
