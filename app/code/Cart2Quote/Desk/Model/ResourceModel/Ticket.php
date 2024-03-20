<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel;

/**
 * Class Ticket
 * @package Cart2Quote\Desk\Model\ResourceModel
 */
class Ticket extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource ticket model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('desk_ticket', 'ticket_id');
    }
}
