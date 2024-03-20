<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Model\ResourceModel\Report;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Settlement
 * @package     Pronko\Elavon\Model\ResourceModel\Report
 */
class Settlement extends AbstractDb
{
    protected function _construct() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->_useIsObjectNew = true;
        $this->_isPkAutoIncrement = false;
        $this->_init('pronko_elavon_transaction_report', 'txn_id');
    }
}
