<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;

/**
 * Class Status
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket
 */
class Status extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource status model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('desk_ticket_status', 'status_id');
    }

    /**
     * Load ticket status by code
     *
     * @param \Cart2Quote\Desk\Model\Ticket\Status $ticketStatus
     * @param string $statusCode
     * @return $this
     */
    public function loadByCode(\Cart2Quote\Desk\Model\Ticket\Status $ticketStatus, $statusCode)
    {
        $this->load($ticketStatus, $statusCode, 'code');
        return $this;
    }
}
