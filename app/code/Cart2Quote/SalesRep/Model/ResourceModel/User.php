<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel;

/**
 * Class User
 * @package Cart2Quote\SalesRep\Model\ResourceModel
 */
class User extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('salesrep_user', 'id');
        // die("test model/resourceModel/user");
        
    }
}
