<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab\Container;

/**
 * Class Status
 * @package Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab\Container
 */
class Status extends \Cart2Quote\Desk\Block\Adminhtml\Edit\Container\Status
{
    /**
     * Get ticket data
     *
     * @return \Cart2Quote\Desk\Model\Ticket
     */
    public function getTicket()
    {
        return $this->getParentBlock()->getTicket();
    }
}
