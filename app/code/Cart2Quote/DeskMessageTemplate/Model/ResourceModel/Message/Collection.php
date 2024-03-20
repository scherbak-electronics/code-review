<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message;

/**
 * Class Collection
 * @package Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'message_id';

    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Cart2Quote\DeskMessageTemplate\Model\Message::class,
            \Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message::class
        );
    }

    /**
     * Get collection data as options hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('content', 'title');
    }
}