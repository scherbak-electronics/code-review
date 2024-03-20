<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Config\Source;

/**
 * Class UserRoles
 * @package Cart2Quote\SalesRep\Model\Config\Source
 */
class UserRoles implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Authorization\Model\ResourceModel\Role\Grid\CollectionFactory
     */
    protected $roleCollectionFactory;

    /**
     * UserRoles constructor.
     * @param \Magento\Authorization\Model\ResourceModel\Role\Grid\CollectionFactory $roleCollectionFactory
     */
    public function __construct(
        \Magento\Authorization\Model\ResourceModel\Role\Grid\CollectionFactory $roleCollectionFactory
)   {
        $this->roleCollectionFactory = $roleCollectionFactory;
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
        $roleCollection = $this->roleCollectionFactory->create();

        foreach ($roleCollection as $user) {
            $values['value'] = $user->getRoleId();
            $values['label'] = $user->getRoleName();
            $users[] = $values;
        }

        return $users;
    }
}
