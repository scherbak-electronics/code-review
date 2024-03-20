<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Plugin\Adminhtml\Sales;

use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid;
use Magento\Framework\Registry;

/**
 * Class TransactionPlugin
 * @package     Pronko\Elavon\Plugin\Adminhtml\Sales
 */
class TransactionPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * TransactionPlugin constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // @codingStandardsIgnoreStart
    public function beforeGetTransactionAdditionalInfo(Grid $grid)
    {// @codingStandardsIgnoreEnd
        $transaction = $this->registry->registry('current_transaction');
        if ($transaction instanceof TransactionInterface) {
            if (!$transaction->getAdditionalInformation('raw_details_info') &&
                $transaction->getAdditionalInformation('pasref')
            ) {
                $transaction->setAdditionalInformation('raw_details_info', $transaction->getAdditionalInformation());
            }
        }
    }
}
