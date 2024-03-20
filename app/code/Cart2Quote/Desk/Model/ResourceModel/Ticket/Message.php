<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;

/**
 * Class Message
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket
 */
class Message extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource status model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('desk_ticket_message', 'message_id');
    }
}
