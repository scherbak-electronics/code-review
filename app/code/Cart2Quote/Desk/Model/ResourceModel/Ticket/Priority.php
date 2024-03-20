<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;

/**
 * Class Priority
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket
 */
class Priority extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource status model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('desk_ticket_priority', 'priority_id');
    }

    /**
     * Load ticket priority by code
     *
     * @param \Cart2Quote\Desk\Model\Ticket\Priority $ticketPriority
     * @param string $priorityCode
     * @return $this
     */
    public function loadByCode(\Cart2Quote\Desk\Model\Ticket\Priority $ticketPriority, $priorityCode)
    {
        $this->load($ticketPriority, $priorityCode, 'code');
        return $this;
    }
}
