<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Desk\Model\Ticket;

/**
 * Class Status
 * @package Cart2Quote\Desk\Model\Ticket
 */
class Status extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_OPEN = 'open';
    const STATUS_PENDING = 'pending';
    const STATUS_SOLVED = 'solved';

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\ResourceModel\Ticket\Status');
    }
}
