<?php

namespace Customerprice\Attachment\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init('Customerprice\Attachment\Model\Grid', 'Customerprice\Attachment\Model\ResourceModel\Grid');
    }
}
