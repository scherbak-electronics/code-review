<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // @codingStandardsIgnoreStart
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {// @codingStandardsIgnoreEnd
        $setup->startSetup();

        /**
         * Create table 'pronko_elavon_transaction_report'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('pronko_elavon_transaction_report')
        )->addColumn(
            'txn_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'primary' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Txn ID'
        )->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'Transaction Id'
        )->addColumn(
            'account',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            [],
            'Account'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'Order Increment Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Status'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['default' => '0.0000'],
            'Amount'
        )->addColumn(
            'base_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            ['nullable' => false],
            'Currency'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'batch_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            [],
            'Batch Id'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Message'
        )->addColumn(
            'auth_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            [],
            'Auth Code'
        )->addIndex(
            $setup->getIdxName(
                'pronko_elavon_transaction_report',
                ['transaction_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['transaction_id']
        )->setComment(
            'Elavon Transaction Report'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
