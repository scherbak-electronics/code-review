<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Model\ResourceModel;

/**
 * Class Message
 * @package Cart2Quote\DeskMessageTemplate\Model\ResourceModel
 */
class Message extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource ticket model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('desk_message_template', 'message_id');
    }
}
