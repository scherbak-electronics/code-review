<?php

namespace Rainytownmedia\Restrictspam\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Logspam extends AbstractDb
{
    public function _construct()
    {
        $this->_init('rainytownmedia_log_spam', 'entity_id');
    }
}
