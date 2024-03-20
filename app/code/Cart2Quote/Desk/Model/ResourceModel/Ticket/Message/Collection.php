<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket\Message;

/**
 * Class Collection
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket\Message
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\Ticket\Message', 'Cart2Quote\Desk\Model\ResourceModel\Ticket\Message');
    }

    /**
     * Add user information to the message
     *
     * @return $this
     */
    public function innerJoinUser()
    {
        $this->getSelect()->joinLeft(
            ['au' => $this->getTable('admin_user')],
            'au.user_id = main_table.user_id',
            [
                'user_firstname' => 'au.firstname',
                'user_lastname' => 'au.lastname',
                'email' => 'au.email'
            ]
        );
        return $this;
    }

    /**
     * Add customer information to the message.
     *
     * @return $this
     */
    public function innerJoinCustomer()
    {
        $this->getSelect()->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'ce.entity_id = main_table.customer_id',
            [
                'email' => 'ce.email',
            ]
        );
        return $this;
    }

    /**
     * Add customer and user information.
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this
            ->innerJoinCustomer()
            ->innerJoinUser();

        return parent::_beforeLoad();
    }
}
