<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Setup;

/**
 * Class Uninstall
 *
 * @package Cart2Quote\AutoProposal\Setup
 */
class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $quotationQuoteTable = $setup->getTable('quotation_quote');

        $columns = [
            \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::SEND_NOTIFY_SALESREP_EMAIL,
            \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::NOTIFY_SALESREP_EMAIL_SENT
        ];
        foreach ($columns as $column) {
            if ($connection->tableColumnExists($quotationQuoteTable, $column)) {
                $connection->dropColumn($quotationQuoteTable, $column);
            }
        }

        $setup->endSetup();
    }
}
