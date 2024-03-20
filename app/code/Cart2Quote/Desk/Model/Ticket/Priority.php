<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\Ticket;

use Magento\Catalog\Model\Product;

/**
 * Class Priority
 * @package Cart2Quote\Desk\Model\Ticket
 */
class Priority extends \Magento\Framework\Model\AbstractModel
{
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority');
    }
}
