<?php

namespace Rainytownmedia\Restrictspam\Model\ResourceModel\Logspam;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Rainytownmedia\Restrictspam\Model\Logspam', 'Rainytownmedia\Restrictspam\Model\ResourceModel\Logspam');
    }
}