<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel;

/**
 * Class Type
 * @package Cart2Quote\SalesRep\Model\ResourceModel
 */
class Type extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('salesrep_type', 'type_id');
    }
}

