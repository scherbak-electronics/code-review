<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Cart2Quote\Desk\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade schema action
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $quoteIdColumnName = 'quote_id';
            $ticketTable = $setup->getTable('desk_ticket');
            if (!$setup->getConnection()->tableColumnExists($ticketTable, $quoteIdColumnName)) {
                $setup->getConnection()->addColumn(
                    $ticketTable,
                    'quote_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'comment' => 'Quote ID'
                    ]
                );
            }

            $customerIdColumn = 'customer_id';
            $ticketTable = $setup->getTable('desk_ticket');
            if ($setup->getConnection()->tableColumnExists($ticketTable, $customerIdColumn)) {
                $setup->getConnection()
                    ->addForeignKey(
                        $setup->getFkName('desk_ticket', $customerIdColumn, 'customer_entity', 'entity_id'),
                        $ticketTable,
                        $customerIdColumn,
                        $setup->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    );
            }

            $ticketMessageTable = $setup->getTable('desk_ticket_message');
            if ($setup->getConnection()->tableColumnExists($ticketMessageTable, $customerIdColumn)) {
                $setup->getConnection()
                    ->addForeignKey(
                        $setup->getFkName('desk_ticket_message', $customerIdColumn, 'customer_entity', 'entity_id'),
                        $ticketMessageTable,
                        $customerIdColumn,
                        $setup->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    );
                $setup->getConnection()
                    ->addForeignKey(
                        $setup->getFkName('desk_ticket_message', 'ticket_id', $ticketTable, 'ticket_id'),
                        $ticketMessageTable,
                        'ticket_id',
                        $ticketTable,
                        'ticket_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    );
            }
        }

        if (version_compare($context->getVersion(), '1.3.1') < 0) {
            $ticketTable = $setup->getTable('desk_ticket');
            if (!$setup->getConnection()->tableColumnExists($ticketTable, 'assignee_name')) {
                $setup->getConnection()->addColumn(
                    $ticketTable,
                    'assignee_name',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        'nullable' => true,
                        'comment' => 'Assignee Name'
                    ]
                );
            }

            if (!$setup->getConnection()->tableColumnExists($ticketTable, 'customer_email')) {
                $setup->getConnection()->addColumn(
                    $ticketTable,
                    'customer_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        'nullable' => true,
                        'comment' => 'Customer Email'
                    ]
                );
            }

            if (!$setup->getConnection()->tableColumnExists($ticketTable, 'customer_name')) {
                $setup->getConnection()->addColumn(
                    $ticketTable,
                    'customer_name',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        'nullable' => true,
                        'comment' => 'Customer name'
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '1.3.2') < 0) {
            $quoteIdColumnName = 'customer_viewed_at';
            $ticketTable = $setup->getTable('desk_ticket');
            if (!$setup->getConnection()->tableColumnExists($ticketTable, $quoteIdColumnName)) {
                $setup->getConnection()->addColumn(
                    $ticketTable,
                    $quoteIdColumnName,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                        'comment' => 'Last time customer viewed ticket'
                    ]
                );
            }
        }
    }
}
