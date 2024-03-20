<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Cart2Quote\AutoProposal\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $quotationQuoteTable = $setup->getTable('quotation_quote');
        $connection->addColumn(
            $quotationQuoteTable,
            \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::SEND_NOTIFY_SALESREP_EMAIL,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'comment' => 'Send notify salesrep email'
            ]
        );
        $connection->addColumn(
            $quotationQuoteTable,
            \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::NOTIFY_SALESREP_EMAIL_SENT,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'comment' => 'Notify salesrep email sent'
            ]
        );

        $setup->endSetup();
    }
}
