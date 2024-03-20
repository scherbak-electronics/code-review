<?php

namespace Rainytownmedia\Restrictspam\Model;

use Magento\Cron\Exception;
use Magento\Framework\Model\AbstractModel;

class Logspam extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Rainytownmedia\Restrictspam\Model\ResourceModel\Logspam::class);
    }
}